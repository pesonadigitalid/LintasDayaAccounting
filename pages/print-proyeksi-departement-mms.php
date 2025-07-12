<?php
$tahun = $this->validasi->validInput($_GET['tahun']);
$departement = $this->validasi->validInput($_GET['departement']);
$tipe = $this->validasi->validInput($_GET['tipe']);
$tanggal = $this->validasi->validInput($_GET['tanggal']);
if($tanggal=="") $tanggal = date("Y-m-d");

if($tanggal!=""){
    $condTanggal = " AND a.Tanggal<='$tanggal' ";
    $condTanggal2 = " AND b.Tanggal<='$tanggal' ";
    $condTanggal3 = " AND Tanggal<='$tanggal' ";
}

$exp = explode("-",$tanggal);
$tanggalID = $exp[2]."/".$exp[1]."/".$exp[0];


$dep = newQuery("get_row","SELECT * FROM tb_departement WHERE IDDepartement='$departement'");
$bulanList = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember");

$periode = "Departement: ".$dep->NamaDepartement.". Tahun: ".$tahun .". Periode Laporan: ".$tanggalID ;

$db =  new ezSQL_mysql(YGDBUSER,YGDBPASS,YGDBNAME,YGDBHOST);

$totalProyek = newQuery("get_var","SELECT COUNT(*) FROM tb_penjualan WHERE DATE_FORMAT(Tanggal,'%Y')='$tahun' ORDER BY IDPenjualan ASC");
if(!$totalProyek) $totalProyek=0;
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content=""/>
    <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543"/>
    <title>Lintas Daya Accounting</title>
    <link rel="icon" type="image/png" href="<?php echo PRSONTEMPPATH; ?>dist/img/favicon.png"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"/>
    <link rel="stylesheet" href="<?php echo PRSONTEMPPATH; ?>css/print-style-acc.css" media="all"/>
    <style type="text/css" media="all">
        body{
            font-size: 12px !important;
        }
    </style>
</head>

