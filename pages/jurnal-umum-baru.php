<?php
session_start();
if($_GET['bulan']=="" && $_GET['tahun']=="" && $_GET['tanggal']==""){
    $dataLast = newQuery("get_row","SELECT * FROM tb_jurnal WHERE DATE_FORMAT(Tanggal,'%Y-%m')='".date("Y-m")."' ORDER BY NoJurnal DESC");
    if($dataLast) $last = substr($dataLast->NoJurnal,-5); else $last=0;
    do{
        $last++;
        if($last<10000 and $last>=1000)
            $last = "0".$last;
        else if($last<1000 and $last>=100)
            $last = "00".$last;
        else if($last<100 and $last>=10)
            $last = "000".$last;
        else if($last<10)
            $last = "0000".$last;
        $noTransaksi = "01-".date("Ym").$last;
        $checkNoTransaksi = newQuery("get_row","SELECT * FROM tb_jurnal WHERE NoJurnal='$noTransaksi'");
    } while($checkNoTransaksi);
    $query = newQuery("query","INSERT INTO tb_jurnal SET NoJurnal='$noTransaksi', CreatedBy='".$_SESSION['userIDAdmin']."', DateCreated=NOW()");
} else {
    $dataLast = newQuery("get_row","SELECT * FROM tb_jurnal WHERE DATE_FORMAT(Tanggal,'%Y-%m')='".$_GET['tahun']."-".$_GET['bulan']."' ORDER BY NoJurnal DESC");
    if($dataLast) $last = substr($dataLast->NoJurnal,-5); else $last=0;
    do{
        $last++;
        if($last<10000 and $last>=1000)
            $last = "0".$last;
        else if($last<1000 and $last>=100)
            $last = "00".$last;
        else if($last<100 and $last>=10)
            $last = "000".$last;
        else if($last<10)
            $last = "0000".$last;
        $noTransaksi = "01-".$_GET['tahun'].$_GET['bulan'].$last;
        $checkNoTransaksi = newQuery("get_row","SELECT * FROM tb_jurnal WHERE NoJurnal='$noTransaksi'");
    } while($checkNoTransaksi);
    
    if($_GET['tanggal']=="") $tgl = "01"; else $tgl = "0".$_GET['tanggal'];
    $query = newQuery("query","INSERT INTO tb_jurnal SET NoJurnal='$noTransaksi', CreatedBy='".$_SESSION['userIDAdmin']."', DateCreated=NOW(), Tanggal='".$_GET['tahun']."-".$_GET['bulan']."-".$tgl."'");
}
header("location: ".PRSONPATH."jurnal-umum/".$noTransaksi);