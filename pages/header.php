<?php
session_start();
$this->fungsi->authLogin($_SESSION['userStatus'], "userLogin");
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta http-equiv="content-type" content="text/html" />
    <meta name="author" content="Yogi Pratama" />

    <title>Lintas Daya Accounting</title>
    <link rel="icon" type="image/png" href="<?php echo PRSONTEMPPATH; ?>old/dist/img/favicon.png" />
    <link rel="stylesheet" href="<?php echo PRSONTEMPPATH; ?>css/spectre.min.css" />
    <link rel="stylesheet" href="<?php echo PRSONTEMPPATH; ?>css/font-awesome.min.css" />
    <link rel="stylesheet" href="<?php echo PRSONTEMPPATH; ?>css/style.css" />
    <script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/jquery.1.10.2.min.js"></script>
    <script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/app.js"></script>
    <script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/pace.min.js"></script>
    <script>
        window.paceOptions = {
            document: true, // disabled
            eventLag: true,
            restartOnPushState: true,
            restartOnRequestAfter: true,
            ajax: {
                trackMethods: ['POST', 'GET']
            }
        };
    </script>
</head>

<body>
    <section class="section top-level-nav">
        <ul>
            <li><a href="#">File</a></li>
            <li>
                <a href="#">Setting</a>
                <ul>
                    <?php if ($_SESSION["jabatan"] == 3) { ?>
                        <li><a href="<?php echo PRSONPATH; ?>data-master-transaksi">Master Transaksi</a></li>
                    <?php } ?>
                    <li><a href="<?php echo PRSONPATH; ?>setting-saldo-awal/?tahun=<?php echo date("Y"); ?>">Saldo Awal</a></li>
                    <?php if ($_SESSION["jabatan"] == 3) { ?>
                        <li><a href="<?php echo PRSONPATH; ?>setting-target-kontrak/?tahun=<?php echo date("Y"); ?>">Target Kontrak Lintas Daya</a></li>
                        <!-- <li><a href="<?php echo PRSONPATH; ?>fixing-closing-balance">Fixing Closing Balance</a></li> -->
                        <li><a href="<?php echo PRSONPATH; ?>tutup-buku">Tutup Buku Akhir Tahun</a></li>
                    <?php } ?>
                </ul>
            </li>
        </ul>
        <ul style="float: right;">
            <li><a href="<?php echo PRSONPATH; ?>logout"><i class="fa fa-sign-out"></i> Logout</a></li>
        </ul>
    </section>
    <section class="section secondary-level-nav">
        <ul>
            <li class="active"><a href="<?php echo PRSONPATH; ?>">Dashboard</a></li>
            <li><a href="<?php echo PRSONPATH; ?>data-master-rekening">Master Rekening</a></li>
            <li>
                <a href="#">Input Jurnal</a>
                <ul>
                    <li><a href="<?php echo PRSONPATH; ?>jurnal-umum-baru">Jurnal Umum</a></li>
                    <?php if ($_SESSION["jabatan"] == 3) { ?>
                        <li><a href="<?php echo PRSONPATH; ?>jurnal-transaksi-baru">Jurnal Transaksi</a></li>
                    <?php } ?>
                </ul>
            </li>
            <?php if ($_SESSION["jabatan"] == 3) { ?>
                <li>
                    <a href="<?php echo PRSONPATH; ?>history-jurnal">History Jurnal</a>
                </li>
            <?php } ?>
            <li><a href="<?php echo PRSONPATH; ?>buku-besar">Buku Besar</a></li>
            <?php if ($_SESSION["jabatan"] == 3 || $_SESSION["departement"] == 4) { ?>
                <li>
                    <a href="#">Hutang Piutang</a>
                    <ul>
                        <?php if ($_SESSION["jabatan"] == 3) { ?>
                            <li><a href="<?php echo PRSONPATH; ?>data-piutang/">Piutang Proyek LD</a></li>
                            <li><a href="<?php echo PRSONPATH; ?>data-piutang-progress/">Piutang Progress LD</a></li>
                            <li><a href="<?php echo PRSONPATH; ?>data-hutang/">Hutang PO LD</a></li>
                        <?php } ?>
                        <li><a href="<?php echo PRSONPATH; ?>data-piutang-mms/">Piutang MMS</a></li>
                        <li><a href="<?php echo PRSONPATH; ?>data-piutang-progress-mms/">Piutang Progress MMS</a></li>
                        <li><a href="<?php echo PRSONPATH; ?>data-hutang-mms/">Hutang PO MMS</a></li>
                        <?php if ($_SESSION["jabatan"] == 3) { ?>
                            <li><a href="<?php echo PRSONPATH; ?>data-po-belum-terjurnal/">PO Belum Terjurnal</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php if ($_SESSION["jabatan"] == 3 || $_SESSION["departement"] == 4) { ?>
                    <li>
                        <a href="#">Laporan</a>
                        <ul>
                            <?php if ($_SESSION["jabatan"] == 3) { ?>
                                <li><a href="<?php echo PRSONPATH; ?>laporan-data-proyek/">Laporan Data Proyek</a></li>
                                <!--<li><a href="<?php echo PRSONPATH; ?>laporan-jurnal">Laporan Jurnal</a></li>-->
                                <li><a href="<?php echo PRSONPATH; ?>neraca-saldo">Laporan Neraca Saldo</a></li>
                                <li><a href="<?php echo PRSONPATH; ?>neraca">Laporan Neraca</a></li>
                                <li><a href="<?php echo PRSONPATH; ?>laporan-laba-rugi-perusahaan/">Laba Rugi Perusahaan (LD+MMS)</a></li>
                                <li><a href="<?php echo PRSONPATH; ?>laporan-laba-rugi-perusahaan/?type=LD">Laba Rugi Perusahaan LD</a></li>
                                <li><a href="<?php echo PRSONPATH; ?>laporan-laba-rugi-perusahaan/?type=MMS">Laba Rugi Perusahaan MMS</a></li>
                                <li><a href="<?php echo PRSONPATH; ?>laporan-laba-rugi-periode/">Laba Rugi Periode</a></li>
                            <?php } ?>
                            <li><a href="<?php echo PRSONPATH; ?>laporan-laba-rugi-departement/">Laba Rugi Departement</a></li>
                            <li><a href="<?php echo PRSONPATH; ?>laporan-laba-rugi-konsolidasi/">Laba Rugi Konsolidasi</a></li>
                            <li><a href="<?php echo PRSONPATH; ?>laporan-proyeksi-departement/">Proyeksi Per Departement</a></li>
                            <li><a href="<?php echo PRSONPATH; ?>laporan-proyeksi-pelanggan/">Proyeksi Per Pelanggan</a></li>
                            <li><a href="<?php echo PRSONPATH; ?>laporan-proyeksi-projects/">Proyeksi Per Proyek</a></li>
                            <?php if ($_SESSION["jabatan"] == 3) { ?>
                                <li><a href="<?php echo PRSONPATH; ?>laporan-laba-rugi-proyek/">Laba Rugi Proyek</a></li>
                                <li><a href="<?php echo PRSONPATH; ?>laporan-cash-flow-departement/">Cash Flow Departement</a></li>
                                <li><a href="<?php echo PRSONPATH; ?>laporan-cash-flow-proyek/">Cash Flow Proyek</a></li>
                                <li><a href="<?php echo PRSONPATH; ?>laporan-pendapatan/">Laporan Pendapatan Proyek</a></li>
                            <?php } ?>
                            <!--<li><a href="<?php echo PRSONPATH; ?>#">Laporan Neraca Perbandingan</a></li>-->
                            <!--<li><a href="<?php echo PRSONPATH; ?>#">Laporan Laba Rugi</a></li>-->
                            <!--<li><a href="<?php echo PRSONPATH; ?>#">Grafik Laba Rugi</a></li>-->
                        </ul>
                    </li>
                <?php } ?>
            <?php } ?>
        </ul>
        <ul style="float: right;">
            <li><a href="#">Selamat Datang <?php echo $_SESSION['userLevelName']; ?></a></li>
        </ul>
    </section>