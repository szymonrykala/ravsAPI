<?php

declare(strict_types=1);

namespace App\Application\Actions\Configuration;

use App\Domain\Access\AccessRepositoryInterface;
use App\Domain\Configuration\IConfigurationRepository;
use App\Domain\Image\ImageRepositoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;



class UpdateConfiguration extends ConfigurationAction
{
    protected ImageRepositoryInterface $imageRepository;
    protected AccessRepositoryInterface $accessRepository;


    public function __construct(
        LoggerInterface $logger,
        IConfigurationRepository $configurationRepository,
        ImageRepositoryInterface $imageRepository
    ) {
        parent::__construct($logger, $configurationRepository);

        $this->configurationRepository = $configurationRepository;
        $this->imageRepository = $imageRepository;
    }

    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        $form = $this->getFormData();
        $configs = $this->request->getAttribute('configs');

        foreach (['userImage', 'roomImage', 'buildingImage'] as $field) {
            if (isset($form->$field)) $this->imageRepository->byId($form->$field);
        }

        foreach (['ownerAccess', 'defaultUserAccess'] as $field) {
            if (isset($form->$field)) $this->accessRepository->byId($form->$field);
        }

        $configs->update($form);

        $this->configurationRepository->save($configs);
        return $this->respondWithData();
    }
}
