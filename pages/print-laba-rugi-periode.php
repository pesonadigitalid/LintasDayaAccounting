<?php
$bulan = $this->validasi->validInput($_GET['bulan']);
$bulan2 = $this->validasi->validInput($_GET['bulan2']);
$tahun = $this->validasi->validInput($_GET['tahun']);
$departement = $this->validasi->validInput($_GET['departement']);

if($departement!=""){
    $dep = newQuery("get_row","SELECT * FROM tb_departement WHERE IDDepartement='$departement'");
    $sub = "Departement ".$dep->NamaDepartement;
}

if(intval($bulan2)>intval(date("m"))){
    $bulan2 = date("m");
}

$bulanList = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember");

$periode = $bulanList[$bulan]." - ".$bulanList[$bulan2]." ".$tahun;

$db =  new ezSQL_mysql(YGDBUSER,YGDBPASS,YGDBNAME,YGDBHOST);

function CheckSaldo($IDRekening,$condDate, $db,$tipe, $departement){
    $total = 0;
    $queryDetail = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND a.`IDRekening`='".$IDRekening."' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
    if($queryDetail){
        foreach($queryDetail as $dataDetail){
            if($tipe=="KREDIT")
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
    <meta name="description" content=""/>
    <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543"/>
    <title>Lintas Daya Accounting</title>
    <link rel="icon" type="image/png" href="<?php echo PRSONTEMPPATH; ?>dist/img/favicon.png"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"/>
    <link rel="stylesheet" href="<?php echo PRSONTEMPPATH; ?>css/print-style-acc.css" media="all"/>
    <style type="text/css" media="all">
        body{
            font-size: 10px !important;
        }
    </style>
</head>

<body class="center">
    <h1 class="blue">Laba Rugi Periode <?php echo $sub; ?></h1>
    <h3 class="red">Periode: <?php echo $periode; ?></h3>
    <table class="tbLabaRugi" style="width: auto">

        <tr>
            <td width="300" style="font-weight: bold;" class="red">Periode</td>
            <?php 
            for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                if($rep<10) $bln = "0".$rep; else $bln = $rep;
            ?>
            <td width="90" style="text-align: center;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;" class="red"><?php echo $bulanList[$bln]; ?></td>
            <?php } ?>
        </tr>
        <?php
        if($departement==""){
        $nilaiKontrakArray = array();
        $targetArray = array();
        $persentaseArray = array();
        ?>
        <tr>
            <td class="labelHeader">Target</td>
            <?php 
            for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                if($rep<10) $bln = "0".$rep; else $bln = $rep;
                $value = newQuery("get_var","SELECT TargetNilai FROM tb_target_kontrak WHERE Bulan='$rep' AND Tahun='$tahun'");
                if(!$value) $value = 0;
                $targetArray[$bln] = $value;
            ?>
            <td style="text-align: right;"><?php echo number_format($targetArray[$bln],2); ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td class="labelHeader">Nilai Kontrak</td>
            <?php 
            for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                if($rep<10) $bln = "0".$rep; else $bln = $rep;
                $value = newQuery("get_var","SELECT SUM(GrandTotal) FROM tb_proyek WHERE DATE_FORMAT(DateStartProject,'%Y-%m')='$tahun-$bln'");
                if(!$value) $value = 0;
                $nilaiKontrakArray[$bln] = $value;
            ?>
            <td style="text-align: right;"><?php echo number_format($nilaiKontrakArray[$bln],2); ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td class="labelHeader">Pencapaian (%)</td>
            <?php 
            for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                if($rep<10) $bln = "0".$rep; else $bln = $rep;
                if($targetArray[$bln]>0)
                    $persentaseArray[$bln] = ($nilaiKontrakArray[$bln]/$targetArray[$bln])*100;
                else
                    $persentaseArray[$bln] = 0;
            ?>
            <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($persentaseArray[$bln],2); ?>%</td>
            <?php } ?>
        </tr>
        <tr>
            <td class="labelHeader spacer"></td>
            <td style="text-align: right;"></td>
        </tr>
        <?php } ?>
        <tr>
            <td class="labelHeader">Pendapatan</td>
            <?php 
            for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                if($rep<10) $bln = "0".$rep; else $bln = $rep;
            ?>
            <td style="text-align: right;">Rupiah</td>
            <?php } ?>
        </tr>
        <?php
        $labarugi = 0;
        $pendapatan = 0;
        $biaya = 0;
        $biayaLain = 0;

        $labarugiArray = array();
        $pendapatanArray = array();
        $biayaArray = array();
        $biayaLainArray = array();
        $pendapatanbrutoArray = array();
        $totalppnArray = array();

        $hppArray = array();
        $biayaArray = array();
        $labarugiArray = array();
        $totalArray = array();

        $query = newQuery("get_results","SELECT * FROM `tb_master_rekening` WHERE IDParent='63' ORDER BY KodeRekening ASC");
        if($query){
            foreach($query as $data){
                if($data->Tipe=="D"){
                    ?>
                    <tr>
                        <td class="labelHeader2 deep1"><?php echo $data->KodeRekening."&nbsp;&nbsp;&nbsp;".ucwords(strtolower($data->NamaRekening)); ?></td>
                        <?php 
                        for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                            if($rep<10) $bln = "0".$rep; else $bln = $rep;
                            $condDate = " AND DATE_FORMAT(a.Tanggal,'%Y-%m')='$tahun-$bln' ";
                            if($departement!="") $condDate .= " AND b.IDDepartement='$departement' ";
                        ?>
                        <td style="text-align: right;"><?php echo CheckSaldo($data->IDRekening,$condDate, $db,"KREDIT",$departement); ?></td>
                        <?php } ?>
                        
                    </tr>
                    <?php
                    for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                        if($rep<10) $bln = "0".$rep; else $bln = $rep;

                        $condDate = " AND DATE_FORMAT(a.Tanggal,'%Y-%m')='$tahun-$bln' ";
                        if($departement!="") $condDate .= " AND b.IDDepartement='$departement' ";
                        $queryDetail = newQuery("get_results","SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND a.`IDRekening`='".$data->IDRekening."' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
                        if($queryDetail){
                            foreach($queryDetail as $dataDetail){
                                $jurnal = newQuery("get_row","SELECT * FROM tb_jurnal WHERE IDJurnal='".$dataDetail->IDJurnal."'");

                                if($jurnal->NoRef!='' && $jurnal->Tipe=='1'){
                                    $invoice = newQuery("get_row","SELECT * FROM tb_proyek_invoice WHERE IDInvoice='".$jurnal->NoRef."'");
                                    if($invoice){
                                        if($invoice->PPNPersen>0){
                                            $ppn = $dataDetail->Kredit*$invoice->PPNPersen/100;
                                            $totalppnArray[$bln] += $ppn;
                                        }
                                    }
                                }
                                $jurnalTandingan = newQuery("get_row","SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='".$dataDetail->IDJurnal."' AND a.IDJurnalDetail!='".$dataDetail->IDJurnalDetail."' AND a.IDRekening=b.IDRekening");
                                $pendapatanArray[$bln] += $dataDetail->Kredit;
                            }
                        }
                    }
                } else {
                    $querySub = newQuery("get_results","SELECT * FROM `tb_master_rekening` WHERE IDParent='".$data->IDRekening."' ORDER BY KodeRekening ASC");
                    if($querySub){
                        foreach($querySub as $dataSub){
                            if($dataSub->Tipe=="D"){
                                ?>
                                <tr>
                                    <td class="labelHeader2 deep1"><?php echo $dataSub->KodeRekening."&nbsp;&nbsp;&nbsp;".ucwords(strtolower($dataSub->NamaRekening)); ?></td>
                                    <?php 
                                    for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                                        if($rep<10) $bln = "0".$rep; else $bln = $rep;
                                        $condDate = " AND DATE_FORMAT(a.Tanggal,'%Y-%m')='$tahun-$bln' ";
                                        if($departement!="") $condDate .= " AND b.IDDepartement='$departement' ";
                                    ?>
                                    <td style="text-align: right;"><?php echo CheckSaldo($dataSub->IDRekening,$condDate,$db,"KREDIT",$departement); ?></td>
                                    <?php } ?>
                                </tr>
                                <?php
                                for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                                    if($rep<10) $bln = "0".$rep; else $bln = $rep;

                                    $condDate = " AND DATE_FORMAT(a.Tanggal,'%Y-%m')='$tahun-$bln' ";
                                    if($departement!="") $condDate .= " AND b.IDDepartement='$departement' ";
                                    $queryDetail = newQuery("get_results","SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND a.`IDRekening`='".$dataSub->IDRekening."' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
                                    if($queryDetail){
                                        foreach($queryDetail as $dataDetail){
                                            $jurnal = newQuery("get_row","SELECT * FROM tb_jurnal WHERE IDJurnal='".$dataDetail->IDJurnal."'");
                                            if($jurnal->NoRef!='' && $jurnal->Tipe=='1'){
                                                $invoice = newQuery("get_row","SELECT * FROM tb_proyek_invoice WHERE IDInvoice='".$jurnal->NoRef."'");
                                                if($invoice){
                                                    if($invoice->PPNPersen>0){
                                                        $ppn = $dataDetail->Kredit*$invoice->PPNPersen/100;
                                                        $totalppnArray[$bln] += $ppn;
                                                    }
                                                }
                                            }
                                            $jurnalTandingan = newQuery("get_row","SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='".$dataDetail->IDJurnal."' AND a.IDJurnalDetail!='".$dataDetail->IDJurnalDetail."' AND a.IDRekening=b.IDRekening");
                                            $pendapatanArray[$bln] += $dataDetail->Kredit;
                                        }
                                    }

                                }
                            }
                        }
                    }
                }
            }
            for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                if($rep<10) $bln = "0".$rep; else $bln = $rep;

                $pendapatanbrutoArray[$bln] = $pendapatanArray[$bln];
                $pendapatanArray[$bln] = $pendapatanbrutoArray[$bln]-$totalppnArray[$bln];
            }
            ?>
            <tr>
                <td class="labelHeader">Total Pendapatan</td>
                <?php
                for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                    if($rep<10) $bln = "0".$rep; else $bln = $rep;

                    ?>
                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($pendapatanbrutoArray[$bln],2); ?></td>
                    <?php
                }
                ?>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td class="labelHeader">Pajak Pendapatan</td>
            <td style="text-align: right;"></td>
        </tr>
        <tr>
            <td class="deep1">Pajak Pendapatan Periode <?php echo $tanggalDisplay;?> </td>
            <?php
            for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                if($rep<10) $bln = "0".$rep; else $bln = $rep;
                ?>
                <td style="text-align: right;"><?php echo number_format($totalppnArray[$bln],2); ?></td>
                <?php
            }
            ?>
        </tr>
        <tr>
            <td class="labelHeader">Total Pajak Pendapatan</td>
            <?php
            for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                if($rep<10) $bln = "0".$rep; else $bln = $rep;
                ?>
                <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($totalppnArray[$bln],2); ?></td>
                <?php
            }
            ?>
        </tr>
        <tr>
            <td class="labelHeader">Total Total Pendapatan Bersih</td>
            <?php
            for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                if($rep<10) $bln = "0".$rep; else $bln = $rep;

                ?>
                <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($pendapatanArray[$bln],2); ?></td>
                <?php
            }
            ?>
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
        $query = newQuery("get_results","SELECT * FROM `tb_master_rekening` WHERE IDParent='70' ORDER BY KodeRekening ASC");
        if($query){
            foreach($query as $data){
                if($data->Tipe=="D"){
                    ?>
                    <tr>
                        <td class="labelHeader2 deep1"><?php echo $data->KodeRekening."&nbsp;&nbsp;&nbsp;".ucwords(strtolower($data->NamaRekening)); ?></td>
                        <?php 
                        for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                            if($rep<10) $bln = "0".$rep; else $bln = $rep;
                            $condDate = " AND DATE_FORMAT(a.Tanggal,'%Y-%m')='$tahun-$bln' ";
                            if($departement!="") $condDate .= " AND b.IDDepartement='$departement' ";
                        ?>
                        <td style="text-align: right;"><?php echo CheckSaldo($data->IDRekening,$condDate,$db,"DEBET",$departement); ?></td>
                        <?php } ?>
                    </tr>
                    <?php
                    for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                        if($rep<10) $bln = "0".$rep; else $bln = $rep;

                        $condDate = " AND DATE_FORMAT(a.Tanggal,'%Y-%m')='$tahun-$bln' ";
                        if($departement!="") $condDate .= " AND b.IDDepartement='$departement' ";
                        $queryDetail = newQuery("get_results","SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND a.`IDRekening`='".$data->IDRekening."' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
                        if($queryDetail){
                            foreach($queryDetail as $dataDetail){
                                $jurnalTandingan = newQuery("get_row","SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='".$dataDetail->IDJurnal."' AND a.IDJurnalDetail!='".$dataDetail->IDJurnalDetail."' AND a.IDRekening=b.IDRekening");
                                $hppArray[$bln] += $dataDetail->Debet;
                            }
                        }
                    }
                } else {
                    $querySub = newQuery("get_results","SELECT * FROM `tb_master_rekening` WHERE IDParent='".$data->IDRekening."' ORDER BY KodeRekening ASC");
                    if($querySub){
                        foreach($querySub as $dataSub){
                            if($dataSub->Tipe=="D"){
                                ?>
                                <tr>
                                    <td class="labelHeader2 deep1"><?php echo $dataSub->KodeRekening."&nbsp;&nbsp;&nbsp;".ucwords(strtolower($dataSub->NamaRekening)); ?></td>
                                    <?php 
                                    for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                                        if($rep<10) $bln = "0".$rep; else $bln = $rep;
                                        $condDate = " AND DATE_FORMAT(a.Tanggal,'%Y-%m')='$tahun-$bln' ";
                                        if($departement!="") $condDate .= " AND b.IDDepartement='$departement' ";
                                    ?>
                                    <td style="text-align: right;"><?php echo CheckSaldo($dataSub->IDRekening,$condDate,$db,"DEBET",$departement); ?></td>
                                    <?php } ?>
                                </tr>
                                <?php
                                for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                                    if($rep<10) $bln = "0".$rep; else $bln = $rep;

                                    $condDate = " AND DATE_FORMAT(a.Tanggal,'%Y-%m')='$tahun-$bln' ";
                                    if($departement!="") $condDate .= " AND b.IDDepartement='$departement' ";
                                    $queryDetail = newQuery("get_results","SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND a.`IDRekening`='".$dataSub->IDRekening."' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
                                    if($queryDetail){
                                        foreach($queryDetail as $dataDetail){
                                            $jurnalTandingan = newQuery("get_row","SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='".$dataDetail->IDJurnal."' AND a.IDJurnalDetail!='".$dataDetail->IDJurnalDetail."' AND a.IDRekening=b.IDRekening");
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
                <?php
                for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                    if($rep<10) $bln = "0".$rep; else $bln = $rep;

                    ?>
                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($hppArray[$bln],2); ?></td>
                    <?php
                }
                ?>
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
        $query = newQuery("get_results","SELECT * FROM `tb_master_rekening` WHERE IDParent='73' ORDER BY KodeRekening ASC");
        if($query){
            foreach($query as $data){
                if($data->Tipe=="D"){
                    ?>
                    <tr>
                        <td class="labelHeader2 deep1"><?php echo $data->KodeRekening."&nbsp;&nbsp;&nbsp;".ucwords(strtolower($data->NamaRekening)); ?></td>
                        <?php 
                        for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                            if($rep<10) $bln = "0".$rep; else $bln = $rep;
                            $condDate = " AND DATE_FORMAT(a.Tanggal,'%Y-%m')='$tahun-$bln' ";
                            if($departement!="") $condDate .= " AND b.IDDepartement='$departement' ";
                        ?>
                        <td style="text-align: right;"><?php echo CheckSaldo($data->IDRekening,$condDate,$db,"DEBET",$departement); ?></td>
                        <?php } ?>
                    </tr>
                    <?php
                    for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                        if($rep<10) $bln = "0".$rep; else $bln = $rep;

                        $condDate = " AND DATE_FORMAT(a.Tanggal,'%Y-%m')='$tahun-$bln' ";
                        if($departement!="") $condDate .= " AND b.IDDepartement='$departement' ";
                        $queryDetail = newQuery("get_results","SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND a.`IDRekening`='".$data->IDRekening."' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
                        if($queryDetail){
                            foreach($queryDetail as $dataDetail){
                                $jurnalTandingan = newQuery("get_row","SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='".$dataDetail->IDJurnal."' AND a.IDJurnalDetail!='".$dataDetail->IDJurnalDetail."' AND a.IDRekening=b.IDRekening");
                                $biayaArray[$bln] += $dataDetail->Debet;
                            }
                        }
                    }
                } else {
                    $querySub = newQuery("get_results","SELECT * FROM `tb_master_rekening` WHERE IDParent='".$data->IDRekening."' ORDER BY KodeRekening ASC");
                    if($querySub){
                        foreach($querySub as $dataSub){
                            if($dataSub->Tipe=="D"){
                                ?>
                                <tr>
                                    <td class="labelHeader2 deep1"><?php echo $dataSub->KodeRekening."&nbsp;&nbsp;&nbsp;".ucwords(strtolower($dataSub->NamaRekening)); ?></td>
                                    <?php 
                                    for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                                        if($rep<10) $bln = "0".$rep; else $bln = $rep;
                                        $condDate = " AND DATE_FORMAT(a.Tanggal,'%Y-%m')='$tahun-$bln' ";
                                        if($departement!="") $condDate .= " AND b.IDDepartement='$departement' ";
                                    ?>
                                    <td style="text-align: right;"><?php echo CheckSaldo($dataSub->IDRekening,$condDate,$db,"DEBET",$departement); ?></td>
                                    <?php } ?>
                                    
                                </tr>
                                <?php
                                for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                                    if($rep<10) $bln = "0".$rep; else $bln = $rep;

                                    $condDate = " AND DATE_FORMAT(a.Tanggal,'%Y-%m')='$tahun-$bln' ";
                                    if($departement!="") $condDate .= " AND b.IDDepartement='$departement' ";
                                    $queryDetail = newQuery("get_results","SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND a.`IDRekening`='".$dataSub->IDRekening."' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
                                    if($queryDetail){
                                        foreach($queryDetail as $dataDetail){
                                            $jurnalTandingan = newQuery("get_row","SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='".$dataDetail->IDJurnal."' AND a.IDJurnalDetail!='".$dataDetail->IDJurnalDetail."' AND a.IDRekening=b.IDRekening");
                                            $biayaArray[$bln] += $dataDetail->Debet;
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
                <?php
                for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                    if($rep<10) $bln = "0".$rep; else $bln = $rep;

                    ?>
                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($biayaArray[$bln],2); ?></td>
                    <?php
                }
                ?>
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
        $query = newQuery("get_results","SELECT * FROM `tb_master_rekening` WHERE IDParent='101' ORDER BY KodeRekening ASC");
        if($query){
            foreach($query as $data){
                if($data->Tipe=="D"){
                    ?>
                    <tr>
                        <td class="labelHeader2 deep1"><?php echo $data->KodeRekening."&nbsp;&nbsp;&nbsp;".ucwords(strtolower($data->NamaRekening)); ?></td>
                        <?php 
                        for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                            if($rep<10) $bln = "0".$rep; else $bln = $rep;
                            $condDate = " AND DATE_FORMAT(a.Tanggal,'%Y-%m')='$tahun-$bln' ";
                            if($departement!="") $condDate .= " AND b.IDDepartement='$departement' ";
                        ?>
                        <td style="text-align: right;"><?php echo CheckSaldo($data->IDRekening,$condDate,$db,"DEBET",$departement); ?></td>
                        <?php } ?>
                    </tr>
                    <?php
                    for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                        if($rep<10) $bln = "0".$rep; else $bln = $rep;

                        $condDate = " AND DATE_FORMAT(a.Tanggal,'%Y-%m')='$tahun-$bln' ";
                        if($departement!="") $condDate .= " AND b.IDDepartement='$departement' ";
                        $queryDetail = newQuery("get_results","SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND a.`IDRekening`='".$data->IDRekening."' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
                        if($queryDetail){
                            foreach($queryDetail as $dataDetail){
                                $jurnalTandingan = newQuery("get_row","SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='".$dataDetail->IDJurnal."' AND a.IDJurnalDetail!='".$dataDetail->IDJurnalDetail."' AND a.IDRekening=b.IDRekening");
                                $biayaLainArray[$bln] += $dataDetail->Debet;
                            }
                        }
                    }
                } else {
                    $querySub = newQuery("get_results","SELECT * FROM `tb_master_rekening` WHERE IDParent='".$data->IDRekening."' ORDER BY KodeRekening ASC");
                    if($querySub){
                        foreach($querySub as $dataSub){
                            if($dataSub->Tipe=="D"){
                                ?>
                                <tr>
                                    <td class="labelHeader2 deep1"><?php echo $dataSub->KodeRekening."&nbsp;&nbsp;&nbsp;".ucwords(strtolower($dataSub->NamaRekening)); ?></td>
                                    <?php 
                                    for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                                        if($rep<10) $bln = "0".$rep; else $bln = $rep;
                                        $condDate = " AND DATE_FORMAT(a.Tanggal,'%Y-%m')='$tahun-$bln' ";
                                        if($departement!="") $condDate .= " AND b.IDDepartement='$departement' ";
                                    ?>
                                    <td style="text-align: right;"><?php echo CheckSaldo($dataSub->IDRekening,$condDate,$db,"DEBET",$departement); ?></td>
                                    <?php } ?>
                                    
                                </tr>
                                <?php
                                for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                                    if($rep<10) $bln = "0".$rep; else $bln = $rep;

                                    $condDate = " AND DATE_FORMAT(a.Tanggal,'%Y-%m')='$tahun-$bln' ";
                                    if($departement!="") $condDate .= " AND b.IDDepartement='$departement' ";
                                    $queryDetail = newQuery("get_results","SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND a.`IDRekening`='".$dataSub->IDRekening."' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
                                    if($queryDetail){
                                        foreach($queryDetail as $dataDetail){
                                            $jurnalTandingan = newQuery("get_row","SELECT b.* FROM tb_jurnal_detail a, tb_master_rekening b WHERE a.IDJurnal='".$dataDetail->IDJurnal."' AND a.IDJurnalDetail!='".$dataDetail->IDJurnalDetail."' AND a.IDRekening=b.IDRekening");
                                            $biayaLainArray[$bln] += $dataDetail->Debet;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                if($rep<10) $bln = "0".$rep; else $bln = $rep;
                $labarugiArray[$bln] = $pendapatanArray[$bln]-$hppArray[$bln]-$biayaArray[$bln]-$biayaLainArray[$bln];
                $totalArray[$bln] = $pendapatanArray[$bln]+$biayaArray[$bln]+$biayaLainArray[$bln]+$hppArray[$bln];
            }
            ?>
            <tr>
                <td class="labelHeader">Total Biaya Lain</td>
                <?php
                for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                    if($rep<10) $bln = "0".$rep; else $bln = $rep;

                    ?>
                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($biayaLainArray[$bln],2); ?></td>
                    <?php
                }
                ?>
            </tr>
            <tr>
                <td class="labelHeader">Laba/Rugi</td>
                <?php
                for($rep=intval($bulan);$rep<=intval($bulan2);$rep++){
                    if($rep<10) $bln = "0".$rep; else $bln = $rep;

                    ?>
                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($labarugiArray[$bln],2); ?></td>
                    <?php
                }
                ?>
            </tr>
            <?php
        }
        ?>
    </table>
    <script type="text/javascript">
        setTimeout(function(){
            window.print();
        },1500);
    </script>
</body>
</html>