<?php

declare(strict_types=1);

namespace App\Application\Actions\Configuration;

use Psr\Http\Message\ResponseInterface as Response;


class ViewConfiguration extends ConfigurationAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $configs = $this->configurationRepository->load();

        $this->logger->info('configuration has been fieved');

        return $this->respondWithData($configs);
    }
}
