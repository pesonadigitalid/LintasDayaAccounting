<?php
$tgl = $this->validasi->validInput($_GET['tanggal']);
$bulan = $this->validasi->validInput($_GET['bulan']);
$tahun = $this->validasi->validInput($_GET['tahun']);
$departement = $this->validasi->validInput($_GET['departement']);
$id_proyek = $this->validasi->validInput($_GET['id_proyek']);
$print_header = $this->validasi->validInput($_GET['print_header']);
$print_detail = $this->validasi->validInput($_GET['print_detail']);

$daritanggal = $this->validasi->validInput($_GET['daritanggal']);
$sampaitanggal = $this->validasi->validInput($_GET['sampaitanggal']);

$status = 1;
if ($daritanggal == "" && $sampaitanggal == "") {
    $bulan = date("m");
    $tahun = date("Y");
    $daritanggal = date('01/m/Y');
    $sampaitanggal  = date('t/m/Y');
    $daritanggalEN = date('Y-m-01');
    $sampaitanggalEN  = date('Y-m-t');
} else {
    $exp = explode("/", $daritanggal);
    $daritanggalEN = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
    $exp = explode("/", $sampaitanggal);
    $sampaitanggalEN  = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
}

$idDepartements = array(0, 1, 3, 5);
$idTipe = '1';
$periode = $daritanggal . " - " . $sampaitanggal;
$condDate = " AND b.Tanggal BETWEEN '$daritanggalEN' AND '$sampaitanggalEN'";
$tanggal = $tahun . "-" . $bulan . "-01";
$tanggalID = "01/" . $bulan . " / " . $tahun;
$tanggalDisplay = $periode;

if ($id_proyek != '') $condDate .= " AND b.IDProyek='$id_proyek' ";

$db =  new ezSQL_mysql(YGDBUSER, YGDBPASS, YGDBNAME, YGDBHOST);

