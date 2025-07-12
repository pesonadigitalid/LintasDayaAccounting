<?php
$tgl = $this->validasi->validInput($_GET['tanggal']);
$bulan = $this->validasi->validInput($_GET['bulan']);
$tahun = $this->validasi->validInput($_GET['tahun']);
$status = $this->validasi->validInput($_GET['status']);
$jenis_transaksi = $this->validasi->validInput($_GET['jenis_transaksi']);
$proyek = $this->validasi->validInput($_GET['proyek']);
$departement = $this->validasi->validInput($_GET['departement']);
$tanpa_saldo_awal = $this->validasi->validInput($_GET['tanpa_saldo_awal']);
$ada_transaksi = $this->validasi->validInput($_GET['ada_transaksi']);
$company = $this->validasi->validInput($_GET['company']);

if ($_SESSION["locked"] != '') {
    $departement = $_SESSION["departement"];
}

$dariTanggal = $this->validasi->validInput($_GET['daritanggal']);
$sampaiTanggal = $this->validasi->validInput($_GET['sampaitanggal']);
if ($dariTanggal == "") {
    if ($jenis_transaksi != "Semua Rekening") {
        $cond = " AND a.IDRekening='$jenis_transaksi'";
    }
    if ($tgl != "") {
        $tanggal = $tahun . "-" . $bulan . "-" . $tgl;
        $tanggalID = $tgl . "/" . $bulan . "/" . $tahun;
        $tanggalDisplay = $tgl . " " . $this->fungsi->changeMonthNameID($bulan) . " " . $tahun;

        $periode = $tahun . "-" . $bulan . "-" . $tgl;
        $condDate = "DATE_FORMAT(Tanggal,'%Y-%m-%d')='$periode'";
    } else {
        $tanggal = $tahun . "-" . $bulan . "-01";
        $tanggalID = "01/" . $bulan . "/" . $tahun;
        $tanggalDisplay = $this->fungsi->changeMonthNameID($bulan) . " " . $tahun;

        $periode = $tahun . "-" . $bulan;
        $condDate = "DATE_FORMAT(Tanggal,'%Y-%m')='$periode'";
    }
} else {
    $exp = explode("/", $dariTanggal);
    $dariTanggalEN = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
    $tgl = $exp[0];
    $bulan = $exp[1];
    $tahun = $exp[2];

    if ($sampaiTanggal != "") {
        $exp = explode("/", $sampaiTanggal);
        $sampaiTanggalEN = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
        $condDate = " Tanggal BETWEEN '$dariTanggalEN' AND '$sampaiTanggalEN' ";
        $tanggalDisplay = $dariTanggal . " - " . $sampaiTanggal;
    } else {
        $condDate = " Tanggal = '$dariTanggalEN'";
        $tanggalDisplay = $dariTanggal;
    }

    if ($jenis_transaksi != "Semua Rekening") {
        $cond = " AND a.IDRekening='$jenis_transaksi'";
    }
}

if ($proyek != "") {
    $condProyek = " AND IDJurnal IN (SELECT IDJurnal FROM tb_jurnal WHERE IDProyek='$proyek') ";
}

if ($company == "" && $departement != "") {
    $condProyek .= " AND IDJurnal IN (SELECT IDJurnal FROM tb_jurnal WHERE IDDepartement='$departement') ";
} else if ($company == "LD") {
    $departement = "";
    $condProyek .= " AND IDJurnal IN (SELECT IDJurnal FROM tb_jurnal WHERE IDDepartement<>'4') ";
} else if ($company == "MMS") {
    $departement = "";
    $condProyek .= " AND IDJurnal IN (SELECT IDJurnal FROM tb_jurnal WHERE IDDepartement='4') ";
}

function sumSaldo($saldoAwal, $debet, $kredit, $posisi)
{
    if ($posisi == 'Debet') {
        $closing = $saldoAwal + $debet - $kredit;
    } else {
        $closing = $saldoAwal - $debet + $kredit;
    }
    return $closing;
}
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="" />
    <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543" />
    <title>Lintas Daya Accounting</title>
    <link rel="icon" type="image/png" href="<?php echo PRSONTEMPPATH; ?>dist/img/favicon.png" />
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
    <link rel="stylesheet" href="<?php echo PRSONTEMPPATH; ?>css/print-style.css" media="all" />
</head>

