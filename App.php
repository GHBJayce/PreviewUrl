<?php

namespace App;

use JonnyW\PhantomJs\Client;
use JonnyW\PhantomJs\DependencyInjection\ServiceContainer;

class App
{
    public function phpPhantomjs()
    {
        $url = empty($_GET['url']) ? 'https://baidu.com' : $_GET['url'];

        if (!parse_url($url)) {
            exit('错误的url');
        }

        $replace_list = [
            'http://' => '^_',
            'https://' => '^_^',
            '\\' => ord('\\'),
            '/' => ord('/'),
            ':' => ord(':'),
            '*' => ord('*'),
            '?' => ord('?'),
            '"' => ord('"'),
            '<' => ord('<'),
            '>' => ord('>'),
            '|' => ord('|'),
        ];

        $screenshot_path = 'screenshot/'.strtr(rtrim($url, '/'), $replace_list).'.jpg';

        if (!file_exists($screenshot_path)) {
            /*
             * 按照自定义脚本写法，跑起来我发现会运行超时，并且会卡死nginx服务，重启nginx服务无法正常打开页面，必须重启电脑才能恢复服务
             *
             * 估计哪个步骤出了问题
             *
             * 我的环境：php 5.6 + nginx 1.11.5
             */
            /*
            $location = __DIR__ . DIRECTORY_SEPARATOR . 'script';
            $serviceContainer = ServiceContainer::getInstance();
            $procedureLoader = $serviceContainer->get('procedure_loader_factory')->createProcedureLoader($location);
            */

            $client = Client::getInstance();
            $client->getEngine()->setPath(__DIR__.DIRECTORY_SEPARATOR.'bin/phantomjs.exe');

            /*
            $client->setProcedure('get_capture_base64');
            $client->getProcedureLoader()->addLoader($procedureLoader);
            */

            /*
            $request  = $client->getMessageFactory()->createRequest();
            $response = $client->getMessageFactory()->createResponse();
            */

            $width = '1024';
            $height = '768';
            $top = 0;
            $left = 0;

            $request = $client->getMessageFactory()->createCaptureRequest($url, 'get');
            $request->setOutputFile($screenshot_path);
            $request->setViewportSize($width, $height);
            $request->setCaptureDimensions($width, $height, $top, $left);

            // var_dump($client->getProcedure());die; // 下面之前dump还未调用服务

            // 以下code已经开始调用服务
            $response = $client->getMessageFactory()->createResponse();
            $client->send($request, $response);
        }

        $this->generate($screenshot_path);
    }

    /**
     * 直接用命令调用服务
     *
     * 跑了几次结果发现，有一些网址内容大多没办法在30秒内跑完
     *
     * 能跑出图来一般要花个3秒甚至更久
     */
    public function command()
    {
        set_time_limit(240);
        header('Content-Type: image/jpeg');
        $url = $_GET['url'];
        $exe = __DIR__ . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'phantomjs.exe';
        $script = __DIR__ .DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'script.js';
        $res = exec("{$exe} {$script} {$url}");
        if ($res !== 'error') {
//        var_dump($res);die;
            $image = imagecreatefromstring(base64_decode($res));
            imagejpeg($image);
            imagedestroy($image);
        }
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
        $root = 'http://'.$_SERVER['HTTP_HOST'];
        $path = '/index.php?action=';

        $url_list = [
            [
                'url' => 'https://baidu.com',
                'text' => '百度',
            ],
            [
                'url' => 'https://so.com',
                'text' => '360搜索',
            ],
            [
                'url' => 'https://cn.bing.com',
                'text' => 'bing搜索',
            ],
            [
                'url' => 'https://www.hotbot.com',
                'text' => 'HotBot搜索',
            ],
            [
                'url' => 'https://www.sina.com.cn',
                'text' => '新浪',
            ],
            [
                'url' => 'http://www.sohu.com',
                'text' => '搜狐',
            ],
            [
                'url' => 'https://www.qq.com',
                'text' => '腾讯网',
            ],
            [
                'url' => 'http://www.ifeng.com',
                'text' => '凤凰网',
            ],
            [
                'url' => 'https://weibo.com',
                'text' => '微博',
            ],
            [
                'url' => 'https://www.youku.com',
                'text' => '优酷',
            ],
            [
                'url' => 'http://www.iqiyi.com',
                'text' => '爱奇艺',
            ],
            [
                'url' => 'https://taobao.com',
                'text' => '淘宝',
            ],
            [
                'url' => 'https://tmall.com',
                'text' => '天猫',
            ],
            [
                'url' => 'https://jd.com',
                'text' => '京东',
            ],
            [
                'url' => 'https://www.php.net',
                'text' => 'PHP',
            ],
            [
                'url' => 'https://www.bootcss.com',
                'text' => 'Bootstrap',
            ],
            [
                'url' => 'https://v3.bootcss.com/components/#progress-striped',
                'text' => '组件 · Bootstrap v3 中文文档',
            ],
            [
                'url' => 'https://learnku.com/docs/laravel/5.8/releases/3876',
                'text' => 'Laravel 5.8中文文档',
            ],
            [
                'url' => 'http://jonnnnyw.github.io/php-phantomjs',
                'text' => 'php-phantomjs docs',
            ],
        ];

        $lis = '';

        foreach ($url_list as $v) {
//            $lis .= "<li><a href=\"{$v['url']}\"><img data-original=\"{$root}{$path}command&url={$v['url']}\"><p>{$v['text']}</p></a></li>";
            $lis .= "<li><a href=\"{$v['url']}\"><img data-original=\"{$root}{$path}phpPhantomjs&url={$v['url']}\"><p>{$v['text']}</p></a></li>";
        }

        $html = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'test.html');
        $html = strtr($html, [
            '{{ $url_list }}' => $lis,
        ]);

        echo $html;
    }

    protected function generate($image_path)
    {
        header('Content-Type: image/jpeg');
        $image = imagecreatefromjpeg($image_path);
        imagejpeg($image);
        imagedestroy($image);
    }
}