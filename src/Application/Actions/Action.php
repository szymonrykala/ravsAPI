<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Application\Exception\HttpConflictException;
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




abstract class Action
{
    protected const ROOM_ID = 'room_id';
    protected const BUILDING_ID = 'building_id';
    protected const ADDRESS_ID = 'address_id';
    protected const USER_ID = 'user_id';
    protected const ACCESS_ID = 'access_id';
    protected const IMAGE_ID = 'image_id';
    protected const RESERVATION_ID = 'reservation_id';
    protected const REQUEST_SUBJECT = 'request_subject';
    protected const REQUEST_SUBJECT_ID = 'subject_id';

    protected const SEARCH_STRING = 'search';
    protected const FROM_DATE = 'from';
    protected const TO_DATE = 'to';

    /** HTTP request */
    protected Request $request;

    /** HTTP response */
    protected Response $response;

    protected ?Pagination $pagination = NULL;

    /** HTTP query args and params */
    protected array $args;


    public function __construct(
        protected LoggerInterface $logger
    ) {}

    /**
     * Called to handle request
     * @throws HttpNotFoundException
     * @throws HttpConflictException
     * @throws HttpForbiddenException
     * @throws HttpBadRequestException
     * @throws HttpUnauthorizedException
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

        } catch (Ex\DomainConflictException $e) {
            throw new HttpConflictException($this->request, $e->getMessage());

        } catch (Ex\DomainForbiddenOperationException $e) {
            throw new HttpForbiddenException($this->request, $e->getMessage());

        } catch (Ex\DomainBadRequestException $e) {
            throw new HttpBadRequestException($this->request, $e->getMessage());

        } catch (Ex\DomainUnauthenticatedException $e) {
            throw new HttpUnauthorizedException($this->request, $e->getMessage());
        }
    }

    /**
     * controller action handling endpoint request
     */
    abstract protected function action(): Response;

    /**
     * get form data of the request
     */
    protected function getFormData(): stdClass
    {
        return (object) $this->request->getParsedBody();
    }


    /**
     * Resolves argument from URI string
     * @param mixed $default
     * @throws HttpBadRequestException
     */
    protected function resolveArg(string $name, $default = NULL)
    {
        if (!isset($this->args[$name])) {
            if ($default !== NULL) return $default;

            throw new HttpBadRequestException($this->request, "Nie uda??o si?? pobra?? parametru `{$name}`.");
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

            throw new HttpBadRequestException($this->request, "Nie uda??o si?? pobra?? parametru `${name}` z zapytania.");
        }

        return $_GET[$name];
    }


    /**
     * prepares pagination feature object
     */
    public function preparePagination(): Pagination
    {
        $currentPage = (int) $this->resolveQueryArg(Pagination::CURRENT_PAGE, 0);
        $onPage = (int) $this->resolveQueryArg(Pagination::ITEMS_ON_PAGE, 15);

        $this->pagination = new Pagination($currentPage, $onPage);
        return $this->pagination;
    }


    /**
     * responds with given data
     * @param mixed $data
     */
    protected function respondWithData($data = null, int $statusCode = 200): Response
    {
        $payload = new ActionPayload($statusCode, $data, NULL, $this->pagination);

        return $this->respond($payload);
    }

    /**
     * sends response from the API with provided $payload
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
