<?php
$bulan = "01";
$bulan2 = "12";
$tahun = date("Y");

if($_POST['filterbutton']){
    $bulan = $this->validasi->validInput($_POST['bulan']);
    $bulan2 = $this->validasi->validInput($_POST['bulan2']);
    $tahun = $this->validasi->validInput($_POST['tahun']);
    $departement = $this->validasi->validInput($_POST['departement']);

    header("location: ".PRSONPATH."print-laba-rugi-periode/?bulan=$bulan&bulan2=$bulan2&tahun=$tahun&departement=$departement");
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
                    <div class="column col-3">
                        <select name="bulan" class="form-select" style="width: 100%">
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
                    </div>
                    <div class="column col-3">
                        <select name="bulan2" class="form-select" style="width: 100%">
                            <option value="01" <?php if($bulan2=="01") echo "selected"; ?>>Januari</option>
                            <option value="02" <?php if($bulan2=="02") echo "selected"; ?>>Februari</option>
                            <option value="03" <?php if($bulan2=="03") echo "selected"; ?>>Maret</option>
                            <option value="04" <?php if($bulan2=="04") echo "selected"; ?>>April</option>
                            <option value="05" <?php if($bulan2=="05") echo "selected"; ?>>Mei</option>
                            <option value="06" <?php if($bulan2=="06") echo "selected"; ?>>Juni</option>
                            <option value="07" <?php if($bulan2=="07") echo "selected"; ?>>Juli</option>
                            <option value="08" <?php if($bulan2=="08") echo "selected"; ?>>Agustus</option>
                            <option value="09" <?php if($bulan2=="09") echo "selected"; ?>>September</option>
                            <option value="10" <?php if($bulan2=="10") echo "selected"; ?>>Oktober</option>
                            <option value="11" <?php if($bulan2=="11") echo "selected"; ?>>November</option>
                            <option value="12" <?php if($bulan2=="12") echo "selected"; ?>>Desember</option>
                        </select>
                    </div>
                    <div class="column col-2">
                       <select name="tahun" class="form-select" style="width: 100%">
                            <?php for($i=2012;$i<=date("Y");$i++){ ?>
                            <option value="<?php echo $i; ?>" <?php if($tahun==$i) echo "selected"; ?>><?php echo $i; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="columns">
                    <div class="column col-2"></div>
                    <div class="column col-3">
                        <select name="departement" id="departement" class="form-select" style="width: 100%;" <?php echo $_SESSION["locked"]; ?>>
                            <option value="">Semua Departement</option>
                            <option value="0">Departement Umum</option>
                            <option value="1">Departement Konstruksi</option>
                            <option value="3">Departement Maintenance</option>
                            <option value="5">Departement Design</option>
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