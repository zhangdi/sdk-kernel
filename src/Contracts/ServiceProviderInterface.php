<?php


namespace ZhangDi\SdkKernel\Contracts;


use ZhangDi\SdkKernel\Application;

interface ServiceProviderInterface
{
    /**
     * 注册服务
     *
     * @param Application $app 应用
     */
    public function register(Application $app);
}