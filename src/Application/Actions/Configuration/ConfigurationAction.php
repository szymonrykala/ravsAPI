<?php

declare(strict_types=1);

namespace App\Application\Actions\Configuration;


use App\Application\Actions\Action;
use App\Domain\Configuration\IConfigurationRepository;

use Psr\Log\LoggerInterface;

abstract class ConfigurationAction extends Action
{

    public function __construct(
        LoggerInterface $logger,
        protected IConfigurationRepository $configurationRepository
    ) {
        parent::__construct($logger);
    }
}
