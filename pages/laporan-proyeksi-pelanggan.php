<?php
$bulan = "01";
$bulan2 = "12";
$tahun = date("Y");

if($_POST['filterbutton']){
    $bulan = $this->validasi->validInput($_POST['bulan']);
    $bulan2 = $this->validasi->validInput($_POST['bulan2']);
    $tahun = $this->validasi->validInput($_POST['tahun']);
    $pelanggan = $this->validasi->validInput($_POST['pelanggan']);
    $tipe = $this->validasi->validInput($_POST['tipe']);

    header("location: ".PRSONPATH."print-proyeksi-pelanggan/?tahun=$tahun&pelanggan=$pelanggan&tipe=$tipe");
}
?>
<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
             <div class="columns">
             <div class="column col-7">
            <h5>Laporan Laba Rugi Periode<small>Laporan Laba Rugi Periode</small></h5>
            <?php if(isset($notif)){ ?>
                <div class="toast toast-<?php echo $notif['class']; ?>">
                    <button class="btn btn-clear float-right"></button>
                    <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
                </div>
            <?php } ?>
            <form method="POST" action="" class="form-horizontal form-bordered" style="padding: 0px 0;border-top:solid 1px #d3d3d3;border-bottom:solid 1px #d3d3d3;">
                <div class="columns">
                    <div class="column col-2">
                        <label class="form-label" for="input-example-1">Periode : </label>
                    </div>
                    <div class="column col-2">
                       <select name="tahun" class="form-select" style="width: 100%">
                            <?php for($i=2012;$i<=date("Y");$i++){ ?>
                            <option value="<?php echo $i; ?>" <?php if($tahun==$i) echo "selected"; ?>><?php echo $i; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="column col-4">
                        <select name="pelanggan" id="pelanggan" class="form-select" style="width: 100%;" <?php echo $_SESSION["locked"]; ?>>
                            <?php
                            $query = newQuery("get_results","SELECT * FROM tb_pelanggan WHERE Kategori!='4' ORDER BY NamaPelanggan ASC");
                            if($query){
                                foreach($query as $data){
                                    ?><option value="<?php echo $data->IDPelanggan; ?>"><?php echo $data->NamaPelanggan; ?></option><?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="column col-4">
                        <select name="tipe" id="tipe" class="form-select" style="width: 100%;">
                            <option value="1">Rekap</option>
                            <option value="2">Detail</option>
                        </select>
                    </div>
                    <div class="column col-3">
                        <button type="submit" name="filterbutton" value="1" class="btn btn-success">View Report</button>
                    </div>
                </div>
            </form>
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