<?php

class VagrantModel extends \Lit\LitMs\LitMsModel {

    //vagrant box list
    function vagrantBoxList(){
        $cmd = "vagrant box list";
        $boxList = [];
        $execRet = $this->runCmd($cmd,true);
        foreach ($execRet as $str) {
            if(substr_count($str,"There are no installed boxes") == 0){
                $boxList[] = current(explode(" ",$str));
            }
        }
        return $boxList;
    }

    //vagrant init
    function vagrantInit($vagrantConfig){
        $vagrantConfig['hostId'] = isset($vagrantConfig['hostId'])?$vagrantConfig['hostId']:uniqid();
        $vagrantConfig['hostName'] = isset($vagrantConfig['hostName'])?$vagrantConfig['hostName']:$vagrantConfig['hostId'] ;
        $string = file_get_contents(VAGRANT_DATA_DIR."Vagrantfile");
        $vagrantFileString = \Lit\Litool\LiString::ReplaceStringVariable($string,$vagrantConfig);
        $vagrantDir = $this->getVagrantDir($vagrantConfig['hostId']);
        if(is_dir($vagrantDir)){
            return -1;
        }
        if(mkdir($vagrantDir)){
            $vagrantFile = $this->getVagrantFile($vagrantConfig['hostId']);
            if(file_put_contents($vagrantFile,$vagrantFileString)){
                $this->saveVagrantConfig($vagrantConfig['hostId'],$vagrantConfig);
                return $vagrantConfig['hostId'];
            }else{
                return -2;
            }
        }else{
            return -3;
        }
    }

    //vagrant up
    function vagrantUp( $hostId ){
        $hostDir = $this->getVagrantDir( $hostId );
        $cmd = "cd {$hostDir} && vagrant up &";
        $this->runCmd($cmd);
    }

    //vagrant status
    function vagrantStatus( $hostId ){
        $hostDir = $this->getVagrantDir( $hostId );
        $cmd = "cd {$hostDir} && vagrant status";
        $execRet = $this->runCmd($cmd,true);
        var_dump ($execRet);

    }

    //vagrant reload
    function vagrantReload( $hostId ){
        $hostDir = $this->getVagrantDir( $hostId );
        $cmd = "cd {$hostDir} && vagrant reload &";
        $this->runCmd($cmd);
    }

    //vagrant halt
    function vagrantHalt( $hostId ){
        $hostDir = $this->getVagrantDir( $hostId );
        $cmd = "cd {$hostDir} && vagrant halt &";
        $this->runCmd($cmd);
    }

    //vagrant destroy
    function vagrantDestroy( $hostId ){
        $hostDir = $this->getVagrantDir( $hostId );
        $cmd = "cd {$hostDir} && vagrant destroy -f && rm -rf  {$hostDir} &";
        $this->runCmd($cmd);
    }

    //是否有效虚拟机
    function vagrantIsEff( $hostId ){
        if(empty($hostId)){
            return false;
        }
        if( is_file($this->getVagrantFile($hostId)) ) {
            return true;
        }else{
            return false;
        }
    }

    //获取vagrant主机目录
    function getVagrantDir( $hostId ){
        return VAGRANT_ROOT.$hostId.DIRECTORY_SEPARATOR;
    }

    //获取vagrant主机目录
    function getVagrantFile( $hostId ){
        return $this->getVagrantDir($hostId)."Vagrantfile";
    }

    //获取vagrant配置目录
    function getVagrantConfigFile($hostId){
        return $this->getVagrantDir($hostId)."VmConfig";
    }
    function getVagrantConfig($hostId){
        $str = file_get_contents($this->getVagrantConfigFile($hostId));
        return json_decode($str,true);
    }

    function saveVagrantConfig($hostId,$vagrantConfig){
        file_put_contents($this->getVagrantConfigFile($hostId),json_encode($vagrantConfig));
    }

    function runCmd($cmd,$ret = false){
//        echo $cmd;
//        return [];
        if($ret){
            $exeRes = [];
            exec($cmd,$exeRes);
            return $exeRes;
        }else{
            exec($cmd);
            return [];
        }
    }
}