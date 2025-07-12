<?php
$tgl = $this->validasi->validInput($_GET['tanggal']);
$bulan = $this->validasi->validInput($_GET['bulan']);
$tahun = $this->validasi->validInput($_GET['tahun']);
$departement = $this->validasi->validInput($_GET['departement']);
$dataDepartement = newQuery("get_row","SELECT * FROM tb_departement WHERE IDDepartement='$departement'");
$daritanggal = $this->validasi->validInput($_GET['daritanggal']);
$sampaitanggal = $this->validasi->validInput($_GET['sampaitanggal']);

$status = 1;
if($daritanggal=="" && $sampaitanggal==""){
    $bulan = date("m");
    $tahun = date("Y");
    $daritanggal = date('01/m/Y'); 
    $sampaitanggal  = date('t/m/Y');
    $daritanggalEN = date('Y-m-01'); 
    $sampaitanggalEN  = date('Y-m-t');
} else {
    $exp = explode("/",$daritanggal);
    $daritanggalEN = $exp[2]."-".$exp[1]."-".$exp[0]; 
    $exp = explode("/",$sampaitanggal);
    $sampaitanggalEN  = $exp[2]."-".$exp[1]."-".$exp[0]; 
}

$tanggalDisplay = $daritanggal." s/d ".$sampaitanggal;

$periode = $tahun."-".$bulan;
$condDate = " AND b.Tanggal BETWEEN '$daritanggalEN' AND '$sampaitanggalEN'";
$tanggal = $tahun."-".$bulan."-01";
$tanggalID = "01/".$bulan."/".$tahun;

if($departement=='4') $idTipe='4'; else $idTipe='1';
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
</head>

<body class="center">
    <h1 class="blue">Cash Flow Departement</h1>
    <h3 class="red">Periode: <?php echo $tanggalDisplay; ?></h3>
    <table class="tbLabaRugi" style="max-width: 500px">
        <tr>
            <td width="400"></td>
            <td width="100" style="text-align: right;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;" class="red">Saldo</td>
        </tr>
        <tr>
            <td class="labelHeader"></td>
            <td style="text-align: right;">Rupiah</td>
        </tr>
        <?php
        $query = newQuery("get_results","SELECT b.IDProyek FROM tb_jurnal b WHERE b.IDProyek IS NOT NULL $condDate AND b.IDDepartement='$departement' GROUP BY b.IDProyek");
        if($query){
            foreach($query as $data){
                $proyek = newQuery("get_row","SELECT * FROM tb_proyek WHERE IDProyek='".$data->IDProyek."'");

                if($data->IDProyek==0)
                    $namaProyek = "UMUM";
                else
                    $namaProyek = $proyek->KodeProyek." / ".$proyek->Tahun." / ".$proyek->NamaProyek;
                $tPendapatan = newQuery("get_var","SELECT SUM(a.Kredit) FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='".$proyek->IDProyek."' $condDate AND IDRekening IN (SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent='63' UNION SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent IN (SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent='63'))");

                if($proyek->PPNPersen>0){
                    $tPendapatan = $tPendapatan - (10/100 * $tPendapatan);
                }

                $tHPP = newQuery("get_var","SELECT SUM(a.Debet) FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='".$proyek->IDProyek."' $condDate AND IDRekening IN (SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent='70' UNION SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent IN (SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent='70'))");

                $tBiaya = newQuery("get_var","SELECT SUM(a.Debet) FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='".$proyek->IDProyek."' $condDate AND IDRekening IN (SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent='73' UNION SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent IN (SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent='73'))");
                $total = $tPendapatan - $tHPP - $tBiaya;
                $biaya += $total;
                ?>
                <tr>
                    <td class="labelHeader"><?php echo $namaProyek; ?></td>
                    <td style="text-align: right;"><strong><?php echo number_format($total,2); ?></strong></td>
                </tr>
                <?php
                $qPendapatan = newQuery("get_results","SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='".$proyek->IDProyek."' $condDate AND IDRekening IN (
                    SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent='63' 
                    UNION SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent IN (SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent='63')
                    UNION SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent='70' 
                    UNION SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent IN (SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent='70') 
                    UNION SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent='73' 
                    UNION SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent IN (SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent='73')
                    UNION SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent='101' 
                    UNION SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent IN (SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent='101')
                    )
                    ORDER BY a.Tanggal ASC");
                if($qPendapatan){
                    foreach($qPendapatan as $dPendapatan){
                        if($dPendapatan->Kredit>0){
                            $status = "Pendapatan";
                            $nominal = $dPendapatan->Kredit;
                        } else {
                            $status = "Biaya";
                            $nominal = $dPendapatan->Debet;
                        }

                        if($proyek->PPNPersen>0 && $status=="Pendapatan"){
                            $sPendapatan = "(".number_format($nominal - (10/100 * $nominal),2).")";
                            $ppnNote = " (PPN 10%) ";
                        } else {
                            $sPendapatan = "(".number_format($nominal,2).")";
                            $ppnNote = "";
                        }
                        ?>
                        <tr>
                            <td class="labelHeader2 deep1" style="font-weight: normal;"><?php echo "<strong>".$dPendapatan->TanggalID."</strong> &nbsp;&nbsp;&nbsp;&nbsp;".$dPendapatan->Keterangan.$ppnNote; ?></td>
                            <td style="text-align: right;"><?php echo $sPendapatan; ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
                <
                <tr>
                    <td class="labelHeader spacer"></td>
                    <td style="text-align: right;"></td>
                </tr>
                <?php
            }
        }
        ?>
        <tr>
            <td class="labelHeader spacer"></td>
            <td style="text-align: right;"></td>
        </tr>
        <tr>
            <td class="labelHeader">Total Cash Flow Departement</td>
            <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($biaya,2); ?></td>
        </tr>
    </table><br><br>

    <script type="text/javascript">
        setTimeout(function(){
            window.print();
        },1500);
    </script>
</body>
</html>