<body>
    <table>
        <tr>
            <td width="50%" class="bottom">
                <h1>CV. LINTAS DAYA</h1>
                <p style="margin-top: 5px;">JL. Tukad Citarum I, No. 10, Renon, Perum Surya Graha Asih, Kota Denpasar, Bali<br />Phone. (0361) 238055, Fax. -</p>
            </td>
            <td width="50%" align="right" class="bottom">
                Tanggal Cetak : <?php echo date("d/m/Y"); ?>
            </td>
        </tr>
    </table>
    <div class="laporanTitle">
        <h1 class="underline">** BUKU BESAR **</h1>Periode : <?php echo $tanggalDisplay; ?>
    </div>
    <?php

    function getSaldoAwal($tanggal, $bulan, $tahun, $idRekening)
    {
        $batasan = $tahun . "-01-01";
        global $proyek;
        global $departement;
        global $company;

        if ($_SESSION["locked"] != '' && $departement == '') {
            $departement = $_SESSION["departement"];
        }

        if ($proyek != "") {
            $condProyek = " AND IDJurnal IN (SELECT IDJurnal FROM tb_jurnal WHERE IDProyek='$proyek') ";
        }

        if ($company == "" && $departement != "") {
            $condProyek .= " AND IDJurnal IN (SELECT IDJurnal FROM tb_jurnal WHERE IDDepartement='$departement') ";
        } else if ($company == "LD") {
            $departement = "";
            $condProyek .= " AND IDJurnal IN (SELECT IDJurnal FROM tb_jurnal WHERE IDDepartement<>'4') ";
        } else if ($company == "MMS") {
            $departement = "";
            $condProyek .= " AND IDJurnal IN (SELECT IDJurnal FROM tb_jurnal WHERE IDDepartement='4') ";
        }

        if ($tanggal != "") {
            $p = $tahun . "-" . $bulan . "-" . $tanggal;
            $cond = " AND DATE_FORMAT(Tanggal,'%Y-%m-%d') < '$p' AND DATE_FORMAT(Tanggal,'%Y-%m-%d') > '$batasan'";
        } else {
            $p = $tahun . "-" . $bulan;
            $cond = " AND DATE_FORMAT(Tanggal,'%Y-%m') < '$p' AND DATE_FORMAT(Tanggal,'%Y-%m-%d') > '$batasan'";
        }


        $dataRekening = newQuery("get_row", "SELECT * FROM tb_master_rekening WHERE IDRekening='$idRekening'");
        $saldoAwal = newQuery("get_row", "SELECT * FROM tb_saldo_awal WHERE IDRekening='$idRekening' and Tahun='$tahun'");

        if ($saldoAwal) $saldoAwal = $saldoAwal->SaldoAwal;
        else $saldoAwal = 0;
        $kredit = 0;
        $debet = 0;

        $debet = newQuery("get_var", "SELECT SUM(Debet) FROM tb_jurnal_detail WHERE IDRekening='$idRekening' $cond $condProyek");
        if (!$debet) $debet = 0;
        $kredit = newQuery("get_var", "SELECT SUM(Kredit) FROM tb_jurnal_detail WHERE IDRekening='$idRekening' $cond $condProyek");
        if (!$kredit) $kredit = 0;


        if ($dataRekening->Posisi == 'Debet') {
            $closing = $saldoAwal + $debet - $kredit;
        } else {
            $closing = $saldoAwal - $debet + $kredit;
        }
        return $closing;
    }

    if ($status == "1") {
        $query = newQuery("get_results", "SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.Tipe='D' $cond ORDER BY KodeRekening ASC");
        if ($query) {
            foreach ($query as $data) {

                if ($this->fungsi->authAccessRekening($data->Posisi, $data->KodeRekening) == 1) {

                    if ($data->IDCurrency > 1) $add = " (" . $data->Nama . ")";
                    else $add = "";
                    $dataRekening = newQuery("get_row", "SELECT * FROM tb_master_rekening WHERE IDRekening='" . $data->IDRekening . "'");
                    $debet = getSaldoAwal($tgl, $bulan, $tahun, $data->IDRekening);

                    $debetK = 0;
                    $kreditK = 0;
                    $qRest = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $data->IDRekening . "' $condProyek ORDER BY Tanggal ASC, JurnalRef ASC");
                    if ($qRest) {
                        $adaTransaksi = 1;
                        foreach ($qRest as $dRest) {
                            $i++;
                            $debetK += $dRest->Debet;
                            $kreditK += $dRest->Kredit;
                            $dJurnal = newQuery("get_row", "SELECT * FROM tb_jurnal WHERE IDJurnal='" . $dRest->IDJurnal . "'");
                        }
                    } else $adaTransaksi = 0;

                    if ($ada_transaksi == "1" && $adaTransaksi == 0) $hide = true;
                    else $hide = false;

                    $closing = $debetK - $kreditK;
                    $saldo = $debet;
                    // if(($closing!=0 || $debet!=0) && !$hide){
                    if (($closing != 0 || $debet != 0 || $adaTransaksi > 0) && !$hide) {
    ?>
                        <strong>Kode Perkiraan : </strong><?php echo $data->KodeRekening; ?><br /><strong>Nama Perkiraan : </strong><?php echo $data->NamaRekening . $add; ?>
                        <table class="tabelList3" cellpadding="0" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="20">No</th>
                                    <!-- <th width="90">No. Jurnal</th> -->
                                    <th width="50">No. Bukti</th>
                                    <th width="60">Dept.</th>
                                    <th width="60">Proyek</th>
                                    <th width="60">Tanggal</th>
                                    <th style="text-align: left;">Keterangan</th>
                                    <th width="60">Debet</th>
                                    <th width="60">Kredit</th>
                                    <th width="60">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                if ($tanpa_saldo_awal == "") {
                                ?>
                                    <tr>
                                        <td style="text-align: center;"><?php echo $i; ?></td>
                                        <!-- <td></td> -->
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td style="text-align: center;"><?php echo $tanggalID; ?></td>
                                        <td><strong>Saldo awal</strong></td>
                                        <td style="text-align: right;"><?php echo number_format($debet, 2); ?></td>
                                        <td style="text-align: right;"><?php echo number_format(0, 2); ?></td>
                                        <td style="text-align: right;"><?php echo number_format($saldo, 2); ?></td>
                                    </tr>
                                    <?php
                                } else {
                                    $debet = 0;
                                    $saldo = 0;
                                }
                                $kredit = 0;
                                $qRest = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $data->IDRekening . "' $condProyek ORDER BY Tanggal ASC, JurnalRef ASC");
                                if ($qRest) {
                                    foreach ($qRest as $dRest) {
                                        $i++;
                                        $debet += $dRest->Debet;
                                        $kredit += $dRest->Kredit;
                                        $saldo = sumSaldo($saldo, $dRest->Debet, $dRest->Kredit, $data->Posisi);
                                        $dJurnal = newQuery("get_row", "SELECT * FROM tb_jurnal WHERE IDJurnal='" . $dRest->IDJurnal . "'");
                                        $proyek = newQuery("get_row", "SELECT * FROM tb_proyek WHERE IDProyek='" . $dJurnal->IDProyek . "'");
                                        if ($proyek) $proyek = $proyek->KodeProyek . "/" . $proyek->Tahun;
                                        else $proyek = "Umum";
                                        $dept = newQuery("get_row", "SELECT * FROM tb_departement WHERE IDDepartement='" . $dJurnal->IDDepartement . "'");
                                        if ($dept) $dept = $dept->NamaDepartement;
                                        else $dept = "Umum";
                                    ?>
                                        <tr>
                                            <td style="text-align: center;"><?php echo $i; ?></td>
                                            <!-- <td style="text-align: center;"><?php if ($dJurnal) echo $dJurnal->NoJurnal;
                                                                                    else echo "0000"; ?></td> -->
                                            <td style="text-align: center;"><?php echo $dRest->JurnalRef; ?></td>
                                            <td style="text-align: center;"><?php echo $dept; ?></td>
                                            <td style="text-align: center;"><?php echo $proyek; ?></td>
                                            <td style="text-align: center;"><?php echo $dRest->TanggalID; ?></td>
                                            <td><strong><?php echo $dRest->Keterangan; ?></strong></td>
                                            <td style="text-align: right;"><?php echo number_format($dRest->Debet, 2); ?></td>
                                            <td style="text-align: right;"><?php echo number_format($dRest->Kredit, 2); ?></td>
                                            <td style="text-align: right;"><?php echo number_format($saldo, 2); ?></td>
                                        </tr>
                                <?php
                                    }
                                }
                                $closing = $debet - $kredit;
                                ?>
                                <tr>
                                    <td colspan="5" class="noborder"></td>
                                    <td style="text-align: right;" class="noborder"></td>
                                    <td style="text-align: right;" class="border-bottom2"><strong><?php echo number_format($debet, 2); ?></strong></td>
                                    <td style="text-align: right;" class="border-bottom2"><strong>(<?php echo number_format($kredit, 2); ?>)</strong></td>
                                    <td style="text-align: right;" class="border-bottom2"><strong>(<?php echo number_format($saldo, 2); ?>)</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="noborder"></td>
                                    <td style="text-align: right;" class="noborder"><strong>CLOSING : </strong></td>
                                    <td style="text-align: right;" class="border-bottom2"><?php echo number_format($closing, 2); ?></td>
                                    <td style="text-align: right;" class="border-bottom2">-</td>
                                    <td style="text-align: right;" class="border-bottom2">-</td>
                                </tr>
                            </tbody>
                        </table><br /><br />
                <?php
                    }
                }
            }
        }
    } else {
        $query = newQuery("get_results", "SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.Tipe='D' $cond ORDER BY KodeRekening ASC");
        if ($query) {
            foreach ($query as $data) {
                if ($data->IDCurrency > 1) $add = " (" . $data->Nama . ")";
                else $add = "";

                $dataRekening = newQuery("get_row", "SELECT * FROM tb_master_rekening WHERE IDRekening='" . $data->IDRekening . "'");
                $debet = getSaldoAwal($tgl, $bulan, $tahun, $data->IDRekening);
                ?>
                <strong>Kode Perkiraan : </strong><?php echo $data->KodeRekening; ?><br /><strong>Nama Perkiraan : </strong><?php echo $data->NamaRekening . $add; ?>
                <table class="tabelList2" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="20">No</th>
                            <!-- <th width="90">No. Jurnal</th> -->
                            <th width="50">No. Bukti</th>
                            <th width="60">Dept.</th>
                            <th width="60">Proyek</th>
                            <th width="60">Tanggal</th>
                            <th style="text-align: left;">Keterangan</th>
                            <th width="60">Debet</th>
                            <th width="60">Kredit</th>
                            <th width="60">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;

                        ?>
                        <tr>
                            <td style="text-align: center;"><?php echo $i; ?></td>
                            <!-- <td></td> -->
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="text-align: center;"><?php echo $tanggalID; ?></td>
                            <td><strong>Saldo awal</strong></td>
                            <td style="text-align: right;"><?php echo number_format($debet, 2); ?></td>
                            <td style="text-align: right;"><?php echo number_format(0, 2); ?></td>
                        </tr>
                        <?php
                        $kredit = 0;
                        $qRest = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE DATE_FORMAT(Tanggal,'%Y-%m')='$periode' AND IDRekening='" . $data->IDRekening . "' $condProyek ORDER BY Tanggal ASC, JurnalRef ASC");
                        if ($qRest) {
                            foreach ($qRest as $dRest) {
                                $i++;
                                $debet += $dRest->Debet;
                                $kredit += $dRest->Kredit;
                                $dJurnal = newQuery("get_row", "SELECT * FROM tb_jurnal WHERE IDJurnal='" . $dRest->IDJurnal . "'");
                                $proyek = newQuery("get_row", "SELECT * FROM tb_proyek WHERE IDProyek='" . $dJurnal->IDProyek . "'");
                                if ($proyek) $proyek = $proyek->KodeProyek . "/" . $proyek->Tahun;
                                else $proyek = "Umum";
                                $dept = newQuery("get_row", "SELECT * FROM tb_departement WHERE IDDepartement='" . $dJurnal->IDDepartement . "'");
                                if ($dept) $dept = $dept->NamaDepartement;
                                else $dept = "Umum";
                        ?>
                                <tr>
                                    <td style="text-align: center;"><?php echo $i; ?></td>
                                    <!-- <td style="text-align: center;"><?php if ($dJurnal) echo $dJurnal->NoJurnal;
                                                                            else echo "0000"; ?></td> -->
                                    <td style="text-align: center;"><?php echo $dRest->JurnalRef; ?></td>
                                    <td style="text-align: center;"><?php echo $dept; ?></td>
                                    <td style="text-align: center;"><?php echo $proyek; ?></td>
                                    <td style="text-align: center;"><?php echo $dRest->TanggalID; ?></td>
                                    <td><strong><?php echo $dRest->Keterangan; ?></strong></td>
                                    <td style="text-align: right;"><?php echo number_format($dRest->Debet, 2); ?></td>
                                    <td style="text-align: right;"><?php echo number_format($dRest->Kredit, 2); ?></td>
                                </tr>
                        <?php
                            }
                        }
                        $closing = $debet - $kredit;
                        ?>
                        <tr>
                            <td colspan="5" class="noborder"></td>
                            <td style="text-align: right;" class="noborder"></td>
                            <td style="text-align: right;" class="border-bottom2"><strong><?php echo number_format($debet, 2); ?></strong></td>
                            <td style="text-align: right;" class="border-bottom2"><strong>(<?php echo number_format($kredit, 2); ?>)</strong></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="noborder"></td>
                            <td style="text-align: right;" class="noborder"><strong>CLOSING : </strong></td>
                            <td style="text-align: right;" class="border-bottom2"><?php echo number_format($closing, 2); ?></td>
                            <td style="text-align: right;" class="border-bottom2">-</td>
                        </tr>
                    </tbody>
                </table><br /><br />
    <?php
            }
        }
    }
    ?>

    <script type="text/javascript">
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>