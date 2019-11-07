<?php
//autoload
require(dirname(__DIR__).'/vendor/autoload.php');

//自定义常量
define("VAGRANT_ROOT","/home/ghost/vagrant/auto/");  //vagrant 虚拟机文件保存目录

define("VAGRANT_DATA_DIR",__DIR__.DIRECTORY_SEPARATOR."Data".DIRECTORY_SEPARATOR); //系统文件保存目录

define("VAGRANT_PASSWORD","123@456@"); //虚拟机ROOT默认密码


$server = new \Lit\LitMs\LitMsServer();

$server
        ->setHttpHost("0.0.0.0")    //设置监听host ip
        ->setHttpPort(8080)    //设置 监听端口
        ->setWorkerNum(10)    //设置 进程数量
        ->setWorkDir(__DIR__)    //设置项目目录
        ->setDaemonize(false)    //设置是否守护进程
        ->setOpenBaseDir(__DIR__)    //设置读取安全目录
        ->setOpenBaseDir(dirname(__DIR__).DIRECTORY_SEPARATOR."vendor")    //设置读取安全目录
        ->setOpenBaseDir(VAGRANT_ROOT)
        ->setLogFile("/tmp/litmsError.log")    //设置错误日志文件
        ->setLogLevel(0)    //设置输出错误等级
        ->setSlowLogFile("/tmp/litmsSlow.log")    //设置慢日志文件
        ->setSlowTimeOut(1)    //设置慢日志时间
        ->setDocumentRoot(__DIR__.DIRECTORY_SEPARATOR."Static")    //设置静态目录
        ->run();