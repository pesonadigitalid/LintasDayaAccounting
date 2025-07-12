<?php
include_once "../ajax-loader.php";
$idjurnal = $validasi->validInput($_POST['idjurnal']);
$notransaksi = $validasi->validInput($_POST['notransaksi']);
$no_bukti = $validasi->validInput($_POST['no_bukti']);
$departement = $validasi->validInput($_POST['departement']);
$keterangan = $validasi->validInput($_POST['keterangan']);

$tgl_transaksi = $validasi->validInput($_POST['tgl_transaksi']);

$cartArray = $validasi->validInput($_POST['cartArray']);
$balanceDebet = $validasi->validInput($_POST['balanceDebet']);
$balanceKredit = $validasi->validInput($_POST['balanceKredit']);

$no_bg = $validasi->validInput($_POST['no_bg']);
$tanggal_jatuh_tempo = $validasi->validInput($_POST['tanggal_jatuh_tempo']);
if ($tanggal_jatuh_tempo != "") {
    $exp = explode("/", $tanggal_jatuh_tempo);
    $tanggal_jatuh_tempo = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
}
$id_proyek = $validasi->validInput($_POST['id_proyek']);
$kategori = $validasi->validInput($_POST['kategori']);
$no_bukti_invoice = $validasi->validInput($_POST['no_bukti_invoice']);
$no_bukti_po = $validasi->validInput($_POST['no_bukti_po']);

$no_ref = $validasi->validInput($_POST['no_ref']);

$jenis_po = $validasi->validInput($_POST['jenis_po']);
$jurnal_ppn_invoice = $validasi->validInput($_POST['jurnal_ppn_invoice']);

$no_kwitansi = $validasi->validInput($_POST['no_kwitansi']);
$tanggal_kwitansi = $validasi->validInput($_POST['tanggal_kwitansi']);
if ($tanggal_kwitansi != "") {
    $exp = explode("/", $tanggal_kwitansi);
    $tanggal_kwitansi = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
}
$metode_pembayaran = $validasi->validInput($_POST['metode_pembayaran']);
$bank = $validasi->validInput($_POST['bank']);
$no_rek = $validasi->validInput($_POST['no_rek']);
$tanggal_bayar = $validasi->validInput($_POST['tanggal_bayar']);
if ($tanggal_bayar != "") {
    $exp = explode("/", $tanggal_bayar);
    $tanggal_bayar = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
}

/*if($kategori=="1"){
    $row = $db->get_row("SELECT * FROM tb_proyek_invoice WHERE IDInvoice='$no_ref'");
    if($row){
        $no_ref = $row->NoInvoice;
        $db->query("UPDATE tb_proyek_invoice SET Status='1' WHERE IDInvoice='$no_ref'");
    }
} else if($kategori=="3" || $kategori=="4"){
    $row = $db->get_row("SELECT * FROM tb_po WHERE IDPO='$no_ref'");
    if($row){
        $no_ref = $row->NoPo;
    }
}*/

//RESET SISA
$dataJurnal = $db->get_row("SELECT * FROM tb_jurnal WHERE IDJurnal='$idjurnal'");
$no_ref_old = $dataJurnal->NoRef;
$id_proyek_old = $dataJurnal->IDProyek;
if ($dataJurnal->Tipe == "1") {
    $db->query("UPDATE tb_proyek_invoice SET Sisa=(Sisa+" . $dataJurnal->Debet . ") WHERE IDInvoice='$no_ref_old'");
    $db->query("UPDATE tb_proyek SET JumlahPembayaran=(JumlahPembayaran-" . $dataJurnal->Debet . "), SisaPembayaran=(GrandTotal-JumlahPembayaran) WHERE IDProyek='$id_proyek_old'");
} else if ($dataJurnal->Tipe == "3" || $dataJurnal->Tipe == "5") {
    $db->query("UPDATE tb_po SET Sisa=(Sisa+" . $dataJurnal->Debet . "), TotalPembayaran=(TotalPembayaran-" . $dataJurnal->Debet . ") WHERE IDPO='$no_ref_old'");
} else if ($dataJurnal->Tipe == "4") {
    $res = $db->query("UPDATE tb_penjualan_invoice SET Sisa=(Sisa+" . $dataJurnal->Debet . ") WHERE IDInvoice='$no_ref_old'");
    $r = $db->get_row("SELECT * FROM tb_penjualan_invoice WHERE IDInvoice='$no_ref_old'");
    $db->query("UPDATE tb_penjualan SET TotalPembayaran=(TotalPembayaran-" . $dataJurnal->Debet . "), Sisa=(GrandTotal-TotalPembayaran) WHERE IDPenjualan='" . $r->IDPenjualan . "'");
}

$cartArray = json_decode($cartArray);
$exp = explode("/", $tgl_transaksi);
$tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
$idTanggal = $exp[2] . $exp[1] . $exp[0];

