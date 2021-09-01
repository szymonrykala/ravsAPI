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

use DateTime;



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
     * @param array $data from database
     * @return Image $image
     */
    protected function newItem(array $data): Image
    {
        return new Image(
            (int) $data['id'],
            $data['path'],
            new DateTime($data['created']),
            new DateTime($data['updated'])
        );
    }


    /**
     * {@inheritdoc}
     */
    public function deleteById(int $id): void
    {
        $image = $this->byId($id);

        if (strpos($image->path, 'default') !== False || !unlink($this->public . $image->path)) {
            throw new ImageDeleteException($image->path);
        }

        $sql = "DELETE FROM `$this->table` WHERE `id` = :id";
        $params = [':id' => $image->id];
        $this->db->query($sql, $params);
    }

    /**
     * {@inheritdoc}
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
     * @param string $directory
     * @param UploadFileInterface $uploadedFile
     * @return string $filename
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
