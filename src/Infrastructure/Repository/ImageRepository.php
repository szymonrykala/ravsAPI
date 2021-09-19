<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Application\Settings\SettingsInterface;

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

    public function __construct(IDatabase $db, SettingsInterface $settings)
    {
        parent::__construct($db);
        $settings = $settings->get('image');
        $this->directory = $settings['directory'];
        $this->maxSize = $settings['maxSize'];
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
            new JsonDateTime($data['created']),
            new JsonDateTime($data['updated'])
        );
    }


    /**
     * Delete image is it is not default and nobody is use it
     * {@inheritDoc}
     * @throws ImageDeleteException
     */
    public function delete(Model $image): void
    {
        // if image is one of the defaults, or can not delete from some reason throw error
        if (strpos($image->path, 'default') !== False || !unlink($this->public . $image->path)) {
            throw new ImageDeleteException($image->path);
        }

        // image is not default imageand is used by noone
        $sql = "DELETE FROM $this->table WHERE `id` = :imageId
                    AND `id` NOT IN (SELECT `value` FROM $this->configTable WHERE `key` IN ('BUILDING_IMAGE','ROOM_IMAGE','USER_IMAGE'))
                    AND (
                        SELECT COUNT(i.id) 
                            FROM image i
                                LEFT JOIN user u ON i.id = u.image 
                                LEFT JOIN room r ON i.id = r.image 
                                LEFT JOIN building b ON b.image = i.id 
                            WHERE i.id = :imageId
                    ) = 0
        ";
        $this->db->query($sql, [':imageId' => $image->id]);
    }

    /**
     * {@inheritDoc}
     * @throws ImageSizeExceededException
     */
    public function save(UploadedFileInterface $file): int
    {
        if ($file->getSize() > $this->maxSize) {
            throw new ImageSizeExceededException($this->maxSize);
        }
        $filename = $this->moveUploadedFile($this->public . $this->directory, $file);

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
    private function moveUploadedFile(string $directory, UploadedFileInterface $uploadedFile): string
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);

        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . $filename);

        return $filename;
    }
}
