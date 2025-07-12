<?php
$idpenjualan = $this->validasi->validInput($_GET['idpenjualan']);
$dariTanggal = $this->validasi->validInput($_GET['daritanggal']);
$sampaiTanggal = $this->validasi->validInput($_GET['sampaitanggal']);

$cond = "";
if ($idpenjualan != "") {
    $cond .= " AND IDPenjualan='$idpenjualan'";
}

if ($dariTanggal != "") {
    $exp = explode("/", $dariTanggal);
    $dariTanggalEN = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
    $tgl = $exp[0];
    $bulan = $exp[1];
    $tahun = $exp[2];
    if ($sampaiTanggal != "") {
        $exp = explode("/", $sampaiTanggal);
        $sampaiTanggalEN = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
        $cond .= " AND Tanggal BETWEEN '$dariTanggalEN' AND '$sampaiTanggalEN' ";
    } else {
        $cond .= " AND Tanggal = '$dariTanggalEN'";
    }
}
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="" />
    <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543" />
    <title>Lintas Daya Accounting</title>
    <link rel="icon" type="image/png" href="<?php echo PRSONTEMPPATH; ?>dist/img/favicon.png" />
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
    <link rel="stylesheet" href="<?php echo PRSONTEMPPATH; ?>css/print-style.css" media="all" />
</head>

<body>
    <table>
        <tr>
            <td width="50%" class="bottom">
                <h1>CV. LINTAS DAYA</h1>
                <p style="margin-top: 5px;">JL. Tukad Citarum I, No. 10, Renon, Perum Surya Graha Asih, Kota Denpasar, Bali<br />Phone. (0361) 238055, Fax. -</p>
            </td>
            <td width="50%" align="right" class="bottom">
                Tanggal Cetak : <?php echo date("d/m/Y"); ?>
            </td>
        </tr>
    </table>
    <div class="laporanTitle">
        <h1 class="underline">** REKAP DATA PIUTANG PROGRESS MMS **</h1><?php echo $subtitle; ?>
    </div>
    <table class="tabelList2" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="120">No. Invoice</th>
                <th>Pelanggan</th>
                <th width="100">Tanggal</th>
                <th width="120">No. SPB</th>
                <th width="120">Total</th>
                <th width="140">Diskon</th>
                <th width="140">PPN</th>
                <th width="120">Grand Total</th>
                <th width="120">Terbayar</th>
                <th width="120">Piutang</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_penjualan_invoice WHERE Sisa>0 $cond ORDER BY Tanggal ASC, NoInvoice ASC");
            if ($query) {
                $total = 0;
                $diskon = 0;
                $ppn = 0;
                $grandtotal = 0;
                $terbayar = 0;
                $piutang = 0;
                foreach ($query as $data) {
                    $dataPenjualan = newQuery("get_row", "SELECT * FROM tb_penjualan WHERE IDPenjualan='" . $data->IDPenjualan . "'");
                    $dataPelanggan = newQuery("get_row", "SELECT * FROM tb_pelanggan WHERE IDPelanggan='" . $dataPenjualan->IDPelanggan . "'");
                    $totalBayar = newQuery("get_var", "SELECT SUM(Debet) FROM tb_jurnal WHERE NoRef='" . $data->IDInvoice . "' AND Tipe='4'");
                    $total += $data->Jumlah;
                    $diskon += $data->Diskon;
                    $ppn += $data->PPN;
                    $grandtotal += $data->GrandTotal;
                    $terbayar += $totalBayar;
                    $piutang += $data->Sisa;
            ?>
                    <tr>
                        <td><a href="#" onclick="window.open('<?php echo PRSONPATH; ?>detail-pembayaran-invoice-mms/?invoice=<?php echo $data->IDInvoice; ?>','winname','directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1200,height=600'); return false;"><?php echo $data->NoInvoice; ?></a></td>
                        <td><?php echo $dataPelanggan->NamaPelanggan; ?></td>
                        <td><?php echo $data->TanggalID; ?></td>
                        <td><?php echo $dataPenjualan->NoPenjualan; ?></td>
                        <td><?php echo number_format($data->Jumlah, 2); ?></td>
                        <td><?php echo number_format($data->Diskon, 2); ?> (<?php echo $data->DiskonPersen; ?>%)</td>
                        <td><?php echo number_format($data->PPN, 2); ?> (<?php echo $data->PPNPersen; ?>%)</td>
                        <td><?php echo number_format($data->GrandTotal, 2); ?></td>
                        <td><?php echo number_format($totalBayar, 2); ?></td>
                        <td><strong><?php echo number_format($data->Sisa, 2); ?></strong></td>
                    </tr>
                <?php
                }
                ?>
                <tr>
                    <td colspan="4" style="text-align: right;"><strong>TOTAL : </strong></td>
                    <td><strong><?php echo number_format($total, 2); ?></strong></td>
                    <td><strong><?php echo number_format($diskon, 2); ?></strong></td>
                    <td><strong><?php echo number_format($ppn, 2); ?></strong></td>
                    <td><strong><?php echo number_format($grandtotal, 2); ?></strong></td>
                    <td><strong><?php echo number_format($terbayar, 2); ?></strong></td>
                    <td><strong><?php echo number_format($piutang, 2); ?></strong></td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
    <script type="text/javascript">
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>