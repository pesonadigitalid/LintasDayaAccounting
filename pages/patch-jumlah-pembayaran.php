<?php
$minDate = "2023-01-01";

$i = 0;

// PROYEK
echo "RUN PROYEK<br/>";
$query = newQuery("get_results", "SELECT * FROM tb_proyek_invoice WHERE Tanggal>='$minDate'");
if ($query) {
    foreach ($query as $data) {
        $no_ref = $data->IDInvoice;
        $id_proyek = $data->IDProyek;
        $grandTotal = $data->GrandTotal;
        $sisa = $data->Sisa;

        $totalPembayaran = newQuery("get_var", "SELECT SUM(Debet) FROM tb_jurnal WHERE NoRef='$no_ref' AND Tipe='1'");
        if (!$totalPembayaran) $totalPembayaran = 0;

        if ($sisa != ($grandTotal - $totalPembayaran)) {
            echo $data->NoInv . "<br/>";
            $i++;
        }

        newQuery("query", "UPDATE tb_proyek_invoice SET Sisa=(GrandTotal-$totalPembayaran) WHERE IDInvoice='$no_ref'");

        $totalBayarProyek = newQuery("get_var", "SELECT SUM(Debet) FROM tb_jurnal WHERE NoRef IN (SELECT IDInvoice FROM tb_proyek_invoice WHERE IDProyek='$id_proyek') AND Tipe='1'");
        if (!$totalBayarProyek) $totalBayarProyek = 0;

        newQuery("query", "UPDATE tb_proyek SET JumlahPembayaran='$totalBayarProyek', SisaPembayaran=(GrandTotal-JumlahPembayaran) WHERE IDProyek='$id_proyek'");
    }
}

// PO
echo "RUN PO<br/>";
$query = newQuery("get_results", "SELECT * FROM tb_po WHERE Tanggal>='$minDate'");
if ($query) {
    foreach ($query as $data) {
        $no_ref = $data->IDPO;
        $sisa = $data->Sisa;
        $grandTotal = $data->GrandTotal;

        $totalPembayaran = newQuery("get_var", "SELECT SUM(Debet) FROM tb_jurnal WHERE NoRef='$no_ref' AND (Tipe='3' OR Tipe='5')");
        if (!$totalPembayaran) $totalPembayaran = 0;

        if ($sisa != ($grandTotal - $totalPembayaran)) {
            echo $data->NoPo . "<br/>";
            $i++;
        }

        newQuery("query", "UPDATE tb_po SET Sisa=(GrandTotal-$totalPembayaran), TotalPembayaran='$totalPembayaran' WHERE IDPO='$no_ref'");
    }
}

// MMS
echo "RUN MMS<br/>";
$query = newQuery("get_results", "SELECT * FROM tb_penjualan_invoice WHERE Tanggal>='$minDate'");
if ($query) {
    foreach ($query as $data) {
        $no_ref = $data->IDInvoice;
        $sisa = $data->Sisa;
        $grandTotal = $data->GrandTotal;
        $idPenjualan = $data->IDPenjualan;

        $totalPembayaran = newQuery("get_var", "SELECT SUM(Debet) FROM tb_jurnal WHERE NoRef='$no_ref' AND Tipe='4'");
        if (!$totalPembayaran) $totalPembayaran = 0;

        if ($sisa != ($grandTotal - $totalPembayaran)) {
            echo $data->NoInvoice . "<br/>";
            $i++;
        }

        newQuery("query", "UPDATE tb_penjualan_invoice SET Sisa=(GrandTotal-$totalPembayaran) WHERE IDInvoice='$no_ref'");

        $totalPembayaran2 = newQuery("get_var", "SELECT SUM(Debet) FROM tb_jurnal WHERE NoRef IN (SELECT IDInvoice FROM tb_penjualan_invoice WHERE IDPenjualan='$idPenjualan') AND Tipe='4'");
        if (!$totalPembayaran2) $totalPembayaran2 = 0;

        newQuery("query", "UPDATE tb_penjualan SET TotalPembayaran='$totalPembayaran2', Sisa=(GrandTotal-TotalPembayaran) WHERE IDPenjualan='$idPenjualan'");
    }
}
echo $i;
