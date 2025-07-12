<?php
$proyek = $this->validasi->validInput($_GET['proyek']);
$cond = " AND IDProyek='$proyek'";
$totalProyek = newQuery("get_var","SELECT COUNT(*) FROM tb_proyek_invoice WHERE IDProyek>0 $cond");
if(!$totalProyek) $totalProyek=0;
$grandTotalProyek = newQuery("get_var","SELECT SUM(GrandTotal) FROM tb_proyek WHERE IDProyek>0 $cond");
if(!$grandTotalProyek) $grandTotalProyek=0;

$totalTerbayar=0;
$query = newQuery("get_results","SELECT * FROM tb_proyek_invoice WHERE IDProyek>0 $cond");
if($query){
    foreach($query as $data){
        $totalBayar = newQuery("get_var","SELECT SUM(Debet) FROM tb_jurnal WHERE NoRef='".$data->IDInvoice."'");
        $totalTerbayar+=$totalBayar;
    }
}

$totalPiutang = $grandTotalProyek-$totalTerbayar;
?>
<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
            <div class="widgetMainContainer">
                <div class="columns">
                    <div class="column col-3">
                      <div class="widged-bar bg-success">
                        <div class="widgetContainer">
                          <p class="widgedTitle">Grand Total Proyek <i class="fa fa-chevron-right"></i></p>
                          <p class="widgedValue">Rp. <?php echo number_format($grandTotalProyek,2); ?></p>
                          <i class="fa fa-dollar bg-icon"></i>
                        </div>
                      </div>
                    </div>
                    <div class="column col-3">
                      <div class="widged-bar bg-primary">
                        <div class="widgetContainer">
                          <p class="widgedTitle">Total Invoice <i class="fa fa-chevron-right"></i></p>
                          <p class="widgedValue"><?php echo $totalProyek; ?></p>
                          <i class="fa fa-gear bg-icon"></i>
                        </div>
                      </div>
                    </div>
                    <div class="column col-3">
                      <div class="widged-bar bg-danger">
                        <div class="widgetContainer">
                          <p class="widgedTitle">Total Terbayar <i class="fa fa-chevron-right"></i></p>
                          <p class="widgedValue">Rp. <?php echo number_format($totalTerbayar,2); ?></p>
                          <i class="fa fa-envelope bg-icon"></i>
                        </div>
                      </div>
                    </div>
                    <div class="column col-3">
                      <div class="widged-bar bg-warning">
                        <div class="widgetContainer">
                          <p class="widgedTitle">Total Piutang <i class="fa fa-chevron-right"></i></p>
                          <p class="widgedValue">Rp. <?php echo number_format($totalPiutang,2); ?></p>
                          <i class="fa fa-send bg-icon"></i>
                        </div>
                      </div>
                    </div>
                </div>
            </div>
            <h5>Laporan Data Proyek<small>Laporan Data Proyek</small></h5>
            <form method="GET" action="" class="form-horizontal form-bordered" style="padding: 0px 0;border-top:solid 1px #d3d3d3;border-bottom:solid 1px #d3d3d3;">
                <div class="columns">
                    <div class="column col-2">
                        <label class="form-label" for="input-example-1">Proyek :</label>
                    </div>
                    <div class="column col-4">
                        <select name="proyek" class="form-select" style="width: 100%">
                            <option value="">Semua Proyek</option>
                            <?php
                            $query = newQuery("get_results","SELECT * FROM tb_proyek ORDER BY Tahun DESC, KodeProyek ASC");
                            if($query){
                                foreach($query as $data){
                                    ?><option value="<?php echo $data->IDProyek; ?>" <?php if($proyek==$data->IDProyek) echo "selected"; ?>><?php echo $data->KodeProyek." / ".$data->Tahun." / ".$data->NamaProyek; ?></option><?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="column col-2">
                        <button type="submit" name="filterbutton" value="1" class="btn btn-success">Filter</button>
                    </div>
                </div>
            </form>
            <table class="table new-table">
                <thead>
                    <tr>
                        <th width="120">No. Invoice</th>
                        <th>Proyek</th>
                        <th width="100">Tanggal</th>
                        <th width="120">Total</th>
                        <th width="140">PPN</th>
                        <th width="120">Grand Total</th>
                        <th width="120">Terbayar</th>
                        <th width="120">Piutang Progress</th>
                    </tr>
                </thead>
                <body>
                    <?php
                    $query = newQuery("get_results","SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_proyek_invoice WHERE IDProyek>0 $cond ORDER BY Tanggal ASC, NoInv ASC");
                    if($query){
                        $total = 0;
                        $ppn = 0;
                        $grandtotal = 0;
                        $terbayar = 0;
                        $piutang = 0;
                        foreach($query as $data){
                            $dataProyek = newQuery("get_row","SELECT * FROM tb_proyek WHERE IDProyek='".$data->IDProyek."'");
                            $totalBayar = newQuery("get_var","SELECT SUM(Debet) FROM tb_jurnal WHERE NoRef='".$data->IDInvoice."'");
                            $total+=$data->Jumlah;
                            $ppn+=$data->PPN;
                            $grandtotal+=$data->GrandTotal;
                            $terbayar+=$totalBayar;
                            $piutang+=$data->Sisa;
                            ?>
                            <tr>
                                <td><a href="#" onclick="window.open('<?php echo PRSONPATH; ?>detail-pembayaran-invoice/?invoice=<?php echo $data->IDInvoice; ?>','winname','directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1200,height=600'); return false;"><?php echo $data->NoInv; ?></a></td>
                                <td><?php echo $dataProyek->KodeProyek."/".$dataProyek->Tahun."/".$dataProyek->NamaProyek; ?></td>
                                <td><?php echo $data->TanggalID; ?></td>
                                <td><?php echo number_format($data->Jumlah,2); ?></td>
                                <td><?php echo number_format($data->PPN,2); ?> (<?php echo $data->PPNPersen; ?>%)</td>
                                <td><?php echo number_format($data->GrandTotal,2); ?></td>
                                <td><?php echo number_format($totalBayar,2); ?></td>
                                <td><strong><?php echo number_format($data->Sisa,2); ?></strong></td>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr>
                            <td colspan="3" style="text-align: right;"><strong>TOTAL : </strong></td>
                            <td><strong><?php echo number_format($total,2); ?></strong></td>
                            <td><strong><?php echo number_format($ppn,2); ?></strong></td>
                            <td><strong><?php echo number_format($grandtotal,2); ?></strong></td>
                            <td><strong><?php echo number_format($terbayar,2); ?></strong></td>
                            <td><strong><?php echo number_format($piutang,2); ?></strong></td>
                        </tr>
                        <?php
                    } else {
                        ?>
                        <tr>
                            <td colspan="8">Tidak ada invoice yang dibuat untuk proyek ini.</td>
                        </tr>
                        <?php
                    }
                    ?>
                </body>
            </table>
        </section>
    </section>
<?php include "pages/footer.php"; ?>