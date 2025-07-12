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

$periode = $tahun."-".$bulan;
$condDate = " AND b.Tanggal BETWEEN '$daritanggalEN' AND '$sampaitanggalEN'";
$tanggal = $tahun."-".$bulan."-01";
$tanggalID = "01/".$bulan."/".$tahun;

if($departement=='4') $idTipe='4'; else $idTipe='1';
?>
<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
             <div class="columns">
             <div class="column col-7">
            <h5>Laporan Laba Rugi Departement<small>Laporan Laba Rugi Departement</small></h5>
            <?php if(isset($notif)){ ?>
                <div class="toast toast-<?php echo $notif['class']; ?>">
                    <button class="btn btn-clear float-right"></button>
                    <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
                </div>
            <?php } ?>
            <form method="GET" action="" class="form-horizontal form-bordered" style="padding: 0px 0;border-top:solid 1px #d3d3d3;border-bottom:solid 1px #d3d3d3;">
                <div class="columns">
                    <div class="column col-2">
                        <label class="form-label" for="input-example-1">Periode : </label>
                    </div>
                    <!-- <div class="column col-4">
                        <select name="bulan" class="form-select">
                            <option value="01" <?php if($bulan=="01") echo "selected"; ?>>Januari</option>
                            <option value="02" <?php if($bulan=="02") echo "selected"; ?>>Februari</option>
                            <option value="03" <?php if($bulan=="03") echo "selected"; ?>>Maret</option>
                            <option value="04" <?php if($bulan=="04") echo "selected"; ?>>April</option>
                            <option value="05" <?php if($bulan=="05") echo "selected"; ?>>Mei</option>
                            <option value="06" <?php if($bulan=="06") echo "selected"; ?>>Juni</option>
                            <option value="07" <?php if($bulan=="07") echo "selected"; ?>>Juli</option>
                            <option value="08" <?php if($bulan=="08") echo "selected"; ?>>Agustus</option>
                            <option value="09" <?php if($bulan=="09") echo "selected"; ?>>September</option>
                            <option value="10" <?php if($bulan=="10") echo "selected"; ?>>Oktober</option>
                            <option value="11" <?php if($bulan=="11") echo "selected"; ?>>November</option>
                            <option value="12" <?php if($bulan=="12") echo "selected"; ?>>Desember</option>
                        </select>
                        <select name="tahun" class="form-select">
                            <?php for($i=2012;$i<=date("Y");$i++){ ?>
                            <option value="<?php echo $i; ?>" <?php if($tahun==$i) echo "selected"; ?>><?php echo $i; ?></option>
                            <?php } ?>
                        </select>
                    </div> -->
                    <div class="column col-3">
                        <input class="form-input input-calendar" id="daritanggal" name="daritanggal" type="text" value="<?php echo $daritanggal; ?>"/>
                    </div>
                    <div class="column col-3">
                        <input class="form-input input-calendar" id="sampaitanggal" name="sampaitanggal" type="text" value="<?php echo $sampaitanggal; ?>"/>
                    </div>
                    <div class="column col-3">
                        <select name="departement" class="form-select" style="width: 100%;">
                            <option value="0">Umum</option>
                            <?php
                            $query = newQuery("get_results","SELECT * FROM tb_departement ORDER BY NamaDepartement ASC");
                            if($query){
                                foreach($query as $data){
                                    ?><option value="<?php echo $data->IDDepartement; ?>" <?php if($departement==$data->IDDepartement) echo "selected"; ?>><?php echo $data->NamaDepartement; ?></option><?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="columns">
                    <div class="column col-2">
                    </div>
                    <div class="column col-3" style="padding-top: 0;">
                        <button type="submit" name="filterbutton" value="1" class="btn btn-success">Filter</button>
                        <?php if($departement!=""){ ?>
                        <!-- <a href="<?php echo PRSONPATH."print-laba-rugi-departement/?bulan=$bulan&tahun=$tahun&departement=$departement"; ?>" target="_blank" class="btn btn-danger"><i class="fa fa-print"></i> Print</a> -->
                        <a href="<?php echo PRSONPATH."print-cash-flow-departement/?daritanggal=$daritanggal&sampaitanggal=$sampaitanggal&departement=$departement"; ?>" target="_blank" class="btn btn-danger"><i class="fa fa-print"></i> Print</a>
                        <?php } ?>
                    </div>
                </div>
            </form>
            <?php if($departement!=""){ ?>
            <table class="table report-table">
                <tr>
                    <th colspan="2"><?php echo $dataDepartement->NamaDepartement; ?></th>
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

                        $tBiayaLain = newQuery("get_var","SELECT SUM(a.Debet) FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='".$proyek->IDProyek."' $condDate AND IDRekening IN (SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent='101' UNION SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent IN (SELECT IDRekening FROM `tb_master_rekening` WHERE IDParent='101'))");
                        $total = $tPendapatan - $tHPP - $tBiaya - $tBiayaLain;
                        $biaya += $total;
                        ?>
                        <tr>
                            <td><strong><?php echo $namaProyek; ?></strong></td>
                            <td class="saldo"><strong><?php echo number_format($total,2); ?></strong></td>
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
                                    <td class="deep1"><?php echo "<strong>".$dPendapatan->TanggalID."</strong> &nbsp;&nbsp;&nbsp;&nbsp;".$dPendapatan->Keterangan.$ppnNote; ?></td>
                                    <td class="saldo"><?php echo $sPendapatan; ?></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        <tr>
                            <td colspan="2"></td>
                        </tr>
                        <?php
                    }
                }
                ?>
                <tr>
                    <td class="deep1" style="text-align: right;"><strong>TOTAL CASH FLOW DEPARTEMENT : </strong></td>
                    <td class="saldo"><strong><?php echo number_format($biaya,2); ?></strong></td>
                </tr>
            </table>
            <?php } ?>
        </section>
    </section>
    <script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/pikaday.js"></script>
    <script type="text/javascript">
        var pickerDefault = new Pikaday({
            field: document.getElementById('daritanggal'),
            format: 'DD/MM/YYYY',
        });
        var pickerDefault = new Pikaday({
            field: document.getElementById('sampaitanggal'),
            format: 'DD/MM/YYYY',
        });
    </script>
<?php include "pages/footer.php"; ?>