if ($kategori == "0") {
    $no_ref = "";
}

$query = $db->query("UPDATE tb_jurnal SET IDDepartement='$departement', NoBukti='$no_bukti', NoRef='$no_ref', Tanggal='$tanggal', Debet='$balanceDebet', Kredit='$balanceKredit', ModifiedBy='1', DateModified=NOW(), Keterangan='$keterangan', IDProyek='$id_proyek', Tipe='$kategori', BGJatuhTempo='$tanggal_jatuh_tempo', NoBG='$no_bg', NoKwitansi='$no_kwitansi', TanggalKwitansi='$tanggal_kwitansi', TipePembayaran='$metode_pembayaran', Bank='$bank', NoBank='$no_rek', TanggalPembayaran='$tanggal_bayar', JurnalPPNInvoice='$jurnal_ppn_invoice' WHERE IDJurnal='$idjurnal'");

if ($kategori == "1") {
    $totalPembayaran = $db->get_var("SELECT SUM(Debet) FROM tb_jurnal WHERE NoRef='$no_ref' AND Tipe='1'");
    if (!$totalPembayaran) $totalPembayaran = 0;
    $db->query("UPDATE tb_proyek_invoice SET Sisa=(GrandTotal-$totalPembayaran) WHERE IDInvoice='$no_ref'");
    $totalBayarProyek = $db->get_var("SELECT SUM(Debet) FROM tb_jurnal WHERE NoRef IN (SELECT IDInvoice FROM tb_proyek_invoice WHERE IDProyek='$id_proyek') AND Tipe='1'");
    if (!$totalBayarProyek) $totalBayarProyek = 0;
    $db->query("UPDATE tb_proyek SET JumlahPembayaran='$totalBayarProyek', SisaPembayaran=(GrandTotal-JumlahPembayaran) WHERE IDProyek='$id_proyek'");
} else if ($kategori == "3" || $kategori == "5") {
    $totalPembayaran = $db->get_var("SELECT SUM(Debet) FROM tb_jurnal WHERE NoRef='$no_ref' AND (Tipe='3' OR Tipe='5')");
    if (!$totalPembayaran) $totalPembayaran = 0;

    $po = $db->get_row("SELECT * FROM tb_po WHERE IDPO='$no_ref'");
    $sisa = $po->GrandTotal - $totalPembayaran;
    $db->query("UPDATE tb_po SET Sisa='$sisa', TotalPembayaran='$totalPembayaran' WHERE IDPO='$no_ref'");
} else if ($kategori == "4") {
    $totalPembayaran = $db->get_var("SELECT SUM(Debet) FROM tb_jurnal WHERE NoRef='$no_ref' AND Tipe='4'");
    if (!$totalPembayaran) $totalPembayaran = 0;

    $d = $db->get_row("SELECT * FROM tb_penjualan_invoice WHERE IDInvoice='$no_ref'");
    $sisa = $d->GrandTotal - $totalPembayaran;
    $db->query("UPDATE tb_penjualan_invoice SET Sisa='$sisa' WHERE IDInvoice='$no_ref'");

    $totalPembayaran2 = $db->get_var("SELECT SUM(Debet) FROM tb_jurnal WHERE NoRef IN (SELECT IDInvoice FROM tb_penjualan_invoice WHERE IDPenjualan='" . $d->IDPenjualan . "') AND Tipe='4'");
    if (!$totalPembayaran2) $totalPembayaran2 = 0;
    $r = $db->get_row("SELECT * FROM tb_penjualan WHERE IDPenjualan='" . $d->IDPenjualan . "'");
    $sisa = $r->GrandTotal - $totalPembayaran2;
    $db->query("UPDATE tb_penjualan SET TotalPembayaran='$totalPembayaran2', Sisa='$sisa' WHERE IDPenjualan='" . $r->IDPenjualan . "'");
}

if ($kategori == "3") {
    $db->query("UPDATE tb_po SET JenisPO='$jenis_po' WHERE IDPO='$no_ref'");
}

$db->query("DELETE FROM tb_jurnal_detail WHERE IDJurnal='$idjurnal'");

foreach ($cartArray as $data) {
    if (isset($data)) {
        $debet = $data->Debet;
        $kredit = $data->Kredit;

        $getPosisi = $db->get_var("SELECT Posisi FROM tb_master_rekening WHERE IDRekening='" . $data->IDRekening . "'");
        $db->query("INSERT INTO tb_jurnal_detail SET IDJurnal='$idjurnal', JurnalRef='$no_bukti', IDRekening='" . $data->IDRekening . "', Tanggal='$tanggal', Debet='" . $debet . "', Kredit='" . $kredit . "', Closing='0', MataUang='1', Kurs='" . $data->Kurs . "', Keterangan='" . $keterangan . "', Pos='$getPosisi'");
    }
}
echo json_encode(array("status" => "1", "msg" => $notransaksi));
