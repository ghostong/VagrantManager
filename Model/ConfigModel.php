<?php

class ConfigModel extends \Lit\LitMs\LitMsModel {

    //获取vagrant配置目录
    function getConfigFile($hostId){
        return $this->getConfigDir($hostId)."VmConfig";
    }

    //获取配置
    function getConfig($hostId){
        $str = file_get_contents($this->getConfigFile($hostId));
        return json_decode($str,true);
    }

    //保存目录
    function saveConfig($hostId,$vagrantConfig){
        file_put_contents($this->getConfigFile($hostId),json_encode($vagrantConfig));
    }

    function updateConfig($hostId,$modify){
        $config = $this->getConfig($hostId);
        $config = array_merge($config,$modify);
        $this->saveConfig($hostId,$config);
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