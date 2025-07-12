<?php
$departement = $this->validasi->validInput($_GET['departement']);
$periode = $this->validasi->validInput($_GET['periode']);
$sampaitanggal = $this->validasi->validInput($_GET['sampaitanggal']);
if ($departement != "") {
    $cond = " AND IDDepartement='$departement'";
}
if ($periode != "") {
    $cond .= " AND Tahun='$periode'";
    $subtitle = "Periode: $periode";
}
if ($sampaitanggal != "") {
    $exp = explode("/", $sampaitanggal);
    $sampaitanggalEN = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
    $cond2 = " AND Tanggal<='$sampaitanggalEN'";
} else {
    $sampaitanggal = date("d/m/Y");
    $cond2 = " AND Tanggal<='" . date("Y-m-d") . "'";
}

$subtitle .= ". Sampai Dengan: $sampaitanggal";
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
        <h1 class="underline">** REKAP DATA PIUTANG **</h1><?php echo $subtitle; ?>
    </div>
    <table class="tabelList2" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="70">No. Proyek</th>
                <th>Nama Proyek</th>
                <th width="70">Nilai</th>
                <th width="100">PPN</th>
                <th width="70">Nilai Kontrak</th>
                <th width="70">Total Invoice</th>
                <th width="70">Piutang Proyek</th>
                <th width="70">Piutang Progress</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = newQuery("get_results", "SELECT * FROM tb_proyek WHERE SisaPembayaran>0 $cond ORDER BY Tahun DESC, KodeProyek ASC");
            if ($query) {
                $total = 0;
                $ppn = 0;
                $grandtotal = 0;
                $terbayar = 0;
                $piutang = 0;
                $progress = 0;
                $piutangPiutang = 0;
                foreach ($query as $data) {
                    $total += $data->Nominal;
                    $ppn += $data->PPN;
                    $grandtotal += $data->GrandTotal;
                    // $terbayar += $data->JumlahPembayaran;
                    // $piutang += $data->SisaPembayaran;

                    $grandTotalProyek = newQuery("get_var", "SELECT SUM(GrandTotal) FROM tb_proyek_invoice WHERE IDProyek='" . $data->IDProyek . "' $cond2");
                    if (!$grandTotalProyek) {
                        $grandTotalProyek = 0;
                    } else {
                        $progress += $grandTotalProyek;
                    }

                    $totalTerbayar = newQuery("get_var", "SELECT SUM(GrandTotal-Sisa) FROM tb_proyek_invoice WHERE IDProyek='" . $data->IDProyek . "' $cond2");
                    if (!$totalTerbayar) {
                        $totalTerbayar = 0;
                    } else {
                        $terbayar += $totalTerbayar;
                    }

                    $sisaPiutang = $data->GrandTotal - $totalTerbayar;
                    $piutang += $sisaPiutang;

                    $totalPiutang = newQuery("get_var", "SELECT SUM(Sisa) FROM tb_proyek_invoice WHERE IDProyek='" . $data->IDProyek . "' $cond2");
                    if (!$totalPiutang) {
                        $totalPiutang = 0;
                    } else {
                        $piutangPiutang += $totalPiutang;
                    }
            ?>
                    <tr>
                        <td><a href="#" onclick="window.open('<?php echo PRSONPATH; ?>detail-pembayaran-piutang/?proyek=<?php echo $data->IDProyek; ?>','winname','directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1200,height=600'); return false;"><?php echo $data->KodeProyek . "/" . $data->Tahun; ?></a></td>
                        <td><?php echo $data->NamaProyek; ?></td>
                        <td><?php echo number_format($data->Nominal, 2); ?></td>
                        <td><?php echo number_format($data->PPN, 2); ?> (<?php echo $data->PPNPersen; ?> %)</td>
                        <td><?php echo number_format($data->GrandTotal, 2); ?></td>
                        <td><?php echo number_format($grandTotalProyek, 2); ?></td>
                        <td><strong><?php echo number_format($sisaPiutang, 2); ?></strong></td>
                        <td><strong><?php echo number_format($totalPiutang, 2); ?></strong></td>
                    </tr>
                <?php
                }
                ?>
                <tr>
                    <td colspan="2" style="text-align: right;"><strong>TOTAL : </strong></td>
                    <td><strong><?php echo number_format($total, 2); ?></strong></td>
                    <td><strong><?php echo number_format($ppn, 2); ?></strong></td>
                    <td><strong><?php echo number_format($grandtotal, 2); ?></strong></td>
                    <td><strong><?php echo number_format($progress, 2); ?></strong></td>
                    <td><strong><?php echo number_format($piutang, 2); ?></strong></td>
                    <td><strong><?php echo number_format($piutangPiutang, 2); ?></strong></td>
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