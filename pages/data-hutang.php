<?php
$departement = $this->validasi->validInput($_GET['departement']);
$supplier = $this->validasi->validInput($_GET['supplier']);
$no_po = $this->validasi->validInput($_GET['no_po']);
$proyek = $this->validasi->validInput($_GET['proyek']);
$status = $this->validasi->validInput($_GET['status']);
$dariTanggal = $this->validasi->validInput($_GET['daritanggal']);
$sampaiTanggal = $this->validasi->validInput($_GET['sampaitanggal']);

if($departement!=""){
    $cond = " AND IDProyek IN (SELECT IDProyek FROM tb_proyek WHERE IDDepartement='$departement') ";
}
if($supplier!=""){
    $cond = " AND IDSupplier='$supplier' ";
}
if($proyek!=""){
    $cond = " AND IDProyek='$proyek' ";
}

if($status!=""){
    $cond2 = " Sisa<=0 ";
} else {
    $cond2 = " Sisa>0 ";
}

if($dariTanggal!="") {
    $exp = explode("/",$dariTanggal);
    $dariTanggalEN = $exp[2]."-".$exp[1]."-".$exp[0];
    $tgl = $exp[0];
    $bulan = $exp[1];
    $tahun = $exp[2];
    if($sampaiTanggal!=""){
        $exp = explode("/",$sampaiTanggal);
        $sampaiTanggalEN = $exp[2]."-".$exp[1]."-".$exp[0];
        $cond .= " AND Tanggal BETWEEN '$dariTanggalEN' AND '$sampaiTanggalEN' ";
    } else {
        $cond .= " AND Tanggal = '$dariTanggalEN'";
    }
}


