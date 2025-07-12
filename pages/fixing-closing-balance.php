<?php
$bulan = date("m");
$tahun = date("Y");
if($_POST['submit']){
    $bulan = $this->validasi->validInput($_GET['bulan']);
    $tahun = $this->validasi->validInput($_GET['tahun']);
    $tanggal = $tahun."-".$bulan."-01";
    $periode = $tahun."-".$bulan;
    $query=newQuery("get_results","SELECT DISTINCT(IDRekening) FROM tb_jurnal_detail");
    if($query){
        foreach($query as $data){
            $jurnalTerakhir = newQuery("get_row","SELECT * FROM tb_jurnal_detail WHERE IDRekening='".$data->IDRekening."' AND Tanggal<='$tanggal' ORDER BY Tanggal DESC, IDJurnalDetail DESC");
            $dataRekening = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDRekening='".$data->IDRekening."'");
            if(!$jurnalTerakhir){
                $closing = $dataRekening->SaldoAwal;
            } else {
                $closing = $jurnalTerakhir->Closing;
            }
            $qRest = newQuery("get_results","SELECT * FROM tb_jurnal_detail WHERE IDRekening='".$data->IDRekening."' ORDER BY Tanggal ASC, IDJurnalDetail ASC");
            if($qRest){
                foreach($qRest as $dRest){
                    $diff = $dRest->Debet-$dRest->Kredit;
                    $closing = $closing+$diff;
                    newQuery("query","UPDATE tb_jurnal_detail SET Closing='$closing' WHERE IDJurnalDetail='".$dRest->IDJurnalDetail."'");
                }
            }
        }
    }
    $notif = array("class"=>"success","msg"=>"Closing Balance berhasing disesuaikan!");
}
?>
<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
            <div class="col-sm-7">
                <h5>Fixing Closing Balance<small>Gunakan tools ini untuk membenahi closing balance dari detail jurnal</small></h5>
                <?php if(isset($notif)){ ?>
                    <div class="toast toast-<?php echo $notif['class']; ?>">
                        <button class="btn btn-clear float-right"></button>
                        <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
                    </div>
                <?php } ?>
                <div class="form-header">Filter data yang akan dibenahi nilai closingnya</div>
                <form method="POST" action="" class="form-horizontal form-bordered">
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label class="form-label" for="input-example-1">Dari Periode : </label>
                        </div>
                        <div class="col-sm-9">
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
                    </div>
                    <div class="form-footer">
                        <button type="submit" name="submit" value="1" class="btn btn-danger"><i class="fa fa-tools"></i> Fixing</button>
                    </div>
                </form>
            </div>
        </section>
    </section>
    <link href="<?php echo PRSONTEMPPATH; ?>css/select2.min.css" rel="stylesheet" />
    <script src="<?php echo PRSONTEMPPATH; ?>scripts/select2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $("#jenis_transaksi").select2();
        });
    </script>
<?php include "pages/footer.php"; ?>