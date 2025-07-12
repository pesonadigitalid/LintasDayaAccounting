<?php
$NoPo = $this->validasi->validInput($_GET['NoPo']);
$dataPO = newQuery("get_row","SELECT * FROM tb_po WHERE NoPO='$NoPo'");

$grandTotalPO = newQuery("get_var","SELECT SUM(GrandTotal) FROM tb_po WHERE NoPO='$NoPo'");
$jumlahBayar = newQuery("get_var","SELECT COUNT(*) FROM tb_jurnal WHERE NoRef='".$dataPO->IDPO."' AND Tipe='3'");
$totalTerbayar = newQuery("get_var","SELECT SUM(TotalPembayaran) FROM tb_po WHERE NoPO='$NoPo'");
$totalHutang = newQuery("get_var","SELECT SUM(Sisa) FROM tb_po WHERE NoPO='$NoPo'");
?>
<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
            <div class="widgetMainContainer">
                <div class="columns">
                    <div class="column col-3">
                      <div class="widged-bar bg-success">
                        <div class="widgetContainer">
                          <p class="widgedTitle">Grand Total PO <i class="fa fa-chevron-right"></i></p>
                          <p class="widgedValue">Rp. <?php echo number_format($grandTotalPO,2); ?></p>
                          <i class="fa fa-dollar bg-icon"></i>
                        </div>
                      </div>
                    </div>
                    <div class="column col-3">
                      <div class="widged-bar bg-primary">
                        <div class="widgetContainer">
                          <p class="widgedTitle">Jumlah Bayar<i class="fa fa-chevron-right"></i></p>
                          <p class="widgedValue"><?php echo $jumlahBayar; ?></p>
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
                          <p class="widgedTitle">Sisa Hutang <i class="fa fa-chevron-right"></i></p>
                          <p class="widgedValue">Rp. <?php echo number_format($totalHutang,2); ?></p>
                          <i class="fa fa-send bg-icon"></i>
                        </div>
                      </div>
                    </div>
                </div>
            </div>
            <h5>Detail Hutang PO<small>Pengelolaan data hutang PO</small></h5>
            <?php if(isset($notif)){ ?>
                <div class="toast toast-<?php echo $notif['class']; ?>">
                    <button class="btn btn-clear float-right"></button>
                    <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
                </div>
            <?php } ?>
            <table class="table new-table">
                <thead>
                    <tr>
                        <th width="20">No.</th>
                        <th width="90">No Bukti</th>
                        <th width="120">Tgl. Pembayaran</th>
                        <th width="270">Dari Rekening</th>
                        <th>Keterangan</th>
                        <th width="200">Grand Total</th>
                    </tr>
                </thead>
                <body>
                    <?php
                    $query = newQuery("get_results","SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal WHERE NoRef='".$dataPO->IDPO."' AND (Tipe='3' OR Tipe='5')");
                    if($query){
                        $total = 0;
                        $i=0;
                        foreach($query as $data){
                            $i++;
                            $dJurnal = newQuery("get_row","SELECT * FROM tb_jurnal_detail WHERE IDJurnal='".$data->IDJurnal."' AND Kredit>0");
                            $rekening = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDRekening='".$dJurnal->IDRekening."'");
                            $total += $data->Kredit;
                            ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><a href="#" onclick="window.open('<?php echo PRSONPATH; ?>jurnal-umum/<?php echo $data->NoJurnal; ?>','winname','directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1200,height=600'); return false;"><?php if($data->NoBukti!="") echo $data->NoBukti; else echo "0000"; ?></a></td>
                                <td><?php echo $data->TanggalID; ?></td>
                                <td><?php echo $rekening->NamaRekening; ?></td>
                                <td><?php echo $dJurnal->Keterangan; ?></td>
                                <td><?php echo number_format($data->Kredit,2); ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr>
                            <td colspan="5" style="text-align: right;"><strong>Total Pembayaran : </strong></td>
                            <td><strong><?php echo number_format($total,2); ?></strong></td>
                        </tr>
                        <tr>
                            <td colspan="5" style="text-align: right;"><strong>Sisa Hutang : </strong></td>
                            <td><strong><?php echo number_format(($grandTotalPO-$total),2); ?></strong></td>
                        </tr>
                        <?php
                    } else {
                      ?>
                        <tr>
                            <td colspan="6">Belum ada Pembayaran yang dapat ditampilkan dari PO ini.</td>
                        </tr>
                        <?php
                    }
                    ?>
                </body>
            </table>
        </section>
    </section>
<?php include "pages/footer.php"; ?>