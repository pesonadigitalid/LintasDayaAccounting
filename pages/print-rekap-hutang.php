<?php
$departement = $this->validasi->validInput($_GET['departement']);
$supplier = $this->validasi->validInput($_GET['supplier']);
$no_po = $this->validasi->validInput($_GET['no_po']);
$proyek = $this->validasi->validInput($_GET['proyek']);
$status = $this->validasi->validInput($_GET['status']);
$dariTanggal = $this->validasi->validInput($_GET['dariTanggal']);
$sampaiTanggal = $this->validasi->validInput($_GET['sampaiTanggal']);

$cond = "";

if($departement!=""){
    $cond = " AND IDProyek IN (SELECT IDProyek FROM tb_proyek WHERE IDDepartement='$departement') ";
}
if($supplier!=""){
    $cond = " AND IDSupplier='$supplier' ";
}
if($proyek!=""){
    $cond = " AND IDProyek='$proyek' ";
}

if($status!=""){
    $cond2 = " Sisa<=0 ";
} else {
    $cond2 = " Sisa>0 ";
}

if($dariTanggal!="") {
    $exp = explode("/",$dariTanggal);
    $dariTanggalEN = $exp[2]."-".$exp[1]."-".$exp[0];
    $tgl = $exp[0];
    $bulan = $exp[1];
    $tahun = $exp[2];
    if($sampaiTanggal!=""){
        $exp = explode("/",$sampaiTanggal);
        $sampaiTanggalEN = $exp[2]."-".$exp[1]."-".$exp[0];
        $cond .= " AND Tanggal BETWEEN '$dariTanggalEN' AND '$sampaiTanggalEN' ";
        $subtitle = "Periode: $dariTanggal s/d $sampaiTanggal";
    } else {
        $cond .= " AND Tanggal = '$dariTanggalEN'";
        $subtitle = "Periode: $dariTanggal";
    }
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
        <h1 class="underline">** REKAP DATA HUTANG **</h1><?php echo $subtitle; ?>
    </div>
    <table class="tabelList2" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="70">No. PO</th>
                <th width="70">Proyek</th>
                <th>Nama Supplier</th>
                <th width="70">Sub Total</th>
                <th width="90">PPN</th>
                <th width="70">Grand Total</th>
                <th width="70">Total Bayar</th>
                <th width="70">Sisa Hutang</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if($no_po!=""){
                $query = newQuery("get_results","SELECT * FROM tb_po WHERE NoPo='$no_po' AND IsLD='1' ORDER BY NoPo ASC");
            } else {
                $query = newQuery("get_results","SELECT * FROM tb_po WHERE $cond2 $cond AND IsLD='1' ORDER BY NoPo ASC");
            }
            
            if($query){
                $total = 0;
                $ppn = 0;
                $grandtotal = 0;
                $terbayar = 0;
                $hutang = 0;
                foreach($query as $data){
                    $total+=$data->Total2;
                    $ppn+=$data->PPN;
                    $grandtotal+=$data->GrandTotal;
                    $terbayar+=$data->TotalPembayaran;
                    $hutang+=$data->Sisa;
                    
                    $supplier = newQuery("get_row","SELECT * FROM tb_supplier WHERE IDSupplier='".$data->IDSupplier."'");
                    $proyek = newQuery("get_row","SELECT * FROM tb_proyek WHERE IDProyek='".$data->IDProyek."'");
                    if($proyek) $proyek=$proyek->KodeProyek."/".$proyek->Tahun;
                    else $proyek="UMUM";
                    ?>
                    <tr>
                        <td><?php echo $data->NoPo; ?></td>
                        <td style="text-align: center;"><?php echo $proyek; ?></td>
                        <td><?php echo $supplier->NamaPerusahaan; ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->Total2,2); ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->PPN,2); ?> (<?php echo $data->PPNPersen; ?> %)</td>
                        <td style="text-align: right;"><?php echo number_format($data->GrandTotal,2); ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->TotalPembayaran,2); ?></td>
                        <td style="text-align: right;"><strong><?php echo number_format($data->Sisa,2); ?></strong></td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>TOTAL : </strong></td>
                    <td style="text-align: right;"><strong><?php echo number_format($total,2); ?></strong></td>
                    <td style="text-align: right;"><strong><?php echo number_format($ppn,2); ?></strong></td>
                    <td style="text-align: right;"><strong><?php echo number_format($grandtotal,2); ?></strong></td>
                    <td style="text-align: right;"><strong><?php echo number_format($terbayar,2); ?></strong></td>
                    <td style="text-align: right;"><strong><?php echo number_format($hutang,2); ?></strong></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <script type="text/javascript">
        window.onload = function () { window.print(); }
    </script>
</body>
</html>