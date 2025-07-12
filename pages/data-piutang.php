<?php
$departement = $this->validasi->validInput($_GET['departement']);
$periode = $this->validasi->validInput($_GET['periode']);
$sampaitanggal = $this->validasi->validInput($_GET['sampaitanggal']);
if ($departement != "") {
    $cond = " AND IDDepartement='$departement'";
}
if ($periode != "") {
    $cond .= " AND Tahun='$periode'";
}
if ($sampaitanggal != "") {
    $exp = explode("/", $sampaitanggal);
    $sampaitanggalEN = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
    $cond2 = " AND Tanggal<='$sampaitanggalEN'";
} else {
    $sampaitanggal = date("d/m/Y");
    $cond2 = " AND Tanggal<='" . date("Y-m-d") . "'";
}

$totalProyek = newQuery("get_var", "SELECT COUNT(*) FROM tb_proyek WHERE SisaPembayaran>0 $cond");
$grandTotalProyek = newQuery("get_var", "SELECT SUM(GrandTotal) FROM tb_proyek WHERE SisaPembayaran>0 $cond");
// $totalTerbayar = newQuery("get_var", "SELECT SUM(JumlahPembayaran) FROM tb_proyek WHERE SisaPembayaran>0 $cond");
// $totalPiutang = newQuery("get_var", "SELECT SUM(SisaPembayaran) FROM tb_proyek WHERE SisaPembayaran>0 $cond");

