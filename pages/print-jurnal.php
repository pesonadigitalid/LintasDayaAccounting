<?php
$bulan = $this->validasi->validInput($_GET['bulan']);
$tahun = $this->validasi->validInput($_GET['tahun']);
$jenis_transaksi = $this->validasi->validInput($_GET['jenis_transaksi']);
if($jenis_transaksi!="Semua Rekening"){
    $cond = " AND a.IDRekening='$jenis_transaksi'";
}
$tanggal = $tahun."-".$bulan."-01";
$tanggalID = "01/".$bulan."/".$tahun;
$periode = $tahun."-".$bulan;
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
    <link rel="stylesheet" href="<?php echo PRSONTEMPPATH; ?>css/print-style.css" media="all"/>
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
        <h1 class="underline">** LAPORAN JURNAL **</h1>Periode : <?php echo $this->fungsi->changeMonthNameID($bulan)." ".$tahun; ?>
    </div>
    <table class="tabelList2" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="text-align: left;" width="20">Tgl</th>
                <th style="text-align: left;" width="90">No Jurnal</th>
                <th style="text-align: left;">Keterangan</th>
                <th style="text-align: left;" width="150">Nama Perkiraan</th>
                <th width="100">Debet</th>
                <th width="100">Kredit</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $debet = 0;
            $kredit = 0;
            $query = newQuery("get_results","SELECT a.*, b.KodeRekening, b.NamaRekening, DATE_FORMAT(a.Tanggal,'%d') AS Tgl, c.NoJurnal FROM tb_jurnal_detail a, tb_master_rekening b, tb_jurnal c WHERE a.`IDRekening`=b.`IDRekening` AND a.IDJurnal=c.IDJurnal AND DATE_FORMAT(a.Tanggal,'%Y-%m')='2016-08' ORDER BY a.Tanggal ASC, a.IDJurnalDetail ASC");
            if($query){
                foreach($query as $data){
                    $dDebet = $data->Debet;
                    $dKredit = $data->Kredit;
                    $debet += $dDebet;
                    $kredit += $dKredit;
                    ?>
                    <tr>
                        <td><?php echo $data->Tgl; ?></td>
                        <td><?php echo $data->NoJurnal; ?></td>
                        <td><?php echo $data->Keterangan; ?></td>
                        <td><?php echo ucwords(strtolower($data->KodeRekening." ".$data->NamaRekening)); ?></td>
                        <td style="text-align: right;"><?php if($dDebet>=0) echo number_format($dDebet,2); else echo "(".number_format(abs($dDebet),2).")"; ?></td>
                        <td style="text-align: right;"><?php if($dKredit>=0) echo number_format($dKredit,2); else echo "(".number_format(abs($dKredit),2).")"; ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
            <tr>
                <td colspan="4"></td>
                <td style="text-align: right;"><strong><?php echo number_format($debet,2); ?></strong></td>
                <td style="text-align: right;"><strong><?php echo number_format($kredit,2); ?></strong></td>
            </tr>
        </tbody>
    </table>
    <script type="text/javascript">
        window.onload = function () { window.print(); }
    </script>
</body>
</html>