<?php
if ($_POST['user_name']) {
    $user_name = $this->validasi->validInput($_POST['user_name']);
    $user_password = $this->validasi->validInput(md5($_POST['user_password']));

    $queryAdmin = newQuery("get_row", "SELECT * FROM tb_karyawan WHERE Usernm='$user_name' AND Passwd='$user_password' AND Status='1'");
    if ($queryAdmin) {
        $departement = newQuery("get_row", "SELECT * FROM tb_departement WHERE IDDepartement='" . $queryAdmin->IDDepartement . "'");
        if ($queryAdmin->Nama == "Administrator")
            $level = 0;
        else
            $level = 1;
        $_SESSION['userIDAdmin'] = $queryAdmin->IDKaryawan;
        $_SESSION['userLevel'] = $level;
        $_SESSION['userLevelName'] = $queryAdmin->Nama;
        $_SESSION['userName'] = $queryAdmin->Nama;
        $_SESSION['userSpesific'] = $departement->NamaDepartement;
        $_SESSION['userType'] = "wAdmin";
        $_SESSION['userStatus'] = "userLogin";

        $_SESSION["uid"] = $queryAdmin->IDKaryawan;
        $_SESSION["name"] = $queryAdmin->Nama;
        $_SESSION["level"] = $level;
        $_SESSION["departement"] = $queryAdmin->IDDepartement;
        if ($_SESSION["departement"] == 4)
            $_SESSION["locked"] = 'disabled=""';
        else
            $_SESSION["locked"] = '';
        $_SESSION["jabatan"] = $queryAdmin->IDJabatan;

        // var_dump($queryAdmin);

        header("Location: " . PRSONPATH);

        die();
    } else {
        $notif['class'] = "error";
        $notif['msg'] = "User/Password anda salah... ";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="" />
    <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543" />

    <title>Lintas Daya - Accounting</title>

    <link rel="icon" type="image/png" href="<?php echo PRSONTEMPPATH; ?>old/dist/img/favicon.png" />

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />

    <link rel="stylesheet" href="<?php echo PRSONTEMPPATH; ?>old/addons/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo PRSONTEMPPATH; ?>old/addons/css/font-awesome.min.css" />
    <link rel="stylesheet" href="<?php echo PRSONTEMPPATH; ?>old/dist/css/AdminLTE.css" />
    <link rel="stylesheet" href="<?php echo PRSONTEMPPATH; ?>old/plugins/iCheck/square/blue.css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo" style="line-height: 0.6em;">
            <a href=""><b>Lintas Daya</b> Accounting<br /><small style="font-size: 14px;font-weight: bold;margin-top:0;padding-top:0;color: #666;">The M.E.P Contractor</small></a>
        </div>
        <div class="login-box-body">
            <p class="login-box-msg">Silahkan masukan username dan password anda untuk mulai menjalankan sistem...</p>
            <?php if ($notif) { ?>
                <div class="alert alert-<?php echo $notif['class']; ?>"><?php echo $notif['msg']; ?></div>
            <?php } ?>
            <form action="" method="post">
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" name="user_name" placeholder="Username" />
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" name="user_password" placeholder="Password" />
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-8"></div>
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat"><i class="fa fa-lock"></i> Login</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="<?php echo PRSONTEMPPATH; ?>old/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <script src="<?php echo PRSONTEMPPATH; ?>old/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo PRSONTEMPPATH; ?>old/plugins/iCheck/icheck.min.js"></script>
    <script>
        $(function() {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
    </script>
</body>

</html>