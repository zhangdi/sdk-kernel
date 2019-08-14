<?php


namespace ZhangDi\SdkKernel\Providers;


use ZhangDi\SdkKernel\Contracts\ServiceProviderInterface;
use ZhangDi\SdkKernel\Application;
use ZhangDi\SdkKernel\Support\Collection;

class ConfigServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $obj = new Collection($app->getConfig());

        $app->set('config', $obj);
    }

}