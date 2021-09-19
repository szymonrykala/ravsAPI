<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\{
    ResponseInterface as Response,
    ServerRequestInterface as Request
};

use Psr\Http\Server\{
    MiddlewareInterface as Middleware,
    RequestHandlerInterface as RequestHandler
};

use Opis\JsonSchema\{
    Errors\ErrorFormatter
};

use App\Domain\Exception\HttpUnprocessableEntityException;
use App\Utils\JsonSchemaValidator;

class SchemaValidationMiddleware implements Middleware
{

    private array $loadMap = [
        'POST' => [
            '/users/auth' => '/user/login.json',
            '/users/key' => '/user/generateKey.json',
            '/users' => '/user/create.json',
            '/accesses' => '/access/create.json',
            '/addresses' => '/address/create.json',
            '/addresses/id/buildings' => '/building/create.json',
            '/addresses/id/buildings/id/rooms' => '/room/create.json',
            '/addresses/id/buildings/id/rooms/id/reservations' => '/reservation/create.json',
        ],
        'PATCH' => [
            '/users/activate' => '/user/activate.json',
            '/users/password' => '/user/changePassword.json',
            '/users/id' => '/user/update.json',
            '/users/id/access' => '/user/updateAccess.json',
            '/accesses/id' => '/access/update.json',
            '/addresses/id' => '/address/update.json',
            '/addresses/id/buildings/id' => '/building/update.json',
            '/addresses/id/buildings/id/rooms/id' => '/room/update.json',
            '/addresses/id/buildings/id/rooms/id/reservations/id' => '/reservation/update.json',
            '/configurations' => '/configuration/update.json',
            '/addresses/id/buildings/id/rooms/id/reservations/id/keys' => '/key/update.json',
            '/addresses/id/buildings/id/rooms/id/keys' => '/key/update.json',

        ]
    ];

    /** @var Request request  */
    private Request $request;

    /** @var string request method */
    private string $method;

    /** @var string modified request URI */
    private string $path;

    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $this->request = $request;
        $this->method = $request->getMethod();
        $this->path = $this->getProcessURI();

        if ($this->shouldBeValidated()) {

            $validator = new JsonSchemaValidator();
            $validator->loadDefaults();

            $data = $this->request->getParsedBody();

            $this->result = $validator->validate(
                $data,
                JsonSchemaValidator::BASE_URI . $this->loadMap[$this->method][$this->path]
            );


            if (!$this->result->isValid()) {
                $this->combineResult();
            }
        }


        return $handler->handle($this->request);
    }

    /**
     * decide wether request body be validated or not
     * @return bool
     */
    private function shouldBeValidated(): bool
    {
        return isset($this->loadMap[$this->method][$this->path]);
    }

    /**
     * returns modified ( /d => 'id') path string
     * @return string
     */
    private function getProcessURI(): string
    {
        return preg_replace(
            '/([a-z]+)\/(\d+)/',
            '$1/id',
            $this->request->getUri()->getPath()
        );
    }


    private function combineResult(): void
    {
        $error = (new ErrorFormatter())->format($this->result->error());

        $errorMessage = '';
        foreach ($error as $root => $message) {
            $errorMessage .= $root . ' - ' . $message[0] . ' ';
        }

        throw new HttpUnprocessableEntityException($this->request, $errorMessage);
    }
}