<body class="center">
    <?php
    if($totalProyek==0) echo "Tidak ada proyek untuk saat ini";
    else {
        $limit = 10;
        $TotalAll = array();
        for($start=0;$start<=$totalProyek;$start+=$limit){
        ?>
        <div class="newPage">
        <?php if($tipe=="1"){ ?>
        <h1 class="blue">Proyeksi SPB MMS</h1>
        <h3 class="red"><?php echo $periode; ?></h3>
        <table class="tbLabaRugi" style="width: auto">

            <tr>
                <td style="font-weight: bold;" class="red">SPB</td>
                <?php
                $ProyekArray = array();
                $ProyekNamaArray = array();
                $KontrakArray = array();

                $PendapatanArray = array();
                $DPPArray = array();
                $PPH2Array = array();
                $PPH10Array = array();
                $TotalPajakArray = array();

                $query = newQuery("get_results","SELECT * FROM tb_penjualan WHERE DATE_FORMAT(Tanggal,'%Y')='$tahun' ORDER BY IDPenjualan ASC LIMIT $start,$limit");
                if($query){
                    foreach($query as $data){
                        $pelanggan = newQuery("get_var","SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='".$data->IDPelanggan."'")
                        ?><td width="90" style="text-align: center;border-bottom: solid 1px #333;padding-bottom: 5px;"><?php echo $pelanggan; ?></td><?php
                        array_push($ProyekArray,$data->IDPenjualan);
                        array_push($ProyekNamaArray,$data->NoPenjualan);
                        array_push($KontrakArray,$data->GrandTotal);

                        $id = $data->IDPenjualan;

                        $pendapatan2 = 0;

                        $ppn10 = 0;
                        $pph2 = 0;
                        $dpp = 0;
                        $totalPajak = 0;

                        $q = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_penjualan_invoice WHERE IDPenjualan='$id' $condTanggal3 ORDER BY IDPenjualan ASC");
                        if($q){

                            foreach($q as $d){
                                if(($d->Sisa>0 && $d->Sisa<1) || $d->Sisa<0) $sisa = 0; else  $sisa = $d->Sisa;

                                //Pajak
                                $temp_dpp = 0;
                                $temp_ppn = 0;
                                $temp_pph = 0;
                                if($d->PPNPersen>0){
                                    $temp_dpp = $d->Jumlah;
                                    $temp_ppn = $d->PPN;
                                    // $temp_pph = round($temp_dpp*0.02,2);
                                    $temp_pph = 0;
                                }

                                $pendapatan2 += ($d->GrandTotal-$d->Sisa);

                                $dpp += $temp_dpp;
                                $pph2 += $temp_pph;
                                $ppn10 += $temp_ppn;

                            }
                        }

                        array_push($PendapatanArray, $pendapatan2);
                        array_push($DPPArray, $dpp);
                        array_push($PPH2Array, $pph2);
                        array_push($PPH10Array, $ppn10);
                        array_push($TotalPajakArray, ($pph2+$ppn10));

                        $TotalAll['Kontrak'] += $data->GrandTotal;
                        $TotalAll['Pendapatan'] += $pendapatan2;
                        $TotalAll['DPP'] += $dpp;
                        $TotalAll['PPH2'] += $pph2;
                        $TotalAll['PPN10'] += $ppn10;
                        $TotalAll['TotalPajak'] += ($pph2+$ppn10);
                    }
                }
                ?>
                <td width="90" style="text-align: center;border-bottom: solid 1px #333;padding-bottom: 5px;"></td>
            </tr>
            <tr>
                <td style="font-weight: bold;"></td>
                <?php
                for($i=0;$i<count($ProyekNamaArray);$i++){
                    ?><td style="text-align: center;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;" class="red"><?php echo $ProyekNamaArray[$i]; ?></td><?php
                }
                ?><td style="text-align: center;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;" class="red">Total</td>
            </tr>
            <tr>
                <td style="font-weight: bold;" class="red">Penerimaan</td>
                <?php
                for($i=0;$i<count($ProyekArray);$i++){
                    ?><td style="text-align: right;">Rupiah</td><?php
                }
                ?>
                <td style="text-align: right;">Rupiah</td>
            </tr>
            <tr>
                <td class="labelHeader">Kontrak</td>
                <?php
                for($i=0;$i<count($KontrakArray);$i++){
                    ?><td style="text-align: center;text-align:right;"><?php echo number_format($KontrakArray[$i],2); ?></td><?php
                }
                ?>
                <td style="text-align: center;text-align:right;"><?php echo number_format($TotalAll['Kontrak'],2); ?></td>
            </tr>
            <tr>
                <td class="labelHeader">Total Pendapatan</td>
                <?php
                foreach($PendapatanArray as $value){
                    ?><td style="text-align: center;text-align:right;border-bottom: solid 1px #333;padding-bottom: 5px;"><?php echo number_format($value,2); ?></td><?php
                }
                ?>
                <td style="text-align: center;text-align:right;border-bottom: solid 1px #333;padding-bottom: 5px;"><?php echo number_format($TotalAll['Pendapatan'],2); ?></td>
            </tr>
            <tr>
                <td class="labelHeader">DPP</td>
                <?php
                foreach($DPPArray as $value){
                    ?><td style="text-align: right;"><?php echo number_format($value,2); ?></td><?php
                }
                ?>
                <td style="text-align: center;text-align:right;"><?php echo number_format($TotalAll['DPP'],2); ?></td>
            </tr>
            <tr>
                <td class="labelHeader">PPN 10% & 11%</td>
                <?php
                foreach($PPH10Array as $value){
                    ?><td style="text-align: right;"><?php echo number_format($value,2); ?></td><?php
                }
                ?>
                <td style="text-align: center;text-align:right;"><?php echo number_format($TotalAll['PPN10'],2); ?></td>
            </tr>
            <tr>
                <td class="labelHeader">PPH 2%</td>
                <?php
                foreach($PPH2Array as $value){
                    ?><td style="text-align: center;text-align:right;border-bottom: solid 1px #333;padding-bottom: 5px;"><?php echo number_format($value,2); ?></td><?php
                }
                ?>
                <td style="text-align: center;text-align:right;border-bottom: solid 1px #333;padding-bottom: 5px;"><?php echo number_format($TotalAll['PPH2'],2); ?></td>
            </tr>
            <tr>
                <td class="labelHeader">Total Pajak</td>
                <?php
                foreach($TotalPajakArray as $value){
                    ?><td style="text-align: right;font-weight: bold;"><?php echo number_format($value,2); ?></td><?php
                }
                ?>
                <td style="text-align: right;font-weight: bold;"><?php echo number_format($TotalAll['TotalPajak'],2); ?></td>
            </tr>
            <tr>
                <td class="labelHeader"></td>
                <td style="text-align: right;"></td>
            </tr>
            <tr>
                <td style="font-weight: bold;" class="red">Pengeluaran</td>
                <?php
                for($i=0;$i<count($ProyekArray);$i++){
                    ?><td style="text-align: right;"></td><?php
                }
                ?>
                <td></td>
            </tr>
            <tr>
                <td class="labelHeader">Biaya Material</td>
                <?php
                $BiayaMaterialArray = array();
                foreach($ProyekArray as $id){

                    $query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_penjualan_surat_jalan WHERE IDPenjualan='$id' $condTanggal3 ORDER BY IDSuratJalan DESC");
                    $material1 = 0;
                    if($query){
                        foreach($query as $data){
                            $TotalHPPDetail = 0;
                            $q = newQuery("get_results", "SELECT *, SUM(Qty) AS QTY_REAL, AVG(HPP) AS HPP_AVG, AVG(HPPReal) AS HPP_REAL_AVG FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='" . $data->NoSuratJalan . "' GROUP BY IDBarang, SN ORDER BY NoUrut ASC");
                            if($q){
                                foreach($q as $d){
                                    if($d->IsInstallasi){
                                        $HPPAudit = newQuery("get_var", "SELECT SUM(SubTotal) FROM tb_audit_detail WHERE SPGudang<0 AND NoAudit IN (SELECT NoAudit FROM tb_audit WHERE IDPenjualanMMS='" . $data->IDPenjualan . "')");
                                        if(!$HPPAudit) $HPPAudit=0;
                                        $HPPAudit = abs($HPPAudit);
                                        $TotalHPPDetail+=$HPPAudit;
                                    } else {
                                        $TotalHPPDetail+=$d->SubTotalHPP;
                                    }
                                }
                            }
                            $material1 += $TotalHPPDetail;
                        }
                    }
                    ?><td style="text-align: right;"><?php echo number_format($material1,2); ?></td><?php
                    array_push($BiayaMaterialArray, $material1);
                    $TotalAll['BiayaMaterial'] += $material1;
                }
                ?>
                <td style="text-align: center;text-align:right;"><?php echo number_format($TotalAll['BiayaMaterial'],2); ?></td>
            </tr>
            <tr>
                <td class="labelHeader">Biaya Tenaga/Overhead</td>
                <?php
                $TenagaArray = array();
                foreach($ProyekArray as $id){
                    $tenaga1 = 0;

                    $query = $db->get_results("SELECT a.*, d.NamaPerusahaan, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_po_detail b, tb_barang c, tb_supplier d WHERE a.NoPo=b.NoPO AND b.IDBarang=c.IDBarang AND a.IDSupplier=d.IDSupplier AND a.IsLD='0' AND c.IsBarang='2' AND a.IDPenjualan='$id' $condTanggal GROUP BY NoPo ORDER BY d.NamaPerusahaan");
                    if($query){
                        foreach($query as $data){
                            if(($data->Sisa>0 && $data->Sisa<1) || $data->Sisa<0) $sisa = 0; else  $sisa = $data->Sisa;
                            $tenaga1 += $data->GrandTotal;
                        }
                    }

                    ?><td style="text-align: right;"><?php echo number_format($tenaga1,2); ?></td><?php
                    array_push($TenagaArray, $tenaga1);
                    $TotalAll['Tenaga'] += $tenaga1;
                }
                ?>
                <td style="text-align: center;text-align:right;"><?php echo number_format($TotalAll['Tenaga'],2); ?></td>
            </tr>
            <tr>
                <td class="labelHeader">Total Pengeluaran</td>
                <?php
                $TotalPengeluaranArray = array();
                foreach($ProyekArray as $key=>$id){
                    $totalPengeluaran = $BiayaMaterialArray[$key]+$TenagaArray[$key];


                    ?><td style="text-align: center;text-align:right;border-bottom: solid 1px #333;padding-bottom: 5px;font-weight: bold;"><?php echo number_format($totalPengeluaran,2); ?></td><?php
                    array_push($TotalPengeluaranArray, $totalPengeluaran);
                }
                ?>
                <td style="text-align: center;text-align:right;border-bottom: solid 1px #333;padding-bottom: 5px;font-weight: bold;"><?php echo number_format($TotalAll['BiayaMaterial']+$TotalAll['Tenaga'],2); ?></td>
            </tr>
            <tr>
                <td class="labelHeader">Laba/Rugi</td>
                <?php
                $LabaRugiArray = array();
                foreach($ProyekArray as $key=>$id){
                    $labaRugi = ($PendapatanArray[$key]-$TotalPajakArray[$key])-$TotalPengeluaranArray[$key];

                    ?><td style="text-align: center;text-align:right;border-bottom: solid 1px #333;padding-bottom: 5px;font-weight: bold;"><?php echo number_format($labaRugi,2); ?></td><?php
                    array_push($LabaRugiArray, $labaRugi);
                }
                ?>
                <td style="text-align: center;text-align:right;border-bottom: solid 1px #333;padding-bottom: 5px;font-weight: bold;"><?php echo number_format($TotalAll['Pendapatan'] - $TotalAll['TotalPajak'] - ($TotalAll['BiayaMaterial']+$TotalAll['Tenaga']),2); ?></td>
            </tr>
        </table>
        <?php } ?>

        <?php if($tipe=="2"){ ?>
        <?php
        // REPEAT DETAIL PROYEKSI
        $ProyekArray = array();
        $query = newQuery("get_results","SELECT * FROM tb_penjualan WHERE DATE_FORMAT(Tanggal,'%Y')='$tahun' ORDER BY IDPenjualan ASC LIMIT $start,$limit");
        if($query){
            foreach($query as $data){
                array_push($ProyekArray,$data->IDPenjualan);
            }
        }
        foreach($ProyekArray as $key=>$id){
            $dataProyek = newQuery("get_row","SELECT * FROM tb_penjualan WHERE IDPenjualan='".$id."'");
            $pelanggan = newQuery("get_var","SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='".$dataProyek->IDPelanggan."'");
            $cond = " ";
            $cond2 = " ";
            $cond3 = " ";
            ?>
            <div class="newPage">
                <h1 class="blue" style="line-height: 1.6em">Proyeksi Penjualan <?php echo $dataProyek->NoPenjualan; ?></h1>
                <h3 class="red">Pelanggan: <?php echo $pelanggan; ?>, Tahun: <?php echo $tahun; ?></h3>
                <table class="tbLabaRugi" style="width: 800px">

                    <tr>
                        <td style="font-weight: bold;" class="red">Proyeksi</td>
                        <td width="90" style="text-align: center;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;" class="red">Grand Total</td>
                        <td width="90" style="text-align: center;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;" class="red">Terbayar</td>
                        <td width="90" style="text-align: center;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;" class="red">Sisa</td>
                    </tr>
                    <tr>
                        <td class="labelHeader">Penerimaan</td>
                        <td colspan="3">
                    </tr>
                    <?php
                    $Pendapatan1 = 0;
                    $Pendapatan2 = 0;
                    $Pendapatan3 = 0;
                    $DPP = 0;
                    $PPH2 = 0;
                    $PPH10 = 0;
                    $TotalPajak = 0;
                    $query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_penjualan_invoice WHERE IDPenjualan='$id' $cond3 $condTanggal3 ORDER BY IDPenjualan ASC");
                    if($query){
                        foreach($query as $data){
                            if(($data->Sisa>0 && $data->Sisa<1) || $data->Sisa<0) $sisa = 0; else  $sisa = $data->Sisa;

                            //Pajak
                            $temp_dpp = 0;
                            $temp_ppn = 0;
                            $temp_pph = 0;
                            if($data->PPNPersen>0){
                                $temp_dpp = $data->Jumlah;
                                $temp_ppn = $data->PPN;
                                // $temp_pph = round($temp_dpp*0.02,2);
                                $temp_pph = 0;
                            }
                            $Pendapatan1 += $data->GrandTotal;
                            $Pendapatan2 += ($data->GrandTotal-$data->Sisa);
                            $Pendapatan3 += $sisa;

                            $DPP += $temp_dpp;
                            $PPH2 += $temp_pph;
                            $PPH10 += $temp_ppn;
                            $TotalPajak += ($temp_pph+$temp_ppn);
                            ?>
                            <tr>
                                <td class="labelHeader2 deep1" style="font-weight: normal;"><?php echo $data->NoInvoice." / ".$data->TanggalID." / ".$data->Keterangan; ?></td>
                                <td style="text-align: right;"><?php echo number_format($Pendapatan1,2); ?></td>
                                <td style="text-align: right;"><?php echo number_format($Pendapatan2,2); ?></td>
                                <td style="text-align: right;"><?php echo number_format($Pendapatan3,2); ?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    <tr>
                        <td class="labelHeader">Total Pendapatan</td>
                        <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Pendapatan1,2); ?></td>
                        <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Pendapatan2,2); ?></td>
                        <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Pendapatan3,2); ?></td>
                    </tr>
                    <tr>
                        <td class="labelHeader">DPP</td>
                        <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($DPP,2); ?></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                    </tr>
                    <tr>
                        <td class="labelHeader">PPN 10% & 11%</td>
                        <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($PPH10,2); ?></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                    </tr>
                    <tr>
                        <td class="labelHeader">PPH 2%</td>
                        <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($PPH2,2); ?></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                    </tr>
                    <tr>
                        <td class="labelHeader">Total Pajak</td>
                        <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($TotalPajak,2); ?></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                    </tr>
                    <tr>
                        <td class="labelHeader"></td>
                        <td style="text-align: right;"></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;" class="red">Pengeluaran</td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td class="labelHeader">Biaya Material</td>
                        <td colspan="3"></td>
                    </tr>
                    <?php
                    $Material1 = 0;
                    $Material2 = 0;
                    $Material3 = 0;

                    $query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_penjualan_surat_jalan WHERE IDPenjualan='$id' $condTanggal3 ORDER BY IDSuratJalan DESC");
                    if($query){
                        foreach($query as $data){
                            $TotalHPPDetail = 0;

                            $q = newQuery("get_results", "SELECT *, SUM(Qty) AS QTY_REAL, AVG(HPP) AS HPP_AVG, AVG(HPPReal) AS HPP_REAL_AVG FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='" . $data->NoSuratJalan . "' GROUP BY IDBarang, SN ORDER BY NoUrut ASC");
                            if ($q) {
                                foreach ($q as $d) {
                                    if ($d->IsInstallasi) {
                                        $HPPAudit = newQuery("get_var", "SELECT SUM(GrandTotal) FROM tb_audit WHERE IDPenjualanMMS='" . $data->IDPenjualan . "'");
                                        if (!$HPPAudit) $HPPAudit = 0;
                                        $HPPAudit = abs($HPPAudit);
                                        $TotalHPPDetail += $HPPAudit;
                                    } else {
                                        $TotalHPPDetail += $d->SubTotalHPP;
                                    }
                                }
                            }
                            ?>
                            <tr>
                                <td class="labelHeader2 deep1" style="font-weight: normal;"><?php echo $data->NoSuratJalan." / ".$data->TanggalID; ?></td>
                                <td style="text-align: right;"><?php echo number_format($TotalHPPDetail,2); ?></td>
                                <td style="text-align: right;"><?php echo number_format($TotalHPPDetail,2); ?></td>
                                <td style="text-align: right;"><?php echo number_format(0,2); ?></td>
                            </tr>
                            <?php

                            $Material1 += $TotalHPPDetail;
                            $Material2 += $TotalHPPDetail;
                            $Material3 += 0;
                        }
                    }
                    ?>
                    <tr>
                        <td class="labelHeader">Total B. Material</td>
                        <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Material1,2); ?></td>
                        <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Material2,2); ?></td>
                        <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Material3,2); ?></td>
                    </tr>
                    <tr>
                        <td class="labelHeader">Biaya Tenaga/Overhead</td>
                        <td colspan="3"></td>
                    </tr>
                    <?php
                    $Tenaga1 = 0;
                    $Tenaga2 = 0;
                    $Tenaga3 = 0;

                    $query = $db->get_results("SELECT a.*, d.NamaPerusahaan, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_po_detail b, tb_barang c, tb_supplier d WHERE a.NoPo=b.NoPO AND b.IDBarang=c.IDBarang AND a.IDSupplier=d.IDSupplier AND a.IsLD='0' AND c.IsBarang='2' AND a.IDPenjualan='$id'  $condTanggal GROUP BY NoPo ORDER BY d.NamaPerusahaan");
                    if($query){
                        foreach($query as $data){
                            $supplier = $db->get_row("SELECT * FROM tb_supplier WHERE IDSupplier='".$data->IDSupplier."'");
                            if($supplier) $supplier = $supplier->NamaPerusahaan; else $supplier="-";
                            if(($data->Sisa>0 && $data->Sisa<1) || $data->Sisa<0) $sisa = 0; else  $sisa = $data->Sisa;

                            ?>
                            <tr>
                                <td class="labelHeader2 deep1" style="font-weight: normal;"><?php echo $data->NoPo." / ".$data->TanggalID." / ".$supplier; ?></td>
                                <td style="text-align: right;"><?php echo number_format($data->GrandTotal,2); ?></td>
                                <td style="text-align: right;"><?php echo number_format($data->TotalPembayaran,2); ?></td>
                                <td style="text-align: right;"><?php echo number_format($sisa,2); ?></td>
                            </tr>
                            <?php

                            $Tenaga1 += $data->GrandTotal;
                            $Tenaga2 += $data->TotalPembayaran;
                            $Tenaga3 += $sisa;
                        }
                    }
                    ?>
                    <tr>
                        <td class="labelHeader">Total B. Tenaga/Overhead</td>
                        <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Tenaga1,2); ?></td>
                        <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Tenaga2,2); ?></td>
                        <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Tenaga3,2); ?></td>
                    </tr>
                    <tr>
                        <td class="labelHeader">Total Pengeluaran</td>
                        <?php
                        $Pengeluaran1 = $Material1+$Tenaga1;
                        $Pengeluaran2 = $Material2+$Tenaga2;
                        $Pengeluaran3 = $Material3+$Tenaga3;
                        ?>
                        <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Pengeluaran1,2); ?></td>
                        <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Pengeluaran2,2); ?></td>
                        <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Pengeluaran3,2); ?></td>
                    </tr>
                    <tr>
                        <td class="labelHeader">Laba/Rugi</td>
                        <?php
                        $Profit = ($Pendapatan2-$TotalPajak)-$Pengeluaran1;
                        ?>
                        <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Profit,2); ?></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                    </tr>
                </table>
            </div>
            <?php
        }
        ?>
        <?php } ?>
        </div>
        <?php
        }
    } ?>
    <script type="text/javascript">
        setTimeout(function(){
            window.print();
        },1500);
    </script>
</body>
</html>