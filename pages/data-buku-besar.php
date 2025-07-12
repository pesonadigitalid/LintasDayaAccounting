<?php
$dariTanggal = $this->validasi->validInput($_GET['daritanggal']);
$sampaiTanggal = $this->validasi->validInput($_GET['sampaitanggal']);
$proyek = $this->validasi->validInput($_GET['proyek']);
$departement = $this->validasi->validInput($_GET['departement']);
$tanpa_saldo_awal = $this->validasi->validInput($_GET['tanpa_saldo_awal']);
$no_bukti = $this->validasi->validInput($_GET['no_bukti']);
$ada_transaksi = $this->validasi->validInput($_GET['ada_transaksi']);
$company = $this->validasi->validInput($_GET['company']);

if ($_SESSION["locked"] != '') {
    $departement = $_SESSION["departement"];
}


if ($dariTanggal == "") {
    $tgl = $this->validasi->validInput($_GET['tanggal']);
    $bulan = $this->validasi->validInput($_GET['bulan']);
    $tahun = $this->validasi->validInput($_GET['tahun']);
    $lastDay = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
    $dariTanggal = "01/$bulan/$tahun";
    $dariTanggalEN = "$tahun-$bulan-01";
    $sampaiTanggal = "$lastDay/$bulan/$tahun";
    $sampaiTanggalEN = "$tahun-$bulan-$lastDay";
    $condDate = " Tanggal BETWEEN '$dariTanggalEN' AND '$sampaiTanggalEN' ";
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
    } else {
        $condDate = " Tanggal = '$dariTanggalEN'";
    }
}

