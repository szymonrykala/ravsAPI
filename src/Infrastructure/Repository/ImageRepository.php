<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Application\Settings\SettingsInterface;
use App\Domain\Configuration\Configuration;
use App\Domain\Configuration\IConfigurationRepository;
use Psr\Http\Message\UploadedFileInterface;
use App\Infrastructure\Repository\BaseRepository;

use App\Infrastructure\Database\IDatabase;

use App\Domain\Image\{
    Image,
    ImageDeleteException,
    ImageSizeExceededException,
    ImageRepositoryInterface
};
use App\Domain\Model\Model;
use App\Utils\JsonDateTime;



class ImageRepository extends BaseRepository implements ImageRepositoryInterface
{

    private string $public = __DIR__ . '/../../../public';

    protected string $table = 'image';
    protected Configuration $configuration;

    public function __construct(
        IDatabase $db,
        SettingsInterface $settings,
        IConfigurationRepository $configurationRepository
    ) {
        parent::__construct($db);
        $settings = $settings->get('image');
        $this->directory = $settings['directory'];
        $this->configuration = $configurationRepository->load();
    }

    /**
     * {@inheritDoc}
     * @return Image $image
     */
    protected function newItem(array $data): Image
    {
        return new Image(
            (int) $data['id'],
            $data['path'],
            (int) $data['size'],
            new JsonDateTime($data['created']),
            new JsonDateTime($data['updated'])
        );
    }

    public function allLike(string $prefix): array
    {
        $sql = "SELECT * FROM $this->table WHERE `path` LIKE :path";
        $result = $this->db->query($sql, [
            ':path' => "%$prefix%"
        ]);

        return array_map(fn ($data) => $this->newItem($data), $result);
    }


    /**
     * Delete image if it is not default and nobody is use it
     * {@inheritDoc}
     * @throws ImageDeleteException
     */
    public function delete(Model $image): void
    {

        // query need to check if image is used to not throw an error while deleting user image while deleting user
        // image is not default imageand is used by noone
        $sql = "DELETE FROM $this->table WHERE `id` = :imageId
                    AND `id` NOT IN (SELECT `value` FROM $this->configTable WHERE `key` IN ('BUILDING_IMAGE','ROOM_IMAGE','USER_IMAGE'))
                    AND (
                           `id` IN(SELECT distinct image from room)
                        OR `id` IN(SELECT distinct image from building)
                        OR `id` IN(SELECT distinct image from user)
                    ) = 0
                ";
        $resp = $this->db->query($sql, [':imageId' => $image->id]);

        if(!empty($resp)){
            unlink($this->public . $image->path);
        }
    }


    /**
     * {@inheritDoc}
     * @throws ImageSizeExceededException
     */
    public function save(UploadedFileInterface $file, string $prefix = ''): int
    {
        if ($file->getSize() > $this->configuration->maxImageSize) {
            throw new ImageSizeExceededException($this->configuration->maxImageSize);
        }
        $filename = $this->moveUploadedFile($this->public . $this->directory, $file, $prefix);

        $sql = "INSERT INTO `$this->table` (`path`, `size`) VALUES(:path, :size)";
        $params = [
            'path' => $this->directory . $filename,
            'size' => $file->getSize()
        ];
        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }

    /**
     * Moves uploaded file to new location and return it's name
     */
    private function moveUploadedFile(string $directory, UploadedFileInterface $uploadedFile, string $prefix = ''): string
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);

        $basename = $prefix . '_' . bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . $filename);

        return $filename;
    }
}
