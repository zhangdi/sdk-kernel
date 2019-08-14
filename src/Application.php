<?php


namespace ZhangDi\SdkKernel;


use GuzzleHttp\Client;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use ZhangDi\SdkKernel\Contracts\ServiceProviderInterface;
use ZhangDi\SdkKernel\Exceptions\InvalidConfigException;
use ZhangDi\SdkKernel\Providers\ConfigServiceProvider;
use ZhangDi\SdkKernel\Providers\LogServiceProvider;
use ZhangDi\SdkKernel\Providers\RequestServiceProvider;
use ZhangDi\SdkKernel\Support\Collection;

/**
 * @package ZhangDi\Sdk\Kernel
 *
 * @property Logger $logger
 * @property Client $client
 * @property Collection $config
 */
abstract class Application extends ContainerBuilder
{
    /**
     * @var string 应用 ID
     */
    public $id = 'base';
    /**
     * @var string 应用名称
     */
    public $name = 'Base Application';
    /**
     * @var array 服务提供商
     */
    protected $providers = [];
    /**
     * @var array 默认配置
     */
    protected $defaultConfig = [];

    /**
     * @var array 用户配置
     */
    protected $userConfig = [];


    public function __construct(array $config)
    {
        parent::__construct();

        $this->userConfig = $config;

        $this->registerProviders($this->getProviders());
    }


    /**
     * 获取服务提供商
     *
     * @return array
     */
    public function getProviders(): array
    {
        return \array_merge([
            ConfigServiceProvider::class,
            LogServiceProvider::class,
            RequestServiceProvider::class
        ], $this->providers);
    }

    /**
     * 注册服务提供商
     *
     * @param array $providers
     * @throws InvalidConfigException
     */
    public function registerProviders(array $providers)
    {
        foreach ($providers as $name => $provider) {
            if (\is_int($name)) {
                throw new InvalidConfigException('providers array key must be as string.');
            }
            $obj = new $provider();

            if ($obj instanceof ServiceProviderInterface) {
                $obj->register($this);
            } else {
                throw new InvalidConfigException('Provider class must implements ' . ServiceProviderInterface::class);
            }

        }
    }

    /**
     * 获取配置信息
     *
     * @return array
     */
    public function getConfig(): array
    {
        $config = [
            'request' => [
                'timeout' => 30
            ]
        ];

        return \array_replace_recursive($config, $this->defaultConfig, $this->userConfig);
    }

    /**
     * 魔术方法，根据名称找服务对象
     *
     * @param string $name
     * @return object
     * @throws \Exception
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }
}