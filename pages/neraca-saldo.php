<?php
$bulan = date("m");
$tahun = date("Y");
$tanggal = date("d/m/Y");
?>
<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
            <div class="col-sm-7">
                <h5>Neraca Saldo<small>Laporan neraca saldo transaksi</small></h5>
                <?php if(isset($notif)){ ?>
                    <div class="toast toast-<?php echo $notif['class']; ?>">
                        <button class="btn btn-clear float-right"></button>
                        <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
                    </div>
                <?php } ?>
                <div class="form-header">Filter data yang akan dicetak</div>
                <form method="GET" action="<?php echo PRSONPATH; ?>print-neraca-saldo/" class="form-horizontal form-bordered">
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label class="form-label" for="input-example-1">Periode : </label>
                        </div>
                        <div class="col-sm-5">
                            <input class="form-input input-calendar" id="tanggal" name="tanggal" type="text" value="<?php echo $tanggal; ?>"/>
                            <!-- <select name="bulan" class="form-select">
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
                            </select> -->
                        </div>
                    </div>
                    <div class="form-footer">
                        <button type="submit" name="submit" value="1" class="btn btn-danger"><i class="fa fa-print"></i> Print</button>
                    </div>
                </form>
            </div>
        </section>
    </section>
    <link href="<?php echo PRSONTEMPPATH; ?>css/select2.min.css" rel="stylesheet" />
    <script src="<?php echo PRSONTEMPPATH; ?>scripts/select2.min.js"></script>
    <script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/pikaday.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var pickerDefault = new Pikaday({
                field: document.getElementById('tanggal'),
                format: 'DD/MM/YYYY',
            });
        });
    </script>
<?php include "pages/footer.php"; ?>