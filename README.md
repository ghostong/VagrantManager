## VagrantManager
    VagrantManager 一个网页版的Vagrant管理工具 .
    Vagrant 是一个强大的虚拟环境管理的工具 .
    此版本开发的初衷是快速给研发部门部署虚拟机 .
 
#### 1. 环境依赖
    PHP 7.2 +
    php-swoole
    PHP Composer
    
#### 2. composer.json 配置文件
````json
{
  "require": {
    "lit/litms": "dev-master",
    "lit/utils": "dev-master"
  }
}
````
#### 3. 服务配置
    LITMS_HTTP_HOST    服务监听IP
    LITMS_HTTP_PORT    服务监听端口
    VAGRANT_ROOT       vagrant 虚拟机文件保存目录(此为自定义目录,需有读写权限)
    VAGRANT_DATA_DIR   系统文件保存目录 (此为自定义目录,需有读写权限)
    VAGRANT_PASSWORD   虚拟机ROOT默认密码
    LITMS_OPEN_BASEDIR 读取文件限制(安全)目录

#### 4. 启动运行环境
````php
php Server.php
````

#### 5. 后记
    突发想法,耗时7天(空余时间),用我仅有的一点前端知识+多年不用的PHP开发此本项目.