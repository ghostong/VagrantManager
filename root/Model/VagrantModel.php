<?php
/**
 * Created by IntelliJ IDEA.
 * User: ghost
 * Date: 2019-10-29
 * Time: 15:49
 */

class VagrantModel extends \Lit\LitMs\LitMsModel {

    //获取当前可用的操作系统
    function vagrantBoxList(){
        $cmd = "vagrant box list";
        $boxList = [];
        $execRet = [];
        exec($cmd,$execRet);
        foreach ($execRet as $str) {
            if(substr_count($str,"There are no installed boxes") == 0){
                $boxList[] = current(explode(" ",$str));
            }
        }
        return Success(["boxList"=>$boxList]);
    }

    //创建虚拟机
    function vagrantAddVm ($request) {
        $vagrant['hostId'] = uniqid();
        $vagrant['hostName'] = $vagrant['hostId'] ;
        $vagrant['cpuNum'] = $request->post["cpuNum"];
        $vagrant['memNum'] = $request->post["memNum"];
        $vagrant['opSystem'] = $request->post["opSystem"];
        $string = file_get_contents(VAGRANT_DATA_DIR."Vagrantfile");
        $vagrantFileString = \Lit\Litool\LiString::ReplaceStringVariable($string,$vagrant);
        $vagrantDir = $this->getVagrantDir($vagrant['hostId']);
        if(is_dir($vagrantDir)){
            return Error(0,"Vagrant 目录".$vagrantDir." 已经存在,不能重复创建");
        }
        if(mkdir($vagrantDir)){
            $vagrantFile = $vagrantDir."Vagrantfile";
            if(file_put_contents($vagrantFile,$vagrantFileString)){
                return Success(["hostId"=>$vagrant['hostId']]);
            }else{
                return Error(0,"创建 Vagrantfile ".$vagrantFile." 失败");
            }
        }else{
            return Error(0,"创建 Vagrant 目录 ".$vagrantDir." 失败");
        }
    }

    //虚拟机列表
    function vagrantVmList (){
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


    function getVagrantDir( $hostId ){
        return VAGRANT_ROOT.$hostId.DIRECTORY_SEPARATOR;
    }
}