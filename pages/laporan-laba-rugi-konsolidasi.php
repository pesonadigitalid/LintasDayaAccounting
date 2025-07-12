<?php
$tgl = $this->validasi->validInput($_GET['tanggal']);
$bulan = $this->validasi->validInput($_GET['bulan']);
$tahun = $this->validasi->validInput($_GET['tahun']);
$departement = $this->validasi->validInput($_GET['departement']);
$id_proyek = $this->validasi->validInput($_GET['id_proyek']);
$print_header = $this->validasi->validInput($_GET['print_header']);
$print_detail = $this->validasi->validInput($_GET['print_detail']);

if (!$_GET['filterbutton']) {
    $print_header = 1;
    $print_detail = 0;
}

$print_header = 1;
$print_detail = 0;

if ($_SESSION["locked"] != '') {
    $departement = $_SESSION["departement"];
}


$daritanggal = $this->validasi->validInput($_GET['daritanggal']);
$sampaitanggal = $this->validasi->validInput($_GET['sampaitanggal']);

$status = 1;
if ($daritanggal == "" && $sampaitanggal == "") {
    $bulan = date("m");
    $tahun = date("Y");
    $daritanggal = date('01/m/Y');
    $sampaitanggal  = date('t/m/Y');
    $daritanggalEN = date('Y-m-01');
    $sampaitanggalEN  = date('Y-m-t');
} else {
    $exp = explode("/", $daritanggal);
    $daritanggalEN = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
    $exp = explode("/", $sampaitanggal);
    $sampaitanggalEN  = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
}

$periode = $tahun . "-" . $bulan;
$condDate = " AND b.Tanggal BETWEEN '$daritanggalEN' AND '$sampaitanggalEN'";
$tanggal = $tahun . "-" . $bulan . "-01";
$tanggalID = "01/" . $bulan . "/" . $tahun;

if ($departement == '4') $idTipe = '4';
else $idTipe = '1';
if ($id_proyek != '') $condDate .= " AND b.IDProyek='$id_proyek' ";

if ($_GET['filterbutton']) {
    header("location: " . PRSONPATH . "print-laba-rugi-konsolidasi/?daritanggal=$daritanggal&sampaitanggal=$sampaitanggal&print_header=$print_header");
}
?>
<?php include "pages/header.php"; ?>
<section class="section section-body bg-grey">
    <section id="overview" class="grid-hero container">
        <div class="columns">
            <div class="column col-7">
                <h5>Laporan Laba Rugi Konsolidasi<small>Laporan Laba Rugi Konsolidasi</small></h5>
                <?php if (isset($notif)) { ?>
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
                            <option value="01" <?php if ($bulan == "01") echo "selected"; ?>>Januari</option>
                            <option value="02" <?php if ($bulan == "02") echo "selected"; ?>>Februari</option>
                            <option value="03" <?php if ($bulan == "03") echo "selected"; ?>>Maret</option>
                            <option value="04" <?php if ($bulan == "04") echo "selected"; ?>>April</option>
                            <option value="05" <?php if ($bulan == "05") echo "selected"; ?>>Mei</option>
                            <option value="06" <?php if ($bulan == "06") echo "selected"; ?>>Juni</option>
                            <option value="07" <?php if ($bulan == "07") echo "selected"; ?>>Juli</option>
                            <option value="08" <?php if ($bulan == "08") echo "selected"; ?>>Agustus</option>
                            <option value="09" <?php if ($bulan == "09") echo "selected"; ?>>September</option>
                            <option value="10" <?php if ($bulan == "10") echo "selected"; ?>>Oktober</option>
                            <option value="11" <?php if ($bulan == "11") echo "selected"; ?>>November</option>
                            <option value="12" <?php if ($bulan == "12") echo "selected"; ?>>Desember</option>
                        </select>
                        <select name="tahun" class="form-select">
                            <?php for ($i = 2012; $i <= date("Y"); $i++) { ?>
                            <option value="<?php echo $i; ?>" <?php if ($tahun == $i) echo "selected"; ?>><?php echo $i; ?></option>
                            <?php } ?>
                        </select>
                    </div> -->
                        <div class="column col-4">
                            <input class="form-input input-calendar" id="daritanggal" name="daritanggal" type="text" value="<?php echo $daritanggal; ?>" />
                        </div>
                        <div class="column col-4">
                            <input class="form-input input-calendar" id="sampaitanggal" name="sampaitanggal" type="text" value="<?php echo $sampaitanggal; ?>" />
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column col-2"></div>
                        <div class="column col-3">
                            <button type="submit" name="filterbutton" value="1" class="btn btn-success">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="column col-5" style="padding-top: 160px;">
            </div>
        </div>
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