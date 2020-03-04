<?php
//autoload
require(__DIR__.'/vendor/autoload.php');

//自定义常量
define("VAGRANT_ROOT","/vagrant/auto/");  //vagrant 虚拟机文件保存目录

define("VAGRANT_DATA_DIR",__DIR__.DIRECTORY_SEPARATOR."Data".DIRECTORY_SEPARATOR); //系统文件保存目录

define("VAGRANT_PASSWORD","123@456@"); //虚拟机ROOT默认密码


$server = new \Lit\Ms\LitMsServer();

$server
        ->setHttpHost("0.0.0.0")    //设置监听host ip
        ->setHttpPort(8080)    //设置 监听端口
        ->setWorkerNum(10)    //设置 进程数量
        ->setWorkDir(__DIR__)    //设置项目目录
        ->setDaemonize(false)    //设置是否守护进程
        ->setOpenBaseDir(__DIR__)    //设置读取安全目录
        ->setOpenBaseDir(VAGRANT_ROOT)
        ->setDocumentRoot(__DIR__.DIRECTORY_SEPARATOR."Static")    //设置静态目录
        ->run();