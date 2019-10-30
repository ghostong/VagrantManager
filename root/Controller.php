<?php
class Controller extends Lit\LitMs\LitMsController {
    function __construct(){
        //首页
        $this->get('/',function (){
            return View("Index.html");
        });

        //创建虚拟机页面
        $this->get('/vm/add',function (){
            return View("AddVm.html");
        });

        //获取可用操作系统
        $this->get('/api/opSystemList',function (){
            return Model("VmApi")->opSystemList();
        });

        //提交创建虚拟机请求
        $this->post('/api/submitAdd',function ($request){
            return Model("VmApi")->addVm($request);
        });

        //虚拟机列表
        $this->get('/api/vmList',function (){
            return Model("VmApi")->vmList();
        });

        //虚拟机开机
        $this->post('/api/vmUp',function ($request){
            return Model("VmApi")->vmUp($request);
        });

        //虚拟机重启
        $this->post('/api/vmReload',function ($request){
            return Model("VmApi")->vmReload($request);
        });

        //虚拟机关机
        $this->post('/api/vmOff',function ($request){
            return Model("VmApi")->vmOff($request);
        });

        //虚拟机释放
        $this->post('/api/vmDestroy',function ($request){
            return Model("VmApi")->vmDestroy($request);
        });

        //网卡列表
        $this->get('/api/netCardList',function (){
            return Model("VmApi")->netCardList();
        });
    }
}