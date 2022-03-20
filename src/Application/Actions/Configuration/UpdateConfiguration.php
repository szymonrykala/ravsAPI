<?php

declare(strict_types=1);

namespace App\Application\Actions\Configuration;

use App\Domain\Access\IAccessRepository;
use App\Domain\Configuration\IConfigurationRepository;
use App\Domain\Configuration\Validation\UpdateValidator;
use App\Domain\Image\IImageRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;



class UpdateConfiguration extends ConfigurationAction
{

    public function __construct(
        LoggerInterface $logger,
        IConfigurationRepository $configurationRepository,
        protected IImageRepository $imageRepository,
        protected IAccessRepository $accessRepository
    ) {
        parent::__construct($logger, $configurationRepository);
    }

    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        $form = clone $this->getFormData(); // copy value to not pass reference into the logging

        $validator = new UpdateValidator();
        $validator->validateForm($form);

        $configs = $this->configurationRepository->load();

        // check if the specified accesses exists
        foreach (['defaultUserAccess'] as $field) {
            if (isset($form->$field))
                $this->accessRepository->byId($form->$field);
        }

        $configs->update($form);

        $this->configurationRepository->save($configs);

        $this->logger->info('Configuration has been updated');

        return $this->respondWithData();
    }
}
