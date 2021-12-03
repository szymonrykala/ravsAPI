<?php

declare(strict_types=1);

namespace App\Application\Actions\Configuration;

use App\Domain\Access\IAccessRepository;
use App\Domain\Configuration\IConfigurationRepository;
use App\Domain\Image\IImageRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;



class UpdateConfiguration extends ConfigurationAction
{
    protected IImageRepository $imageRepository;
    protected IAccessRepository $accessRepository;


    public function __construct(
        LoggerInterface $logger,
        IConfigurationRepository $configurationRepository,
        IImageRepository $imageRepository,
        IAccessRepository $accessRepository
    ) {
        parent::__construct($logger, $configurationRepository);

        $this->configurationRepository = $configurationRepository;
        $this->imageRepository = $imageRepository;
        $this->accessRepository = $accessRepository;
    }

    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        $form = $this->getFormData();
        $configs = $this->configurationRepository->load();

        // check if the specified accesses exists
        foreach (['defaultUserAccess'] as $field) {
            if (isset($form->$field))
                $this->accessRepository->byId($form->$field);
        }

        $configs->update($form);

        $this->configurationRepository->save($configs);
        return $this->respondWithData();
    }
}
