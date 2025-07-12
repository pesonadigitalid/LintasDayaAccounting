<?php
include_once "../ajax-loader.php";
$idjurnal = $validasi->validInput($_POST['idjurnal']);
$notransaksi = $validasi->validInput($_POST['notransaksi']);
$no_bukti = $validasi->validInput($_POST['no_bukti']);
$keterangan = $validasi->validInput($_POST['keterangan']);

$tgl_transaksi = $validasi->validInput($_POST['tgl_transaksi']);

$cartArray = $validasi->validInput($_POST['cartArray']);
$balanceDebet = $validasi->validInput($_POST['balanceDebet']);
$balanceKredit = $validasi->validInput($_POST['balanceKredit']);

$cartArray = json_decode($cartArray);
$exp = explode("/",$tgl_transaksi);
$tanggal = $exp[2]."-".$exp[1]."-".$exp[0];
$idTanggal = $exp[2].$exp[1].$exp[0];

$query = $db->query("UPDATE tb_jurnal SET NoBukti='$no_bukti', NoRef='', Tanggal='$tanggal', Debet='$balanceDebet', Kredit='$balanceKredit', ModifiedBy='1', DateModified=NOW(), Keterangan='$keterangan' WHERE IDJurnal='$idjurnal'");

$db->query("DELETE FROM tb_jurnal_detail WHERE IDJurnal='$idjurnal'");

foreach($cartArray as $data){
    if(isset($data)){
        $debet = $data->Debet;
        $kredit = $data->Kredit;
        $getPosisi = $db->get_var("SELECT Posisi FROM tb_master_transaksi_rekening WHERE IDRekening='".$data->IDRekening."' AND IDTransaksi=(SELECT IDTransaksi FROM tb_master_transaksi WHERE Keterangan='$keterangan')");
        $db->query("INSERT INTO tb_jurnal_detail SET IDJurnal='$idjurnal', JurnalRef='$no_bukti', IDRekening='".$data->IDRekening."', Tanggal='$tanggal', Debet='".$debet."', Kredit='".$kredit."', Closing='$closing', MataUang='1', Kurs='".$data->Kurs."', Keterangan='".$keterangan."', Pos='$getPosisi'");
    
    }
}
echo json_encode(array("status"=>"1","msg"=>$notransaksi));