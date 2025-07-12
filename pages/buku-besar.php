<?php
$bulan = date("m");
$tahun = date("Y");

if($_POST['submit']=="1"){
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $jenis_transaksi = $_POST['jenis_transaksi'];
    $status = $_POST['status'];
    $url = PRSONPATH."print-buku-besar/?bulan=$bulan&tahun=$tahun&jenis_transaksi=$jenis_transaksi&status=$status";
    ?>
    <script>
        window.onload = function(){
             window.open("<?php echo $url; ?>",'winname','directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1200,height=600');
        }
    </script>
    <?php
} else if($_POST['submit']=="2"){
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $jenis_transaksi = $_POST['jenis_transaksi'];
    $status = $_POST['status'];
    $url = PRSONPATH."data-buku-besar/?bulan=$bulan&tahun=$tahun&jenis_transaksi=$jenis_transaksi&status=$status";
    header("Location: ".$url);
}
?>
<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
            <div class="col-sm-7">
                <h5>Buku Besar<small>Laporan buku besar transaksi</small></h5>
                <?php if(isset($notif)){ ?>
                    <div class="toast toast-<?php echo $notif['class']; ?>">
                        <button class="btn btn-clear float-right"></button>
                        <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
                    </div>
                <?php } ?>
                <div class="form-header">Filter data yang akan ditampilkan</div>
                <form method="POST" action="" class="form-horizontal form-bordered">
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label class="form-label" for="input-example-1">Bulan / Tahun : </label>
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
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label class="form-label" for="input-example-1">Rekening Perkiraan</label>
                        </div>
                        <div class="col-sm-9">
                            <select name="jenis_transaksi" id="jenis_transaksi" class="form-select" style="width: 100%;">
                                <option>Semua Rekening</option>
                                <?php
                                $query = newQuery("get_results","SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.Tipe='D' ORDER BY KodeRekening ASC");
                                if($query){
                                    foreach($query as $data){
                                        if($this->fungsi->authAccessRekening($data->Posisi,$data->KodeRekening)==1) {
                                            ?><option value="<?php echo $data->IDRekening; ?>" <?php if($rekening[$key]==$data->IDRekening) echo "selected"; ?>><?php echo $data->KodeRekening." - ".ucwords(strtolower($data->NamaRekening)); ?></option><?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label class="form-label" for="input-example-1"></label>
                        </div>
                        <div class="col-sm-9">
                            <label class="form-switch" style="margin-top:5px;">
                                <input type="checkbox" name="status" value="1" <?php if($status=="1") echo "checked"; ?>/>
                                <i class="form-icon"></i> Tampilkan hanya rekening yang memiliki saldo
                            </label>
                        </div>
                    </div>
                    <div class="form-footer">
                        <button type="submit" name="submit" value="2" class="btn btn-primary"><i class="fa fa-search"></i> Display</button>
                        <button type="submit" name="submit" value="1" class="btn btn-danger"><i class="fa fa-print"></i> Print</button>
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