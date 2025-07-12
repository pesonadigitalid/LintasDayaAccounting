<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
            <?php
            if($_GET['remove']){
                $remove = $_GET['remove'];
                $cek = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDCurrency='$remove'");
                if($cek){
                    $notif = array("class"=>"danger","msg"=>"Kurs Mata Uang tidak dapat dihapus karena telah terhubung ke rekening!");
                } else {
                    newQuery("query","DELETE FROM tb_currency WHERE IDCurrency='$remove'");
                    $notif = array("class"=>"success","msg"=>"Kurs Mata Uang berhasil dihapus!");
                }
            }
            
            if($_POST['submit']){
                $id_currency = $_POST['id_currency'];
                $mata_uang = strtoupper($_POST['mata_uang']);
                
                if($mata_uang==""){
                    $notif = array("class"=>"danger","msg"=>"Lengkapi data anda terlebih dahulu!");
                } else {
                    $cek = newQuery("get_row","SELECT * FROM tb_currency WHERE Nama='$mata_uang' AND IDCurrency!='$id_currency'");
                    if($cek){
                        $notif = array("class"=>"danger","msg"=>"Data tidak dapat disimpan! Kurs Mata Uang telah tersedia!");
                    } else {
                        if($id_currency!=""){
                            $sql = "UPDATE tb_currency SET Nama='$mata_uang' WHERE IDRekening='".$id_currency."'";
                        } else {
                            $sql = "INSERT INTO tb_currency SET Nama='$mata_uang'";
                        }
                        
                        $query = newQuery("query",$sql);
                        if($query){
                            $notif = array("class"=>"success","msg"=>"Data berhasil disimpan!");
                        } else {
                            $notif = array("class"=>"danger","msg"=>"Data gagal disimpan. Silahkan coba kembali nanti!");
                        }
                    }
                
                }
            }
            
            if($_POST['submitk']){
                $idk = $_POST['idk'];
                $kurs = $_POST['kurs'];
                foreach($idk as $key=>$value){
                    newQuery("query","UPDATE tb_currency SET Kurs='".str_replace(",","",$kurs[$key])."' WHERE IDCurrency='".$value."'");
                }
                $notif = array("class"=>"success","msg"=>"Data Kurs berhasil disimpan!");
            }
            ?>
            <h5>Kurs Mata Uang<small>Pengelolaan data kurs mata uang</small></h5>
            <?php if(isset($notif)){ ?>
                <div class="toast toast-<?php echo $notif['class']; ?>">
                    <button class="btn btn-clear float-right"></button>
                    <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
                </div>
            <?php } ?>
            <form method="POST" action="">
            <table class="table new-table">
                <thead>
                    <tr>
                        <th width="40">No</th>
                        <th>Mata Uang</th>
                        <th width="100">Kurs Rupiah</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <body>
                    <tr>
                        <td class="spacer" colspan="6"></td>
                    </tr>
                    <?php
                    $query = newQuery("get_results","SELECT * FROM tb_currency ORDER BY IDCurrency ASC");
                    if($query){
                        $i=0;
                        foreach($query as $data){
                            $i++;
                            ?>
                            <tr>
                                <td><?php echo number_format($i); ?></td>
                                <td><strong> <?php echo $data->Nama; ?></strong></td>
                                <td><input type="text" name="kurs[]" class="form-input price small-input" value="<?php echo number_format($data->Kurs); ?>"/><input type="hidden" name="idk[]" value="<?php echo $data->IDCurrency; ?>"/></td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-sm" onclick="return editRecord('<?php echo $data->IDCurrency; ?>','<?php echo $data->Nama; ?>')"><i class="fa fa-edit"></i> Edit</a>
                                    <a href="<?php echo PRSONPATH; ?>data-kurs/?remove=<?php echo $data->IDCurrency; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')"><i class="fa fa-remove"></i> Hapus</a>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </body>
            </table>
            <p><a href="#" class="btn btn-primary modal-trigger" modal-target="modalWindow"><i class="fa fa-plus-circle"></i> Tambah</a> <button type="submit" name="submitk" value="1" class="btn btn-success">Simpan Kurs</button></p>
            </form>
        </section>
    </section>
    <div id="modalWindow" class="modal">
        <div class="modal-overlay"></div>
        <div class="modal-container">
            <div class="modal-header">
                <button class="btn btn-clear float-right modal-close-trigger"></button>
                <div class="modal-title">Tambah Kurs Mata Uang</div>
            </div>
            <form method="POST" action="">
            <div class="modal-body">
                <div class="content">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <div class="col-sm-3">
                                <label class="form-label" for="input-example-1">Nama Mata Uang</label>
                            </div>
                            <div class="col-sm-9">
                                <input class="form-input" type="text" name="mata_uang" id="mata_uang"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="id_currency" id="id_currency" value=""/>
                <button class="btn btn-link modal-close-trigger">Batal</button>
                <button type="submit" name="submit" value="1" class="btn btn-primary">Simpan</button>
            </div>
            </form>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function(){ 
            $('.price').focus(function(){
               $(this).val($(this).val().toString().replace(/,/g,"")); 
            });
            
            $('.price').focusout(function(){
               $(this).val(numberWithCommas($(this).val())); 
            });
        });
        function editRecord(a,b){
            $('#mata_uang').val(b);
            $('#id_currency').val(a);
            $('#modalWindow').addClass("active");
            return false;
        }
        function numberWithCommas(x) {
            var x = x.toString().replace(/,/g,"");
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
<?php include "pages/footer.php"; ?>