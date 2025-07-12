<?php
$tanggal = date("d/m/Y");

if ($_POST['filterbutton']) {
    $proyek = implode(",", $this->validasi->validInput($_POST['proyek']));
    $tipe = $this->validasi->validInput($_POST['tipe']);
    $tanggal = $this->validasi->validInput($_POST['tanggal']);
    if ($tanggal == "") $tanggal = date("Y-m-d");
    else {
        $exp = explode("/", $tanggal);
        $tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
    }

    // var_dump($proyek);

    header("location: " . PRSONPATH . "print-proyeksi-projects/?tahun=$tahun&proyek=$proyek&tipe=$tipe&tanggal=$tanggal");
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
                        <div class="column col-4">
                            <select name="tipe" id="tipe" class="form-select" style="width: 100%;">
                                <option value="1">Rekap</option>
                                <option value="2">Detail</option>
                            </select>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column col-2">
                            <label class="form-label" for="input-example-1">Proyek : </label>
                        </div>
                        <div class="column col-10">
                            <select name="proyek[]" id="proyek" class="form-select" style="width: 100%;">
                                <?php
                                $query = newQuery("get_results", "SELECT * FROM tb_proyek ORDER BY Tahun, KodeProyek ASC");
                                if ($query) {
                                    foreach ($query as $data) {
                                ?>
                                        <option value="<?php echo $data->IDProyek; ?>" <?php if ($proyek == $data->IDProyek) echo "selected"; ?>><?php echo $data->KodeProyek . "/" . $data->Tahun . " - " . $data->NamaProyek; ?></option>
                                <?php
                                    }
                                }
                                ?>
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
<link href="<?php echo PRSONTEMPPATH; ?>css/select2.min.css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/moment.min.js"></script>
<script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/pikaday.js"></script>
<script src="<?php echo PRSONTEMPPATH; ?>scripts/select2.min.js"></script>
<script type="text/javascript">
    var pickerDefault = new Pikaday({
        field: document.getElementById('tanggal'),
        format: 'DD/MM/YYYY',
    });
    $(document).ready(function() {
        $('#proyek').val('');
        $("#proyek").select2({
            multiple: true
        });
    });
</script>
<?php include "pages/footer.php"; ?>