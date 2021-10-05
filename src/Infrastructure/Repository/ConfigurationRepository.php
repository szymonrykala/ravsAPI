<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Configuration\Configuration;
use App\Domain\Configuration\IConfigurationRepository;
use App\Infrastructure\Database\IDatabase;


final class ConfigurationRepository implements IConfigurationRepository
{

    private string $table = 'configuration';


    /**
     * @param IDatabase db database
     */
    public function __construct(IDatabase $db)
    {
        $this->db = $db;
        $this->db->connect();
    }


    /**
     * {@inheritDoc}
     */
    public function load(): Configuration
    {
        $data = $this->db->query("SELECT * FROM $this->table");
        return new Configuration($data);
    }

    /**
     * {@inheritDoc}
     */
    public function save(Configuration $configuration): void
    {
        $configuration->validate();
        $arr = [
            'BUILDING_IMAGE' => $configuration->buildingImage,
            'DEFAULT_USER_ACCESS' => $configuration->defaultUserAccess,
            'MAX_IMAGE_SIZE' => $configuration->maxImageSize,
            'MAX_RESERVATION_TIME' => $configuration->maxReservationTime->i,
            'MIN_RESERVATION_TIME' => $configuration->minReservationTime->i,
            'ROOM_IMAGE' => $configuration->roomImage,
            'USER_IMAGE' => $configuration->userImage,
            'RESERVATION_HISTORY' => $configuration->reservationHistory,
            'REQUEST_HISTORY' => $configuration->requestHistory
        ];

        $sql = "";

        foreach ($arr as $key => $value) {
            $sql .= "UPDATE `$this->table` SET `value` = :$key WHERE `key` = '$key';";
            $params[':' . $key] = $value;
        }

        $this->db->query($sql, $params);
    }
}