$totalProyek = newQuery("get_var","SELECT COUNT(*) FROM tb_po WHERE $cond2 $cond AND IsLD='1'");
$grandTotalProyek = newQuery("get_var","SELECT SUM(GrandTotal) FROM tb_po WHERE $cond2 $cond AND IsLD='1'");
$totalTerbayar = newQuery("get_var","SELECT SUM(TotalPembayaran) FROM tb_po WHERE $cond2 $cond AND IsLD='1'");
$totalPiutang = newQuery("get_var","SELECT SUM(Sisa) FROM tb_po WHERE $cond2 $cond AND IsLD='1'");
?>
<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
            <div class="widgetMainContainer">
                <div class="columns">
                    <div class="column col-3">
                      <div class="widged-bar bg-success">
                        <div class="widgetContainer">
                          <p class="widgedTitle">Total PO Terhutang <i class="fa fa-chevron-right"></i></p>
                          <p class="widgedValue"><?php echo $totalProyek; ?></p>
                          <i class="fa fa-gear bg-icon"></i>
                        </div>
                      </div>
                    </div>
                    <div class="column col-3">
                      <div class="widged-bar bg-primary">
                        <div class="widgetContainer">
                          <p class="widgedTitle">Total Nilai <i class="fa fa-chevron-right"></i></p>
                          <p class="widgedValue">Rp. <?php echo number_format($grandTotalProyek,2); ?></p>
                          <i class="fa fa-dollar bg-icon"></i>
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
                          <p class="widgedTitle">Total Hutang <i class="fa fa-chevron-right"></i></p>
                          <p class="widgedValue">Rp. <?php echo number_format($totalPiutang,2); ?></p>
                          <i class="fa fa-send bg-icon"></i>
                        </div>
                      </div>
                    </div>
                </div>
            </div>
            <h5>Hutang PO Proyek<small>Pengelolaan data hutang PO proyek</small></h5>
            <?php if(isset($notif)){ ?>
                <div class="toast toast-<?php echo $notif['class']; ?>">
                    <button class="btn btn-clear float-right"></button>
                    <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
                </div>
            <?php } ?>
            <form method="GET" action="" class="form-horizontal form-bordered" style="padding: 0px 0;border-top:solid 1px #d3d3d3;border-bottom:solid 1px #d3d3d3;">
                <div class="columns">
                    <div class="column col-1">
                        <label class="form-label" for="input-example-1">Periode :</label>
                    </div>
                    <div class="column col-2">
                        <input class="form-input input-calendar" id="daritanggal" name="daritanggal" type="text" value="<?php echo $dariTanggal; ?>"/>
                    </div>
                    <div class="column col-2">
                        <input class="form-input input-calendar" id="sampaitanggal" name="sampaitanggal" type="text" value="<?php echo $sampaiTanggal; ?>"/>
                    </div>
                </div>
                <div class="columns">
                    <div class="column col-1">
                        <label class="form-label" for="input-example-1">Dept. :</label>
                    </div>
                    <div class="column col-2">
                        <select name="departement" class="form-select" style="width: 100%">
                            <option value="">Semua Departement</option>
                            <option value="0">Umum</option>
                            <?php
                            $query = newQuery("get_results","SELECT * FROM tb_departement WHERE AvailableOnProject='1' AND IDDepartement<>'4' ORDER BY NamaDepartement ASC");
                            if($query){
                                foreach($query as $data){
                                    ?><option value="<?php echo $data->IDDepartement; ?>" <?php if($departement==$data->IDDepartement) echo "selected"; ?>><?php echo $data->NamaDepartement; ?></option><?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="column col-1">
                        <label class="form-label" for="input-example-1">Supplier :</label>
                    </div>
                    <div class="column col-2">
                        <select name="supplier" class="form-select" style="width: 100%">
                            <option value="">Semua Supplier</option>
                            <?php
                            $query = newQuery("get_results","SELECT * FROM tb_supplier WHERE Kategori<>'4' ORDER BY NamaPerusahaan ASC");
                            if($query){
                                foreach($query as $data){
                                    ?><option value="<?php echo $data->IDSupplier; ?>" <?php if($supplier==$data->IDSupplier) echo "selected"; ?>><?php echo $data->NamaPerusahaan; ?></option><?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="column col-1">
                        <label class="form-label" for="input-example-1">Proyek :</label>
                    </div>
                    <div class="column col-3">
                        <select name="proyek" class="form-select" style="width: 100%">
                            <option value="" <?php if($proyek=="") echo "selected"; ?>>Semua Proyek</option>
                            <option value="0" <?php if($proyek=="0") echo "selected"; ?>>Umum</option>
                            <?php
                            $query = newQuery("get_results","SELECT * FROM tb_proyek ORDER BY Tahun, KodeProyek ASC");
                            if($query){
                                foreach($query as $data){
                                    ?><option value="<?php echo $data->IDProyek; ?>" <?php if($proyek==$data->IDProyek) echo "selected"; ?>><?php echo $data->KodeProyek."/".$data->Tahun." ".$data->NamaProyek; ?></option><?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="columns">
                    <div class="column col-1">
                        <label class="form-label" for="input-example-1">No PO :</label>
                    </div>
                    <div class="column col-2">
                        <input type="text" name="no_po" class="form-input" value="<?php echo $no_po; ?>">
                    </div>
                    <div class="column col-1">
                        <label class="form-label" for="input-example-1">Status :</label>
                    </div>
                    <div class="column col-2">
                        <select name="status" class="form-select" style="width: 100%">
                            <option value="">PO Hutang</option>
                            <option value="1" <?php if($status==1) echo "selected"; ?>>PO Lunas</option>
                        </select>
                    </div>
                    <div class="column col-2">
                        <button type="submit" name="filterbutton" value="1" class="btn btn-success">Filter</button>
                        <a href="<?php echo PRSONPATH; ?>print-rekap-hutang/?departement=<?php echo $departement; ?>&supplier=<?php echo $supplier; ?>&no_po=<?php echo $no_po; ?>&proyek=<?php echo $proyek; ?>&status=<?php echo $status; ?>&dariTanggal=<?php echo $dariTanggal; ?>&sampaiTanggal=<?php echo $sampaiTanggal; ?>" class="btn btn-danger" target="_blank">Print</a>
                    </div>
                </div>
            </form>
            <table class="table new-table">
                <thead>
                    <tr>
                        <th width="120">No. PO</th>
                        <th width="120">Proyek</th>
                        <th>Nama Supplier</th>
                        <th width="90">Sub Total</th>
                        <th width="100">PPN</th>
                        <th width="90">Grand Total</th>
                        <th width="90">Total Bayar</th>
                        <th width="90">Sisa Hutang</th>
                        <th width="90">Aksi</th>
                    </tr>
                </thead>
                <body>
                    <?php
                    if($no_po!=""){
                        $query = newQuery("get_results","SELECT * FROM tb_po WHERE NoPo='$no_po' AND IsLD='1' ORDER BY NoPo ASC");
                    } else {
                        $query = newQuery("get_results","SELECT * FROM tb_po WHERE $cond2 $cond AND IsLD='1' ORDER BY NoPo ASC");
                    }
                    
                    if($query){
                        $total = 0;
                        $ppn = 0;
                        $grandtotal = 0;
                        $terbayar = 0;
                        $hutang = 0;
                        foreach($query as $data){
                            $total+=$data->Total2;
                            $ppn+=$data->PPN;
                            $grandtotal+=$data->GrandTotal;
                            $terbayar+=$data->TotalPembayaran;
                            $hutang+=$data->Sisa;
                            
                            $supplier = newQuery("get_row","SELECT * FROM tb_supplier WHERE IDSupplier='".$data->IDSupplier."'");
                            $proyek = newQuery("get_row","SELECT * FROM tb_proyek WHERE IDProyek='".$data->IDProyek."'");
                            if($proyek) $proyek=$proyek->KodeProyek."/".$proyek->Tahun;
                            else $proyek="UMUM";
                            ?>
                            <tr>
                                <td><a href="../../smartoffice/#/purchase-order/detail/<?php echo $data->NoPo; ?>" target="_blank"><?php echo $data->NoPo; ?></a></td>
                                <td><?php echo $proyek; ?></td>
                                <td><?php echo $supplier->NamaPerusahaan; ?></td>
                                <td><?php echo number_format($data->Total2,2); ?></td>
                                <td><?php echo number_format($data->PPN,2); ?> (<?php echo $data->PPNPersen; ?> %)</td>
                                <td><?php echo number_format($data->GrandTotal,2); ?></td>
                                <td><?php echo number_format($data->TotalPembayaran,2); ?></td>
                                <td><strong><?php echo number_format($data->Sisa,2); ?></strong></td>
                                <td><a href="<?php echo PRSONPATH; ?>data-detail-hutang/?NoPo=<?php echo $data->NoPo; ?>">Detail</a></td>
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
                            <td><strong><?php echo number_format($hutang,2); ?></strong></td>
                            <td></td>
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
            field: document.getElementById('daritanggal'),
            format: 'DD/MM/YYYY',
        });
        var pickerDefault = new Pikaday({
            field: document.getElementById('sampaitanggal'),
            format: 'DD/MM/YYYY',
        });
    </script>
<?php include "pages/footer.php"; ?>