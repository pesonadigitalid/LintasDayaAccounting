<?php
$type = $this->validasi->validInput($_GET['type']);
$tgl = $this->validasi->validInput($_GET['tanggal']);
$bulan = $this->validasi->validInput($_GET['bulan']);
$tahun = $this->validasi->validInput($_GET['tahun']);

$daritanggal = $this->validasi->validInput($_GET['daritanggal']);
$sampaitanggal = $this->validasi->validInput($_GET['sampaitanggal']);
$status = 1;

$print_header = $this->validasi->validInput($_GET['print_header']);
$print_detail = $this->validasi->validInput($_GET['print_detail']);
// if($bulan!="" && $tahun!=""){
//     $jenis_transaksi = $this->validasi->validInput($_GET['jenis_transaksi']);
//     if($jenis_transaksi!="Semua Rekening"){
//         $cond = " AND a.IDRekening='$jenis_transaksi'";
//     }
// } else {
//     $bulan = date("m");
//     $tahun = date("Y");
// }
// if($tgl!=""){
//     if($tgl<10) $tgl="0".$tgl;
//     $periode = $tahun."-".$bulan."-".$tgl;
//     $condDate = "DATE_FORMAT(Tanggal,'%Y-%m-%d')='$periode'";
//     $tanggal = $tahun."-".$bulan."-".$tgl;
//     $tanggalID = $tgl."/".$bulan."/".$tahun;
//     $tanggalDisplay = $tgl." ".$this->fungsi->changeMonthNameID($bulan)." ".$tahun;

// }else{
//     $periode = $tahun."-".$bulan;
//     $condDate = "DATE_FORMAT(Tanggal,'%Y-%m')='$periode'";
//     $tanggal = $tahun."-".$bulan."-01";
//     $tanggalID = "01/".$bulan."/".$tahun;
//     $tanggalDisplay = $this->fungsi->changeMonthNameID($bulan)." ".$tahun;
// }
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
$condDate = "Tanggal BETWEEN '$daritanggalEN' AND '$sampaitanggalEN'";

if ($type != "") {
    if ($type == "LD") {
        $sub = " - Lintas Daya";
        $condDate .= " AND IDJurnal IN (SELECT IDJurnal FROM tb_jurnal WHERE Tanggal BETWEEN '$daritanggalEN' AND '$sampaitanggalEN' AND IDDepartement<>'4')";
    }
    if ($type == "MMS") {
        $sub = " - MMS";
        $condDate .= " AND IDJurnal IN (SELECT IDJurnal FROM tb_jurnal WHERE Tanggal BETWEEN '$daritanggalEN' AND '$sampaitanggalEN' AND IDDepartement='4')";
    }
}

$tanggalDisplay = $daritanggal . " - " . $sampaitanggal;

$db =  new ezSQL_mysql(YGDBUSER, YGDBPASS, YGDBNAME, YGDBHOST);

