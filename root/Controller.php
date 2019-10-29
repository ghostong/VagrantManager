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


        $this->get('/vagrant/BoxList',function (){
            return Model("Vagrant")->vagrantBoxList();
        });

    }
}