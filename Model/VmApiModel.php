<?php
class VmApiModel extends \Lit\Ms\LitMsModel {

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
        $vagrantConfig['nickName'] = $request->post["nickName"];

        $hostId = Model("Vagrant")->vagrantInit($vagrantConfig);
        if( $hostId == -1 ){
            return Error(0,"Vagrant 目录已经存在,不能重复创建");
        }elseif($hostId == -2){
            return Error(0,"创建 Vagrantfile 失败");
        }elseif($hostId == -3){
            return Error(0,"创建 Vagrant 目录失败");
        }else{
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

    //镜像列表
    function imageList(){
        $list = Model("Image")->imageList();
        return Success(["imageDir"=>Model("Image")->getBoxDir(),"imageList"=>$list]);
    }

    //导入镜像
    function imageImport ($request) {
        $imageFile = $request->post["imageFile"];
        $imageName = $request->post["imageName"];
        if(Model("Vagrant")->vagrantBoxAdd($imageFile,$imageName)){
            return Success();
        }else{
            return Error();
        }
    }

    //删除镜像
    function imageDelete ($request) {
        $imageFile = $request->post["imageFile"];
        $imageName = $request->post["imageName"];
        if(Model("Vagrant")->vagrantBoxDelete($imageFile,$imageName)){
            return Success();
        }else{
            return Error();
        }
    }

    //配置目录
    function config () {
        $str = "";
        //VAGRANT_ROOT
        if(!defined("VAGRANT_ROOT")){
            $str .= "<p> <mark>VAGRANT_ROOT</mark> 常量未定义,请在<mark>Server.php</mark>中定义.</p>";
        }elseif(!is_dir(VAGRANT_ROOT)){
            $str .= "<p>". VAGRANT_ROOT ."不是有效目录,请自行创建.</p>";
        }elseif(!is_writable(VAGRANT_ROOT)){
            $str .= "<p>". VAGRANT_ROOT ."不可写,请修改目录权限.</p>";
        }

        //VAGRANT_DATA_DIR
        if(!defined("VAGRANT_DATA_DIR")){
            $str .= "<p> <mark>VAGRANT_DATA_DIR</mark> 常量未定义,请在<mark>Server.php</mark>中定义.</p>";
        }elseif(!is_dir(VAGRANT_DATA_DIR)){
            $str .= "<p>". VAGRANT_DATA_DIR ."不是有效目录,请自行创建.</p>";
        }elseif(!is_writable(VAGRANT_DATA_DIR)){
            $str .= "<p>". VAGRANT_DATA_DIR ."不可写,请修改目录权限.</p>";
        }

        if(!is_dir(Model("Image")->getBoxDir())){
            $str .= "<p>". Model("Image")->getBoxDir() ."不是有效目录,请自行创建.</p>";
        }elseif(!is_writable(Model("Image")->getBoxDir())){
            $str .= "<p>". Model("Image")->getBoxDir() ."不可写,请修改目录权限.</p>";
        }

        //VAGRANT_PASSWORD
        if(!defined("VAGRANT_PASSWORD")){
            $str .= "<p> <mark>VAGRANT_PASSWORD</mark> 常量未定义,请在<mark>Server.php</mark>中定义. 此常量为默认虚拟机SSH登录密码</p>";
        }

        $def = "<p> 配置验证成功! </p>";

        $success = array (
            "checkRresault" => $str ? : $def,
            "boxDir"=>Model("Image")->getBoxDir(),
            "passWord" => defined("VAGRANT_PASSWORD") ? VAGRANT_PASSWORD : "未定义",
        );
        return Success($success);
    }

}