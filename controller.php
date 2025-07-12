<?php
/**
 * Author : Yogi Pratama | Mail me [at] youputra@gmail.com
 * Description :
 * Licence to 
 * @copyright 2014.
 */

class Controller{
    private $object;
    private $page;
    private $action;
    private $page_number;
    public $fungsi;
    public $db;
    public $validasi;
    public $input;
    public $model;
    public $hooks;
    
    function __construct(){
        $this->loadLibrary();
        $this->verifyLicence();
        $this->extractURL();
        $this->loadDefinition();
        $this->loadView();
    }
    
    function loadLibrary(){
        include_once "library/class.sqlcore.php";
        include_once "library/class.sqlmysql.php";
        $this->db = new ezSQL_mysql(YGDBUSER,YGDBPASS,YGDBNAME,YGDBHOST);
        include_once "library/class.fungsi.php";
        $this->fungsi = new Fungsi;
        include_once "library/class.validasi.php";
        $this->validasi = new Validasi;
        include_once "library/class.input.php";
        $this->input = new InputForm;
        
        include_once "core/model.php";
        $this->model = new Model;
        /*
        include_once "core/hooks.php";
        $this->hooks = new Hooks;
        */
        include_once "core/alias.php";
        
    }
    
    function loadView(){
        session_start();
        if($this->page!=""){
            if(file_exists("pages/".$this->page.".php")){
                require_once("pages/".$this->page.".php");
            } else {
                if (file_exists("pages/404.php"))
                    require_once("pages/404.php");
                else
                    echo "File not found";
            }
        } else {
            include_once "pages/index.php";
        }
    }
    
    function loadDefinition(){
        define("PRSONTEMPPATH","http://".$_SERVER['HTTP_HOST']."/accounting/"."themes/ygsimply/");
        define("PRSONPATH","http://".$_SERVER['HTTP_HOST']."/accounting/");
        define("PRSONFILEPATH","http://".$_SERVER['HTTP_HOST']."/accounting/"."files");
    }
    function extractURL(){
        $url = str_replace("http://".$_SERVER['HTTP_HOST']."/accounting/","","http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        $url = preg_split('[\\/]', $url, -1, PREG_SPLIT_NO_EMPTY);
        $this->page =  (isset($url[0])) ? $this->fungsi->antiSQLInjection($url[0]) : "";
        $this->action = (isset($url[1])) ? $this->fungsi->antiSQLInjection($url[1]) : "";
        $this->page_number = (isset($url[2])) ? $this->fungsi->antiSQLInjection($url[2]) : "";
        $this->additional_url_parameter = (isset($url[3])) ? $this->fungsi->antiSQLInjection($url[3]) : "";
    }
    
    function verifyLicence(){
        /*
        $k = "Y06!P5NC";
        $d = mcrypt_ecb(MCRYPT_DES, $k, base64_decode($this->model->getOption("ACTIVATIONCODE")), MCRYPT_DECRYPT); 
        $e = explode(";",$d);
        if($e[0]!=$this->model->getOption("LAST_SYNC"))
            die("LICENSE KEY IS INVALID.<br/>Please call your webmaster to get a new licese key.<br/>Webmaster Contact Person: Yogi Pratama<br/>Phone: 085737654543<br/>Mail: yogi@pesonacreative.com");*/
    }
}
?>