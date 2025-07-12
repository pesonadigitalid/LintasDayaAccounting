<?php
$tgl = $this->validasi->validInput($_GET['tanggal']);
$bulan = $this->validasi->validInput($_GET['bulan']);
$tahun = $this->validasi->validInput($_GET['tahun']);
$departement = $this->validasi->validInput($_GET['departement']);

$status = 1;
if($bulan=="" && $tahun==""){
    $bulan = date("m");
    $tahun = date("Y");
}

//SELECT c.`KodeProyek`, c.`Tahun`, c.`NamaProyek`, SUM(b.`Debet`) AS Pendapatan FROM tb_jurnal a, tb_jurnal_detail b, tb_proyek c WHERE a.IDJurnal=b.IDJurnal AND a.IDProyek!="" AND a.Tipe='1' AND b.`Debet`>0 AND a.`IDProyek`=c.`IDProyek` GROUP BY a.`IDProyek`

$periode = $tahun."-".$bulan;
$condDate = "DATE_FORMAT(Tanggal,'%Y-%m')='$periode'";
$tanggal = $tahun."-".$bulan."-01";
$tanggalID = "01/".$bulan."/".$tahun;
?>
<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
             <div class="columns">
             <div class="column col-7">
            <h5>Laporan Pendapatan</h5>
            <?php if(isset($notif)){ ?>
                <div class="toast toast-<?php echo $notif['class']; ?>">
                    <button class="btn btn-clear float-right"></button>
                    <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
                </div>
            <?php } ?>
            <form method="GET" action="" class="form-horizontal form-bordered" style="padding: 0px 0;border-top:solid 1px #d3d3d3;border-bottom:solid 1px #d3d3d3;">
                <div class="columns">
                    <div class="column col-2">
                        <label class="form-label" for="input-example-1">Bulan :</label>
                    </div>
                    <div class="column col-5">
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
                    </div>
                    <div class="column col-3">
                        <button type="submit" name="filterbutton" value="1" class="btn btn-success">Filter</button>
                    </div>
                </div>
            </form>
            <table class="table report-table">
                <tr>
                    <th colspan="2">PENDAPATAN PROYEK PPN</th>
                </tr>
                <?php
                $totalPendapatan = 0;
                $totalPendapatanPPN = 0;
                $totalPendapatanNONPPN = 0;
                $query = newQuery("get_results","SELECT c.`KodeProyek`, c.`Tahun`, c.`NamaProyek`, SUM(b.`Debet`) AS Pendapatan FROM tb_jurnal a, tb_jurnal_detail b, tb_proyek c WHERE a.IDJurnal=b.IDJurnal AND a.IDProyek!='' AND c.PPN>0 AND a.Tipe='1' AND b.`Debet`>0 AND a.`IDProyek`=c.`IDProyek` AND DATE_FORMAT(a.Tanggal,'%Y-%m')='$tahun-$bulan' GROUP BY a.`IDProyek`");
                if($query){
                    foreach($query as $data){
                        $totalPendapatan += $data->Pendapatan;
                        $totalPendapatanPPN += $data->Pendapatan;
                        ?>
                        <tr>
                            <td class="deep1"><?php echo $data->KodeProyek."/".$data->Tahun."/".$data->NamaProyek; ?></td>
                            <td class="saldo"><strong>Rp. <?php echo number_format($data->Pendapatan,2); ?></strong></td>
                        </tr>
                        <?php
                    }
                }
                ?>
                <tr>
                    <td class="deep1" style="text-align: right;"><strong>TOTAL PENDAPATAN : </strong></td>
                    <td class="saldo"><strong>Rp. <?php echo number_format($totalPendapatanPPN,2); ?></strong></td>
                </tr>
                <tr>
                    <th colspan="2">PENDAPATAN PROYEK NON-PPN</th>
                </tr>
                <?php
                $query = newQuery("get_results","SELECT c.`KodeProyek`, c.`Tahun`, c.`NamaProyek`, SUM(b.`Debet`) AS Pendapatan FROM tb_jurnal a, tb_jurnal_detail b, tb_proyek c WHERE a.IDJurnal=b.IDJurnal AND a.IDProyek!='' AND c.PPN='0' AND a.Tipe='1' AND b.`Debet`>0 AND a.`IDProyek`=c.`IDProyek` AND DATE_FORMAT(a.Tanggal,'%Y-%m')='$tahun-$bulan' GROUP BY a.`IDProyek`");
                if($query){
                    foreach($query as $data){
                        $totalPendapatan += $data->Pendapatan;
                        $totalPendapatanPPN += $data->Pendapatan;
                        ?>
                        <tr>
                            <td class="deep1"><?php echo $data->KodeProyek."/".$data->Tahun."/".$data->NamaProyek; ?></td>
                            <td class="saldo"><strong>Rp. <?php echo number_format($data->Pendapatan,2); ?></strong></td>
                        </tr>
                        <?php
                    }
                }
                ?>
                <tr>
                    <td class="deep1" style="text-align: right;"><strong>TOTAL PENDAPATAN : </strong></td>
                    <td class="saldo"><strong>Rp. <?php echo number_format($biaya,2); ?></strong></td>
                </tr>
                <tr>
                    <td class="deep1" style="text-align: right;"><strong>GRAND TOTAL : </strong></td>
                    <td class="saldo"><strong>Rp. <?php echo number_format($totalPendapatan,2); ?></strong></td>
                </tr>
            </table>
            </div>
        </section>
    </section>
<?php include "pages/footer.php"; ?>