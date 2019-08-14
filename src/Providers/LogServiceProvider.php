<?php


namespace ZhangDi\SdkKernel\Providers;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use ZhangDi\SdkKernel\Application;
use ZhangDi\SdkKernel\Contracts\ServiceProviderInterface;

class LogServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $config = $app->getConfig()['log'] ?? null;
        if ($config == null) {
            $config = [
                'file' => \sys_get_temp_dir() . '/logs/' . $app->id . '.log',
            ];
        }
        $logger = new Logger($app->name);
        $logger->pushHandler(new StreamHandler($config['file']));

        $app->set('logger', $logger);
    }

}