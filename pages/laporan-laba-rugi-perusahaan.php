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

if(!$_GET['filterbutton']){
    $print_header=1;
    $print_detail=1;
}
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

// }else{
//     $periode = $tahun."-".$bulan;
//     $condDate = "DATE_FORMAT(Tanggal,'%Y-%m')='$periode'";
//     $tanggal = $tahun."-".$bulan."-01";
//     $tanggalID = "01/".$bulan."/".$tahun;
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
?>
<?php include "pages/header.php"; ?>
<section class="section section-body bg-grey">
    <section id="overview" class="grid-hero container">
        <div class="columns">
            <div class="column col-7">
                <h5>Laporan Laba Rugi Perusahaan <?php echo $sub; ?><small>Laporan Laba Rugi Perusahaan <?php echo $sub; ?></small></h5>
                <?php if (isset($notif)) { ?>
                    <div class="toast toast-<?php echo $notif['class']; ?>">
                        <button class="btn btn-clear float-right"></button>
                        <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
                    </div>
                <?php } ?>
                <form method="GET" action="" class="form-horizontal form-bordered" style="padding: 0px 0;border-top:solid 1px #d3d3d3;border-bottom:solid 1px #d3d3d3;">
                    <div class="columns">
                        <div class="column col-2">
                            <label class="form-label" for="input-example-1">Periode :</label>
                        </div>
                        <!-- <div class="column col-5">
                        <select name="bulan" class="form-select">
                            <option value="01" <?php if ($bulan == "01") echo "selected"; ?>>Januari</option>
                            <option value="02" <?php if ($bulan == "02") echo "selected"; ?>>Februari</option>
                            <option value="03" <?php if ($bulan == "03") echo "selected"; ?>>Maret</option>
                            <option value="04" <?php if ($bulan == "04") echo "selected"; ?>>April</option>
                            <option value="05" <?php if ($bulan == "05") echo "selected"; ?>>Mei</option>
                            <option value="06" <?php if ($bulan == "06") echo "selected"; ?>>Juni</option>
                            <option value="07" <?php if ($bulan == "07") echo "selected"; ?>>Juli</option>
                            <option value="08" <?php if ($bulan == "08") echo "selected"; ?>>Agustus</option>
                            <option value="09" <?php if ($bulan == "09") echo "selected"; ?>>September</option>
                            <option value="10" <?php if ($bulan == "10") echo "selected"; ?>>Oktober</option>
                            <option value="11" <?php if ($bulan == "11") echo "selected"; ?>>November</option>
                            <option value="12" <?php if ($bulan == "12") echo "selected"; ?>>Desember</option>
                        </select>
                        <select name="tahun" class="form-select">
                            <?php for ($i = 2012; $i <= date("Y"); $i++) { ?>
                            <option value="<?php echo $i; ?>" <?php if ($tahun == $i) echo "selected"; ?>><?php echo $i; ?></option>
                            <?php } ?>
                        </select>
                    </div> -->
                        <div class="column col-3">
                            <input class="form-input input-calendar" id="daritanggal" name="daritanggal" type="text" value="<?php echo $daritanggal; ?>" />
                        </div>
                        <div class="column col-3">
                            <input class="form-input input-calendar" id="sampaitanggal" name="sampaitanggal" type="text" value="<?php echo $sampaitanggal; ?>" />
                        </div>
                    </div>

                    <div class="columns">
                        <div class="column col-2"></div>
                        <div class="column col-3"><input type="checkbox" name="print_header" value="1" <?php if ($print_header == '1') echo "checked"; ?>> Print Header</div>
                        <div class="column col-3"><input type="checkbox" name="print_detail" value="1" <?php if ($print_detail == '1') echo "checked"; ?>> Print Detail</div>
                    </div>

                    <div class="columns">
                        <div class="column col-2"></div>
                        <div class="column col-3">
                            <input type="hidden" id="type" name="type" value="<?php echo $type; ?>" />
                            <button type="submit" name="filterbutton" value="1" class="btn btn-success">Filter</button>
                            <!-- <a href="<?php echo PRSONPATH . "print-laba-rugi-perusahaan/?bulan=$bulan&tahun=$tahun"; ?>" class="btn btn-danger" target="_blank"><i class="fa fa-print"></i> Print</a> -->
                            <a href="<?php echo PRSONPATH . "print-laba-rugi-perusahaan/?daritanggal=$daritanggal&sampaitanggal=$sampaitanggal&type=$type&print_header=$print_header&print_detail=$print_detail"; ?>" class="btn btn-danger" target="_blank"><i class="fa fa-print"></i> Print</a>
                        </div>
                    </div>
                </form>
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
                                            <td class="deep1"><?php echo "<strong>" . $dataDetail->TanggalID . "</strong> &nbsp;&nbsp;&nbsp;&nbsp;" . $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                            <td class="saldo"><?php echo number_format($dataDetail->Kredit, 2); ?></td>
                                        </tr>
                                    <?php
                                    }
                                    if ($total > 0) {
                                    ?>
                                        <tr>
                                            <td class="deep1" style="text-align: right;"><strong>TOTAL:</strong></td>
                                            <td class="saldo"><?php echo number_format($total, 2); ?></td>
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
                                                        <td class="deep1"><?php echo "<strong>" . $dataDetail->TanggalID . "</strong> &nbsp;&nbsp;&nbsp;&nbsp;" . $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                                        <td class="saldo"><?php echo number_format($dataDetail->Kredit, 2); ?></td>
                                                    </tr>
                                                <?php
                                                }
                                                if ($total > 0) {
                                                ?>
                                                    <tr>
                                                        <td class="deep1" style="text-align: right;"><strong>TOTAL:</strong></td>
                                                        <td class="saldo"><?php echo number_format($total, 2); ?></td>
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
                                $queryDetail = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $data->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                if ($queryDetail) {
                                    $total = 0;
                                    foreach ($queryDetail as $dataDetail) {
                                        $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                        $hpp += $dataDetail->Debet;
                                        $total += $dataDetail->Debet;
                                ?>
                                        <tr>
                                            <td class="deep1"><?php echo "<strong>" . $dataDetail->TanggalID . "</strong> &nbsp;&nbsp;&nbsp;&nbsp;" . $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                            <td class="saldo"><?php echo number_format($dataDetail->Debet, 2); ?></td>
                                        </tr>
                                    <?php
                                    }
                                    if ($total > 0) {
                                    ?>
                                        <tr>
                                            <td class="deep1" style="text-align: right;"><strong>TOTAL:</strong></td>
                                            <td class="saldo"><?php echo number_format($total, 2); ?></td>
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
                                            $queryDetail = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $dataSub->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                            if ($queryDetail) {
                                                $total = 0;
                                                foreach ($queryDetail as $dataDetail) {
                                                    $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                                    $hpp += $dataDetail->Debet;
                                                    $total += $dataDetail->Debet;
                                            ?>
                                                    <tr>
                                                        <td class="deep1"><?php echo "<strong>" . $dataDetail->TanggalID . "</strong> &nbsp;&nbsp;&nbsp;&nbsp;" . $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                                        <td class="saldo"><?php echo number_format($dataDetail->Debet, 2); ?></td>
                                                    </tr>
                                                <?php
                                                }
                                                if ($total > 0) {
                                                ?>
                                                    <tr>
                                                        <td class="deep1" style="text-align: right;"><strong>TOTAL:</strong></td>
                                                        <td class="saldo"><?php echo number_format($total, 2); ?></td>
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
                                $queryDetail = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $data->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                if ($queryDetail) {
                                    $total = 0;
                                    foreach ($queryDetail as $dataDetail) {
                                        $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                        $biaya += $dataDetail->Debet;
                                        $total += $dataDetail->Debet;
                                ?>
                                        <tr>
                                            <td class="deep1"><?php echo "<strong>" . $dataDetail->TanggalID . "</strong> &nbsp;&nbsp;&nbsp;&nbsp;" . $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                            <td class="saldo"><?php echo number_format($dataDetail->Debet, 2); ?></td>
                                        </tr>
                                    <?php
                                    }
                                    if ($total > 0) {
                                    ?>
                                        <tr>
                                            <td class="deep1" style="text-align: right;"><strong>TOTAL:</strong></td>
                                            <td class="saldo"><?php echo number_format($total, 2); ?></td>
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
                                            $queryDetail = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $dataSub->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                            if ($queryDetail) {
                                                $total = 0;
                                                foreach ($queryDetail as $dataDetail) {
                                                    $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                                    $biaya += $dataDetail->Debet;
                                                    $total += $dataDetail->Debet;
                                            ?>
                                                    <tr>
                                                        <td class="deep1"><?php echo "<strong>" . $dataDetail->TanggalID . "</strong> &nbsp;&nbsp;&nbsp;&nbsp;" . $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                                        <td class="saldo"><?php echo number_format($dataDetail->Debet, 2); ?></td>
                                                    </tr>
                                                <?php
                                                }
                                                if ($total > 0) {
                                                ?>
                                                    <tr>
                                                        <td class="deep1" style="text-align: right;"><strong>TOTAL:</strong></td>
                                                        <td class="saldo"><?php echo number_format($total, 2); ?></td>
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
                                $queryDetail = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $data->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                if ($queryDetail) {
                                    $total = 0;
                                    foreach ($queryDetail as $dataDetail) {
                                        $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                        $biayaLain += $dataDetail->Debet;
                                        $total += $dataDetail->Debet;
                                ?>
                                        <tr>
                                            <td class="deep1"><?php echo "<strong>" . $dataDetail->TanggalID . "</strong> &nbsp;&nbsp;&nbsp;&nbsp;" . $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                            <td class="saldo"><?php echo number_format($dataDetail->Debet, 2); ?></td>
                                        </tr>
                                    <?php
                                    }
                                    if ($total > 0) {
                                    ?>
                                        <tr>
                                            <td class="deep1" style="text-align: right;"><strong>TOTAL:</strong></td>
                                            <td class="saldo"><?php echo number_format($total, 2); ?></td>
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
                                            $queryDetail = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $dataSub->IDRekening . "' ORDER BY Tanggal ASC, JurnalRef ASC");
                                            if ($queryDetail) {
                                                $total = 0;
                                                foreach ($queryDetail as $dataDetail) {
                                                    $jurnalTandingan = newQuery("get_row", "SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='" . $dataDetail->IDJurnal . "' AND a.IDJurnalDetail!='" . $dataDetail->IDJurnalDetail . "' AND a.IDRekening=b.IDRekening");
                                                    $biayaLain += $dataDetail->Debet;
                                                    $total += $dataDetail->Debet;
                                            ?>
                                                    <tr>
                                                        <td class="deep1"><?php echo "<strong>" . $dataDetail->TanggalID . "</strong> &nbsp;&nbsp;&nbsp;&nbsp;" . $dataDetail->Keterangan . "<br/> " . $jurnalTandingan->NamaRekening; ?></td>
                                                        <td class="saldo"><?php echo number_format($dataDetail->Debet, 2); ?></td>
                                                    </tr>
                                                <?php
                                                }
                                                if ($total > 0) {
                                                ?>
                                                    <tr>
                                                        <td class="deep1" style="text-align: right;"><strong>TOTAL:</strong></td>
                                                        <td class="saldo"><?php echo number_format($total, 2); ?></td>
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
            </div>
            <div class="column col-5" style="padding-top: 160px;">
                <div id="chartContainer" style="width:100%; height:400px;"></div>
            </div>
        </div>
    </section>
</section>
<script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/moment.min.js"></script>
<script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/pikaday.js"></script>
<script type="text/javascript">
    var pickerDefault = new Pikaday({
        field: document.getElementById('daritanggal'),
        format: 'DD/MM/YYYY',
    });
    var pickerDefault = new Pikaday({
        field: document.getElementById('sampaitanggal'),
        format: 'DD/MM/YYYY',
    });
</script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
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
            }]
        });
    });
</script>
<?php include "pages/footer.php"; ?>