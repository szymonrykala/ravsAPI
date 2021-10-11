<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Configuration\Configuration;
use App\Domain\Configuration\IConfigurationRepository;
use Psr\Http\Message\UploadedFileInterface;
use App\Infrastructure\Repository\BaseRepository;

use App\Infrastructure\Database\IDatabase;

use App\Domain\Image\{
    FileTypeException,
    Image,
    ImageSizeExceededException,
    ImageRepositoryInterface,
};
use App\Domain\Model\Model;
use App\Utils\JsonDateTime;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Stream;

class ImageRepository extends BaseRepository implements ImageRepositoryInterface
{

    private const DIRECTORY = __DIR__ . '/../../../public/images/';

    protected string $table = 'image';
    protected Configuration $configuration;

    public function __construct(ContainerInterface $di ) {
        parent::__construct($di->get(IDatabase::class));
        $this->configuration = $di->get(IConfigurationRepository::class)->load();
    }

    /**
     * {@inheritDoc}
     * @return Image $image
     */
    protected function newItem(array $data): Image
    {
        return new Image(
            (int) $data['id'],
            $data['name'],
            (int) $data['size'],
            new JsonDateTime($data['created']),
            new JsonDateTime($data['updated'])
        );
    }

    /**
     * Delete image if it is not default and nobody is use it
     * {@inheritDoc}
     * @param Image $image
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

        if (!empty($resp)) {
            unlink($this->public . $image->name);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function viewImageFile(int $id):Stream
    {
        $image = $this->byId($id);
        $file = file_get_contents($this::DIRECTORY . $image->name);
        return (new StreamFactory())->createStream($file);
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

        $filename = $this->moveUploadedFile($this::DIRECTORY, $file);

        $sql = "INSERT INTO `$this->table` (`name`, `size`) VALUES(:name, :size)";
        $params = [
            'name' => $filename,
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

        $basename = '_' . bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . $filename);

        return $filename;
    }
}
