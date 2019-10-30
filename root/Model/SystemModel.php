<?php

class SystemModel extends \Lit\LitMs\LitMsModel {
    function getNetCardName(){
        $ret = [];
        foreach( swoole_get_local_mac() as $key => $val) {
            $ret[] = $key;
        }
        return $ret;
    }

}