<?php


namespace ZhangDi\SdkKernel\Providers;


use GuzzleHttp\Client;
use ZhangDi\SdkKernel\Application;
use ZhangDi\SdkKernel\Contracts\ServiceProviderInterface;

class RequestServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app->register('request', Client::class);
    }

}