<?php

class ConfigModel{

    //获取vagrant配置目录
    function getConfigFile($hostId){
        return $this->getConfigDir($hostId)."VmConfig";
    }

    function getConfig($hostId){
        $str = file_get_contents($this->getConfigFile($hostId));
        return json_decode($str,true);
    }

    function saveConfig($hostId,$vagrantConfig){
        file_put_contents($this->getConfigFile($hostId),json_encode($vagrantConfig));
    }

    //获取vagrant主机目录
    function getConfigDir( $hostId ){
        return VAGRANT_ROOT.$hostId.DIRECTORY_SEPARATOR;
    }

    //是否有效配置
    function configIsEff( $hostId ){
        if(empty($hostId)){
            return false;
        }
        if( is_file($this->getConfigFile($hostId)) ) {
            return true;
        }else{
            return false;
        }
    }

}