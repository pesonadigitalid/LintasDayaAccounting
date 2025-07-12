<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
            <?php
            if($_GET['remove']){
                $remove = $_GET['remove'];
                newQuery("query","DELETE FROM tb_master_transaksi WHERE IDTransaksi='$remove'");
                $notif = array("class"=>"success","msg"=>"Master Transaksi berhasil dihapus!");
            }
            ?>
            <h5>Master Transaksi<small>Pengelolaan data master transaksi</small></h5>
            <?php if(isset($notif)){ ?>
                <div class="toast toast-<?php echo $notif['class']; ?>">
                    <button class="btn btn-clear float-right"></button>
                    <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
                </div>
            <?php } ?>
            <table class="table new-table">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th width="100">Kode</th>
                        <th>Keterangan</th>
                        <th width="150">Rekening Debet</th>
                        <th width="150">Rekening Kredit</th>
                        <th width="100">Status</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <body>
                    <tr>
                        <td class="spacer" colspan="7"></td>
                    </tr>
                    <?php
                    $query = newQuery("get_results","SELECT * FROM tb_master_transaksi ORDER BY IDTransaksi ASC");
                    if($query){
                        $i=0;
                        foreach($query as $data){
                            $i++;
                            $debet="";
                            $kredit="";
                            $qDetail = newQuery("get_results","SELECT a.*, b.KodeRekening, b.NamaRekening FROM tb_master_transaksi_rekening a, tb_master_rekening b WHERE a.IDTransaksi='".$data->IDTransaksi."' AND a.IDRekening=b.IDRekening");
                            if($qDetail){
                                foreach($qDetail as $dDetail){
                                    if($dDetail->Posisi=="Debet")
                                        $debet.=$dDetail->NamaRekening.", ";
                                    else
                                        $kredit.=$dDetail->NamaRekening.", ";
                                }
                                $debet = substr($debet,0,-2);
                                $kredit = substr($kredit,0,-2);
                            }
                            ?>
                            <tr class="all">
                                <td><?php echo $i; ?></td>
                                <td><?php echo $data->KodeTransaksi; ?></td>
                                <td><strong> <?php echo $data->Keterangan; ?></strong></td>
                                <td><?php echo $debet; ?></td>
                                <td><?php echo $kredit; ?></td>
                                <td><?php if($data->Status==1) echo "Aktif"; else echo "Tdk Aktif"; ?></td>
                                <td>
                                    <a href="<?php echo PRSONPATH; ?>master-transaksi/<?php echo $data->IDTransaksi; ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                    <a href="<?php echo PRSONPATH; ?>data-master-transaksi/?remove=<?php echo $data->IDTransaksi; ?>" onclick="return confirm('Yakin ingin menghapus?')" class="btn btn-danger btn-sm"><i class="fa fa-remove"></i> Hapus</a>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="7"><strong>Tidak ada data yang dapat ditampilkan...</strong></td>
                        </tr>
                        <?php
                    }
                    ?>
                </body>
            </table>
            <p><a href="master-transaksi/" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Tambah</a></p>
        </section>
    </section>
<?php include "pages/footer.php"; ?>