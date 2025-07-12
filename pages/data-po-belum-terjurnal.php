<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
            <h5>PO Belum Terjual<small>Data PO yang belum terjurnal</small></h5>
            <table class="table new-table">
                <thead>
                    <tr>
                        <th width="120">No. PO</th>
                        <th width="100">Tanggal</th>
                        <th>Supplier</th>
                        <th width="120">Total</th>
                        <th width="140">PPN</th>
                        <th width="120">Grand Total</th>
                    </tr>
                </thead>
                <body>
                    <?php
                    $query = newQuery("get_results","SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po WHERE IDPO NOT IN (SELECT DISTINCT(NoRef) FROM tb_jurnal WHERE (Tipe='3' OR Tipe='6') AND NoRef IS NOT NULL) AND IsLD='1'");
                    if($query){
                        foreach($query as $data){
                            $supplier = newQuery("get_var","SELECT NamaPerusahaan FROM tb_supplier WHERE IDSupplier='".$data->IDSupplier."'");
                            ?>
                            <tr>
                                <td><a href="../../smartoffice/#/purchase-order/detail/<?php echo $data->NoPo; ?>" target="_blank"><?php echo $data->NoPo; ?></a></td>
                                <td><?php echo $data->TanggalID; ?></td>
                                <td><?php echo $supplier; ?></td>
                                <td><?php echo number_format($data->Total,2); ?></td>
                                <td><?php echo number_format($data->PPN,2); ?> (<?php echo $data->PPNPersen; ?>%)</td>
                                <td><?php echo number_format($data->GrandTotal,2); ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="6">Semua PO telah terjurnal untuk saat ini.</td>
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