<?php

/**
 * Author : Yogi Pratama | Mail me [at] youputra@gmail.com
 * Description :
 * Licence to 
 * @copyright 2013.
 */

class Model{
    protected $db;
    protected $validasi;
    
    function __construct(){
        $this->db = new ezSQL_mysql(YGDBUSER,YGDBPASS,YGDBNAME,YGDBHOST);
        $this->validasi = new Validasi;
    }
    
    function getOption($option_name){
        $query = $this->db->get_row("SELECT value FROM tb_system_config WHERE label='".$option_name."'");
        if($query){
            return $query->value;
        }
    }
    
    function setOption($option_name,$value){
        $query = $this->db->query("UPDATE tb_system_config SET `value`='".$value."' WHERE label='".$option_name."'");
        return $query;
    }
    
    function newQuery($type,$sql){
        return $this->db->$type($sql);
    }
}

?>