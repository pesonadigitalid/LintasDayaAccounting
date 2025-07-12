<?php
$invoice = $this->validasi->validInput($_GET['invoice']);
if($invoice!=""){
    $cond = " AND NoRef='$invoice' AND Tipe='1'";
}

$dataInvoice = newQuery("get_row","SELECT * FROM tb_proyek_invoice WHERE IDInvoice='$invoice'");
$dataProyek = newQuery("get_row","SELECT * FROM tb_proyek WHERE IDProyek='".$dataInvoice->IDProyek."'");

$totalProyek = newQuery("get_var","SELECT COUNT(*) FROM tb_jurnal WHERE NoRef='$invoice' AND Tipe='1'");
if(!$totalProyek) $totalProyek=0;
$grandTotalProyek = newQuery("get_var","SELECT GrandTotal FROM tb_proyek_invoice WHERE IDInvoice='$invoice'");
if(!$grandTotalProyek) $grandTotalProyek=0;

$totalPiutang = newQuery("get_var","SELECT SUM(Sisa) FROM tb_proyek_invoice WHERE IDInvoice='$invoice'");
if(!$totalPiutang) $totalPiutang=0;

$totalTerbayar=0;
$query = newQuery("get_results","SELECT * FROM tb_proyek_invoice WHERE IDInvoice='$invoice'");
if($query){
    foreach($query as $data){
        $totalBayar = newQuery("get_var","SELECT SUM(Debet) FROM tb_jurnal WHERE NoRef='".$data->IDInvoice."' AND Tipe='1'");
        $totalTerbayar+=$totalBayar;
    }
}
?>
<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
            <div class="widgetMainContainer">
                <div class="columns">
                    <div class="column col-3">
                      <div class="widged-bar bg-primary">
                        <div class="widgetContainer">
                          <p class="widgedTitle">Grand Total Invoice <i class="fa fa-chevron-right"></i></p>
                          <p class="widgedValue">Rp. <?php echo number_format($grandTotalProyek,2); ?></p>
                          <i class="fa fa-dollar bg-icon"></i>
                        </div>
                      </div>
                    </div>
                    <div class="column col-3">
                      <div class="widged-bar bg-success">
                        <div class="widgetContainer">
                          <p class="widgedTitle">Total Pembayaran <i class="fa fa-chevron-right"></i></p>
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
                          <p class="widgedTitle">Sisa Piutang <i class="fa fa-chevron-right"></i></p>
                          <p class="widgedValue">Rp. <?php echo number_format($totalPiutang,2); ?></p>
                          <i class="fa fa-send bg-icon"></i>
                        </div>
                      </div>
                    </div>
                </div>
            </div>
            <h5>Detail Pembayaran No Invoice <?php echo $dataInvoice->NoInv." - ".$dataProyek->KodeProyek."/".$dataProyek->Tahun."/".$dataProyek->NamaProyek; ?></h5>
            <!-- <form method="GET" action="" class="form-horizontal form-bordered" style="padding: 0px 0;border-top:solid 1px #d3d3d3;border-bottom:solid 1px #d3d3d3;">
                <div class="columns">
                    <div class="column col-2">
                        <label class="form-label" for="input-example-1">No Invoice :</label>
                    </div>
                    <div class="column col-4">
                        <select name="invoice" class="form-select">
                            <?php
                            /*
                            $query = newQuery("get_results","SELECT * FROM tb_proyek_invoice ORDER BY IDInvoice ASC, ");
                            if($query){
                                foreach($query as $data){
                                    ?><option value="<?php echo $data->IDProyek; ?>" <?php if($invoice==$data->IDProyek) echo "selected"; ?>><?php echo $data->KodeProyek." / ".$data->Tahun." / ".$data->NamaProyek; ?></option><?php
                                }
                            }
                            */
                            ?>
                        </select>
                    </div>
                    <div class="column col-2">
                        <button type="submit" name="filterbutton" value="1" class="btn btn-success">Filter</button>
                    </div>
                </div>
            </form> -->
            <table class="table new-table">
                <thead>
                    <tr>
                        <th width="120">No. Bukti</th>
                        <th>No Invoice</th>
                        <th width="100">Tanggal</th>
                        <th width="120">Total</th>
                    </tr>
                </thead>
                <body>
                    <?php
                    $query = newQuery("get_results","SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_jurnal WHERE IDJurnal>0 $cond ORDER BY Tanggal ASC, NoBukti ASC");
                    if($query){
                        $total = 0;
                        foreach($query as $data){
                            $dataInvoice = newQuery("get_row","SELECT * FROM tb_proyek_invoice WHERE IDInvoice='".$data->NoRef."'");

                            $dataProyek = newQuery("get_row","SELECT * FROM tb_proyek WHERE IDProyek='".$dataInvoice->IDProyek."'");
                            $total+=$data->Kredit;
                            ?>
                            <tr>
                                <td><?php echo $data->NoBukti; ?></td>
                                <td><?php echo $dataInvoice->NoInv." - ".$dataProyek->KodeProyek." / ".$dataProyek->Tahun; ?></td>
                                <td><?php echo $data->TanggalID; ?></td>
                                <td><?php echo number_format($data->Kredit,2); ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr>
                            <td colspan="3" style="text-align: right;"><strong>TOTAL : </strong></td>
                            <td><strong><?php echo number_format($total,2); ?></strong></td>
                        </tr>
                        <?php
                    } else {
                        ?>
                        <tr>
                            <td colspan="4">Tidak ada data yang dapat ditampilkan.</td>
                        </tr>
                        <?php
                    }
                    ?>
                </body>
            </table>
        </section>
    </section>
<?php include "pages/footer.php"; ?>