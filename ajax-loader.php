<?php
session_start();
include_once "db-config.php";
include_once "library/class.sqlcore.php";
include_once "library/class.sqlmysql.php";
$db = new ezSQL_mysql(YGDBUSER,YGDBPASS,YGDBNAME,YGDBHOST);
include_once "library/class.fungsi.php";
$fungsi = new Fungsi;
include_once "library/class.validasi.php";
$validasi = new Validasi;
include_once "library/class.input.php";
$input = new InputForm;