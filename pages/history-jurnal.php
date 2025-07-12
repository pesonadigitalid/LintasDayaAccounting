<?php
$cond = " WHERE ";
$param = "?";
if($_POST['filterbutton']){
    $bulan = $this->validasi->validInput($_POST['bulan']);
    $tahun = $this->validasi->validInput($_POST['tahun']);
    $keyword = $this->validasi->validInput($_POST['keyword']);
    if($keyword!=""){
        $cond .= " IDJurnal>0 AND (NoBukti='$keyword' OR Keterangan LIKE '%$keyword%') ";
    } else {
        $cond .= " DATE_FORMAT(Tanggal,'%m') = '$bulan' AND DATE_FORMAT(Tanggal,'%Y') = '$tahun' ";
    }
} else {
    $bulan = date("m");
    $tahun = date("Y");
    $cond .= " DATE_FORMAT(Tanggal,'%m') = '$bulan' AND DATE_FORMAT(Tanggal,'%Y') = '$tahun' ";

    $query = newQuery("get_results","SELECT * FROM tb_jurnal ORDER BY IDJurnal ASC");
    if($query){
        foreach($query as $data){
            $cek = newQuery("get_results","SELECT * FROM tb_jurnal_detail WHERE IDJurnal='".$data->IDJurnal."'");
            if(!$cek){
                newQuery("query","DELETE FROM tb_jurnal WHERE IDJurnal='".$data->IDJurnal."'");
            }
        }
    }
}

?>
<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
            <?php           
            if($_GET['Remove']){
                $notif = array("class"=>"success","msg"=>"Jurnal <strong>".$_GET['Remove']."</strong> berhasil dihapus!");
            }
            ?>
            <h5>History Jurnal<small>Pengelolaan data jurnal</small></h5>
            <?php if(isset($notif)){ ?>
                <div class="toast toast-<?php echo $notif['class']; ?>">
                    <button class="btn btn-clear float-right"></button>
                    <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
                </div>
            <?php } ?>
            <form method="POST" action="" class="form-horizontal form-bordered" style="padding: 0px 0;border-top:solid 1px #d3d3d3;border-bottom:solid 1px #d3d3d3;">
                <div class="columns">
                    <div class="column col-2">
                        <label class="form-label" for="input-example-1">Bulan / Tahun :</label>
                    </div>
                    <div class="column col-3">
                        <select name="bulan" class="form-select">
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
                        </select>
                    </div>
                    <div class="column col-2">
                        <label class="form-label" for="input-example-1">Keyword / No Bukti :</label>
                    </div>
                    <div class="column col-4">
                        <input class="form-input" type="text" name="keyword" value="<?php echo $keyword; ?>" placeholder="Keterangan" />
                    </div>
                    <div class="column col-1">
                        <button type="submit" name="filterbutton" value="1" class="btn btn-danger" onclick="addToCart()">Filter</button>
                    </div>
                </div>
            </form>
            <?php
            $bUmum = newQuery("get_var","SELECT COUNT(*) FROM tb_jurnal $cond AND Status='1'");
            $bTransksi = newQuery("get_var","SELECT COUNT(*) FROM tb_jurnal $cond AND Status='2'");
            ?>
            <ul class="tab">
                <li class="tab-item active">
                    <a href="#" onclick="showRecord('1')">
                        Jurnal Umum
                        <span class="badges"><?php echo $bUmum; ?></span>
                    </a>
                </li>
                <li class="tab-item">
                    <a href="#" onclick="showRecord('2')">
                        Jurnal Transaksi
                        <span class="badges"><?php echo $bTransksi; ?></span>
                    </a>
                </li>
            </ul>
            <table class="table new-table">
                <thead>
                    <tr>
                        <th width="150">No Jurnal</th>
                        <th width="100">No Bukti</th>
                        <th width="80">Tanggal</th>
                        <th>Jenis Transaksi</th>
                        <th width="100">Jumlah</th>
                        <th width="160">Aksi</th>
                    </tr>
                </thead>
                <body>
                    <tr>
                        <td class="spacer" colspan="7"></td>
                    </tr>
                    <?php
                    $query = newQuery("get_results","SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal $cond ORDER BY NoJurnal ASC");
                    if($query){
                        $total = 0;
                        foreach($query as $data){
                            $total += $data->Debet;
                            ?>
                            <tr class="all <?php echo $data->Status; ?>">
                                <td><?php echo $data->NoJurnal; ?></td>
                                <td><?php echo $data->NoBukti; ?></td>
                                <td><?php echo $data->TanggalID; ?></td>
                                <td><strong><?php if($data->Status=="1") echo "Jurnal Umum"; else echo "Jurnal Transaksi"; ?></strong> | <?php echo $data->Keterangan; ?></td>
                                <td><?php echo number_format($data->Debet); ?></td>
                                <td>
                                    <?php if($data->Status=="1"){ ?>
                                    <a href="<?php echo PRSONPATH; ?>jurnal-umum/<?php echo $data->NoJurnal; ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                    <?php } else { ?>
                                    <a href="<?php echo PRSONPATH; ?>jurnal-transaksi/<?php echo $data->NoJurnal; ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                    <?php } ?>
                                    <a href="<?php echo PRSONPATH; ?>remove-jurnal/<?php echo $data->NoJurnal; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin ingin menghapus jurnal ini?');"><i class="fa fa-remove"></i> Hapus</a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr class="all <?php echo $data->Status; ?>">
                            <td colspan="4" style="text-align: right;"><strong>Total:</strong></td>
                            <td><strong><?php echo number_format($total); ?></strong></td>
                            <td>

                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </body>
            </table>
            <p><a href="<?php echo PRSONPATH; ?>jurnal-umum-baru" class="btn btn-primary" id="linkedLink"><i class="fa fa-plus-circle"></i> Tambah</a></p>
        </section>
    </section>
    <script type="text/javascript">
        var hrefUmum = "<?php echo PRSONPATH; ?>jurnal-umum-baru";
        var hrefTransaksi = "<?php echo PRSONPATH; ?>jurnal-transaksi-baru";
        $(document).ready(function(){ showRecord("1"); })
        function showRecord(a){
            $('.all').hide();
            $('.'+a).fadeIn(400);
            if(a=="1"){
                $('#linkedLink').attr("href",hrefUmum);
            } else {
                $('#linkedLink').attr("href",hrefTransaksi);
            }
        }
    </script>
<?php include "pages/footer.php"; ?>