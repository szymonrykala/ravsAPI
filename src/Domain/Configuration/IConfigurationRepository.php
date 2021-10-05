<?php

declare(strict_types=1);

namespace App\Domain\Configuration;

interface IConfigurationRepository
{

    /**
     * @return Configuration object
     */
    public function load(): Configuration;

    /**
     * @param Configuration object
     * @return void
     */
    public function save(Configuration $configuration): void;
}
