<?php
$proyek = $this->validasi->validInput($_GET['proyek']);
$tahun = $this->validasi->validInput($_GET['tahun']);
$status = 1;
$dProyek = newQuery("get_row", "SELECT * FROM tb_proyek WHERE IDProyek='$proyek'");
$isProyekPPN = $dProyek && $dProyek->PPNPersen > 0;
?>
<?php include "pages/header.php"; ?>
<section class="section section-body bg-grey">
    <section id="overview" class="grid-hero container">
        <div class="columns">
            <div class="column col-7">
                <h5>Laporan Laba Rugi Proyek<small>Laporan Laba Rugi Per Project</small></h5>
                <?php if (isset($notif)) { ?>
                    <div class="toast toast-<?php echo $notif['class']; ?>">
                        <button class="btn btn-clear float-right"></button>
                        <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
                    </div>
                <?php } ?>
                <form method="GET" action="" class="form-horizontal form-bordered" style="padding: 0px 0;border-top:solid 1px #d3d3d3;border-bottom:solid 1px #d3d3d3;">
                    <div class="columns">
                        <div class="column col-2">
                            <label class="form-label" for="input-example-1">Proyek :</label>
                        </div>
                        <div class="column col-7">
                            <select name="tahun" id="tahun" class="form-select">
                                <option value="">ALL</option>
                                <?php for ($i = 2012; $i <= date("Y"); $i++) { ?>
                                    <option value="<?php echo $i; ?>" <?php if ($tahun == $i) echo "selected"; ?>><?php echo $i; ?></option>
                                <?php } ?>
                            </select>
                            <select name="proyek" id="proyek" class="form-select" style="max-width: 320px;">
                                <option value="">Pilih Proyek</option>
                                <option value="0" <?php if ($proyek == 0) echo "selected"; ?>>Umum</option>
                                <?php
                                $query = newQuery("get_results", "SELECT * FROM tb_proyek ORDER BY Tahun DESC, KodeProyek ASC");
                                if ($query) {
                                    foreach ($query as $data) {
                                ?><option value="<?php echo $data->IDProyek; ?>" class="proyek proyek<?php echo $data->Tahun; ?>" <?php if ($proyek == $data->IDProyek) echo "selected"; ?>><?php echo $data->KodeProyek . " / " . $data->Tahun . " / " . $data->NamaProyek; ?></option><?php
                                                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                                                        ?>
                            </select>
                        </div>
                        <div class="column col-3">
                            <button type="submit" name="filterbutton" value="1" class="btn btn-success">Filter</button>
                            <?php if ($proyek != "") { ?>
                                <a href="<?php echo PRSONPATH . "print-laba-rugi-proyek/?tahun=$tahun&proyek=$proyek"; ?>" target="_blank" class="btn btn-danger"><i class="fa fa-print"></i> Print</a>
                            <?php } ?>
                        </div>
                    </div>
                </form>
                <?php if ($proyek != "") { ?>
                    <table class="table report-table">
                        <tr>
                            <th colspan="2">PENDAPATAN</th>
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
                        ?>
                                    <tr>
                                        <td><strong><?php echo $data->KodeRekening . " " . $data->NamaRekening; ?></strong></td>
                                        <td class="saldo"></td>
                                    </tr>
                                    <?php
                                    $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND a.`IDRekening`='" . $data->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                    if ($queryDetail) {
                                        foreach ($queryDetail as $dataDetail) {
                                            $jurnal = newQuery("get_row", "SELECT * FROM tb_jurnal WHERE IDJurnal='" . $dataDetail->IDJurnal . "'");
                                            $ppn = 0;
                                            if ($jurnal->NoRef != '' && ($jurnal->Tipe == '1' || $jurnal->Tipe == '8')) {
                                                if ($jurnal->JurnalPPNInvoice == '1') {
                                                    $invoice = new stdClass();
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
                                            }
                                            // else {
                                            //     $dataDetail->Keterangan .= " / PPN Persen : " . $invoice->PPNPersen . "% / PPN : " . number_format($ppn, 2);
                                            // }

                                            $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                            $pendapatan += $dataDetail->Kredit;
                                    ?>
                                            <tr>
                                                <td class="deep1"><?php echo "<strong>" . $dataDetail->TanggalID . "</strong> &nbsp;&nbsp;&nbsp;&nbsp;" . $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                                <td class="saldo"><?php echo number_format($dataDetail->Kredit, 2); ?></td>
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
                                                    <td><strong><?php echo $dataSub->KodeRekening . " " . $dataSub->NamaRekening; ?></strong></td>
                                                    <td class="saldo"></td>
                                                </tr>
                                                <?php
                                                $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND a.`IDRekening`='" . $dataSub->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                                if ($queryDetail) {
                                                    foreach ($queryDetail as $dataDetail) {
                                                        $jurnal = newQuery("get_row", "SELECT * FROM tb_jurnal WHERE IDJurnal='" . $dataDetail->IDJurnal . "'");
                                                        $ppn = 0;
                                                        if ($jurnal->NoRef != '' && ($jurnal->Tipe == '1' || $jurnal->Tipe == '8')) {
                                                            if ($jurnal->JurnalPPNInvoice == '1') {
                                                                $invoice = new stdClass();
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
                                                        }
                                                        // else {
                                                        //     $dataDetail->Keterangan .= " / PPN Persen : " . $invoice->PPNPersen . "% / PPN : " . number_format($ppn, 2);
                                                        // }

                                                        $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                                        $pendapatan += $dataDetail->Kredit;
                                                ?>
                                                        <tr>
                                                            <td class="deep1"><?php echo "<strong>" . $dataDetail->TanggalID . "</strong> &nbsp;&nbsp;&nbsp;&nbsp;" . $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                                            <td class="saldo"><?php echo number_format($dataDetail->Kredit, 2); ?></td>
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
                                <td class="deep1" style="text-align: right;"><strong>TOTAL PENDAPATAN : </strong></td>
                                <td class="saldo"><strong>Rp. <?php echo number_format($pendapatanbruto, 2); ?></strong></td>
                            </tr>
                        <?php
                        }
                        ?>
                        <tr>
                            <th colspan="2">PPN PENDAPATAN</th>
                        </tr>
                        <tr>
                            <td class="deep1"><strong>TOTAL PPN</strong></td>
                            <td class="saldo">Rp. <?php echo number_format($totalppn, 2); ?></td>
                        </tr>
                        <tr>
                            <td class="deep1" style="text-align: right;"><strong>TOTAL PENDAPATAN BERSIH : </strong></td>
                            <td class="saldo"><strong>Rp. <?php echo number_format($pendapatan, 2); ?></strong></td>
                        </tr>
                        <tr>
                            <td class="deep1" style="text-align: right;"></td>
                            <td class="saldo"></td>
                        </tr>
                        <tr>
                            <th colspan="2">HPP</th>
                        </tr>
                        <?php
                        $query = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='70' ORDER BY NamaRekening ASC");
                        if ($query) {
                            foreach ($query as $data) {
                                if ($data->Tipe == "D") {
                        ?>
                                    <tr>
                                        <td><strong><?php echo $data->KodeRekening . " " . $data->NamaRekening; ?></strong></td>
                                        <td class="saldo"></td>
                                    </tr>
                                    <?php
                                    $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND a.`IDRekening`='" . $data->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                    if ($queryDetail) {
                                        foreach ($queryDetail as $dataDetail) {
                                            $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                            $hpp += $dataDetail->Debet;
                                    ?>
                                            <tr>
                                                <td class="deep1"><?php echo "<strong>" . $dataDetail->TanggalID . "</strong> &nbsp;&nbsp;&nbsp;&nbsp;" . $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                                <td class="saldo"><?php echo number_format($dataDetail->Debet, 2); ?></td>
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
                                                    <td><strong><?php echo $dataSub->KodeRekening . " " . $dataSub->NamaRekening; ?></strong></td>
                                                    <td class="saldo"></td>
                                                </tr>
                                                <?php
                                                $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND a.`IDRekening`='" . $dataSub->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                                if ($queryDetail) {
                                                    foreach ($queryDetail as $dataDetail) {
                                                        $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                                        $hpp += $dataDetail->Debet;
                                                ?>
                                                        <tr>
                                                            <td class="deep1"><?php echo "<strong>" . $dataDetail->TanggalID . "</strong> &nbsp;&nbsp;&nbsp;&nbsp;" . $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                                            <td class="saldo"><?php echo number_format($dataDetail->Debet, 2); ?></td>
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
                                <td class="deep1" style="text-align: right;"><strong>TOTAL HPP : </strong></td>
                                <td class="saldo"><strong>Rp. <?php echo number_format($hpp, 2); ?></strong></td>
                            </tr>
                        <?php
                        }
                        ?>
                        <tr>
                            <th colspan="2">BIAYA</th>
                        </tr>
                        <?php
                        $query = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='73' ORDER BY NamaRekening ASC");
                        if ($query) {
                            foreach ($query as $data) {
                                if ($data->Tipe == "D") {
                        ?>
                                    <tr>
                                        <td><strong><?php echo $data->KodeRekening . " " . $data->NamaRekening; ?></strong></td>
                                        <td class="saldo"></td>
                                    </tr>
                                    <?php
                                    $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND a.`IDRekening`='" . $data->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                    if ($queryDetail) {
                                        foreach ($queryDetail as $dataDetail) {
                                            $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                            $biaya += $dataDetail->Debet;
                                    ?>
                                            <tr>
                                                <td class="deep1"><?php echo "<strong>" . $dataDetail->TanggalID . "</strong> &nbsp;&nbsp;&nbsp;&nbsp;" . $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                                <td class="saldo"><?php echo number_format($dataDetail->Debet, 2); ?></td>
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
                                                    <td><strong><?php echo $dataSub->KodeRekening . " " . $dataSub->NamaRekening; ?></strong></td>
                                                    <td class="saldo"></td>
                                                </tr>
                                                <?php
                                                $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND a.`IDRekening`='" . $dataSub->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                                if ($queryDetail) {
                                                    foreach ($queryDetail as $dataDetail) {
                                                        $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                                        $biaya += $dataDetail->Debet;
                                                ?>
                                                        <tr>
                                                            <td class="deep1"><?php echo "<strong>" . $dataDetail->TanggalID . "</strong> &nbsp;&nbsp;&nbsp;&nbsp;" . $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                                            <td class="saldo"><?php echo number_format($dataDetail->Debet, 2); ?></td>
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
                                <td class="deep1" style="text-align: right;"><strong>TOTAL BIAYA : </strong></td>
                                <td class="saldo"><strong>Rp. <?php echo number_format($biaya, 2); ?></strong></td>
                            </tr>
                        <?php
                        }
                        ?>
                        <tr>
                            <th colspan="2">BIAYA LAIN</th>
                        </tr>
                        <?php
                        $query = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='101' ORDER BY NamaRekening ASC");
                        if ($query) {
                            foreach ($query as $data) {
                                if ($data->Tipe == "D") {
                        ?>
                                    <tr>
                                        <td><strong><?php echo $data->KodeRekening . " " . $data->NamaRekening; ?></strong></td>
                                        <td class="saldo"></td>
                                    </tr>
                                    <?php
                                    $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND a.`IDRekening`='" . $data->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                    if ($queryDetail) {
                                        foreach ($queryDetail as $dataDetail) {
                                            $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                            $biayaLain += $dataDetail->Debet;
                                    ?>
                                            <tr>
                                                <td class="deep1"><?php echo "<strong>" . $dataDetail->TanggalID . "</strong> &nbsp;&nbsp;&nbsp;&nbsp;" . $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                                <td class="saldo"><?php echo number_format($dataDetail->Debet, 2); ?></td>
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
                                                    <td><strong><?php echo $dataSub->KodeRekening . " " . $dataSub->NamaRekening; ?></strong></td>
                                                    <td class="saldo"></td>
                                                </tr>
                                                <?php
                                                $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND a.`IDRekening`='" . $dataSub->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                                if ($queryDetail) {
                                                    foreach ($queryDetail as $dataDetail) {
                                                        $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                                        $biayaLain += $dataDetail->Debet;
                                                ?>
                                                        <tr>
                                                            <td class="deep1"><?php echo "<strong>" . $dataDetail->TanggalID . "</strong> &nbsp;&nbsp;&nbsp;&nbsp;" . $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                                            <td class="saldo"><?php echo number_format($dataDetail->Debet, 2); ?></td>
                                                        </tr>
                            <?php
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $labarugi = $pendapatan - $biaya - $biayaLain - $hpp;
                            $total = $pendapatan + $biaya + $biayaLain + $hpp;
                            ?>
                            <tr>
                                <td class="deep1" style="text-align: right;"><strong>TOTAL BIAYA LAIN : </strong></td>
                                <td class="saldo"><strong>Rp. <?php echo number_format($biayaLain, 2); ?></strong></td>
                            </tr>
                            <tr>
                                <td class="deep1" style="text-align: right;"><strong>LABA / RUGI : </strong></td>
                                <td class="saldo"><strong>Rp. <?php echo number_format($labarugi, 2); ?></strong></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </table>
                <?php } ?>
            </div>
            <?php if ($proyek != "") { ?>
                <div class="column col-5" style="padding-top: 160px;">
                    <div id="chartContainer" style="width:100%; height:400px;"></div>
                    <!--<div id="chartContainer2" style="width:100%; height:400px;"></div>-->
                    <?php
                    $nilaiProyek = newQuery("get_var", "SELECT GrandTotal FROM tb_proyek WHERE IDProyek='$proyek'");
                    $persenNilaiProyek = round($pendapatan / $nilaiProyek * 100);

                    $limitPengeluaran = newQuery("get_var", "SELECT LimitPengeluaran FROM tb_proyek WHERE IDProyek='$proyek'");

                    $limitMaterial = newQuery("get_var", "SELECT LimitPengeluaranMaterial FROM tb_proyek WHERE IDProyek='$proyek'");
                    $limitGaji = newQuery("get_var", "SELECT LimitPengeluaranGaji FROM tb_proyek WHERE IDProyek='$proyek'");
                    $limitOverhead = newQuery("get_var", "SELECT LimitPengeluaranOverHead FROM tb_proyek WHERE IDProyek='$proyek'");

                    $totalInvoice = newQuery("get_var", "SELECT SUM(GrandTotal) FROM tb_proyek_invoice WHERE IDProyek='$proyek'");
                    if (!$totalInvoice) $totalInvoice = 0;

                    $totalPiutang = newQuery("get_var", "SELECT SUM(Sisa) FROM tb_proyek_invoice WHERE Sisa>0 AND IDProyek='$proyek'");
                    if (!$totalPiutang) $totalPiutang = 0;

                    $pengeluaranTotal = $pengeluaranGaji + $pengeluaranMaterial + $pengeluaranOverhead;
                    $persenPengeluaran = round($pengeluaranTotal / $limitPengeluaran * 100);

                    $material1 = 0;
                    $material2 = 0;
                    $material3 = 0;
                    $query = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po WHERE IDProyek='$proyek' AND JenisPO='1' ORDER BY IDProyek ASC");
                    if ($query) {
                        foreach ($query as $data) {
                            $material1 += $data->GrandTotal;
                            $material2 += $data->TotalPembayaran;
                            $material3 += $data->Sisa;
                        }
                    }

                    $tenaga1 = 0;
                    $tenaga2 = 0;
                    $tenaga3 = 0;
                    $query = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po WHERE IDProyek='$proyek' AND JenisPO='2' ORDER BY IDProyek ASC");
                    if ($query) {
                        foreach ($query as $data) {
                            $tenaga1 += $data->GrandTotal;
                            $tenaga2 += $data->TotalPembayaran;
                            $tenaga3 += $data->Sisa;
                        }
                    }

                    $overhead1 = 0;
                    $overhead2 = 0;
                    $overhead3 = 0;
                    $query = newQuery("get_results", "SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal a, tb_jurnal_detail b WHERE a.IDJurnal=b.IDJurnal AND b.IDRekening IN (SELECT IDRekening FROM tb_master_rekening WHERE IDParent='73' OR IDParent IN (SELECT IDParent FROM tb_master_rekening WHERE IDParent='73')) AND b.Debet>0 AND a.IDProyek='$proyek'");
                    if ($query) {
                        foreach ($query as $data) {
                            $overhead1 += $data->Debet;
                            $overhead2 += $data->Debet;
                            $overhead3 += 0;
                        }
                    }
                    ?>
                    <table class="table report-table">
                        <tr>
                            <th colspan="4">RINGKASAN PROJEKSI PROYEK</th>
                        </tr>
                        <tr>
                            <th>Nilai Proyek</th>
                            <th>Terbit Invoice</th>
                            <th>Terbayar</th>
                            <th>Sisa</th>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;"><?php echo number_format($nilaiProyek, 0); ?></td>
                            <td style="font-weight: bold;"><?php echo number_format($totalInvoice, 0); ?></td>
                            <td style="font-weight: bold;"><?php echo number_format($pendapatanbruto, 0); ?></td>
                            <td style="font-weight: bold;"><?php echo number_format($totalPiutang, 0); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                        <tr>
                            <th colspan="4">Limitasi Pengeluaran</th>
                        </tr>
                        <tr>
                            <th>Jenis</th>
                            <th>Limit</th>
                            <th>Penggunaan</th>
                            <th>Nilai Hutang</th>
                        </tr>
                        <tr>
                            <td>Limit Material</td>
                            <td><?php echo number_format($limitMaterial, 0); ?></td>
                            <td><?php echo number_format($material1, 0); ?></td>
                            <td><?php echo number_format($material3, 0); ?></td>
                        </tr>
                        <tr>
                            <td>Limit Tenaga</td>
                            <td><?php echo number_format($limitGaji, 0); ?></td>
                            <td><?php echo number_format($tenaga1, 0); ?></td>
                            <td><?php echo number_format($tenaga3, 0); ?></td>
                        </tr>
                        <tr>
                            <td>Limit Overhead</td>
                            <td><?php echo number_format($limitOverhead, 0); ?></td>
                            <td><?php echo number_format($overhead1, 0); ?></td>
                            <td><?php echo number_format($overhead3, 0); ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Total</td>
                            <td style="font-weight: bold;"><?php echo number_format(($limitMaterial + $limitGaji + $limitOverhead), 0); ?></td>
                            <td style="font-weight: bold;"><?php echo number_format(($material1 + $tenaga1 + $overhead1), 0); ?></td>
                            <td style="font-weight: bold;"><?php echo number_format(($material3 + $tenaga3 + $overhead3), 0); ?></td>
                        </tr>
                    </table>
                </div>
        </div>
    <?php } ?>
    <?php
    function createDateRangeArray($strDateFrom, $strDateTo)
    {
        $aryRange = array();

        $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2),     substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
        $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2),     substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

        if ($iDateTo >= $iDateFrom) {
            array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry
            while ($iDateFrom < $iDateTo) {
                $iDateFrom += 86400; // add 24 hours
                array_push($aryRange, date('Y-m-d', $iDateFrom));
            }
        }
        return $aryRange;
    }

    function selectTotalBiayaDate($date, $proyek)
    {
        $biaya = 0;
        $query = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE (IDParent='73' OR IDParent='70') ORDER BY NamaRekening ASC");
        if ($query) {
            foreach ($query as $data) {
                if ($data->Tipe == "D") {
                    $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND b.Tanggal='$date' AND a.`IDRekening`='" . $data->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                    if ($queryDetail) {
                        foreach ($queryDetail as $dataDetail) {
                            $biaya += $dataDetail->Debet;
                        }
                    }
                } else {
                    $querySub = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='" . $data->IDRekening . "' ORDER BY NamaRekening ASC");
                    if ($querySub) {
                        foreach ($querySub as $dataSub) {
                            if ($dataSub->Tipe == "D") {
                                $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND b.Tanggal='$date' AND a.`IDRekening`='" . $dataSub->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                if ($queryDetail) {
                                    foreach ($queryDetail as $dataDetail) {
                                        $biaya += $dataDetail->Debet;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $biaya;
    }

    function selectTotalPendapatanDate($date, $proyek)
    {
        $pendapatan = 0;
        $query = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='63' ORDER BY NamaRekening ASC");
        if ($query) {
            foreach ($query as $data) {
                if ($data->Tipe == "D") {
                    $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND b.Tanggal='$date' AND a.`IDRekening`='" . $data->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                    if ($queryDetail) {
                        foreach ($queryDetail as $dataDetail) {
                            $pendapatan += $dataDetail->Kredit;
                        }
                    }
                } else {
                    $querySub = newQuery("get_results", "SELECT * FROM `tb_master_rekening` WHERE IDParent='" . $data->IDRekening . "' ORDER BY NamaRekening ASC");
                    if ($querySub) {
                        foreach ($querySub as $dataSub) {
                            if ($dataSub->Tipe == "D") {
                                $queryDetail = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND b.Tanggal='$date' AND a.`IDRekening`='" . $dataSub->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                if ($queryDetail) {
                                    foreach ($queryDetail as $dataDetail) {
                                        $pendapatan += $dataDetail->Kredit;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $pendapatan;
    }


    $minDate = newQuery("get_var", "SELECT MIN(Tanggal) FROM tb_jurnal WHERE IDProyek='$proyek'");
    $minDate = date('Y-m-d', strtotime($minDate . ' -1 day'));
    $maxDate = newQuery("get_var", "SELECT MAX(Tanggal) FROM tb_jurnal WHERE IDProyek='$proyek'");
    $nilaiProyek = newQuery("get_var", "SELECT GrandTotal FROM tb_proyek WHERE IDProyek='$proyek'");
    $range = createDateRangeArray($minDate, $maxDate);

    ?>
    </section>
</section>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
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
        <?php if ($proyek) { ?>
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
                }]
            });
            <?php
            /*
        var categ=[
                    <?php 
                     $return = "";
                     foreach($range as $data){
                        $exp = explode("-",$data);
                         $return .= "'".$exp[2]."/".$exp[1]."/".$exp[0]."',";
                     }
                     echo substr($return,0,-1);
                     ?>
                 ];
        Highcharts.chart('chartContainer2', {
            
             chart: {
                 type: 'area'
             },
             title: {
                 text: 'Pendapatan dan Pengeluaran'
             },
             xAxis: {
                 categories: categ,
            tickmarkPlacement: 'on',
            title: {
              text: 'Tanggal',
              style: {
                fontWeight: 'bold'
              },
              formatter: function(){
                return "<h3><b>" + this.value + "</b></h3>";  
              }
            },
            min: 0.5,
            max: categ.length-1.5,
            startOnTick: false,
            endOnTick: false,
            minPadding: 0,
            maxPadding: 0,
            align: "left"       
             },
             yAxis: {
                 title: {
                     text: 'Nilai dalam Rupiah'
                 },
                 labels: {
                     formatter: function () {
                         return this.value / 1000000 + 'jt';
                     }
                 }
             },
             plotOptions: {
                    series: {
                        fillOpacity: 0.3
                    }
                },
             tooltip: {
                 pointFormat: '{series.name} tanggal {point.x} sebesar <b>{point.y:,.0f}</b>'
             },
             series: [{
                 name: 'Nilai Proyek',
                 color: '#23b854',
                 data: [
                     <?php 
                     $return = "";
                     foreach($range as $data){
                         $return .= $nilaiProyek.",";
                     }
                     echo substr($return,0,-1);
                     ?>
                     ]
             }, {
                 name: 'Pendapatan',
                 color: '#23a8b8',
                 data: [
                     <?php 
                     $return = "";
                     $prev = 0;
                     foreach($range as $data){
                         $val = selectTotalPendapatanDate($data,$proyek);
                         if($val) $prev+=$val;
                         $return .= $prev.",";
                     }
                     echo substr($return,0,-1);
                     ?>
                     ]
             },{
                 name: 'Pengeluaran (Biaya/HPP)',
                 color: '#e42626',
                 data: [
                     <?php 
                     $return = "";
                     $prev = 0;
                     foreach($range as $data){
                         $val = selectTotalBiayaDate($data,$proyek);
                         if($val) $prev+=$val;
                         $return .= $prev.",";
                     }
                     echo substr($return,0,-1);
                     ?>
                     ]
             }]
        });
        */ ?>
        <?php } ?>
    });
</script>
<?php include "pages/footer.php"; ?>