<?php include "pages/header.php"; ?>
<section class="section section-body bg-grey">
    <section id="overview" class="grid-hero container">
        <?php
        if ($_GET['remove']) {
            $remove = $_GET['remove'];
            $cek = newQuery("get_row", "SELECT * FROM tb_jurnal_detail WHERE IDRekening='$remove'");
            if ($cek) {
                $notif = array("class" => "danger", "msg" => "Rekening Perkiraan gagal dihapus, karena telah terelasi dengan Jurnal");
            } else {
                newQuery("query", "DELETE FROM tb_master_rekening WHERE IDRekening='$remove'");
                $notif = array("class" => "success", "msg" => "Rekening Perkiraan berhasil dihapus!");
            }
        }
        ?>
        <h5>Rekening Perkiraan<small>Pengelolaan data master rekening perkiraan</small></h5>
        <?php if (isset($notif)) { ?>
            <div class="toast toast-<?php echo $notif['class']; ?>">
                <button class="btn btn-clear float-right"></button>
                <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
            </div>
        <?php } ?>
        <ul class="tab">
            <li class="tab-item active">
                <a href="#" onclick="showRecord('1-0000')">
                    Aktiva
                </a>
            </li>
            <li class="tab-item">
                <a href="#" onclick="showRecord('2-0000')">
                    Kewajiban
                </a>
            </li>
            <li class="tab-item">
                <a href="#" onclick="showRecord('3-0000')">
                    Modal
                </a>
            </li>
            <li class="tab-item">
                <a href="#" onclick="showRecord('4-0000')">
                    Pendapatan
                </a>
            </li>
            <li class="tab-item">
                <a href="#" onclick="showRecord('5-0000')">
                    HPP
                </a>
            </li>
            <li class="tab-item">
                <a href="#" onclick="showRecord('6-0000')">
                    Biaya
                </a>
            </li>
            <li class="tab-item">
                <a href="#" onclick="showRecord('7-0000')">
                    Pendapatan Lain
                </a>
            </li>
            <li class="tab-item">
                <a href="#" onclick="showRecord('8-0000')">
                    Biaya Lain
                </a>
            </li>
            <li class="tab-item">
                <a href="#" onclick="showRecord('all')">
                    Semua Akun
                </a>
            </li>
        </ul>
        <table class="table new-table">
            <thead>
                <tr>
                    <th width="150">No Rek.</th>
                    <th>Nama Perkiraan</th>
                    <th width="100">Mata Uang</th>
                    <th width="100">Normal</th>
                    <th width="50">Tipe</th>
                    <th width="160">Aksi</th>
                </tr>
            </thead>

            <body>
                <tr>
                    <td class="spacer" colspan="6"></td>
                </tr>
                <?php
                $query = newQuery("get_results", "SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.IDParent='0' ORDER BY a.KodeRekening ASC");
                if ($query) {
                    foreach ($query as $data) {
                        ?>
                        <tr class="all <?php echo $data->KodeRekening; ?>">
                            <td><?php echo $data->KodeRekening; ?></td>
                            <td><strong> <?php echo $data->NamaRekening; ?></strong></td>
                            <td><?php echo $data->Nama; ?></td>
                            <td><?php echo $data->Posisi; ?></td>
                            <td><?php echo $data->Tipe; ?></td>
                            <td>
                                <a href="#" class="btn btn-primary btn-sm" onclick="alert('Rekening Perkiraan tidak dapat diedit atau dihapus!');"><i class="fa fa-edit"></i> Edit</a>
                                <a href="#" class="btn btn-danger btn-sm" onclick="alert('Rekening Perkiraan tidak dapat diedit atau dihapus!');"><i class="fa fa-remove"></i> Hapus</a>
                            </td>
                        </tr>
                        <?php
                        $query2 = newQuery("get_results", "SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.IDParent='" . $data->IDRekening . "' ORDER BY a.KodeRekening ASC");
                        if ($query2) {
                            foreach ($query2 as $data2) {
                                ?>
                                <tr class="all <?php echo $data->KodeRekening; ?>">
                                    <td><?php echo $data2->KodeRekening; ?></td>
                                    <td>+--- <strong><?php echo $data2->NamaRekening; ?></strong></td>
                                    <td><?php echo $data2->Nama; ?></td>
                                    <td><?php echo $data2->Posisi; ?></td>
                                    <td><?php echo $data2->Tipe; ?></td>
                                    <td>
                                        <a href="<?php echo PRSONPATH; ?>master-rekening/<?php echo $data2->IDRekening; ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                        <a href="<?php echo PRSONPATH; ?>data-master-rekening/?remove=<?php echo $data2->IDRekening; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')"><i class="fa fa-remove"></i> Hapus</a>
                                    </td>
                                </tr>
                                <?php
                                $query3 = newQuery("get_results", "SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.IDParent='" . $data2->IDRekening . "' ORDER BY a.KodeRekening ASC");
                                if ($query3) {
                                    foreach ($query3 as $data3) {
                                        ?>
                                        <tr class="all <?php echo $data->KodeRekening; ?>">
                                            <td><?php echo $data3->KodeRekening; ?></td>
                                            <td style="padding-left: 40px;">+--- <strong><?php echo $data3->NamaRekening; ?></strong></td>
                                            <td><?php echo $data3->Nama; ?></td>
                                            <td><?php echo $data3->Posisi; ?></td>
                                            <td><?php echo $data3->Tipe; ?></td>
                                            <td>
                                                <a href="<?php echo PRSONPATH; ?>master-rekening/<?php echo $data3->IDRekening; ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                                <a href="<?php echo PRSONPATH; ?>data-master-rekening/?remove=<?php echo $data3->IDRekening; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')"><i class="fa fa-remove"></i> Hapus</a>
                                            </td>
                                        </tr>
                                        <?php
                                        $query4 = newQuery("get_results", "SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.IDParent='" . $data3->IDRekening . "' ORDER BY a.KodeRekening ASC");
                                        if ($query4) {
                                            foreach ($query4 as $data4) {
                                                ?>
                                                <tr class="all <?php echo $data->KodeRekening; ?>">
                                                    <td><?php echo $data4->KodeRekening; ?></td>
                                                    <td style="padding-left:70px;">+--- <strong><?php echo $data4->NamaRekening; ?></strong></td>
                                                    <td><?php echo $data4->Nama; ?></td>
                                                    <td><?php echo $data4->Posisi; ?></td>
                                                    <td><?php echo $data4->Tipe; ?></td>
                                                    <td>
                                                        <a href="<?php echo PRSONPATH; ?>master-rekening/<?php echo $data4->IDRekening; ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                                        <a href="<?php echo PRSONPATH; ?>data-master-rekening/?remove=<?php echo $data4->IDRekening; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')"><i class="fa fa-remove"></i> Hapus</a>
                                                    </td>
                                                </tr>
                                                <?php
                                                $query5 = newQuery("get_results", "SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.IDParent='" . $data4->IDRekening . "' ORDER BY a.KodeRekening ASC");
                                                if ($query5) {
                                                    foreach ($query5 as $data5) {
                                                        ?>
                                                        <tr class="all <?php echo $data->KodeRekening; ?>">
                                                            <td><?php echo $data5->KodeRekening; ?></td>
                                                            <td style="padding-left:100px;">+--- <strong><?php echo $data5->NamaRekening; ?></strong></td>
                                                            <td><?php echo $data5->Nama; ?></td>
                                                            <td><?php echo $data5->Posisi; ?></td>
                                                            <td><?php echo $data5->Tipe; ?></td>
                                                            <td>
                                                                <a href="<?php echo PRSONPATH; ?>master-rekening/<?php echo $data5->IDRekening; ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                                                <a href="<?php echo PRSONPATH; ?>data-master-rekening/?remove=<?php echo $data5->IDRekening; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')"><i class="fa fa-remove"></i> Hapus</a>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                ?>
            </body>
        </table>
        <?php if ($_SESSION["jabatan"] == 3) { ?>
            <p><a href="master-rekening/" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Tambah</a></p>
        <?php } ?>
    </section>
</section>
<script type="text/javascript">
    $(document).ready(function() {
        showRecord("1-0000");
    })

    function showRecord(a) {
        $('.all').hide();
        $('.' + a).fadeIn(400);
    }
</script>
<?php include "pages/footer.php"; ?>