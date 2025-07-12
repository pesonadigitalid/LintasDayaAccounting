<?php
$proyek = $this->validasi->validInput($_GET['proyek']);
$tahun = $this->validasi->validInput($_GET['tahun']);
$debug = $this->validasi->validInput($_GET['debug']);
$status = 1;
$dProyek = newQuery("get_row", "SELECT * FROM tb_proyek WHERE IDProyek='$proyek'");
$isProyekPPN = $dProyek && $dProyek->PPNPersen > 0;
$proyekPPN = $dProyek ? $dProyek->PPNPersen : 0;
$pName = $dProyek ? $dProyek->NamaProyek : "";

$tanggalDisplay = "";
if ($tahun != "") {
    $tanggalDisplay .= "<strong>Tahun </strong> : " . $tahun . ", ";
}
$tanggalDisplay .= $pName;

$db =  new ezSQL_mysql(YGDBUSER, YGDBPASS, YGDBNAME, YGDBHOST);

function CheckSaldo($IDRekening, $condDate = "", $db, $tipe)
{
    $proyek = $_GET['proyek'];
    $total = 0;
    $queryDetail = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND a.`IDRekening`='" . $IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
    if ($queryDetail) {
        foreach ($queryDetail as $dataDetail) {
            if ($tipe == "KREDIT")
                $total += $dataDetail->Kredit;
            else
                $total += $dataDetail->Debet;
        }
    }
    if ($total > 0) return "";
    else return number_format($total, 2);
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
    <h1 class="blue">Laba Rugi Proyek</h1>
    <h3 class="red"><?php echo $tanggalDisplay; ?></h3>
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
        $query = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='63' ORDER BY NamaRekening ASC");
        if ($query) {
            foreach ($query as $data) {
                if ($data->Tipe == "D") {
        ?>
                    <tr>
                        <td class="labelHeader2 deep1"><?php echo $data->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($data->NamaRekening)); ?></td>
                        <td style="text-align: right;"><?php echo CheckSaldo($data->IDRekening, $condDate, $db, "KREDIT"); ?></td>
                    </tr>
                    <?php
                    $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND a.`IDRekening`='" . $data->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                    if ($queryDetail) {
                        foreach ($queryDetail as $dataDetail) {
                            $jurnal = newQuery("get_row", "SELECT * FROM tb_jurnal WHERE IDJurnal='" . $dataDetail->IDJurnal . "'");
                            $ppn = 0;
                            $invoice = new stdClass();
                            $invoice->PPNPersen = 0;
                            if ($jurnal->NoRef != '' && ($jurnal->Tipe == '1' || $jurnal->Tipe == '8')) {
                                if ($jurnal->JurnalPPNInvoice == '1') {
                                    $invoice->PPNPersen = 100;
                                    $ppn = $dataDetail->Kredit;
                                } else {
                                    $invoice = newQuery("get_row", "SELECT * FROM tb_proyek_invoice WHERE IDInvoice='" . $jurnal->NoRef . "'");
                                    if ($invoice) {
                                        if ($invoice->PPNPersen > 0) {
                                            // $ppn = $dataDetail->Kredit * $invoice->PPNPersen / 100;
                                            $dpp = $dataDetail->Kredit * (100 / (100 + $invoice->PPNPersen));
                                            $ppn = $dataDetail->Kredit - $dpp;
                                        }
                                    }
                                }
                                $totalppn += $ppn;
                            }

                            if ($isProyekPPN && $ppn <= 0) {
                                $dataDetail->Keterangan .= " (*)";
                                if ($debug == "1") $dataDetail->Keterangan .= "<br/>No Jurnal : <a href='" . PRSONPATH . "/jurnal-umum/" . $jurnal->NoJurnal . "' target='_blank'>" . $jurnal->NoJurnal . "</a> / PPN Persen : " . $invoice->PPNPersen . "% / PPN : " . number_format($ppn, 2);
                            } else if ($isProyekPPN && $debug == "1") {
                                $dataDetail->Keterangan .= "<br/>No Jurnal : <a href='" . PRSONPATH . "/jurnal-umum/" . $jurnal->NoJurnal . "' target='_blank'>" . $jurnal->NoJurnal . "</a> / PPN Persen : " . $invoice->PPNPersen . "% / PPN : " . number_format($ppn, 2);
                            }

                            $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                            $pendapatan += $dataDetail->Kredit;
                    ?>
                            <tr>
                                <td class="deep2">
                                    <table>
                                        <tr>
                                            <td width="80"><?php echo $dataDetail->TanggalID; ?></td>
                                            <td><?php echo $dataDetail->Keterangan; ?></td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="text-align: right;"><?php echo number_format($dataDetail->Kredit, 2); ?></td>
                            </tr>
                            <?php
                        }
                    }
                } else {
                    $querySub = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='" . $data->IDRekening . "' ORDER BY NamaRekening ASC");
                    if ($querySub) {
                        foreach ($querySub as $dataSub) {
                            if ($dataSub->Tipe == "D") {
                            ?>
                                <tr>
                                    <td class="labelHeader2 deep1"><?php echo $dataSub->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($dataSub->NamaRekening)); ?></td>
                                    <td style="text-align: right;"><?php echo CheckSaldo($dataSub->IDRekening, $condDate, $db, "KREDIT"); ?></td>
                                </tr>
                                <?php
                                $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND a.`IDRekening`='" . $dataSub->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                if ($queryDetail) {
                                    foreach ($queryDetail as $dataDetail) {
                                        $jurnal = newQuery("get_row", "SELECT * FROM tb_jurnal WHERE IDJurnal='" . $dataDetail->IDJurnal . "'");
                                        $ppn = 0;
                                        $invoice = new stdClass();
                                        $invoice->PPNPersen = 0;
                                        if ($jurnal->NoRef != '' && ($jurnal->Tipe == '1' || $jurnal->Tipe == '8')) {
                                            if ($jurnal->JurnalPPNInvoice == '1') {
                                                $invoice->PPNPersen = 100;
                                                $ppn = $dataDetail->Kredit;
                                            } else {
                                                $invoice = newQuery("get_row", "SELECT * FROM tb_proyek_invoice WHERE IDInvoice='" . $jurnal->NoRef . "'");
                                                if ($invoice) {
                                                    if ($invoice->PPNPersen > 0) {
                                                        // $ppn = $dataDetail->Kredit * $invoice->PPNPersen / 100;
                                                        $dpp = $dataDetail->Kredit * (100 / (100 + $invoice->PPNPersen));
                                                        $ppn = $dataDetail->Kredit - $dpp;
                                                    }
                                                }
                                            }
                                            $totalppn += $ppn;
                                        }

                                        if ($isProyekPPN && $ppn <= 0) {
                                            $dataDetail->Keterangan .= " (*)";
                                            if ($debug == "1") $dataDetail->Keterangan .= "<br/>No Jurnal : <a href='" . PRSONPATH . "/jurnal-umum/" . $jurnal->NoJurnal . "' target='_blank'>" . $jurnal->NoJurnal . "</a> / PPN Persen : " . $invoice->PPNPersen . "% / PPN : " . number_format($ppn, 2);
                                        } else if ($isProyekPPN && $debug == "1") {
                                            $dataDetail->Keterangan .= "<br/>No Jurnal : <a href='" . PRSONPATH . "/jurnal-umum/" . $jurnal->NoJurnal . "' target='_blank'>" . $jurnal->NoJurnal . "</a> / PPN Persen : " . $invoice->PPNPersen . "% / PPN : " . number_format($ppn, 2);
                                        }

                                        $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                        $pendapatan += $dataDetail->Kredit;
                                ?>
                                        <tr>
                                            <td class="deep2">
                                                <table>
                                                    <tr>
                                                        <td width="80"><?php echo $dataDetail->TanggalID; ?></td>
                                                        <td><?php echo $dataDetail->Keterangan; ?></td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td style="text-align: right;"><?php echo number_format($dataDetail->Kredit, 2); ?></td>
                                        </tr>
            <?php
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
        ?>
                    <tr>
                        <td class="labelHeader2 deep1"><?php echo $data->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($data->NamaRekening)); ?></td>
                        <td style="text-align: right;"><?php echo CheckSaldo($data->IDRekening, $condDate, $db, "DEBET"); ?></td>
                    </tr>
                    <?php
                    $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID, b.Tipe FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND a.`IDRekening`='" . $data->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                    if ($queryDetail) {
                        foreach ($queryDetail as $dataDetail) {
                            $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                            $hpp += $dataDetail->Debet;

                            if ($debug == "1") $dataDetail->Keterangan .= " / Tipe: " . $dataDetail->Tipe;
                    ?>
                            <tr>
                                <td class="deep2">
                                    <table>
                                        <tr>
                                            <td width="80"><?php echo $dataDetail->TanggalID; ?></td>
                                            <td><?php echo $dataDetail->Keterangan; ?></td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="text-align: right;"><?php echo number_format($dataDetail->Debet, 2); ?></td>
                            </tr>
                            <?php
                        }
                    }
                } else {
                    $querySub = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='" . $data->IDRekening . "' ORDER BY NamaRekening ASC");
                    if ($querySub) {
                        foreach ($querySub as $dataSub) {
                            if ($dataSub->Tipe == "D") {
                            ?>
                                <tr>
                                    <td class="labelHeader2 deep1"><?php echo $dataSub->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($dataSub->NamaRekening)); ?></td>
                                    <td style="text-align: right;"><?php echo CheckSaldo($dataSub->IDRekening, $condDate, $db, "DEBET"); ?></td>
                                </tr>
                                <?php
                                $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND a.`IDRekening`='" . $dataSub->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                if ($queryDetail) {
                                    foreach ($queryDetail as $dataDetail) {
                                        $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                        $hpp += $dataDetail->Debet;

                                        if ($debug == "1") $dataDetail->Keterangan .= " / Tipe: " . $dataDetail->Tipe;
                                ?>
                                        <tr>
                                            <td class="deep2">
                                                <table>
                                                    <tr>
                                                        <td width="80"><?php echo $dataDetail->TanggalID; ?></td>
                                                        <td><?php echo $dataDetail->Keterangan; ?></td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td style="text-align: right;"><?php echo number_format($dataDetail->Debet, 2); ?></td>
                                        </tr>
            <?php
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
        ?>
                    <tr>
                        <td class="labelHeader2 deep1"><?php echo $data->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($data->NamaRekening)); ?></td>
                        <td style="text-align: right;"><?php echo CheckSaldo($data->IDRekening, $condDate, $db, "DEBET"); ?></td>
                    </tr>
                    <?php
                    $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND a.`IDRekening`='" . $data->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                    if ($queryDetail) {
                        foreach ($queryDetail as $dataDetail) {
                            $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                            $biaya += $dataDetail->Debet;

                            if ($debug == "1") $dataDetail->Keterangan .= " / Tipe: " . $dataDetail->Tipe;
                    ?>
                            <tr>
                                <td class="deep2">
                                    <table>
                                        <tr>
                                            <td width="80"><?php echo $dataDetail->TanggalID; ?></td>
                                            <td><?php echo $dataDetail->Keterangan; ?></td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="text-align: right;"><?php echo number_format($dataDetail->Debet, 2); ?></td>
                            </tr>
                            <?php
                        }
                    }
                } else {
                    $querySub = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='" . $data->IDRekening . "' ORDER BY NamaRekening ASC");
                    if ($querySub) {
                        foreach ($querySub as $dataSub) {
                            if ($dataSub->Tipe == "D") {
                            ?>
                                <tr>
                                    <td class="labelHeader2 deep1"><?php echo $dataSub->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($dataSub->NamaRekening)); ?></td>
                                    <td style="text-align: right;"><?php echo CheckSaldo($dataSub->IDRekening, $condDate, $db, "DEBET"); ?></td>
                                </tr>
                                <?php
                                $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND a.`IDRekening`='" . $dataSub->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                if ($queryDetail) {
                                    foreach ($queryDetail as $dataDetail) {
                                        $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                        $biaya += $dataDetail->Debet;

                                        if ($debug == "1") $dataDetail->Keterangan .= " / Tipe: " . $dataDetail->Tipe;
                                ?>
                                        <tr>
                                            <td class="deep2">
                                                <table>
                                                    <tr>
                                                        <td width="80"><?php echo $dataDetail->TanggalID; ?></td>
                                                        <td><?php echo $dataDetail->Keterangan; ?></td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td style="text-align: right;"><?php echo number_format($dataDetail->Debet, 2); ?></td>
                                        </tr>
            <?php
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
        ?>
                    <tr>
                        <td class="labelHeader2 deep1"><?php echo $data->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($data->NamaRekening)); ?></td>
                        <td style="text-align: right;"><?php echo CheckSaldo($data->IDRekening, $condDate, $db, "DEBET"); ?></td>
                    </tr>
                    <?php
                    $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND a.`IDRekening`='" . $data->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                    if ($queryDetail) {
                        foreach ($queryDetail as $dataDetail) {
                            $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                            $biayaLain += $dataDetail->Debet;

                            if ($debug == "1") $dataDetail->Keterangan .= " / Tipe: " . $dataDetail->Tipe;
                    ?>
                            <tr>
                                <td class="deep2">
                                    <table>
                                        <tr>
                                            <td width="80"><?php echo $dataDetail->TanggalID; ?></td>
                                            <td><?php echo $dataDetail->Keterangan; ?></td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="text-align: right;"><?php echo number_format($dataDetail->Debet, 2); ?></td>
                            </tr>
                            <?php
                        }
                    }
                } else {
                    $querySub = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='" . $data->IDRekening . "' ORDER BY NamaRekening ASC");
                    if ($querySub) {
                        foreach ($querySub as $dataSub) {
                            if ($dataSub->Tipe == "D") {
                            ?>
                                <tr>
                                    <td class="labelHeader2 deep1"><?php echo $dataSub->KodeRekening . "&nbsp;&nbsp;&nbsp;" . ucwords(strtolower($dataSub->NamaRekening)); ?></td>
                                    <td style="text-align: right;"><?php echo CheckSaldo($dataSub->IDRekening, $condDate, $db, "DEBET"); ?></td>
                                </tr>
                                <?php
                                $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND a.`IDRekening`='" . $dataSub->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                if ($queryDetail) {
                                    foreach ($queryDetail as $dataDetail) {
                                        $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                        $biayaLain += $dataDetail->Debet;

                                        if ($debug == "1") $dataDetail->Keterangan .= " / Tipe: " . $dataDetail->Tipe;
                                ?>
                                        <tr>
                                            <td class="deep2">
                                                <table>
                                                    <tr>
                                                        <td width="80"><?php echo $dataDetail->TanggalID; ?></td>
                                                        <td><?php echo $dataDetail->Keterangan; ?></td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td style="text-align: right;"><?php echo number_format($dataDetail->Debet, 2); ?></td>
                                        </tr>
            <?php
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
    <div id="chartContainer" style="width:500px; height:500px; margin: 0 auto;"></div>

    <script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/jquery.1.10.2.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script type="text/javascript">
        $(function() {
            $('#tahun').change(function() {
                if ($(this).val() !== "") {
                    $('.proyek').hide();
                    $('.proyek' + $(this).val()).show();
                    $('#proyek').val('');
                } else {
                    $('.proyek').show();
                    $('#proyek').val('');
                }
            });
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
                        ['Pendapatan', <?php if ($pendapatan <= 0) echo "0";
                                        else echo round(($pendapatan / $total * 100), 2); ?>],
                        ['Biaya', <?php if ($biaya <= 0) echo "0";
                                    else echo round(($biaya / $total * 100), 2); ?>],
                        ['HPP', <?php if ($hpp <= 0) echo "0";
                                else echo round(($hpp / $total * 100), 2); ?>]
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