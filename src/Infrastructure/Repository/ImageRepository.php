<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Configuration\Configuration;
use App\Domain\Configuration\IConfigurationRepository;
use App\Domain\Exception\DomainBadRequestException;
use Psr\Http\Message\UploadedFileInterface;
use App\Infrastructure\Repository\BaseRepository;

use App\Domain\Image\{
    FileTypeException,
    Image,
    ImageSizeExceededException,
    IImageRepository,
};
use App\Domain\Model\Model;
use App\Utils\JsonDateTime;
use Cloudinary\Cloudinary;
use Exception;
use Psr\Container\ContainerInterface;



final class ImageRepository extends BaseRepository implements IImageRepository
{
    protected string $table = 'image';
    protected Configuration $configuration;
    private Cloudinary $cloudinary;


    public function __construct(
        ContainerInterface $di,
        IConfigurationRepository $configuration
    ) {
        parent::__construct($di);
        $this->configuration = $configuration->load();
        $this->cloudinary = $di->get(Cloudinary::class);
    }


    /**
     * Uploads file to the Cloudinary and applies transformation
     * @return array uploaded file info
     */
    private function uploadToCloudinary(UploadedFileInterface $file): array
    {
        try {
            $resp = $this->cloudinary->uploadApi()->upload($file->getStream(), [
                'resource_type' => 'image',
                'folder' => 'ravs-images',
                'discard_original_filename' => TRUE,
                'unique_filename' => TRUE,
                'transformation' => 'ravs-image-transformation'
            ]);
        } catch (Exception $e) {
            throw new DomainBadRequestException($e->getMessage());
        }

        return $resp->getArrayCopy();
    }

    /**
     * deletes image from Cloudinary by public_id
     */
    private function deleteFromCloudinary(string $publicId): void
    {
        try {
            $this->cloudinary->adminApi()->deleteAssets($publicId);
        } catch (Exception $e) {
            throw new DomainBadRequestException($e->getMessage());
        }
    }

    /**
     * {@inheritDoc}
     * @return Image $image
     */
    protected function newItem(array $data): Image
    {
        return new Image(
            (int)   $data['id'],
            $data['public_id'],
            (int)   $data['size'],
            $data['url'],
            new JsonDateTime($data['created']),
            new JsonDateTime($data['updated'])
        );
    }


    /**
     * Delete image if it is not default one
     * {@inheritDoc}
     * @param Image $image
     */
    public function delete(Model $image): void
    {
        // default images has url as NULL
        if ($image->url !== NULL) {
            $this->deleteFromCloudinary($image->publicId);
            // delete record from database; 
            $sql = "DELETE FROM $this->table WHERE `id` = :imageId AND `url` IS NOT NULL";
            $this->db->query($sql, [':imageId' => $image->id]);
        }
    }


    /**
     * {@inheritDoc}
     * @throws ImageSizeExceededException
     */
    public function save(UploadedFileInterface $file): int
    {
        if (!str_contains($file->getClientMediaType(), 'image')) throw new FileTypeException();

        if ($file->getSize() > $this->configuration->maxImageSize) {
            throw new ImageSizeExceededException($this->configuration->maxImageSize);
        }

        $data = $this->uploadToCloudinary($file);

        $sql = "INSERT INTO `$this->table` (`url`, `public_id`, `size`, `created`) 
        VALUES(:url, :publicId, :size, :created)";

        $params = [
            'url' => $data['secure_url'],
            'publicId' => $data['public_id'],
            'size' => $data['bytes'],
            'created' => new JsonDateTime($data['created_at'])
        ];

        try {
            $this->db->query($sql, $params);
        } catch (Exception $e) {
            $this->deleteFromCloudinary($data['public_id']);
            throw $e;
        }

        return $this->db->lastInsertId();
    }
}
