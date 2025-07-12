<?php
include_once "../ajax-loader.php";
$tipe = $validasi->validInput($_GET['tipe']);
$proyek = $validasi->validInput($_GET['proyek']);

if ($tipe == "1" || $tipe == "8") {
    $arrayRes = array();
    $query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_proyek_invoice WHERE IDProyek='$proyek' ORDER BY NoInv ASC");
    if ($query) {
        foreach ($query as $data) {
            if ($data->Sisa <= 0) $add = " (LUNAS)";
            else $add = "";
            array_push($arrayRes, array("id" => $data->IDInvoice, "val" => $data->NoInv . " / " . $data->TanggalID . $add, "nominal" => number_format($data->Sisa), "tipe" => "Debet"));
        }
    }
} else if ($tipe == "3" || $tipe == "6") {
    $arrayRes = array();
    $query = $db->get_results("SELECT a.*, b.NamaPerusahaan, DATE_FORMAT(a.`Tanggal`,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.`IDSupplier`=b.`IDSupplier` AND a.IDProyek='$proyek' AND IsLD='1' ORDER BY NoPo ASC");

    if ($query) {
        foreach ($query as $data) {
            if ($data->Sisa <= 0) $add = " (LUNAS)";
            else $add = "";
            if ($data->Keterangan != "") $add2 = " / " . $data->Keterangan;
            else $add2 = "";
            array_push($arrayRes, array("id" => $data->IDPO, "val" => $data->NoPo . " / " . $data->NamaPerusahaan . $add2 . $add, "nominal" => number_format($data->Sisa), "tipe" => "Kredit", "jenis_po" => $data->JenisPO));
        }
    }
} else if ($tipe == "4") {
    $noref = $_GET['noRef'];
    if ($noref !== '') $cond = " OR IDInvoice='$noref'";
    $arrayRes = array();
    $query = $db->get_results("SELECT a.IDInvoice, a.NoInvoice, a.Sisa, c.NamaPelanggan, b.NoPenjualan FROM tb_penjualan_invoice a, tb_penjualan b, tb_pelanggan c WHERE a.`IDPenjualan`=b.`IDPenjualan` AND b.`IDPelanggan`=c.`IDPelanggan` AND (IDInvoice!='' $cond)");

    if ($query) {
        foreach ($query as $data) {
            array_push($arrayRes, array("id" => $data->IDInvoice, "val" => $data->NoInvoice . " / " . $data->NoPenjualan . " / " . $data->NamaPelanggan, "nominal" => number_format($data->Sisa), "tipe" => "Kredit"));
        }
    }
} else if ($tipe == "5" || $tipe == "7") {
    $query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_proyek_invoice WHERE IDProyek='$proyek' ORDER BY NoInv ASC");
    if ($query) {
        foreach ($query as $data) {
            if ($data->Sisa <= 0) $add = " (LUNAS)";
            else $add = "";
            array_push($arrayRes, array("id" => $data->IDInvoice, "val" => $data->NoInv . " / " . $data->TanggalID . $add, "nominal" => number_format($data->Sisa), "tipe" => "Debet"));
        }
    }

    $arrayRes = array();
    $query = $db->get_results("SELECT a.*, b.NamaPerusahaan, DATE_FORMAT(a.`Tanggal`,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.`IDSupplier`=b.`IDSupplier` AND a.IDProyek='$proyek' AND IsLD='0' ORDER BY NoPo ASC");

    if ($query) {
        foreach ($query as $data) {
            if ($data->Sisa <= 0) $add = " (LUNAS)";
            else $add = "";
            array_push($arrayRes, array("id" => $data->IDPO, "val" => $data->NoPo . " / " . $data->NamaPerusahaan . $add, "nominal" => number_format($data->Sisa), "tipe" => "Kredit"));
        }
    }
}
echo json_encode(array("status" => "1", "option" => $arrayRes));
