<?php

class SystemModel extends \Lit\LitMs\LitMsModel {
    function getNetCard(){
        $ret = [];
        foreach( swoole_get_local_ip() as $key => $val) {
            $ret[] = ["name"=>$key,"ip"=>$val];
        }
        return $ret;
    }

}