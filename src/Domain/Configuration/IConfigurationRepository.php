<?php

declare(strict_types=1);

namespace App\Domain\Configuration;

interface IConfigurationRepository
{

    /**
     * loads configuration from database
     */
    public function load(): Configuration;

    /**
     * saves state of configuration object
     */
    public function save(Configuration $configuration): void;
}
