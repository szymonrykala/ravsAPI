<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Domain\Access\IAccessRepository;
use App\Domain\Exception\DomainResourceNotFoundException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpForbiddenException;

class UpdateUserAccess extends UserAction
{
    private IAccessRepository $accessRepository;


    public function __construct(ContainerInterface $di)
    {
        parent::__construct($di);
        $this->accessRepository = $di->get(IAccessRepository::class);
    }

    /**
     * {@inheritDoc}
     */
    public function action(): Response
    {

        $userId = (int) $this->resolveArg($this::USER_ID);
        $form = $this->getFormData();

        if ($userId === 1)
            throw new HttpForbiddenException(
                $this->request,
                "Nie można zmienić klasy dostępu administratora"
            );

        try {
            $this->accessRepository->byId($form->accessId);
        } catch (DomainResourceNotFoundException $e) {
            $e->message = 'Taka klasa dostępu nie istnieje';
            throw $e;
        }

        $user = $this->userRepository->byId($userId);

        $user->accessId = $form->accessId;

        $this->userRepository->save($user);

        $this->logger->info("User id {$user->id} changed access to id {$form->accessId}");

        return $this->respondWithData();
    }
}
