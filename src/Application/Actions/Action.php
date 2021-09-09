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
     * @param  string $name
     * @return mixed
     * @throws HttpBadRequestException
     */
    protected function resolveArg(string $name)
    {
        if (!isset($this->args[$name])) {
            throw new HttpBadRequestException($this->request, "Could not resolve argument `{$name}`.");
        }

        return $this->args[$name];
    }


    /**
     * @param string $keys
     * @return stdClass[] 
     */
    public function collectDatesSearchParam(string $keys = 'created|updated'): array
    {
        $query = $this->request->getUri()->getQuery();

        preg_match_all(
            "/($keys)([=<>])(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\+\d{4})?)/",
            urldecode($query),
            $output_array
        );

        if (empty($output_array) || empty($output_array[0])) {
            return [];
        }

        $results = [];
        for ($i = 0; $i < count($output_array[0]); $i++) {
            array_push($results, (object)[
                'name' => $output_array[1][$i],
                'operator' => $output_array[2][$i],
                'value' => $output_array[3][$i]
            ]);
        }

        return $results;
    }


    public function collectPageQueryParams(): stdClass
    {
        $isPage = isset($_GET['page']) && is_numeric($_GET['page']);
        $pageLimit = isset($_GET['page_limit']) && is_numeric($_GET['page_limit']);

        $data = ['isset' => $isPage];

        if ($isPage) {
            $data['page'] = (int) $_GET['page'];
            $data['limit'] = $pageLimit ?  (int) $_GET['page_limit'] : 20;
        }

        return (object) $data;
    }

    /**
     * @param Pagination data
     * @return void
     */
    public function sendPaginationData(Pagination $data): void {
        $this->pagination = $data;
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
