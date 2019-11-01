<?php

class VagrantModel extends \Lit\LitMs\LitMsModel {

    //vagrant box list
    function vagrantBoxList(){
        $cmd = "vagrant box list";
        $boxList = [];
//        $execRet = $this->runCmd($cmd,true);
        exec($cmd,$execRet);
        foreach ($execRet as $str) {
            if(substr_count($str,"There are no installed boxes") == 0){
                $boxList[] = current(explode(" ",$str));
            }
        }
        return $boxList;
    }

    //vagrant init
    function vagrantInit($vagrantConfig){
        $vagrantConfig['hostId'] = isset($vagrantConfig['hostId']) ? $vagrantConfig['hostId'] : uniqid();
        $vagrantConfig['hostName'] = isset($vagrantConfig['hostName']) ? $vagrantConfig['hostName'] : $vagrantConfig['hostId'];
        $vagrantConfig['nickName'] = (isset($vagrantConfig['nickName']) && !empty($vagrantConfig['nickName'])) ? $vagrantConfig['nickName'] : $vagrantConfig['hostId'];
        $string = file_get_contents(VAGRANT_DATA_DIR."Vagrantfile");
        $vagrantFileString = \Lit\Litool\LiString::ReplaceStringVariable($string,$vagrantConfig);
        $vagrantDir = $this->getVagrantDir($vagrantConfig['hostId']);
        if(is_dir($vagrantDir)){
            return -1;
        }
        if(mkdir($vagrantDir)){
            $vagrantFile = $this->getVagrantFile($vagrantConfig['hostId']);
            if(file_put_contents($vagrantFile,$vagrantFileString)){
                Model("Config")->saveConfig($vagrantConfig['hostId'],$vagrantConfig);
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
        $selfObj = $this;
        go(function () use ($hostId,$selfObj) {
            $hostDir = $this->getVagrantDir( $hostId );
            $cmd = "cd {$hostDir} && vagrant up ";
            co::exec($cmd);
            $ipList = $this->vagrantGetIp($hostId);
            if(!empty($ipList)){
                Model("Config")->updateConfig($hostId,['ipAddress'=>implode(",",$ipList)]);
            }
        });
//        $this->runCmd($cmd);
    }

    //vagrant status
    function vagrantStatus( $hostId ){
        $hostDir = $this->getVagrantDir( $hostId );
        $cmd = "cd {$hostDir} && vagrant status";
//        $execRet = $this->runCmd($cmd,true);
        exec($cmd,$execRet);
        $tmpStr = implode('|',$execRet);
        $status = ["not created","poweroff","running","saved"];
        foreach($status as $val) {
           if ( strpos($tmpStr,$val." (") !== false ) {
               return $val;
           }
        }
        return "none";
    }

    //vagrant reload
    function vagrantReload( $hostId ){
        $selfObj = $this;
        go(function () use ($hostId, $selfObj) {
            $hostDir = $this->getVagrantDir($hostId);
            $cmd = "cd {$hostDir} && vagrant reload ";
            co::exec($cmd);
        });
    }
    //vagrant halt
    function vagrantHalt( $hostId ){
        $selfObj = $this;
        go(function () use ($hostId,$selfObj) {
            $hostDir = $this->getVagrantDir( $hostId );
            $cmd = "cd {$hostDir} && vagrant halt ";
            co::exec($cmd);
        });
    }

    //vagrant destroy
    function vagrantDestroy( $hostId ){
        $selfObj = $this;
        go(function () use ($hostId,$selfObj) {
            $hostDir = $this->getVagrantDir( $hostId );
            $cmd = "cd {$hostDir} && vagrant destroy -f ";
            co::exec($cmd);
            if (PHP_OS === 'Windows') {
                exec(sprintf("rd /s /q %s", escapeshellarg($hostDir)));
            } else {
                exec(sprintf("rm -rf %s", escapeshellarg($hostDir)));
            }
        });
    }

    //vagrant provision
    function vagrantGetIp ( $hostId ) {
        $hostDir = $this->getVagrantDir( $hostId );
        $cmd = "cd {$hostDir} && vagrant provision";
//        $execRet = $this->runCmd($cmd,true);
        exec($cmd,$execRet);
        $ret = [];
        foreach ($execRet as $value) {
            $exp = explode(":",$value);
            $tmp = trim(end($exp));
            preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/",$tmp,$matches);
            if(!empty($matches)){
                $ip = current($matches);
                $ret[] = $ip;
            }
        }
        return $ret;
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

    //获取vagrantfile目录
    function getVagrantFile( $hostId ){
        return $this->getVagrantDir($hostId)."Vagrantfile";
    }

}