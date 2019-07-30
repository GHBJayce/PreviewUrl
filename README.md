
# PreviewUrl

网址预览

主要想尝试利用`phantomjs`无头浏览器访问网址并截图，得到的截图base64码再转交PHP处理输出到浏览器上

## 快速开始

下载[phantomjs.exe](https://phantomjs.org/download.html)，将软件放到`./bin`目录下

```shell
composer install
php -S http://127.0.0.1:8080
```

访问：http://127.0.0.1:8080/index.php?action=index