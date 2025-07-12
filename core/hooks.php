<?php

/**
 * Author : Yogi Pratama | Mail me [at] youputra@gmail.com
 * Description :
 * Licence to 
 * @copyright 2013.
 */

class Hooks{
    protected $hooks=array();
    
    function __construct(){
        return $hooks;
    }
    
    function addHooks($hooks_category,$hooks_title,$hooks_url,$hook_child,$hook_other_menu=array()){
        if(empty($this->hooks[$hooks_category]))
            $this->hooks[$hooks_category]=array(array($hooks_title,$hooks_url,$hook_child,$hook_other_menu));
        else
            array_push($this->hooks[$hooks_category],array($hooks_title,$hooks_url,$hook_child,$hook_other_menu));
    }
    
    function callHooks(){
        return $this->hooks;
    }
}
?>