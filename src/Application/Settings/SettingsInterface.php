<?php

declare(strict_types=1);

namespace App\Application\Settings;

interface SettingsInterface
{
    /**
     * returns value of settings parameter
     */
    public function get(string $key = ''): mixed;
}
