<?php
/**
 * Created by IntelliJ IDEA.
 * User: ghost
 * Date: 2019-10-29
 * Time: 15:49
 */

class VagrantModel extends \Lit\LitMs\LitMsModel {
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
}