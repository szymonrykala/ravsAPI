<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Application\Exception\HttpUnprocessableEntityException;
use Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Validator;



class SchemaValidationMiddleware extends BaseMiddleware
{

    /** path to folder with schemas */
    private const BASE_FOLDER = '../src/Domain/Schema';

    /** base schema uri */
    private const BASE_URI = 'api://schema/';

    /** map of json schemas for each endpoint */
    private const LOAD_MAP = [
        'POST' => [
            '/v1/users/auth' => '/user/login.json',
            '/v1/users/key' => '/user/generateKey.json',
            '/v1/users' => '/user/create.json',
            '/v1/accesses' => '/access/create.json',
            '/v1/addresses' => '/address/create.json',
            '/v1/addresses/id/buildings' => '/building/create.json',
            '/v1/addresses/id/buildings/id/rooms' => '/room/create.json',
            '/v1/addresses/id/buildings/id/rooms/id/reservations' => '/reservation/create.json',
        ],
        'PATCH' => [
            '/v1/users/activate' => '/user/activate.json',
            '/v1/users/password' => '/user/changePassword.json',
            '/v1/users/id' => '/user/update.json',
            '/v1/users/id/access' => '/user/updateAccess.json',
            '/v1/accesses/id' => '/access/update.json',
            '/v1/addresses/id' => '/address/update.json',
            '/v1/addresses/id/buildings/id' => '/building/update.json',
            '/v1/addresses/id/buildings/id/rooms/id' => '/room/update.json',
            '/v1/addresses/id/buildings/id/rooms/id/reservations/id' => '/reservation/update.json',
            '/v1/configurations' => '/configuration/update.json',
            '/v1/addresses/id/buildings/id/rooms/id/reservations/id/keys' => '/key/update.json',
            '/v1/addresses/id/buildings/id/rooms/id/keys' => '/key/update.json',
        ]
    ];

    /**
     * {@inheritDoc}
     */
    public function processRequest(RequestHandler $handler): Response
    {
        $this->path = $this->getProcessURI();

        if ($this->shouldBeValidated()) {

            $validator = new Validator();
            $validator->setMaxErrors(4);
            $validator->resolver()
                ->registerPrefix($this::BASE_URI, $this::BASE_FOLDER);

            $data = $this->request->getParsedBody();

            $result = $validator->validate(
                $data,
                $this::BASE_URI . $this::LOAD_MAP[$this->getMethod()][$this->path]
            );

            if (!$result->isValid()) {
                $this->combineResult($result);
            }
        }

        return $handler->handle($this->request);
    }

    /**
     * decide wether request body be validated or not
     */
    private function shouldBeValidated(): bool
    {
        return isset($this::LOAD_MAP[$this->getMethod()][$this->getProcessURI()]);
    }

    /**
     * returns parsed request path string
     * ie.: /user/2 => /user/id
     */
    private function getProcessURI(): string
    {
        return preg_replace(
            '/([a-z]+)\/(\d+)/',
            '$1/id',
            $this->getPath()
        );
    }

    /**
     * Combines error results from validation
     */
    private function combineResult($result): void
    {
        $error = $result->error();
        $formater = new ErrorFormatter();

        $errorMessage = implode(', ', $formater->format($error, false));

        throw new HttpUnprocessableEntityException($this->request, $errorMessage);
    }
}