function CheckSaldo($IDRekening, $condDate, $db, $tipe, $departement = '0')
{
    $total = 0;
    $queryDetail = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDDepartement`='$departement' AND a.`IDRekening`='" . $IDRekening . "' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
    if ($queryDetail) {
        foreach ($queryDetail as $dataDetail) {
            if ($tipe == "KREDIT")
                $total += $dataDetail->Kredit;
            else
                $total += $dataDetail->Debet;
        }
    }
    return $total;
}

function CheckSaldoDepartements($IDRekening, $condDate, $db, $tipe)
{
    $idDepartements = array(0, 1, 3, 5);
    $saldo = array();
    foreach ($idDepartements as $departement) {
        array_push($saldo, CheckSaldo($IDRekening, $condDate, $db, $tipe, $departement));
    }
    return $saldo;
}

function ShouldDisplay($saldos)
{
    $empty = 0;
    foreach ($saldos as $saldo) {
        if ($saldo == "0.00") $empty++;
    }
    return count($saldos) != $empty;
}

function GetTotal($saldos)
{
    $total = 0;
    foreach ($saldos as $saldo) {
        $total += $saldo;
    }
    return $total;
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
    <link rel="stylesheet" href="<?php echo PRSONTEMPPATH; ?>css/print-style-acc.css" media="all" />
</head>

<body class="center">
    <h1 class="blue">Laba Rugi Konsolidasi</h1>
    <h3 class="red">Periode: <?php echo $tanggalDisplay; ?></h3>
    <table class="tbLabaRugi" style="max-width: 1200px">
        <tr>
            <td width="400" style="font-weight: bold;" class="red">Summary Report</td>
            <td width="100" style="text-align: right;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;" class="red">Umum</td>
            <td width="100" style="text-align: right;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;" class="red">Konstruksi</td>
            <td width="100" style="text-align: right;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;" class="red">Maintenance</td>
            <td width="100" style="text-align: right;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;" class="red">Design</td>
            <td width="100" style="text-align: right;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;" class="red">Total</td>
        </tr>
        <tr>
            <td class="labelHeader">Pendapatan</td>
            <td style="text-align: right;">Rupiah</td>
            <td style="text-align: right;">Rupiah</td>
            <td style="text-align: right;">Rupiah</td>
            <td style="text-align: right;">Rupiah</td>
            <td style="text-align: right;">Rupiah</td>
        </tr>
        <?php
        $pendapatanbruto = array();
        $pendapatan = array();
        $totalppn = array();

        $labarugi = array();
        $biaya = array();
        $biayaLain = array();
        $query = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='63' ORDER BY KodeRekening ASC");
        if ($query) {
            foreach ($query as $data) {
                if ($data->Tipe == "D") {
                    $saldoReks = CheckSaldoDepartements($data->IDRekening, $condDate, $db, "KREDIT");
                    if (ShouldDisplay($saldoReks)) {
        ?>
                        <tr>
                            <td class="labelHeader2 deep1"><?php echo $data->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($data->NamaRekening)); ?></td>
                            <?php foreach ($saldoReks as $saldoRek) { ?>
                                <td style="text-align: right;"><?php echo number_format($saldoRek, 2); ?></td>
                            <?php } ?>
                            <td style="text-align: right;"><?php echo number_format(GetTotal($saldoReks), 2); ?></td>
                        </tr>
                        <?php
                        foreach ($idDepartements as $key => $departement) {
                            $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDDepartement`='$departement' AND a.`IDRekening`='" . $data->IDRekening . "' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
                            if ($queryDetail) {
                                foreach ($queryDetail as $dataDetail) {
                                    $jurnal = newQuery("get_row", "SELECT * FROM tb_jurnal WHERE IDJurnal='" . $dataDetail->IDJurnal . "'");
                                    if ($jurnal->NoRef != '' && $jurnal->Tipe == $idTipe) {
                                        $invoice = newQuery("get_row", "SELECT * FROM tb_proyek_invoice WHERE IDInvoice='" . $jurnal->NoRef . "'");
                                        if ($invoice) {
                                            if ($invoice->PPNPersen > 0) {
                                                // var_dump($departement);
                                                // var_dump($invoice->NoInv . " - " . $invoice->IDProyek . " - " . $dataDetail->Kredit . " - " . $invoice->PPNPersen);
                                                $ppn = $dataDetail->Kredit * $invoice->PPNPersen / 100;
                                                $totalppn[$key] += $ppn;
                                            }
                                        }
                                    }
                                    $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                    $pendapatan[$key] += $dataDetail->Kredit;
                                }
                            }
                        }
                    }
                } else {
                    $querySub = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='" . $data->IDRekening . "' ORDER BY KodeRekening ASC");
                    if ($querySub) {
                        foreach ($querySub as $dataSub) {
                            if ($dataSub->Tipe == "D") {

                                $saldoReks = CheckSaldoDepartements($dataSub->IDRekening, $condDate, $db, "KREDIT");
                                if (ShouldDisplay($saldoReks)) {
                        ?>
                                    <tr>
                                        <td class="labelHeader2 deep1"><?php echo $dataSub->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($dataSub->NamaRekening)); ?></td>
                                        <?php foreach ($saldoReks as $saldoRek) { ?>
                                            <td style="text-align: right;"><?php echo number_format($saldoRek, 2); ?></td>
                                        <?php } ?>
                                        <td style="text-align: right;"><?php echo number_format(GetTotal($saldoReks), 2); ?></td>
                                    </tr>
            <?php

                                    foreach ($idDepartements as $key => $departement) {
                                        $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDDepartement`='$departement' AND a.`IDRekening`='" . $dataSub->IDRekening . "' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
                                        if ($queryDetail) {
                                            foreach ($queryDetail as $dataDetail) {
                                                $jurnal = newQuery("get_row", "SELECT * FROM tb_jurnal WHERE IDJurnal='" . $dataDetail->IDJurnal . "'");
                                                if ($jurnal->NoRef != '' && $jurnal->Tipe == $idTipe) {
                                                    $invoice = newQuery("get_row", "SELECT * FROM tb_proyek_invoice WHERE IDInvoice='" . $jurnal->NoRef . "'");
                                                    if ($invoice) {
                                                        if ($invoice->PPNPersen > 0) {
                                                            $ppn = $dataDetail->Kredit * $invoice->PPNPersen / 100;
                                                            $totalppn[$key] += $ppn;
                                                        }
                                                    }
                                                }
                                                $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                                $pendapatan[$key] += $dataDetail->Kredit;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }


            foreach ($idDepartements as $key => $departement) {
                $pendapatanbruto[$key] = $pendapatan[$key];
                $pendapatan[$key] = $pendapatanbruto[$key] - $totalppn[$key];
            }
            ?>
            <tr>
                <td class="labelHeader">Total Pendapatan</td>
                <?php foreach ($idDepartements as $key => $departement) { ?>
                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($pendapatanbruto[$key], 2); ?></td>
                <?php } ?>
                <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format(GetTotal($pendapatanbruto), 2); ?></td>
            </tr>
        <?php
        }
        ?>
        <tr>
            <td class="labelHeader">Pajak Pendapatan</td>
            <?php foreach ($idDepartements as $key => $departement) { ?>
                <td style="text-align: right;"></td>
            <?php } ?>
            <td style="text-align: right;"></td>
        </tr>
        <tr>
            <td class="deep1">Pajak Pendapatan Periode <?php echo $tanggalDisplay; ?> </td>
            <?php foreach ($idDepartements as $key => $departement) { ?>
                <td style="text-align: right;"><?php echo number_format($totalppn[$key], 2); ?></td>
            <?php } ?>
            <td style="text-align: right;"><?php echo number_format(GetTotal($totalppn), 2); ?></td>
        </tr>
        <tr>
            <td class="labelHeader">Total Pajak Pendapatan</td>
            <?php foreach ($idDepartements as $key => $departement) { ?>
                <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($totalppn[$key], 2); ?></td>
            <?php } ?>
            <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format(GetTotal($totalppn), 2); ?></td>
        </tr>
        <tr>
            <td class="labelHeader">Total Total Pendapatan Bersih</td>
            <?php foreach ($idDepartements as $key => $departement) { ?>
                <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($pendapatan[$key], 2); ?></td>
            <?php } ?>
            <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format(GetTotal($pendapatan), 2); ?></td>
        </tr>
        <tr>
            <td class="labelHeader spacer"></td>
            <?php foreach ($idDepartements as $key => $departement) { ?>
                <td style="text-align: right;"></td>
            <?php } ?>
            <td style="text-align: right;"></td>
        </tr>
        <tr>
            <td class="labelHeader">HPP</td>
            <?php foreach ($idDepartements as $key => $departement) { ?>
                <td style="text-align: right;"></td>
            <?php } ?>
            <td style="text-align: right;"></td>
        </tr>
        <?php
        $query = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='70' ORDER BY KodeRekening ASC");
        if ($query) {
            foreach ($query as $data) {
                if ($data->Tipe == "D") {

                    $saldoReks = CheckSaldoDepartements($data->IDRekening, $condDate, $db, "DEBET");
                    if (ShouldDisplay($saldoReks)) {
        ?>
                        <tr>
                            <td class="labelHeader2 deep1"><?php echo $data->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($data->NamaRekening)); ?></td>
                            <?php foreach ($saldoReks as $saldoRek) { ?>
                                <td style="text-align: right;"><?php echo number_format($saldoRek, 2); ?></td>
                            <?php } ?>
                            <td style="text-align: right;"><?php echo number_format(GetTotal($saldoReks), 2); ?></td>
                        </tr>
                        <?php
                        foreach ($idDepartements as $key => $departement) {
                            $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDDepartement`='$departement' AND a.`IDRekening`='" . $data->IDRekening . "' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
                            if ($queryDetail) {
                                foreach ($queryDetail as $dataDetail) {
                                    $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                    $hpp[$key] += $dataDetail->Debet;
                                }
                            }
                        }
                    }
                } else {
                    $querySub = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='" . $data->IDRekening . "' ORDER BY KodeRekening ASC");
                    if ($querySub) {
                        foreach ($querySub as $dataSub) {
                            if ($dataSub->Tipe == "D") {
                                $saldoReks = CheckSaldoDepartements($dataSub->IDRekening, $condDate, $db, "DEBET");
                                if (ShouldDisplay($saldoReks)) {
                        ?>
                                    <tr>
                                        <td class="labelHeader2 deep1"><?php echo $dataSub->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($dataSub->NamaRekening)); ?></td>
                                        <?php foreach ($saldoReks as $saldoRek) { ?>
                                            <td style="text-align: right;"><?php echo number_format($saldoRek, 2); ?></td>
                                        <?php } ?>
                                        <td style="text-align: right;"><?php echo number_format(GetTotal($saldoReks), 2); ?></td>
                                    </tr>
            <?php
                                    foreach ($idDepartements as $key => $departement) {
                                        $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDDepartement`='$departement' AND a.`IDRekening`='" . $dataSub->IDRekening . "' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
                                        if ($queryDetail) {
                                            foreach ($queryDetail as $dataDetail) {
                                                $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                                $hpp[$key] += $dataDetail->Debet;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            ?>
            <tr>
                <td class="labelHeader">Total HPP</td>
                <?php foreach ($idDepartements as $key => $departement) { ?>
                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($hpp[$key], 2); ?></td>
                <?php } ?>
                <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format(GetTotal($hpp), 2); ?></td>
            </tr>
        <?php
        }
        ?>
        <tr>
            <td class="labelHeader"></td>
            <?php foreach ($idDepartements as $key => $departement) { ?>
                <td style="text-align: right;"></td>
            <?php } ?>
            <td style="text-align: right;"></td>
        </tr>
        <tr>
            <td class="labelHeader">Biaya</td>
            <?php foreach ($idDepartements as $key => $departement) { ?>
                <td style="text-align: right;"></td>
            <?php } ?>
            <td style="text-align: right;"></td>
        </tr>
        <?php
        $query = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='73' ORDER BY KodeRekening ASC");
        if ($query) {
            foreach ($query as $data) {
                if ($data->Tipe == "D") {
                    $saldoReks = CheckSaldoDepartements($data->IDRekening, $condDate, $db, "DEBET");
                    if (ShouldDisplay($saldoReks)) {
        ?>
                        <tr>
                            <td class="labelHeader2 deep1"><?php echo $data->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($data->NamaRekening)); ?></td>
                            <?php foreach ($saldoReks as $saldoRek) { ?>
                                <td style="text-align: right;"><?php echo number_format($saldoRek, 2); ?></td>
                            <?php } ?>
                            <td style="text-align: right;"><?php echo number_format(GetTotal($saldoReks), 2); ?></td>
                        </tr>
                        <?php
                        foreach ($idDepartements as $key => $departement) {
                            $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDDepartement`='$departement' AND a.`IDRekening`='" . $data->IDRekening . "' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
                            if ($queryDetail) {
                                foreach ($queryDetail as $dataDetail) {
                                    $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                    $biaya[$key] += $dataDetail->Debet;
                                }
                            }
                        }
                    }
                } else {
                    $querySub = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='" . $data->IDRekening . "' ORDER BY KodeRekening ASC");
                    if ($querySub) {
                        foreach ($querySub as $dataSub) {
                            if ($dataSub->Tipe == "D") {
                                $saldoReks = CheckSaldoDepartements($dataSub->IDRekening, $condDate, $db, "DEBET");
                                if (ShouldDisplay($saldoReks)) {
                        ?>
                                    <tr>
                                        <td class="labelHeader2 deep1"><?php echo $dataSub->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($dataSub->NamaRekening)); ?></td>
                                        <?php foreach ($saldoReks as $saldoRek) { ?>
                                            <td style="text-align: right;"><?php echo number_format($saldoRek, 2); ?></td>
                                        <?php } ?>
                                        <td style="text-align: right;"><?php echo number_format(GetTotal($saldoReks), 2); ?></td>
                                    </tr>
            <?php
                                    foreach ($idDepartements as $key => $departement) {
                                        $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDDepartement`='$departement' AND a.`IDRekening`='" . $dataSub->IDRekening . "' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
                                        if ($queryDetail) {
                                            foreach ($queryDetail as $dataDetail) {
                                                $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                                $biaya[$key] += $dataDetail->Debet;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            ?>
            <tr>
                <td class="labelHeader">Total Biaya</td>
                <?php foreach ($idDepartements as $key => $departement) { ?>
                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($biaya[$key], 2); ?></td>
                <?php } ?>
                <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format(GetTotal($biaya), 2); ?></td>
            </tr>
        <?php
        }
        ?>
        <tr>
            <td class="labelHeader"></td>
            <?php foreach ($idDepartements as $key => $departement) { ?>
                <td style="text-align: right;"></td>
            <?php } ?>
            <td style="text-align: right;"></td>
        </tr>
        <tr>
            <td class="labelHeader">Biaya Lain</td>
            <?php foreach ($idDepartements as $key => $departement) { ?>
                <td style="text-align: right;"></td>
            <?php } ?>
            <td style="text-align: right;"></td>
        </tr>
        <?php
        $query = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='101' ORDER BY KodeRekening ASC");
        if ($query) {
            foreach ($query as $data) {
                if ($data->Tipe == "D") {
                    $saldoReks = CheckSaldoDepartements($data->IDRekening, $condDate, $db, "DEBET");
                    if (ShouldDisplay($saldoReks)) {
        ?>
                        <tr>
                            <td class="labelHeader2 deep1"><?php echo $data->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($data->NamaRekening)); ?></td>
                            <?php foreach ($saldoReks as $saldoRek) { ?>
                                <td style="text-align: right;"><?php echo number_format($saldoRek, 2); ?></td>
                            <?php } ?>
                            <td style="text-align: right;"><?php echo number_format(GetTotal($saldoReks), 2); ?></td>
                        </tr>
                        <?php
                        foreach ($idDepartements as $key => $departement) {
                            $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDDepartement`='$departement' AND a.`IDRekening`='" . $data->IDRekening . "' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
                            if ($queryDetail) {
                                foreach ($queryDetail as $dataDetail) {
                                    $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                    $biayaLain[$key] += $dataDetail->Debet;
                                }
                            }
                        }
                    }
                } else {
                    $querySub = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='" . $data->IDRekening . "' ORDER BY KodeRekening ASC");
                    if ($querySub) {
                        foreach ($querySub as $dataSub) {
                            if ($dataSub->Tipe == "D") {
                                $saldoReks = CheckSaldoDepartements($dataSub->IDRekening, $condDate, $db, "DEBET");
                                if (ShouldDisplay($saldoReks)) {
                        ?>
                                    <tr>
                                        <td class="labelHeader2 deep1"><?php echo $dataSub->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($dataSub->NamaRekening)); ?></td>
                                        <?php foreach ($saldoReks as $saldoRek) { ?>
                                            <td style="text-align: right;"><?php echo number_format($saldoRek, 2); ?></td>
                                        <?php } ?>
                                        <td style="text-align: right;"><?php echo number_format(GetTotal($saldoReks), 2); ?></td>
                                    </tr>
            <?php
                                    foreach ($idDepartements as $key => $departement) {
                                        $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDDepartement`='$departement' AND a.`IDRekening`='" . $dataSub->IDRekening . "' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
                                        if ($queryDetail) {
                                            foreach ($queryDetail as $dataDetail) {
                                                $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                                $biayaLain[$key] += $dataDetail->Debet;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            foreach ($idDepartements as $key => $departement) {
                $labarugi[$key] = $pendapatan[$key] - $hpp[$key] - $biaya[$key] - $biayaLain[$key];
                $total[$key] = $pendapatan[$key] + $biaya[$key] + $biayaLain[$key] + $hpp[$key];
            }
            ?>
            <tr>
                <td class="labelHeader">Total Biaya Lain</td>
                <?php foreach ($idDepartements as $key => $departement) { ?>
                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($biayaLain[$key], 2); ?></td>
                <?php } ?>
                <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format(GetTotal($biayaLain), 2); ?></td>
            </tr>
            <tr>
                <td class="labelHeader">Laba/Rugi</td>
                <?php foreach ($idDepartements as $key => $departement) { ?>
                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($labarugi[$key], 2); ?></td>
                <?php } ?>
                <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format(GetTotal($labarugi), 2); ?></td>
            </tr>
        <?php
        }
        ?>
    </table><br><br>
    <script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/jquery.1.10.2.min.js"></script>
    <script type="text/javascript">
        setTimeout(function() {
            window.print();
        }, 1500);
    </script>
</body>

</html>