function CheckSaldo($IDRekening, $condDate, $db, $tipe)
{
    $departement = $_GET['departement'];
    $total = 0;
    $queryDetail = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
    if ($queryDetail) {
        foreach ($queryDetail as $dataDetail) {
            if ($tipe == "KREDIT")
                $total += $dataDetail->Kredit;
            else
                $total += $dataDetail->Debet;
        }
    }
    return number_format($total, 2);
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
    <?php if ($print_header == '1') { ?>
        <h1 class="blue">Laba Rugi Perusahaan <?php echo $sub; ?></h1>
        <h3 class="red">Periode: <?php echo $tanggalDisplay; ?></h3>
        <table class="tbLabaRugi" style="max-width: 500px">
            <tr>
                <td width="400" style="font-weight: bold;" class="red">Summary Report</td>
                <td width="100" style="text-align: right;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;" class="red">Saldo</td>
            </tr>
            <tr>
                <td class="labelHeader">Pendapatan</td>
                <td style="text-align: right;">Rupiah</td>
            </tr>
            <?php
            $labarugi = 0;
            $pendapatan = 0;
            $biaya = 0;
            $biayaLain = 0;
            $totalppn = 0;
            $query = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='63' ORDER BY NamaRekening ASC");
            if ($query) {
                foreach ($query as $data) {
                    if ($data->Tipe == "D") {
                        $saldoRek = CheckSaldo($data->IDRekening, $condDate, $db, "KREDIT");
                        if ($saldoRek != "0.00") {
            ?>
                            <tr>
                                <td class="labelHeader2 deep1"><?php echo $data->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($data->NamaRekening)); ?></td>
                                <td style="text-align: right;"><?php echo $saldoRek; ?></td>
                            </tr>
                            <?php
                            $queryDetail = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $data->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                            if ($queryDetail) {
                                foreach ($queryDetail as $dataDetail) {
                                    $jurnal = newQuery("get_row", "SELECT * FROM tb_jurnal WHERE IDJurnal='" . $dataDetail->IDJurnal . "'");
                                    if ($jurnal->NoRef != '' && $jurnal->Tipe == '1') {
                                        $invoice = newQuery("get_row", "SELECT * FROM tb_proyek_invoice WHERE IDInvoice='" . $jurnal->NoRef . "'");
                                        if ($invoice) {
                                            if ($invoice->PPNPersen > 0) {
                                                $ppn = $dataDetail->Kredit * $invoice->PPNPersen / 100;
                                                $totalppn += $ppn;
                                            }
                                        }
                                    }
                                    $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                    $pendapatan += $dataDetail->Kredit;
                                }
                            }
                        }
                    } else {
                        $querySub = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='" . $data->IDRekening . "' ORDER BY NamaRekening ASC");
                        if ($querySub) {
                            foreach ($querySub as $dataSub) {
                                if ($dataSub->Tipe == "D") {
                                    $saldoRek = CheckSaldo($dataSub->IDRekening, $condDate, $db, "KREDIT");
                                    if ($saldoRek != "0.00") {
                            ?>
                                        <tr>
                                            <td class="labelHeader2 deep1"><?php echo $dataSub->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($dataSub->NamaRekening)); ?></td>
                                            <td style="text-align: right;"><?php echo $saldoRek; ?></td>
                                        </tr>
                <?php
                                        $queryDetail = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $dataSub->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                        if ($queryDetail) {
                                            foreach ($queryDetail as $dataDetail) {
                                                $jurnal = newQuery("get_row", "SELECT * FROM tb_jurnal WHERE IDJurnal='" . $dataDetail->IDJurnal . "'");
                                                if ($jurnal->NoRef != '' && $jurnal->Tipe == '1') {
                                                    $invoice = newQuery("get_row", "SELECT * FROM tb_proyek_invoice WHERE IDInvoice='" . $jurnal->NoRef . "'");
                                                    if ($invoice) {
                                                        var_dump($invoice);
                                                        if ($invoice->PPNPersen > 0) {
                                                            $ppn = $dataDetail->Kredit * $invoice->PPNPersen / 100;
                                                            $totalppn += $ppn;
                                                        }
                                                    }
                                                }
                                                $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                                $pendapatan += $dataDetail->Kredit;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $pendapatanbruto = $pendapatan;
                $pendapatan = $pendapatanbruto - $totalppn;
                ?>
                <tr>
                    <td class="labelHeader">Total Pendapatan</td>
                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($pendapatanbruto, 2); ?></td>
                </tr>
            <?php
            }
            ?>
            <tr>
                <td class="labelHeader">Pajak Pendapatan</td>
                <td style="text-align: right;"></td>
            </tr>
            <tr>
                <td class="deep1">Pajak Pendapatan Periode <?php echo $tanggalDisplay; ?> </td>
                <td style="text-align: right;"><?php echo number_format($totalppn, 2); ?></td>
            </tr>
            <tr>
                <td class="labelHeader">Total Pajak Pendapatan</td>
                <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($totalppn, 2); ?></td>
            </tr>
            <tr>
                <td class="labelHeader">Total Total Pendapatan Bersih</td>
                <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($pendapatan, 2); ?></td>
            </tr>
            <tr>
                <td class="labelHeader spacer"></td>
                <td style="text-align: right;"></td>
            </tr>
            <tr>
                <td class="labelHeader">HPP</td>
                <td style="text-align: right;"></td>
            </tr>
            <?php
            $query = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='70' ORDER BY NamaRekening ASC");
            if ($query) {
                foreach ($query as $data) {
                    if ($data->Tipe == "D") {
                        $saldoRek = CheckSaldo($data->IDRekening, $condDate, $db, "DEBET");
                        if ($saldoRek != "0.00") {
            ?>
                            <tr>
                                <td class="labelHeader2 deep1"><?php echo $data->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($data->NamaRekening)); ?></td>
                                <td style="text-align: right;"><?php echo $saldoRek; ?></td>
                            </tr>
                            <?php
                            $queryDetail = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $data->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                            if ($queryDetail) {
                                foreach ($queryDetail as $dataDetail) {
                                    $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                    $hpp += $dataDetail->Debet;
                                }
                            }
                        }
                    } else {
                        $querySub = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='" . $data->IDRekening . "' ORDER BY NamaRekening ASC");
                        if ($querySub) {
                            foreach ($querySub as $dataSub) {
                                if ($dataSub->Tipe == "D") {
                                    $saldoRek = CheckSaldo($dataSub->IDRekening, $condDate, $db, "DEBET");
                                    if ($saldoRek != "0.00") {
                            ?>
                                        <tr>
                                            <td class="labelHeader2 deep1"><?php echo $dataSub->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($dataSub->NamaRekening)); ?></td>
                                            <td style="text-align: right;"><?php echo $saldoRek; ?></td>
                                        </tr>
                <?php
                                        $queryDetail = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $dataSub->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                        if ($queryDetail) {
                                            foreach ($queryDetail as $dataDetail) {
                                                $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                                $hpp += $dataDetail->Debet;
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
                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($hpp, 2); ?></td>
                </tr>
            <?php
            }
            ?>
            <tr>
                <td class="labelHeader"></td>
                <td style="text-align: right;"></td>
            </tr>
            <tr>
                <td class="labelHeader">Biaya</td>
                <td style="text-align: right;"></td>
            </tr>
            <?php
            $query = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='73' ORDER BY NamaRekening ASC");
            if ($query) {
                foreach ($query as $data) {
                    if ($data->Tipe == "D") {
                        $saldoRek = CheckSaldo($data->IDRekening, $condDate, $db, "DEBET");
                        if ($saldoRek != "0.00") {
            ?>
                            <tr>
                                <td class="labelHeader2 deep1"><?php echo $data->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($data->NamaRekening)); ?></td>
                                <td style="text-align: right;"><?php echo $saldoRek; ?></td>
                            </tr>
                            <?php
                            $queryDetail = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $data->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                            if ($queryDetail) {
                                foreach ($queryDetail as $dataDetail) {
                                    $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                    $biaya += $dataDetail->Debet;
                                }
                            }
                        }
                    } else {
                        $querySub = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='" . $data->IDRekening . "' ORDER BY NamaRekening ASC");
                        if ($querySub) {
                            foreach ($querySub as $dataSub) {
                                if ($dataSub->Tipe == "D") {
                                    $saldoRek = CheckSaldo($dataSub->IDRekening, $condDate, $db, "DEBET");
                                    if ($saldoRek != "0.00") {
                            ?>
                                        <tr>
                                            <td class="labelHeader2 deep1"><?php echo $dataSub->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($dataSub->NamaRekening)); ?></td>
                                            <td style="text-align: right;"><?php echo $saldoRek; ?></td>
                                        </tr>
                <?php
                                        $queryDetail = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $dataSub->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                        if ($queryDetail) {
                                            foreach ($queryDetail as $dataDetail) {
                                                $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                                $biaya += $dataDetail->Debet;
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
                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($biaya, 2); ?></td>
                </tr>
            <?php
            }
            ?>
            <tr>
                <td class="labelHeader"></td>
                <td style="text-align: right;"></td>
            </tr>
            <tr>
                <td class="labelHeader">Biaya Lain</td>
                <td style="text-align: right;"></td>
            </tr>
            <?php
            $query = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='101' ORDER BY NamaRekening ASC");
            if ($query) {
                foreach ($query as $data) {
                    if ($data->Tipe == "D") {
                        $saldoRek = CheckSaldo($data->IDRekening, $condDate, $db, "DEBET");
                        if ($saldoRek != "0.00") {
            ?>
                            <tr>
                                <td class="labelHeader2 deep1"><?php echo $data->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($data->NamaRekening)); ?></td>
                                <td style="text-align: right;"><?php echo $saldoRek; ?></td>
                            </tr>
                            <?php
                            $queryDetail = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $data->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                            if ($queryDetail) {
                                foreach ($queryDetail as $dataDetail) {
                                    $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                    $biayaLain += $dataDetail->Debet;
                                }
                            }
                        }
                    } else {
                        $querySub = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='" . $data->IDRekening . "' ORDER BY NamaRekening ASC");
                        if ($querySub) {
                            foreach ($querySub as $dataSub) {
                                if ($dataSub->Tipe == "D") {
                                    $saldoRek = CheckSaldo($dataSub->IDRekening, $condDate, $db, "DEBET");
                                    if ($saldoRek != "0.00") {
                            ?>
                                        <tr>
                                            <td class="labelHeader2 deep1"><?php echo $dataSub->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($dataSub->NamaRekening)); ?></td>
                                            <td style="text-align: right;"><?php echo $saldoRek; ?></td>
                                        </tr>
                <?php
                                        $queryDetail = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $dataSub->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                        if ($queryDetail) {
                                            foreach ($queryDetail as $dataDetail) {
                                                $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                                $biayaLain += $dataDetail->Debet;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $labarugi = $pendapatan - $hpp - $biaya - $biayaLain;
                $total = $pendapatan + $biaya + $biayaLain + $hpp;
                ?>
                <tr>
                    <td class="labelHeader">Total Biaya Lain</td>
                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($biayaLain, 2); ?></td>
                </tr>
                <tr>
                    <td class="labelHeader">Laba/Rugi</td>
                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($labarugi, 2); ?></td>
                </tr>
            <?php
            }
            ?>
        </table><br><br>
    <?php } ?>
    <?php if ($print_detail == '1') { ?>
        <div class="newPage"></div>
        <h1 class="blue">Laba Rugi Perusahaan <?php echo $sub; ?></h1>
        <h3 class="red">Periode: <?php echo $tanggalDisplay; ?></h3>
        <table class="tbLabaRugi" style="max-width: 500px">
            <tr>
                <td width="400"></td>
                <td width="100" style="text-align: right;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;" class="red">Saldo</td>
            </tr>
            <tr>
                <td class="labelHeader">Pendapatan</td>
                <td style="text-align: right;">Rupiah</td>
            </tr>
            <?php
            $labarugi = 0;
            $pendapatan = 0;
            $biaya = 0;
            $biayaLain = 0;
            $total = 0;
            $totalppn = 0;
            $hpp = 0;
            $query = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='63' ORDER BY NamaRekening ASC");
            if ($query) {
                foreach ($query as $data) {
                    if ($data->Tipe == "D") {
                        $saldoRek = CheckSaldo($data->IDRekening, $condDate, $db, "KREDIT");
                        if ($saldoRek != "0.00") {
            ?>
                            <tr>
                                <td class="labelHeader2 deep1"><?php echo $data->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($data->NamaRekening)); ?></td>
                                <td style="text-align: right;"></td>
                            </tr>
                            <?php

                            $queryDetail = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $data->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                            if ($queryDetail) {
                                $total = 0;
                                foreach ($queryDetail as $dataDetail) {
                                    $jurnal = newQuery("get_row", "SELECT * FROM tb_jurnal WHERE IDJurnal='" . $dataDetail->IDJurnal . "'");
                                    if ($jurnal->NoRef != '' && $jurnal->Tipe == '1') {
                                        $invoice = newQuery("get_row", "SELECT * FROM tb_proyek_invoice WHERE IDInvoice='" . $jurnal->NoRef . "'");
                                        if ($invoice) {
                                            if ($invoice->PPNPersen > 0) {
                                                $ppn = $dataDetail->Kredit * $invoice->PPNPersen / 100;
                                                $totalppn += $ppn;
                                            }
                                        }
                                    }
                                    $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");

                                    $pendapatan += $dataDetail->Kredit;
                                    $total += $dataDetail->Kredit;
                            ?>
                                    <tr>
                                        <td class="deep2">
                                            <table>
                                                <tr>
                                                    <td width="80"><?php echo $dataDetail->TanggalID; ?></td>
                                                    <td><?php echo $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td style="text-align: right;"><?php echo number_format($dataDetail->Kredit, 2); ?></td>
                                    </tr>
                                <?php
                                }
                                if ($total > 0) {
                                ?>
                                    <tr>
                                        <td class="labelHeader" style="text-align: right;">Total:</td>
                                        <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($total, 2); ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                    } else {
                        $querySub = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='" . $data->IDRekening . "' ORDER BY NamaRekening ASC");
                        if ($querySub) {
                            foreach ($querySub as $dataSub) {
                                if ($dataSub->Tipe == "D") {
                                    $saldoRek = CheckSaldo($dataSub->IDRekening, $condDate, $db, "KREDIT");
                                    if ($saldoRek != "0.00") {
                                    ?>
                                        <tr>
                                            <td class="labelHeader2 deep1"><?php echo $dataSub->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($dataSub->NamaRekening)); ?></td>
                                            <td style="text-align: right;"></td>
                                        </tr>
                                        <?php
                                        $queryDetail = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $dataSub->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                        if ($queryDetail) {
                                            $total = 0;
                                            foreach ($queryDetail as $dataDetail) {

                                                $jurnal = newQuery("get_row", "SELECT * FROM tb_jurnal WHERE IDJurnal='" . $dataDetail->IDJurnal . "'");
                                                if ($jurnal->NoRef != '' && $jurnal->Tipe == '1') {
                                                    $invoice = newQuery("get_row", "SELECT * FROM tb_proyek_invoice WHERE IDInvoice='" . $jurnal->NoRef . "'");
                                                    if ($invoice) {
                                                        if ($invoice->PPNPersen > 0) {
                                                            $ppn = $dataDetail->Kredit * $invoice->PPNPersen / 100;
                                                            $totalppn += $ppn;
                                                        }
                                                    }
                                                }

                                                $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");

                                                $pendapatan += $dataDetail->Kredit;
                                                $total += $dataDetail->Kredit;
                                        ?>
                                                <tr>
                                                    <td class="deep2">
                                                        <table>
                                                            <tr>
                                                                <td width="80"><?php echo $dataDetail->TanggalID; ?></td>
                                                                <td><?php echo $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td style="text-align: right;"><?php echo number_format($dataDetail->Kredit, 2); ?></td>
                                                </tr>
                                            <?php
                                            }
                                            if ($total > 0) {
                                            ?>
                                                <tr>
                                                    <td class="labelHeader" style="text-align: right;">Total:</td>
                                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($total, 2); ?></td>
                                                </tr>
                <?php
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $pendapatanbruto = $pendapatan;
                $pendapatan = $pendapatanbruto - $totalppn;
                ?>
                <tr>
                    <td class="labelHeader">Total Pendapatan</td>
                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($pendapatanbruto, 2); ?></td>
                </tr>
            <?php
            }
            ?>
            <tr>
                <td class="labelHeader">Pajak Pendapatan</td>
                <td style="text-align: right;"></td>
            </tr>
            <tr>
                <td class="deep1">Pajak Pendapatan Periode <?php echo $tanggalDisplay; ?> </td>
                <td style="text-align: right;"><?php echo number_format($totalppn, 2); ?></td>
            </tr>
            <tr>
                <td class="labelHeader">Total Pajak Pendapatan</td>
                <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($totalppn, 2); ?></td>
            </tr>
            <tr>
                <td class="labelHeader">Total Total Pendapatan Bersih</td>
                <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($pendapatan, 2); ?></td>
            </tr>
            <tr>
                <td class="labelHeader spacer"></td>
                <td style="text-align: right;"></td>
            </tr>
            <tr>
                <td class="labelHeader">HPP</td>
                <td style="text-align: right;"></td>
            </tr>
            <?php
            $query = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='70' ORDER BY NamaRekening ASC");
            if ($query) {
                foreach ($query as $data) {
                    if ($data->Tipe == "D") {
                        $saldoRek = CheckSaldo($data->IDRekening, $condDate, $db, "DEBET");
                        if ($saldoRek != "0.00") {
            ?>
                            <tr>
                                <td class="labelHeader2 deep1"><?php echo $data->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($data->NamaRekening)); ?></td>
                                <td style="text-align: right;"></td>
                            </tr>
                            <?php
                            $queryDetail = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $data->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                            if ($queryDetail) {
                                $total = 0;
                                foreach ($queryDetail as $dataDetail) {
                                    $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                    $hpp += $dataDetail->Debet;
                                    $total += $dataDetail->Debet;
                            ?>
                                    <tr>
                                        <td class="deep2">
                                            <table>
                                                <tr>
                                                    <td width="80"><?php echo $dataDetail->TanggalID; ?></td>
                                                    <td><?php echo $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td style="text-align: right;"><?php echo number_format($dataDetail->Debet, 2); ?></td>
                                    </tr>
                                <?php
                                }
                                if ($total > 0) {
                                ?>
                                    <tr>
                                        <td class="labelHeader" style="text-align: right;">Total:</td>
                                        <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($total, 2); ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                    } else {
                        $querySub = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='" . $data->IDRekening . "' ORDER BY NamaRekening ASC");
                        if ($querySub) {
                            foreach ($querySub as $dataSub) {
                                if ($dataSub->Tipe == "D") {
                                    $saldoRek = CheckSaldo($dataSub->IDRekening, $condDate, $db, "DEBET");
                                    if ($saldoRek != "0.00") {
                                    ?>
                                        <tr>
                                            <td class="labelHeader2 deep1"><?php echo $dataSub->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($dataSub->NamaRekening)); ?></td>
                                            <td style="text-align: right;"></td>
                                        </tr>
                                        <?php
                                        $queryDetail = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $dataSub->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                        if ($queryDetail) {
                                            $total = 0;
                                            foreach ($queryDetail as $dataDetail) {
                                                $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                                $hpp += $dataDetail->Debet;
                                                $total += $dataDetail->Debet;
                                        ?>
                                                <td class="deep2">
                                                    <table>
                                                        <tr>
                                                            <td width="80"><?php echo $dataDetail->TanggalID; ?></td>
                                                            <td><?php echo $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td style="text-align: right;"><?php echo number_format($dataDetail->Debet, 2); ?></td>
                                                </tr>
                                            <?php
                                            }
                                            if ($total > 0) {
                                            ?>
                                                <tr>
                                                    <td class="labelHeader" style="text-align: right;">Total:</td>
                                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($total, 2); ?></td>
                                                </tr>
                <?php
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
                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($hpp, 2); ?></td>
                </tr>
            <?php
            }
            ?>
            <tr>
                <td class="labelHeader"></td>
                <td style="text-align: right;"></td>
            </tr>
            <tr>
                <td class="labelHeader">Biaya</td>
                <td style="text-align: right;"></td>
            </tr>
            <?php
            $query = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='73' ORDER BY NamaRekening ASC");
            if ($query) {
                foreach ($query as $data) {
                    if ($data->Tipe == "D") {
                        $saldoRek = CheckSaldo($data->IDRekening, $condDate, $db, "DEBET");
                        if ($saldoRek != "0.00") {
            ?>
                            <tr>
                                <td class="labelHeader2 deep1"><?php echo $data->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($data->NamaRekening)); ?></td>
                                <td style="text-align: right;"></td>
                            </tr>
                            <?php
                            $queryDetail = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $data->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                            if ($queryDetail) {
                                $total = 0;
                                foreach ($queryDetail as $dataDetail) {
                                    $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                    $biaya += $dataDetail->Debet;
                                    $total += $dataDetail->Debet;
                            ?>
                                    <tr>
                                        <td class="deep2">
                                            <table>
                                                <tr>
                                                    <td width="80"><?php echo $dataDetail->TanggalID; ?></td>
                                                    <td><?php echo $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td style="text-align: right;"><?php echo number_format($dataDetail->Debet, 2); ?></td>
                                    </tr>
                                <?php
                                }
                                if ($total > 0) {
                                ?>
                                    <tr>
                                        <td class="labelHeader" style="text-align: right;">Total:</td>
                                        <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($total, 2); ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                    } else {
                        $querySub = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='" . $data->IDRekening . "' ORDER BY NamaRekening ASC");
                        if ($querySub) {
                            foreach ($querySub as $dataSub) {
                                if ($dataSub->Tipe == "D") {
                                    $saldoRek = CheckSaldo($dataSub->IDRekening, $condDate, $db, "DEBET");
                                    if ($saldoRek != "0.00") {
                                    ?>
                                        <tr>
                                            <td class="labelHeader2 deep1"><?php echo $dataSub->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($dataSub->NamaRekening)); ?></td>
                                            <td style="text-align: right;"></td>
                                        </tr>
                                        <?php
                                        $queryDetail = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $dataSub->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                        if ($queryDetail) {
                                            $total = 0;
                                            foreach ($queryDetail as $dataDetail) {
                                                $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                                $biaya += $dataDetail->Debet;
                                                $total += $dataDetail->Debet;
                                        ?>
                                                <tr>
                                                    <td class="deep2">
                                                        <table>
                                                            <tr>
                                                                <td width="80"><?php echo $dataDetail->TanggalID; ?></td>
                                                                <td><?php echo $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td style="text-align: right;"><?php echo number_format($dataDetail->Debet, 2); ?></td>
                                                </tr>
                                            <?php
                                            }
                                            if ($total > 0) {
                                            ?>
                                                <tr>
                                                    <td class="labelHeader" style="text-align: right;">Total:</td>
                                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($total, 2); ?></td>
                                                </tr>
                <?php
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
                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($biaya, 2); ?></td>
                </tr>
            <?php
            }
            ?>
            <tr>
                <td class="labelHeader"></td>
                <td style="text-align: right;"></td>
            </tr>
            <tr>
                <td class="labelHeader">Biaya Lain</td>
                <td style="text-align: right;"></td>
            </tr>
            <?php
            $query = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='101' ORDER BY NamaRekening ASC");
            if ($query) {
                foreach ($query as $data) {
                    if ($data->Tipe == "D") {
                        $saldoRek = CheckSaldo($data->IDRekening, $condDate, $db, "DEBET");
                        if ($saldoRek != "0.00") {
            ?>
                            <tr>
                                <td class="labelHeader2 deep1"><?php echo $data->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($data->NamaRekening)); ?></td>
                                <td style="text-align: right;"></td>
                            </tr>
                            <?php
                            $queryDetail = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $data->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                            if ($queryDetail) {
                                $total = 0;
                                foreach ($queryDetail as $dataDetail) {
                                    $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                    $biayaLain += $dataDetail->Debet;
                                    $total += $dataDetail->Debet;
                            ?>
                                    <tr>
                                        <td class="deep2">
                                            <table>
                                                <tr>
                                                    <td width="80"><?php echo $dataDetail->TanggalID; ?></td>
                                                    <td><?php echo $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td style="text-align: right;"><?php echo number_format($dataDetail->Debet, 2); ?></td>
                                    </tr>
                                <?php
                                }
                                if ($total > 0) {
                                ?>
                                    <tr>
                                        <td class="labelHeader" style="text-align: right;">Total:</td>
                                        <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($total, 2); ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                    } else {
                        $querySub = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='" . $data->IDRekening . "' ORDER BY NamaRekening ASC");
                        if ($querySub) {
                            foreach ($querySub as $dataSub) {
                                if ($dataSub->Tipe == "D") {
                                    $saldoRek = CheckSaldo($dataSub->IDRekening, $condDate, $db, "DEBET");
                                    if ($saldoRek != "0.00") {
                                    ?>
                                        <tr>
                                            <td class="labelHeader2 deep1"><?php echo $dataSub->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($dataSub->NamaRekening)); ?></td>
                                            <td style="text-align: right;"></td>
                                        </tr>
                                        <?php
                                        $queryDetail = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $dataSub->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                        if ($queryDetail) {
                                            $total = 0;
                                            foreach ($queryDetail as $dataDetail) {
                                                $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                                $biayaLain += $dataDetail->Debet;
                                                $total += $dataDetail->Debet;
                                        ?>
                                                <tr>
                                                    <td class="deep2">
                                                        <table>
                                                            <tr>
                                                                <td width="80"><?php echo $dataDetail->TanggalID; ?></td>
                                                                <td><?php echo $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td style="text-align: right;"><?php echo number_format($dataDetail->Debet, 2); ?></td>
                                                <?php
                                            }
                                            if ($total > 0) {
                                                ?>
                                                <tr>
                                                    <td class="labelHeader" style="text-align: right;">Total:</td>
                                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($total, 2); ?></td>
                                                </tr>
                <?php
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $labarugi = $pendapatan - $hpp - $biaya - $biayaLain;
                $total = $pendapatan + $biaya + $biayaLain + $hpp;
                ?>
                <tr>
                    <td class="labelHeader">Total Biaya Lain</td>
                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($biayaLain, 2); ?></td>
                </tr>
                <tr>
                    <td class="labelHeader">Laba/Rugi</td>
                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($labarugi, 2); ?></td>
                </tr>
            <?php
            }
            ?>
        </table><br><br>
    <?php } ?>
    <?php if ($print_header == '1') { ?>
        <div id="chartContainer" style="width:500px; height:500px; margin: 0 auto;"></div>
    <?php } ?>
    <script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/jquery.1.10.2.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script type="text/javascript">
        $(function() {
            Highcharts.chart('chartContainer', {
                chart: {
                    type: 'pie',
                    options3d: {
                        enabled: true,
                        alpha: 45
                    }
                },
                title: {
                    text: 'Grafik Laba Rugi'
                },
                plotOptions: {
                    pie: {
                        innerSize: 100,
                        depth: 45
                    }
                },
                series: [{
                    name: 'Persentase (%)',
                    data: [
                        ['Pendapatan', <?php echo round(($pendapatan / $total * 100), 2); ?>],
                        ['Biaya', <?php echo round(($biaya / $total * 100), 2); ?>],
                        ['HPP', <?php echo round(($hpp / $total * 100), 2); ?>]
                    ]
                }],
                credits: {
                    enabled: false
                }
            });
        });
    </script>
    <script type="text/javascript">
        setTimeout(function() {
            window.print();
        }, 1500);
    </script>
</body>

</html>