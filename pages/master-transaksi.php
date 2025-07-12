<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
            <div class="col-sm-7">
                <?php
                
                if($_POST['submit']){
                    $kode = $_POST['kode'];
                    $nama = ucfirst(strtolower($_POST['nama']));
                    $status = $_POST['status'];
                    $rekening = $_POST['rekening'];
                    $rekeningpos = $_POST['rekeningpos'];

                    if($kode=="" || $nama==""){
                        $notif = array("class"=>"danger","msg"=>"Lengkapi data anda terlebih dahulu!");
                    } else {
                        $error = 0;
                        $dbt=0;
                        $krt=0;
                        $idR = array();
                        foreach($rekeningpos as $key=>$value){
                            if($value=="Debet")
                                $dbt=1;
                            if($value=="Kredit")
                                $krt=1;
                                
                            if(!in_array($rekening[$key],$idR)){
                                array_push($idR,$rekening[$key]);
                            } else {
                                $error=1;
                                $notif = array("class"=>"danger","msg"=>"Rekening yang anda pilih tidak boleh sama!");
                            }
                        }
                        if($krt!=1 || $dbt!=1){
                            $error=1;
                            $notif = array("class"=>"danger","msg"=>"Anda harus memilih akun rekening Debet dan Kredit!");
                        }
                        if($error==0){
                            $cek = newQuery("get_row","SELECT * FROM tb_master_transaksi WHERE KodeTransaksi='$kode' AND IDTransaksi!='".$this->action."'");
                            $cek2 = newQuery("get_row","SELECT * FROM tb_master_transaksi WHERE Keterangan='$nama' AND IDTransaksi!='".$this->action."'");
                            if($cek){
                                $notif = array("class"=>"danger","msg"=>"Data tidak dapat disimpan! Kode Transaksi telah digunakan!");
                            } else if($cek2){
                                $notif = array("class"=>"danger","msg"=>"Anda sudah memiliki transaksi dengan keterangan tersebut...");
                            } else {
                                if($this->action!=""){
                                    $query = newQuery("query","UPDATE tb_master_transaksi SET KodeTransaksi='$kode', Keterangan='$nama', Status='$status' WHERE IDTransaksi='".$this->action."'");
                                    $lastID = $this->action;
                                } else {
                                    $query = newQuery("query","INSERT INTO tb_master_transaksi SET KodeTransaksi='$kode', Keterangan='$nama'");
                                    $lastID = newQuery("get_var","SELECT LAST_INSERT_ID()");
                                }
                                
                                newQuery("query","DELETE FROM tb_master_transaksi_rekening WHERE IDTransaksi='$lastID'");
                                foreach($rekeningpos as $key=>$value){
                                    newQuery("query","INSERT INTO tb_master_transaksi_rekening SET IDTransaksi='$lastID', IDRekening='".$rekening[$key]."', Posisi='$value'");
                                }
                                $notif = array("class"=>"success","msg"=>"Data berhasil disimpan!");
                                unset($kode, $nama, $rekening, $rekeningpos, $status);
                            }
                        }
                    }
                }
                
                if($this->action!=""){
                    $data = newQuery("get_row","SELECT * FROM tb_master_transaksi WHERE IDTransaksi='".$this->action."'");
                    if($data){
                        $kode = $data->KodeTransaksi;
                        $nama = $data->Keterangan;
                        $status = $data->Status;
                        $rekening = array();
                        $rekeningpos = array();
                        $qDetail = newQuery("get_results","SELECT * FROM tb_master_transaksi_rekening WHERE IDTransaksi='".$this->action."'");
                        if($qDetail){
                            foreach($qDetail as $dDetail){
                                array_push($rekening,$dDetail->IDRekening);
                                array_push($rekeningpos,$dDetail->Posisi);
                            }
                        }
                    }
                }                
                ?>
                <h5><?php if($this->action!=""){ echo "Edit"; } else { echo "Tambah"; } ?> Master Transaksi<small>Pengelolaan data master transaksi</small></h5>
                <?php if(isset($notif)){ ?>
                    <div class="toast toast-<?php echo $notif['class']; ?>">
                        <button class="btn btn-clear float-right"></button>
                        <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
                    </div>
                <?php } ?>
                <div class="form-header">Formulir Master Transaksi</div>
                <form method="POST" action="" class="form-horizontal form-bordered">
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label class="form-label" for="input-example-1">Kode Transaksi</label>
                        </div>
                        <div class="col-sm-9">
                            <input class="form-input" type="text" name="kode" id="kode" value="<?php echo $kode; ?>" placeholder="Kode Transaksi" maxlength="10"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label class="form-label" for="input-example-1">Keterangan</label>
                        </div>
                        <div class="col-sm-9">
                            <input class="form-input" type="text" name="nama" value="<?php echo $nama; ?>" placeholder="Keterangan" />
                        </div>
                    </div>
                    <?php
                    if($rekeningpos){
                        $i=0;
                        foreach($rekeningpos as $key=>$value){
                            $i++;
                            ?>
                            <div class="form-group" id="duplicateContent">
                                <div class="col-sm-3">
                                    <label class="form-label" for="input-example-1">Rekening</label>
                                </div>
                                <div class="col-sm-7">
                                    <select name="rekening[]" class="form-select" style="width: 95%;">
                                        <?php
                                        $query = newQuery("get_results","SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.Tipe='D' ORDER BY KodeRekening ASC");
                                        if($query){
                                            foreach($query as $data){
                                                ?><option value="<?php echo $data->IDRekening; ?>" <?php if($rekening[$key]==$data->IDRekening) echo "selected"; ?>><?php echo $data->KodeRekening." - ".ucwords(strtolower($data->NamaRekening)); ?></option><?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <select name="rekeningpos[]" class="form-select" style="width: 100%;">
                                        <option value="Debet" <?php if($value=="Debet") echo "selected"; ?>>Debet</option>
                                        <option value="Kredit" <?php if($value=="Kredit") echo "selected"; ?>>Kredit</option>
                                    </select>
                                </div>
                                <?php
                                if($i>=3) echo '<div class="col-sm-1"><button type="button" name="submitrekening" value="1" class="btn btn-link" onclick="removeParent(this);"><i class="fa fa-remove"></i></button></div>';
                                ?>
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="form-group">
                            <div class="col-sm-3">
                                <label class="form-label" for="input-example-1">Rekening</label>
                            </div>
                            <div class="col-sm-7">
                                <select name="rekening[]" class="form-select" style="width: 95%;">
                                    <?php
                                    $query = newQuery("get_results","SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.Tipe='D' ORDER BY KodeRekening ASC");
                                    if($query){
                                        foreach($query as $data){
                                            ?><option value="<?php echo $data->IDRekening; ?>" <?php if($rekdebet==$data->IDRekening) echo "selected"; ?>><?php echo $data->KodeRekening." - ".ucwords(strtolower($data->NamaRekening)); ?></option><?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <select name="rekeningpos[]" class="form-select" style="width: 100%;">
                                    <option value="Debet">Debet</option>
                                    <option value="Kredit">Kredit</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="duplicateContent">
                            <div class="col-sm-3">
                                <label class="form-label" for="input-example-1">Rekening</label>
                            </div>
                            <div class="col-sm-7">
                                <select name="rekening[]" class="form-select" style="width: 95%;">
                                    <?php
                                    $query = newQuery("get_results","SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.Tipe='D' ORDER BY KodeRekening ASC");
                                    if($query){
                                        foreach($query as $data){
                                            ?><option value="<?php echo $data->IDRekening; ?>" <?php if($rekdebet==$data->IDRekening) echo "selected"; ?>><?php echo $data->KodeRekening." - ".ucwords(strtolower($data->NamaRekening)); ?></option><?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <select name="rekeningpos[]" class="form-select" style="width: 100%;">
                                    <option value="Debet">Debet</option>
                                    <option value="Kredit">Kredit</option>
                                </select>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <div id="containerRekening" style="margin-bottom: 20px;"></div>
                    <div class="form-group">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-9">
                            <button type="button" name="submitrekening" value="1" class="btn btn-success" onclick="addRekening()"><i class="fa fa-plus"></i> Tambah Rekening</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label class="form-label" for="input-example-1">Status</label>
                        </div>
                        <div class="col-sm-9">
                            <label class="form-switch" style="margin-top:5px;">
                                <input type="checkbox" name="status" value="1" <?php if($status=="1") echo "checked"; ?>/>
                                <i class="form-icon"></i> Aktifkan transaksi
                            </label>
                        </div>
                    </div>
                    <div class="form-footer">
                        <a href="<?php echo PRSONPATH; ?>data-master-transaksi/" class="btn btn-link modal-close-trigger">Kembali</a>
                        <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </section>
    </section>
    <script type="text/javascript">
        $(document).ready(function(){
           $('#parent').change(function(){
                var option = $('option:selected', this).attr('kode-parent');
                var option2 = $('option:selected', this).attr('kode-rekening');
                $('#kodeprefix').val(option);
                $('#kode').val(option2);
           }); 
        });
        
        function addRekening(){
            htmlVal = $('#duplicateContent').html();
            $('#containerRekening').append('<div class="form-group">'+htmlVal+'<div class="col-sm-1"><button type="button" name="submitrekening" value="1" class="btn btn-link" onclick="removeParent(this);"><i class="fa fa-remove"></i></button></div></div>');
        }
        
        function removeParent(a){
            $(a).closest("div.form-group").remove();
        }
    </script>
<?php include "pages/footer.php"; ?>