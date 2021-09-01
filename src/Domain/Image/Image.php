<?php

declare(strict_types=1);

namespace App\Domain\Image;

use App\Domain\Model\Model;
use DateTime;

class Image extends Model
{

    public string $path;

    /**
     * @param int       $id,
     * @param string    $path,
     * @param DateTime    $created,
     * @param DateTime    $updated
     */
    public function __construct(
        int $id,
        string $path,
        DateTime $created,
        DateTime $updated
    ) {
        parent::__construct($id, $created, $updated);

        $this->path = $path;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            "id" => $this->id,
            "path" => $this->path,
            "created" => $this->created->format('c'),
            "updated" => $this->updated->format('c')
        ];
    }
}
