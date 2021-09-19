<?php

declare(strict_types=1);

namespace App\Application\Actions;


use App\Domain\Exception as Ex;
use App\Utils\Pagination;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\{
    ResponseInterface as Response,
    ServerRequestInterface as Request
};
use Slim\Exception\{
    HttpBadRequestException,
    HttpNotFoundException,
    HttpForbiddenException,
    HttpUnauthorizedException
};
use stdClass;

use Opis\JsonSchema\Validator;



abstract class Action
{
    protected LoggerInterface $logger;

    protected Request $request;
    protected Response $response;

    protected ?Pagination $pagination = NULL;

    protected array $args;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws HttpNotFoundException
     * @throws HttpBadRequestException
     * @throws HttpForbiddenException
     * @throws HttpUnauthorizedException
     * @throws HttpConflictException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;
        $this->session = $request->getAttribute('session');


        try {
            $resp = $this->action();

            return $resp;
        } catch (Ex\DomainResourceNotFoundException $e) {
            throw new HttpNotFoundException($this->request, $e->getMessage());
        } catch (Ex\DomainUnauthorizedOperationException $e) {
            throw new HttpUnauthorizedException($this->request, $e->getMessage());
        } catch (Ex\DomainConflictException $e) {
            throw new Ex\HttpConflictException($this->request, $e->getMessage(), 409);
        } catch (Ex\DomainForbiddenOperationException $e) {
            throw new HttpForbiddenException($this->request, $e->getMessage());
        } catch (Ex\DomainBadRequestException $e) {
            throw new HttpBadRequestException($this->request, $e->getMessage());
        }
    }

    /**
     * @return Response
     * @throws DomainResourceNotFoundException
     * @throws HttpBadRequestException
     */
    abstract protected function action(): Response;

    /**
     * @return stdClass
     * @throws HttpBadRequestException
     */
    protected function getFormData(): stdClass
    {
        return $this->request->getParsedBody();
    }


    /**
     * Resolves argument from URI.
     * @param mixed $default
     * @throws HttpBadRequestException
     */
    protected function resolveArg(string $name, $default = NULL)
    {
        if (!isset($this->args[$name])) {
            if ($default !== NULL) return $default;

            throw new HttpBadRequestException($this->request, "Could not resolve URI argument `{$name}`.");
        }

        return $this->args[$name];
    }

    /**
     * Resolves argument from query string.
     * @param mixed $default
     * @throws HttpBadRequestException
     */
    protected function resolveQueryArg(string $name, $default = NULL)
    {
        // $isset = ;
        if (!isset($_GET[$name])) {
            if ($default !== NULL) return $default;

            throw new HttpBadRequestException($this->request, "Could not resolve query argument `${name}`.");
        }

        return $_GET[$name];
    }


    /**
     * @return Pagination
     */
    public function preparePagination(): Pagination
    {
        $currentPage = (int) $this->resolveQueryArg(Pagination::CURRENT_PAGE, 1);

        $onPage = (int) $this->resolveQueryArg(Pagination::ITEMS_ON_PAGE, 15);

        $this->pagination = new Pagination($currentPage, $onPage);
        return $this->pagination;
    }


    /**
     * @param array|object|null $data
     * @param int $statusCode
     * @return Response
     */
    protected function respondWithData($data = null, int $statusCode = 200): Response
    {
        $payload = new ActionPayload($statusCode, $data, NULL, $this->pagination);

        return $this->respond($payload);
    }

    /**
     * @param ActionPayload $payload
     * @return Response
     */
    protected function respond(ActionPayload $payload): Response
    {
        $json = json_encode($payload, JSON_PRETTY_PRINT);
        $this->response->getBody()->write($json);

        return $this->response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($payload->getStatusCode());
    }
}
