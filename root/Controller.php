<?php
class Controller extends Lit\LitMs\LitMsController {
    function __construct(){
        //首页
        $this->get('/',function ($request,&$response){
            return View("Index.html");
        });

        //列表页
        $this->get('/list',function ($request,&$response){
            return Model("List");
        });

        //列表页
        $this->get('/add',function ($request,&$response){
            return View("AddVm.html");
        });


        $this->get('/vagrant/boxList',function (){
            return Model("Vagrant")->vagrantBoxList();
        });

        $this->post('/vagrant/submitAdd',function ($request,&$response){
            return Model("Vagrant")->vagrantAddVm($request);
        });

        $this->get('/vagrant/vmList',function (){
            return Model("Vagrant")->vagrantVmList();
        });

    }
}