$jenis_transaksi = $this->validasi->validInput($_GET['jenis_transaksi']);
if ($jenis_transaksi != "Semua Rekening") {
    $cond = " AND a.IDRekening='$jenis_transaksi'";
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

if ($no_bukti != "") {
    $condDate = " JurnalRef='$no_bukti' ";
    $tanpa_saldo_awal = "1";
}

$status = 1;
/*
if($bulan!="" && $tahun!=""){
    
} else {
    $bulan = date("m");
    $tahun = date("Y");
}
if($tgl!=""){
    if($tgl<10) $tgl="0".$tgl;
    $periode = $tahun."-".$bulan."-".$tgl;
    $condDate = "DATE_FORMAT(Tanggal,'%Y-%m-%d')='$periode'";
    $tanggal = $tahun."-".$bulan."-".$tgl;
    $tanggalID = $tgl."/".$bulan."/".$tahun;
    
}else{
    $periode = $tahun."-".$bulan;
    $condDate = "DATE_FORMAT(Tanggal,'%Y-%m')='$periode'";
    $tanggal = $tahun."-".$bulan."-01";
    $tanggalID = "01/".$bulan."/".$tahun;
}
*/

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
<?php include "pages/header.php"; ?>
<section class="section section-body bg-grey">
    <section id="overview" class="grid-hero container">
        <h5>Laporan Buku Besar<small>Pengelolaan data buku besar</small></h5>
        <?php if (isset($notif)) { ?>
            <div class="toast toast-<?php echo $notif['class']; ?>">
                <button class="btn btn-clear float-right"></button>
                <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
            </div>
        <?php } ?>
        <form method="GET" action="" class="form-horizontal form-bordered" style="padding: 0px 0;border-top:solid 1px #d3d3d3;border-bottom:solid 1px #d3d3d3;">
            <div class="columns">
                <div class="column col-1">
                    <label class="form-label" for="input-example-1">Filter :</label>
                </div>
                <div class="column col-2">
                    <input class="form-input input-calendar" id="daritanggal" name="daritanggal" type="text" value="<?php echo $dariTanggal; ?>" />
                    <!--
                        <select name="tanggal" class="form-select">
                            <option value="">-</option>
                            <?php for ($i = 1; $i <= 31; $i++) { ?>
                            <option value="<?php echo $i; ?>" <?php if ($tgl == $i) echo "selected"; ?>><?php echo $i; ?></option>
                            <?php } ?>
                        </select>
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
                        -->
                </div>
                <div class="column col-2">
                    <input class="form-input input-calendar" id="sampaitanggal" name="sampaitanggal" type="text" value="<?php echo $sampaiTanggal; ?>" />
                    <!--
                        <select name="tanggal" class="form-select">
                            <option value="">-</option>
                            <?php for ($i = 1; $i <= 31; $i++) { ?>
                            <option value="<?php echo $i; ?>" <?php if ($tgl == $i) echo "selected"; ?>><?php echo $i; ?></option>
                            <?php } ?>
                        </select>
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
                        -->
                </div>
                <div class="column col-4">
                    <select name="jenis_transaksi" id="jenis_transaksi" class="form-select" style="width: 100%;">
                        <option>Semua Rekening</option>
                        <?php
                        $query = newQuery("get_results", "SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.Tipe='D' ORDER BY KodeRekening ASC");
                        if ($query) {
                            foreach ($query as $data) {
                                if ($this->fungsi->authAccessRekening($data->Posisi, $data->KodeRekening) == 1) {
                        ?><option value="<?php echo $data->IDRekening; ?>" data-posisi="<?php echo $data->Posisi; ?>" <?php if ($rekdebet == $data->IDRekening) echo "selected"; ?>><?php echo $data->KodeRekening . " - " . ucwords(strtolower($data->NamaRekening)); ?></option><?php
                                                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                                                            ?>
                    </select>
                </div>
                <div class="column col-3">
                    <select name="proyek" id="proyek" class="form-select" style="width: 100%;">
                        <option value="">Semua Proyek</option>
                        <option value="0" <?php if ($proyek == '0') echo "selected"; ?>>Umum</option>
                        <?php
                        $query = newQuery("get_results", "SELECT * FROM tb_proyek ORDER BY Tahun, KodeProyek ASC");
                        if ($query) {
                            foreach ($query as $data) {
                        ?><option value="<?php echo $data->IDProyek; ?>" <?php if ($proyek == $data->IDProyek) echo "selected"; ?>><?php echo $data->KodeProyek . "/" . $data->Tahun . " - " . $data->NamaProyek; ?></option><?php
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                ?>
                    </select>
                </div>
            </div>
            <div class="columns">
                <div class="column col-1">
                </div>
                <div class="column col-2">
                    <select name="departement" class="form-select" style="width: 100%;" <?php echo $_SESSION["locked"]; ?>>
                        <option value="">Semua Departement</option>
                        <option value="0" <?php if ($departement == '0') echo "selected"; ?>>Umum</option>
                        <?php
                        $query = newQuery("get_results", "SELECT * FROM tb_departement ORDER BY NamaDepartement ASC");
                        if ($query) {
                            foreach ($query as $data) {
                        ?><option value="<?php echo $data->IDDepartement; ?>" <?php if ($departement == $data->IDDepartement) echo "selected"; ?>><?php echo $data->NamaDepartement; ?></option><?php
                                                                                                                                                                                                        }
                                                                                                                                                                                                    }
                                                                                                                                                                                                            ?>
                    </select>
                </div>
                <div class="column col-2">
                    <select name="company" class="form-select" style="width: 100%;" <?php echo $_SESSION["locked"]; ?>>
                        <option value="">Semua Group Departement</option>
                        <option value="LD" <?php if ($company == 'LD') echo "selected"; ?>>LD</option>
                        <option value="MMS" <?php if ($company == 'MMS') echo "selected"; ?>>MMS</option>
                    </select>
                </div>
                <div class="column col-2">
                    <label class="form-label" for="input-example-1"><input type="checkbox" name="tanpa_saldo_awal" value="1" <?php if ($tanpa_saldo_awal == "1") echo "checked"; ?>> Tanpa Saldo Awal</label>
                </div>
                <div class="column col-4">
                    <label class="form-label" for="input-example-1"><input type="checkbox" name="ada_transaksi" value="1" <?php if ($ada_transaksi == "1") echo "checked"; ?>> Tampilkan Hanya Rekening dengan Transaksi</label>
                </div>
                <!-- 
                    <div class="column col-2">
                        <input class="form-input" id="no_bukti" name="no_bukti" type="text" value="<?php echo $no_bukti; ?>" placeholder="No Bukti"/>
                    </div> -->
                <div class="column col-3"></div>
            </div>
            <div class="columns">
                <div class="column col-1">
                </div>
                <div class="column col-5">
                    <button type="submit" name="filterbutton" value="1" class="btn btn-success">Filter</button>
                    <a href="<?php echo PRSONPATH . "print-buku-besar/?daritanggal=$dariTanggal&sampaitanggal=$sampaiTanggal&jenis_transaksi=$jenis_transaksi&status=$status&proyek=$proyek&tanpa_saldo_awal=$tanpa_saldo_awal&ada_transaksi=$ada_transaksi&departement=$departement&company=$company"; ?>" class="btn btn-danger"><i class="fa fa-print"></i> Print</a>
                    <a href="<?php echo PRSONPATH . "export-buku-besar/?daritanggal=$dariTanggal&sampaitanggal=$sampaiTanggal&jenis_transaksi=$jenis_transaksi&status=$status&proyek=$proyek&tanpa_saldo_awal=$tanpa_saldo_awal&ada_transaksi=$ada_transaksi&departement=$departementi&company=$company"; ?>" class="btn btn-primary"><i class="fa fa-download"></i> Excel</a>
                    <a href="#" onclick="window.open('<?php echo PRSONPATH; ?>jurnal-umum-baru/?tanggal=<?php echo $tgl; ?>&bulan=<?php echo $bulan; ?>&tahun=<?php echo $tahun; ?>','winname','directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1200,height=600'); return false;" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Jurnal Baru</a>
                </div>
            </div>
        </form>
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
                $cond = " AND DATE_FORMAT(Tanggal,'%Y-%m-%d') < '$p' AND DATE_FORMAT(Tanggal,'%Y-%m-%d') >= '$batasan'";
            } else {
                $p = $tahun . "-" . $bulan;
                $cond = " AND DATE_FORMAT(Tanggal,'%Y-%m') < '$p' AND DATE_FORMAT(Tanggal,'%Y-%m-%d') >= '$batasan'";
            }

            $dataRekening = newQuery("get_row", "SELECT * FROM tb_master_rekening WHERE IDRekening='$idRekening'");
            $saldoAwal = newQuery("get_row", "SELECT * FROM tb_saldo_awal WHERE IDRekening='$idRekening' and Tahun='$tahun'");

            if ($saldoAwal) $saldoAwal = $saldoAwal->SaldoAwal;
            else $saldoAwal = 0;
            $kredit = 0;
            $debet = 0;

            $debet = newQuery("get_var", "SELECT SUM(Debet) FROM tb_jurnal_detail WHERE IDRekening='$idRekening' $cond $condProyek");
            //die("SELECT SUM(Debet) FROM tb_jurnal_detail WHERE IDRekening='$idRekening' $cond $condProyek");
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
                            <h5><?php echo $data->NamaRekening . $add; ?><small>Kode Perkiraan : <?php echo $data->KodeRekening; ?></small></h5>
                            <table class="table new-table">
                                <thead>
                                    <tr>
                                        <th width="40">No</th>
                                        <!-- <th width="120">No. Jurnal</th> -->
                                        <th width="80">No. Bukti</th>
                                        <th width="110">Proyek</th>
                                        <th width="80">Tanggal</th>
                                        <th style="text-align: left;">Keterangan</th>
                                        <th width="120">Debet</th>
                                        <th width="120">Kredit</th>
                                        <th width="120">Saldo</th>
                                    </tr>
                                </thead>

                                <body>
                                    <tr>
                                        <td class="spacer" colspan="7"></td>
                                    </tr>
                                    <?php
                                    $i = 1;
                                    if ($tanpa_saldo_awal == "") {
                                    ?>
                                        <tr>
                                            <td style="text-align: center;"><?php echo $i; ?></td>
                                            <!-- <td></td> -->
                                            <td></td>
                                            <td></td>
                                            <td style="text-align: center;"><?php echo $tanggalID; ?></td>
                                            <!--<td><strong>Saldo awal per <?php echo $this->fungsi->changeMonthNameID($bulan) . " " . $tahun; ?></strong></td>-->
                                            <td><strong>Saldo Awal</strong></td>
                                            <td style="text-align: right;"><?php echo number_format($debet, 2); ?></td>
                                            <td style="text-align: right;"><?php echo number_format(0, 2); ?></td>
                                            <td style="text-align: right;"><?php echo number_format($saldo, 2); ?></td>
                                        </tr>
                                        <?php
                                        $i++;
                                    } else {
                                        $debet = 0;
                                        $saldo = 0;
                                    }
                                    $kredit = 0;
                                    //echo "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='".$data->IDRekening."' ORDER BY Tanggal ASC, IDJurnal ASC";
                                    $qRest = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='" . $data->IDRekening . "' $condProyek ORDER BY Tanggal ASC, JurnalRef ASC");
                                    if ($qRest) {
                                        foreach ($qRest as $dRest) {
                                            $debet += $dRest->Debet;
                                            $kredit += $dRest->Kredit;
                                            $dJurnal = newQuery("get_row", "SELECT * FROM tb_jurnal WHERE IDJurnal='" . $dRest->IDJurnal . "'");
                                            $saldo = sumSaldo($saldo, $dRest->Debet, $dRest->Kredit, $data->Posisi);
                                            $proyek = newQuery("get_row", "SELECT * FROM tb_proyek WHERE IDProyek='" . $dJurnal->IDProyek . "'");
                                            if ($proyek) $proyek = $proyek->KodeProyek . "/" . $proyek->Tahun;
                                            else $proyek = "UMUM";

                                            $exp = explode("/", $dRest->TanggalID);
                                            $tanggalFilter = $exp[2] . "-" . $exp[1];

                                            //get total gvalue of no bukti
                                            if ($dRest->JurnalRef != "") {
                                                $totalAmount = newQuery("get_var", "SELECT SUM(Debet) FROM tb_jurnal WHERE NoBukti='" . $dRest->JurnalRef . "' AND DATE_FORMAT(Tanggal,'%Y-%m')='" . $tanggalFilter . "'");
                                                if (!$totalAmount) $totalAmount = 0;
                                            } else $totalAmount = 0;
                                        ?>
                                            <tr>
                                                <td style="text-align: center;"><?php echo $i; ?></td>
                                                <!-- <td style="text-align: center;"><?php if ($dJurnal->NoJurnal != "") echo $dJurnal->NoJurnal;
                                                                                        else echo "-"; ?></td> -->
                                                <td><a href="#" title="<?php echo number_format($totalAmount); ?>" onclick="window.open('<?php echo PRSONPATH; ?>jurnal-umum/<?php echo $dJurnal->NoJurnal; ?>','winname','directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1200,height=600'); return false;"><?php if ($dRest->JurnalRef != "") echo $dRest->JurnalRef;
                                                                                                                                                                                                                                                                                                                                                                        else echo "0000"; ?></a></td>
                                                <td style="text-align: center;"><?php echo $proyek; ?></td>
                                                <td style="text-align: center;"><?php echo $dRest->TanggalID; ?></td>
                                                <td><strong><?php echo $dRest->Keterangan; ?></strong></td>
                                                <td style="text-align: right;"><?php echo number_format($dRest->Debet, 2); ?></td>
                                                <td style="text-align: right;"><?php echo number_format($dRest->Kredit, 2); ?></td>
                                                <td style="text-align: right;"><?php echo number_format($saldo, 2); ?></td>
                                            </tr>
                                    <?php
                                            $i++;
                                        }
                                    }
                                    $closing = $debet - $kredit;
                                    ?>
                                    <tr>
                                        <td colspan="4" class="noborder"></td>
                                        <td style="text-align: right;" class="noborder"></td>
                                        <td style="text-align: right;" class="border-bottom2"><strong><?php echo number_format($debet, 2); ?></strong></td>
                                        <td style="text-align: right;" class="border-bottom2"><strong>(<?php echo number_format($kredit, 2); ?>)</strong></td>
                                        <td style="text-align: right;" class="border-bottom2"><strong>(<?php echo number_format($saldo, 2); ?>)</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="noborder"></td>
                                        <td style="text-align: right;" class="noborder"><strong>CLOSING : </strong></td>
                                        <td style="text-align: right;" class="border-bottom2"><?php echo number_format($closing, 2); ?></td>
                                        <td style="text-align: right;" class="border-bottom2">-</td>
                                        <td style="text-align: right;" class="border-bottom2">-</td>
                                    </tr>
                                </body>
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
                    $debet = getSaldoAwal($bulan, $tahun, $data->IDRekening);
                    ?>
                    <h4><?php echo $data->NamaRekening . $add; ?><small>Kode Perkiraan : <?php echo $data->KodeRekening; ?></small></h4>
                    <table class="table new-table">
                        <thead>
                            <tr>
                                <th width="40">No</th>
                                <th width="90">No. Jurnal</th>
                                <th width="80">No. Bukti</th>
                                <th width="80">Tanggal</th>
                                <th style="text-align: left;">Keterangan</th>
                                <th width="100">Debet</th>
                                <th width="100">Kredit</th>
                            </tr>
                        </thead>

                        <body>
                            <tr>
                                <td class="spacer" colspan="7"></td>
                            </tr>
                            <?php
                            $i = 1;
                            ?>
                            <tr>
                                <td style="text-align: center;"><?php echo $i; ?></td>
                                <td></td>
                                <td></td>
                                <td style="text-align: center;"><?php echo $tanggalID; ?></td>
                                <td><strong>Saldo awal per <?php echo $this->fungsi->changeMonthNameID($bulan) . " " . $tahun; ?></strong></td>
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
                            ?>
                                    <tr>
                                        <td style="text-align: center;"><?php echo $i; ?></td>
                                        <td style="text-align: center;"><?php if ($dJurnal) echo $dJurnal->NoJurnal;
                                                                        else echo "(System)"; ?></td>
                                        <td><?php echo $dRest->JurnalRef; ?></td>
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
                                <td colspan="4" class="noborder"></td>
                                <td style="text-align: right;" class="noborder"></td>
                                <td style="text-align: right;" class="border-bottom2"><strong><?php echo number_format($debet, 2); ?></strong></td>
                                <td style="text-align: right;" class="border-bottom2"><strong>(<?php echo number_format($kredit, 2); ?>)</strong></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="noborder"></td>
                                <td style="text-align: right;" class="noborder"><strong>CLOSING : </strong></td>
                                <td style="text-align: right;" class="border-bottom2"><?php echo number_format($closing, 2); ?></td>
                                <td style="text-align: right;" class="border-bottom2">-</td>
                            </tr>
                        </body>
                    </table><br /><br />
        <?php
                }
            }
        }
        ?>
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
<link href="<?php echo PRSONTEMPPATH; ?>css/select2.min.css" rel="stylesheet" />
<script src="<?php echo PRSONTEMPPATH; ?>scripts/select2.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#jenis_transaksi").select2();
        <?php if ($jenis_transaksi != "Semua Rekening") { ?>
            $("#jenis_transaksi").val(<?php echo $jenis_transaksi; ?>).trigger('change');
        <?php } ?>
    });
</script>
<?php include "pages/footer.php"; ?>