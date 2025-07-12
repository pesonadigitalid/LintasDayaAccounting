<?php
session_start();
unset($_SESSION['userIDAnggota'],$_SESSION['userIDAdmin'],$_SESSION['userLevel'],$_SESSION['userName'],$_SESSION['userType'],$_SESSION['userStatus']);
header("Location: ".PRSONPATH);
?>