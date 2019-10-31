<?php
class VmApiModel extends \Lit\LitMs\LitMsModel {

    //获取当前可用的操作系统
    function opSystemList(){
        $boxList = Model("Vagrant")->vagrantBoxList();
        return Success(["boxList"=>$boxList]);
    }

    //创建虚拟机
    function addVm ($request) {
        $vagrantConfig['cpuNum'] = $request->post["cpuNum"];
        $vagrantConfig['memNum'] = $request->post["memNum"];
        $vagrantConfig['opSystem'] = $request->post["opSystem"];
        $vagrantConfig['bridgeNetCard'] = $request->post["bridgeNetCard"];

        $hostId = Model("Vagrant")->vagrantInit($vagrantConfig);
        if( $hostId == -1 ){
            return Error(0,"Vagrant 目录已经存在,不能重复创建");
        }elseif($hostId == -2){
            return Error(0,"创建 Vagrantfile 失败");
        }elseif($hostId == -3){
            return Error(0,"创建 Vagrant 目录失败");
        }else{
            Model("Vagrant")->vagrantUp($hostId);
            return Success(["hostId"=>$hostId]);
        }
    }

    //虚拟机开机
    function vmUp ($request) {
        $hostId = $request->post["hostId"];
        if(!Model("Vagrant")->vagrantIsEff($hostId)){
            return Error(0,"hostId: ".$hostId." 不是有效的虚拟机");
        }
        Model("Vagrant")->vagrantUp($hostId);
        return Success(["hostId"=>$hostId]);
    }

    //虚拟机重启
    function vmReload ($request) {
        $hostId = $request->post["hostId"];
        if(!Model("Vagrant")->vagrantIsEff($hostId)){
            return Error(0,"hostId: ".$hostId." 不是有效的虚拟机");
        }
        Model("Vagrant")->vagrantReload($hostId);
        return Success(["hostId"=>$hostId]);
    }

    //虚拟机关机
    function vmOff ($request) {
        $hostId = $request->post["hostId"];
        if(!Model("Vagrant")->vagrantIsEff($hostId)){
            return Error(0,"hostId: ".$hostId." 不是有效的虚拟机");
        }
        Model("Vagrant")->vagrantHalt($hostId);
        return Success(["hostId"=>$hostId]);
    }
    //虚拟机关机
    function vmDestroy ($request) {
        $hostId = $request->post["hostId"];
        if(!Model("Vagrant")->vagrantIsEff($hostId)){
            return Error(0,"hostId: ".$hostId." 不是有效的虚拟机");
        }
        Model("Vagrant")->vagrantDestroy($hostId);
        return Success(["hostId"=>$hostId]);
    }

    //虚拟机列表
    function vmList (){
        $fileIterator = new \FilesystemIterator(VAGRANT_ROOT );
        $vmList = [];
        foreach($fileIterator as $fileInfo) {
            if(substr($fileInfo->getFilename(),0,1) == "."){
                continue;
            }
            if($fileInfo->isDir()){
                $vmList[] = ["hostId"=>$fileInfo->getFilename(),"cTime"=>date("Y-m-d H:i:s",$fileInfo->getCTime())];
            }
        }
        return Success($vmList);
    }

    //获取机器配置
    function getVmConfig ($request){
        $hostId = $request->get["hostId"];
        if(!Model("Config")->configIsEff($hostId)){
            return Error(0,"hostId: ".$hostId." 不是有效的虚拟机");
        }else{
            $config = Model("Config")->getConfig($hostId);
            return Success($config);
        }
    }

    //网卡列表
    function netCardList () {
        $cardList = Model("System")->getNetCard();
        if(empty($cardList)){
            return Error(0,[]);
        }else{
            return Success ($cardList);
        }
    }

    //获取虚拟机运行状态
    function getVmStatus($request){
        $hostId = $request->get["hostId"];
        if(!Model("Vagrant")->vagrantIsEff($hostId)){
            return Error(0,"hostId: ".$hostId." 不是有效的虚拟机");
        }
        $status = Model("Vagrant")->vagrantStatus($hostId);
        return Success(["hostId"=>$hostId,"status"=>$status]);
    }

}