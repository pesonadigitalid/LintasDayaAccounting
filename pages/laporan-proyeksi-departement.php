<?php
$bulan = "01";
$bulan2 = "12";
$tahun = date("Y");
$tanggal = date("d/m/Y");

if ($_SESSION["jabatan"] != 3) {
    $departement = "MMS";
}

if ($_POST['filterbutton']) {
    $bulan = $this->validasi->validInput($_POST['bulan']);
    $bulan2 = $this->validasi->validInput($_POST['bulan2']);
    $tahun = $this->validasi->validInput($_POST['tahun']);
    $departement = $this->validasi->validInput($_POST['departement']);
    $tipe = $this->validasi->validInput($_POST['tipe']);
    $tanggal = $this->validasi->validInput($_POST['tanggal']);
    if ($tanggal == "") $tanggal = date("Y-m-d");
    else {
        $exp = explode("/", $tanggal);
        $tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
    }

    if ($_SESSION["jabatan"] != 3) {
        $departement = "MMS";
    }

    if ($departement == "MMS")
        header("location: " . PRSONPATH . "print-proyeksi-departement-mms/?tahun=$tahun&departement=4&tipe=$tipe&tanggal=$tanggal");
    else
        header("location: " . PRSONPATH . "print-proyeksi-departement/?tahun=$tahun&departement=$departement&tipe=$tipe&tanggal=$tanggal");
}
?>
<?php include "pages/header.php"; ?>
<section class="section section-body bg-grey">
    <section id="overview" class="grid-hero container">
        <div class="columns">
            <div class="column col-7">
                <h5>Proyeksi Proyek Per-Departement Per-Periode<small>Proyeksi Proyek Per-Departement Per-Periode</small></h5>
                <?php if (isset($notif)) { ?>
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
                                <?php for ($i = 2012; $i <= date("Y"); $i++) { ?>
                                    <option value="<?php echo $i; ?>" <?php if ($tahun == $i) echo "selected"; ?>><?php echo $i; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="column col-4">
                            <select name="departement" id="departement" class="form-select" style="width: 100%;" <?php echo $_SESSION["locked"]; ?>>
                                <option value="0">Departement Umum</option>
                                <option value="1">Departement Konstruksi</option>
                                <option value="3">Departement Maintenance</option>
                                <option value="5">Departement Design</option>
                                <option value="MMS" <?php if ($departement == "MMS") echo "selected"; ?>>Departement MMS</option>
                            </select>
                        </div>
                        <div class="column col-4">
                            <select name="tipe" id="tipe" class="form-select" style="width: 100%;">
                                <option value="1">Rekap</option>
                                <option value="2">Detail</option>
                            </select>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column col-2">
                            <label class="form-label" for="input-example-1">Sampai Tgl. : </label>
                        </div>
                        <div class="column col-4">
                            <input class="form-input input-calendar" id="tanggal" name="tanggal" type="text" value="<?php echo $tanggal; ?>" />
                        </div>
                        <div class="column col-3">
                            <button type="submit" name="filterbutton" value="1" class="btn btn-success">View Report</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</section>
<script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/moment.min.js"></script>
<script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/pikaday.js"></script>
<script type="text/javascript">
    var pickerDefault = new Pikaday({
        field: document.getElementById('tanggal'),
        format: 'DD/MM/YYYY',
    });
</script>
<?php include "pages/footer.php"; ?>