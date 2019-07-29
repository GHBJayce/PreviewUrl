<?php

namespace App;

use JonnyW\PhantomJs\Client;
use JonnyW\PhantomJs\DependencyInjection\ServiceContainer;

class App
{
    public function phpPhantomjs()
    {
        /*
         * 按照如下写法，跑起来我发现会运行超时，并且会卡死nginx服务，重启nginx服务无法正常打开页面，必须重启电脑才能恢复服务
         *
         * 估计哪个步骤出了问题
         *
         * 我的环境：php 5.6 + nginx 1.11.5
         */
        $location = __DIR__ . DIRECTORY_SEPARATOR . 'script';
        $serviceContainer = ServiceContainer::getInstance();

        $procedureLoader = $serviceContainer->get('procedure_loader_factory')->createProcedureLoader($location);

        $client = Client::getInstance();

        $client->setProcedure('get_capture_base64');

        $client->getProcedureLoader()->addLoader($procedureLoader);

        $client->getEngine()->setPath('F:\\installed\\phpStudy\\PHPTutorial\\WWW\\preview-url\\bin\\phantomjs.exe');

//        $request  = $client->getMessageFactory()->createRequest();
//        $response = $client->getMessageFactory()->createResponse();

        $request = $client->getMessageFactory()->createCaptureRequest('https://baidu.com', 'get');
        $request->setViewportSize(640, 480);

        var_dump($client->getProcedure());die; // 下面之前dump还未调用服务

        // 以下code已经开始调用服务
        $response = $client->getMessageFactory()->createResponse();
        $client->send($request, $response);
    }

    /**
     * 直接用命令调用服务
     *
     * 跑了几次结果发现，有一些网址内容太多没办法在30秒内跑完
     *
     * 能跑出图来一般要花个3秒甚至更久
     */
    public function command()
    {
        set_time_limit(120);
        header('Content-Type: image/jpeg');
        $url = $_GET['url'];
        $exe = __DIR__ . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'phantomjs.exe';
        $script = __DIR__ .DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'script.js';
        $res = exec("{$exe} {$script} {$url}");
//        var_dump($res);die;
        $image = imagecreatefromstring(base64_decode($res));
        imagejpeg($image);
        imagedestroy($image);
    }

    public function index()
    {
        $root = 'http://'.$_SERVER['HTTP_HOST'];
        $path = '/index.php?action=';

        $list = [
            '<a href="'. $root . $path .'command&url=https://baidu.com">直接调用系统命令</a>',
            '<a href="'. $root . $path .'phpPhantomjs">使用“jonnyw/php-phantomjs”制作的包方式</a>',
            '<a href="'. $root . $path .'test">尝试一下</a>',
        ];

        echo implode('<br><br>', $list);
    }

    public function test()
    {
        echo <<<EOF
<style>
body {
    background-color: #078090;
}
img {
    width: 180px;
    height: 140px;
}
ul {
    list-style: none;
}
li {
    float: left;
    background-color: #9E9E9E;
    text-align: center;
    font-size: 13px;
    margin: 0 25px 25px 0;
}
</style>
EOF;

        $root = 'http://'.$_SERVER['HTTP_HOST'];
        $path = '/index.php?action=';

        $list = [
            '<ul>',
            '<li><a href="{{ baidu }}"><img data-original="'. $root . $path .'command&url={{ baidu }}"><p>百度</p></a></li>',
            '<li><a href="{{ so }}"><img data-original="'. $root . $path .'command&url={{ so }}"><p>360搜索</p></a></li>',
            '<li><a href="{{ bing }}"><img data-original="'. $root . $path .'command&url={{ bing }}"><p>bing搜索</p></a></li>',
            '<li><a href="{{ hotbot }}"><img data-original="'. $root . $path .'command&url={{ hotbot }}"><p>HotBot搜索</p></a></li>',
            '<li><a href="{{ baidu }}"><img data-original="'. $root . $path .'command&url={{ baidu }}"><p>百度</p></a></li>',
            '<li><a href="{{ so }}"><img data-original="'. $root . $path .'command&url={{ so }}"><p>360搜索</p></a></li>',
            '<li><a href="{{ bing }}"><img data-original="'. $root . $path .'command&url={{ bing }}"><p>bing搜索</p></a></li>',
            '<li><a href="{{ hotbot }}"><img data-original="'. $root . $path .'command&url={{ hotbot }}"><p>HotBot搜索</p></a></li>',
            '<li><a href="{{ baidu }}"><img data-original="'. $root . $path .'command&url={{ baidu }}"><p>百度</p></a></li>',
            '<li><a href="{{ so }}"><img data-original="'. $root . $path .'command&url={{ so }}"><p>360搜索</p></a></li>',
            '<li><a href="{{ bing }}"><img data-original="'. $root . $path .'command&url={{ bing }}"><p>bing搜索</p></a></li>',
            '<li><a href="{{ hotbot }}"><img data-original="'. $root . $path .'command&url={{ hotbot }}"><p>HotBot搜索</p></a></li>',
            '</ul>',
            '<script src="https://cdn.bootcss.com/jquery/3.4.1/jquery.min.js"></script>',
            '<script src="https://cdn.bootcss.com/jquery_lazyload/1.9.7/jquery.lazyload.min.js"></script>',
            '<script>$(function () {$("img").lazyload({effect: "fadeIn"});});</script>',
        ];

        $replace_url = [
            '{{ baidu }}' => 'https://baidu.com',
            '{{ so }}' => 'https://so.com',
            '{{ bing }}' => 'https://cn.bing.com',
            '{{ hotbot }}' => 'https://www.hotbot.com',
        ];

        $content = strtr(implode('', $list), $replace_url);

        echo $content;
    }
}