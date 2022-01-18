<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Configuration\Configuration;
use App\Domain\Configuration\IConfigurationRepository;
use App\Infrastructure\Database\IDatabase;


final class ConfigurationRepository implements IConfigurationRepository
{

    private string $table = '`configuration`';


    public function __construct(
        private IDatabase $db
    ) {
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
            'DEFAULT_USER_ACCESS' => $configuration->defaultUserAccess,
            'MAX_IMAGE_SIZE' => $configuration->maxImageSize,
            'MAX_RESERVATION_TIME' => $configuration->maxReservationTime->i,
            'MIN_RESERVATION_TIME' => $configuration->minReservationTime->i,
            'RESERVATION_HISTORY' => $configuration->reservationHistory,
            'REQUEST_HISTORY' => $configuration->requestHistory
        ];

        foreach ($arr as $key => $value) {
            $this->db->query(
                "UPDATE $this->table SET `value` = :$key WHERE `key` = '$key';",
                [
                    ":$key" => $value
                ]
            );
        }
    }
}
