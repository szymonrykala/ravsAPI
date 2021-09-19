<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Domain\Configuration\IConfigurationRepository;
use Psr\Http\Message\{
    ResponseInterface as Response,
    ServerRequestInterface as Request
};

use Psr\Http\Server\{
    MiddlewareInterface as Middleware,
    RequestHandlerInterface as RequestHandler
};



final class ConfigurationLoadingMiddleware implements Middleware
{

    private IConfigurationRepository $configs;

    public function __construct(IConfigurationRepository $configurationRepo)
    {
        $this->configs = $configurationRepo;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        /** @var Configuration */
        $configuration = $this->configs->load();

        return $handler->handle($request->withAttribute('configs',$configuration));
    }
}
