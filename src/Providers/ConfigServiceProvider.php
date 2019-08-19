<?php


namespace ZhangDi\SdkKernel\Providers;


use ZhangDi\Collections\Collection;
use ZhangDi\SdkKernel\Contracts\ServiceProviderInterface;
use ZhangDi\SdkKernel\Application;

class ConfigServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $obj = new Collection($app->getConfig());

        $app->set('config', $obj);
    }

}