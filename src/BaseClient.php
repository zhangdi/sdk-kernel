<?php


namespace ZhangDi\SdkKernel;


use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use ZhangDi\SdkKernel\Contracts\AccessTokenInterface;
use function GuzzleHttp\choose_handler;

class BaseClient
{
    /**
     * @var Application
     */
    public $app;

    /**
     * @var Client
     */
    protected $httpClient;
    protected $middlewares = [];
    /**
     * @var HandlerStack|null
     */
    protected $handlerStack;
    /**
     * @var
     */
    protected $baseUri;
    /**
     * @var AccessTokenInterface|null
     */
    protected $accessToken;

    /**
     * BaseHttpClient constructor.
     * @param Application $app
     */
    public function __construct(Application $app, AccessTokenInterface $accessToken = null)
    {
        $this->app = $app;
        $this->accessToken = $accessToken;
    }

    /**
     * @return Client
     */
    public function getHttpClient(): Client
    {
        if ($this->httpClient == null) {
            $config = $this->app->getConfig()['http_client'] ?? [];
            $this->httpClient = new Client($config);
        }
        return $this->httpClient;
    }

    /**
     * @return HandlerStack
     */
    public function getHandlerStack(): HandlerStack
    {
        if ($this->handlerStack) {
            return $this->handlerStack;
        }
        $this->handlerStack = HandlerStack::create(choose_handler());
        foreach ($this->middlewares as $name => $middleware) {
            $this->handlerStack->push($middleware, $name);
        }
        return $this->handlerStack;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @param bool $rawResponse
     * @return mixed|ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request(string $method, string $uri = '', array $options = [], bool $rawResponse = false)
    {
        $response = $this->performRequest($method, $uri, $options);

        if ($rawResponse) {
            return $response;
        }
        return $this->castResponse($response, $this->app->config->get('response_type', 'raw'));
    }

    /**
     * @param ResponseInterface $response
     * @param string|null $type
     * @return mixed
     */
    protected function castResponse(ResponseInterface $response, string $type = null)
    {
        $type = $type == null ? $type : 'raw';
        switch ($type) {
            case 'json':
                return \json_decode($response->getBody()->getContents(), true);
            case 'raw':
                return $response;
            default:
                return new $type($response);
        }
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function performRequest(string $method, string $uri = '', array $options = []): ResponseInterface
    {
        $this->registerMiddlewares();

        $options['handler'] = $this->getHandlerStack();

        if ($this->baseUri) {
            $options['base_uri'] = $this->baseUri;
        }

        $response = $this->getHttpClient()->request($method, $uri, $options);
        return $response;
    }

    /**
     * 注册中间件
     */
    protected function registerMiddlewares()
    {
        // access_token
        $this->middlewares['access_token'] = $this->accessTokenMiddleware();
    }

    /**
     * Attache access token to request query.
     *
     * @return \Closure
     */
    protected function accessTokenMiddleware()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                if ($this->accessToken) {
                    $request = $this->accessToken->applyToRequest($request, $options);
                }
                return $handler($request, $options);
            };
        };
    }
}