$totalTerbayar = newQuery("get_var", "SELECT SUM(GrandTotal-Sisa) FROM tb_proyek_invoice WHERE IDProyek IN (SELECT IDProyek FROM tb_proyek WHERE SisaPembayaran>0 $cond) $cond2");
$totalPiutang = newQuery("get_var", "SELECT SUM(Sisa) FROM tb_proyek_invoice WHERE IDProyek IN (SELECT IDProyek FROM tb_proyek WHERE SisaPembayaran>0 $cond) $cond2");
?>
<?php include "pages/header.php"; ?>
<section class="section section-body bg-grey">
    <section id="overview" class="grid-hero container">
        <div class="widgetMainContainer">
            <div class="columns">
                <div class="column col-3">
                    <div class="widged-bar bg-success">
                        <div class="widgetContainer">
                            <p class="widgedTitle">Total Proyek <i class="fa fa-chevron-right"></i></p>
                            <p class="widgedValue"><?php echo $totalProyek; ?></p>
                            <i class="fa fa-gear bg-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="column col-3">
                    <div class="widged-bar bg-primary">
                        <div class="widgetContainer">
                            <p class="widgedTitle">Grand Total Proyek <i class="fa fa-chevron-right"></i></p>
                            <p class="widgedValue">Rp. <?php echo number_format($grandTotalProyek, 2); ?></p>
                            <i class="fa fa-dollar bg-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="column col-3">
                    <div class="widged-bar bg-danger">
                        <div class="widgetContainer">
                            <p class="widgedTitle">Total Terbayar <i class="fa fa-chevron-right"></i></p>
                            <p class="widgedValue">Rp. <?php echo number_format($totalTerbayar, 2); ?></p>
                            <i class="fa fa-envelope bg-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="column col-3">
                    <div class="widged-bar bg-warning">
                        <div class="widgetContainer">
                            <p class="widgedTitle">Total Piutang <i class="fa fa-chevron-right"></i></p>
                            <p class="widgedValue">Rp. <?php echo number_format($totalPiutang, 2); ?></p>
                            <i class="fa fa-send bg-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h5>Piutang Proyek<small>Pengelolaan data piutang proyek</small></h5>
        <?php if (isset($notif)) { ?>
            <div class="toast toast-<?php echo $notif['class']; ?>">
                <button class="btn btn-clear float-right"></button>
                <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
            </div>
        <?php } ?>
        <form method="GET" action="" class="form-horizontal form-bordered" style="padding: 0px 0;border-top:solid 1px #d3d3d3;border-bottom:solid 1px #d3d3d3;">
            <div class="columns">
                <div class="column col-2">
                    <label class="form-label" for="input-example-1">Departement :</label>
                </div>
                <div class="column col-2">
                    <select name="departement" class="form-select" style="width: 100%">
                        <option value="">Semua Departement</option>
                        <option value="0">Umum</option>
                        <?php
                        $query = newQuery("get_results", "SELECT * FROM tb_departement WHERE AvailableOnProject='1' ORDER BY NamaDepartement ASC");
                        if ($query) {
                            foreach ($query as $data) {
                        ?><option value="<?php echo $data->IDDepartement; ?>" <?php if ($departement == $data->IDDepartement) echo "selected"; ?>><?php echo $data->NamaDepartement; ?></option><?php
                                                                                                                                                                                            }
                                                                                                                                                                                        }
                                                                                                                                                                                                ?>
                    </select>
                </div>
                <div class="column col-2">
                    <select name="periode" class="form-select" style="width: 100%">
                        <option value="">Semua Periode</option>
                        <?php
                        for ($i = 2015; $i <= date("Y"); $i++) {
                        ?><option value="<?php echo $i; ?>" <?php if ($periode == $i) echo "selected"; ?>><?php echo $i; ?></option><?php
                                                                                                                                }
                                                                                                                                    ?>
                    </select>
                </div>
            </div>
            <div class="columns">
                <div class="column col-2">
                    <label class="form-label" for="input-example-1">Sampai Dengan :</label>
                </div>
                <div class="column col-2">
                    <input class="form-input input-calendar" id="sampaitanggal" name="sampaitanggal" type="text" value="<?php echo $sampaitanggal; ?>" />
                </div>
                <div class="column col-2">
                    <button type="submit" name="filterbutton" value="1" class="btn btn-success">Filter</button>
                    <a href="<?php echo PRSONPATH; ?>print-rekap-piutang-proyek/?departement=<?php echo $departement; ?>&periode=<?php echo $periode; ?>&sampaitanggal=<?php echo $sampaitanggal; ?>" class="btn btn-danger" target="_blank">Print</a>
                </div>
            </div>
        </form>
        <table class="table new-table">
            <thead>
                <tr>
                    <th width="120">No. Proyek</th>
                    <th>Nama Proyek</th>
                    <th width="90">Nilai</th>
                    <th width="100">PPN</th>
                    <th width="90">Nilai Kontrak</th>
                    <th width="90">Total Invoice</th>
                    <th width="90">Piutang Proyek</th>
                    <th width="120">Piutang Progress</th>
                </tr>
            </thead>

            <body>
                <?php
                $query = newQuery("get_results", "SELECT * FROM tb_proyek WHERE SisaPembayaran>0 $cond ORDER BY Tahun DESC, KodeProyek ASC");
                if ($query) {
                    $total = 0;
                    $ppn = 0;
                    $grandtotal = 0;
                    $terbayar = 0;
                    $piutang = 0;
                    $progress = 0;
                    $piutangPiutang = 0;
                    foreach ($query as $data) {
                        $total += $data->Nominal;
                        $ppn += $data->PPN;
                        $grandtotal += $data->GrandTotal;
                        // $terbayar += $data->JumlahPembayaran;
                        // $piutang += $data->SisaPembayaran;

                        $grandTotalProyek = newQuery("get_var", "SELECT SUM(GrandTotal) FROM tb_proyek_invoice WHERE IDProyek='" . $data->IDProyek . "' $cond2");
                        if (!$grandTotalProyek) {
                            $grandTotalProyek = 0;
                        } else {
                            $progress += $grandTotalProyek;
                        }

                        $totalTerbayar = newQuery("get_var", "SELECT SUM(GrandTotal-Sisa) FROM tb_proyek_invoice WHERE IDProyek='" . $data->IDProyek . "' $cond2");
                        if (!$totalTerbayar) {
                            $totalTerbayar = 0;
                        } else {
                            $terbayar += $totalTerbayar;
                        }

                        $sisaPiutang = $data->GrandTotal - $totalTerbayar;
                        $piutang += $sisaPiutang;

                        $totalPiutang = newQuery("get_var", "SELECT SUM(Sisa) FROM tb_proyek_invoice WHERE IDProyek='" . $data->IDProyek . "' $cond2");
                        if (!$totalPiutang) {
                            $totalPiutang = 0;
                        } else {
                            $piutangPiutang += $totalPiutang;
                        }
                ?>
                        <tr>
                            <td><a href="#" onclick="window.open('<?php echo PRSONPATH; ?>detail-pembayaran-piutang/?proyek=<?php echo $data->IDProyek; ?>','winname','directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1200,height=600'); return false;"><?php echo $data->KodeProyek . "/" . $data->Tahun; ?></a></td>
                            <td><?php echo $data->NamaProyek; ?></td>
                            <td><?php echo number_format($data->Nominal, 2); ?></td>
                            <td><?php echo number_format($data->PPN, 2); ?> (<?php echo $data->PPNPersen; ?> %)</td>
                            <td><?php echo number_format($data->GrandTotal, 2); ?></td>
                            <td><?php echo number_format($grandTotalProyek, 2); ?></td>
                            <td><strong><?php echo number_format($sisaPiutang, 2); ?></strong></td>
                            <td><strong><?php echo number_format($totalPiutang, 2); ?></strong></td>
                        </tr>
                    <?php
                    }
                    ?>
                    <tr>
                        <td colspan="2" style="text-align: right;"><strong>TOTAL : </strong></td>
                        <td><strong><?php echo number_format($total, 2); ?></strong></td>
                        <td><strong><?php echo number_format($ppn, 2); ?></strong></td>
                        <td><strong><?php echo number_format($grandtotal, 2); ?></strong></td>
                        <td><strong><?php echo number_format($progress, 2); ?></strong></td>
                        <td><strong><?php echo number_format($piutang, 2); ?></strong></td>
                        <td><strong><?php echo number_format($piutangPiutang, 2); ?></strong></td>
                    </tr>
                <?php
                }
                ?>
            </body>
        </table>
    </section>
</section>
<script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/moment.min.js"></script>
<script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/pikaday.js"></script>
<script type="text/javascript">
    var pickerDefault = new Pikaday({
        field: document.getElementById('sampaitanggal'),
        format: 'DD/MM/YYYY',
    });
</script>
<?php include "pages/footer.php"; ?>