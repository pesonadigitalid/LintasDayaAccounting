<?php

/**
 * Author : Yogi Pratama | Mail me [at] youputra@gmail.com
 * Description :
 * Licence to 
 * @copyright 2014.
 */
 
global $prvmodel;
global $function;
$prvmodel = new Model;
$function = new Fungsi;

function newQuery($type,$sql){
    global $prvmodel;
    return $prvmodel->newQuery($type,$sql);
}

function setPermalink($id="",$file=""){
    return PRSONPATH."$file/".$id;
}
?>