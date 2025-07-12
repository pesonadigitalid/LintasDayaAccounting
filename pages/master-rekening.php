<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
            <div class="col-sm-7">
                <?php
                if($this->action!=""){
                    $data = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDRekening='".$this->action."'");
                    if($data){
                        $parent = $data->IDParent;
                        $kodeprefix = substr($data->KodeRekening,0,2);
                        $kode = substr($data->KodeRekening,2);
                        $nama = strtoupper($data->NamaRekening);
                        $tipe = $data->Tipe;
                        $posisi = $data->Posisi;
                        $currency = $data->IDCurrency;
                    }
                }
                
                if($kodeprefix==""){
                    $kodeprefix = "1-";
                    $kode = "0000";
                }
                
                if($_POST['submit']){
                    $parent = $_POST['parent'];
                    $kodeprefix = $_POST['kodeprefix'];
                    $kode = $kodeprefix.$_POST['kode'];
                    $nama = strtoupper($_POST['nama']);
                    $tipe = $_POST['tipe'];
                    $posisi = $_POST['posisi'];
                    $currency = $_POST['currency'];

                    $pref = substr($kodeprefix, 0, 1);
                    if($pref=="1" || $pref=="5" ||  $pref=="6" ||  $pref=="8") $posisi = "Debet";
                    else $posisi = "Kredit";
                    
                    if($parent=="" || $kode=="" || $nama=="" || $tipe=="" || $currency==""){
                        $notif = array("class"=>"danger","msg"=>"Lengkapi data anda terlebih dahulu!");
                    } else {
                        $cek = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE KodeRekening='$kode' AND IDRekening<>'".$this->action."'");
                        if($cek){
                            $notif = array("class"=>"danger","msg"=>"Data tidak dapat disimpan! Kode Rekening telah digunakan!");
                        } else {
                            if($this->action!=""){
                                $sql = "UPDATE tb_master_rekening SET KodeRekening='$kode', NamaRekening='$nama', IDCurrency='$currency', IDParent='$parent', Posisi='$posisi', Tipe='$tipe' WHERE IDRekening='".$this->action."'";
                            } else {
                                $tanggalSaldoAwal = newQuery("get_var","SELECT TanggalSaldoAwal FROM tb_master_rekening ORDER BY TanggalSaldoAwal DESC");
                                $sql = "INSERT INTO tb_master_rekening SET KodeRekening='$kode', NamaRekening='$nama', IDCurrency='$currency', IDParent='$parent', Posisi='$posisi', Tipe='$tipe', TanggalSaldoAwal='$tanggalSaldoAwal'";
                            }
                            
                            $query = newQuery("query",$sql);
                            if($query){
                                $notif = array("class"=>"success","msg"=>"Data berhasil disimpan!");
                                //unset($parent, $kodeprefix, $kode, $nama, $tipe, $posisi, $currency);
                            } else {
                                $notif = array("class"=>"danger","msg"=>"Data gagal disimpan. Silahkan coba kembali nanti!");
                            }
                        }
                    
                    }
                    $kode = substr($kode,2);
                }
                ?>
                <h5><?php if($this->action!=""){ echo "Edit"; } else { echo "Tambah"; } ?> Rekening Perkiraan<small>Pengelolaan data master rekening perkiraan</small></h5>
                <?php if(isset($notif)){ ?>
                    <div class="toast toast-<?php echo $notif['class']; ?>">
                        <button class="btn btn-clear float-right"></button>
                        <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
                    </div>
                <?php } ?>
                <div class="form-header">Formulir Rekening Perkiraan</div>
                <form method="POST" action="" class="form-horizontal form-bordered">
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label class="form-label" for="input-example-1">Kelompok Rekening</label>
                        </div>
                        <div class="col-sm-9">
                            <select name="parent" id="parent" class="form-select">
                                <?php
                                $query = newQuery("get_results","SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.Tipe='H' ORDER BY KodeRekening ASC");
                                if($query){
                                    foreach($query as $data){
                                        ?><option value="<?php echo $data->IDRekening; ?>" kode-parent="<?php echo substr($data->KodeRekening,0,2); ?>" kode-rekening="<?php echo substr($data->KodeRekening,2); ?>" <?php if($parent==$data->IDRekening) echo "selected"; ?>><?php echo ucwords(strtolower($data->NamaRekening)); ?></option><?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label class="form-label" for="input-example-1">Kode Rekening</label>
                        </div>
                        <div class="col-sm-1" style="margin-right: 10px;">
                            <input class="form-input" type="text" name="kodeprefix" id="kodeprefix" placeholder="" value="<?php echo $kodeprefix; ?>" readonly=""/>
                        </div>
                        <div class="col-sm-8" style="padding-right: 10px;">
                            <input class="form-input" type="text" name="kode" id="kode" value="<?php echo $kode; ?>" placeholder="Kode Rekening" maxlength="4"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label class="form-label" for="input-example-1">Nama Rekening</label>
                        </div>
                        <div class="col-sm-9">
                            <input class="form-input" type="text" name="nama" value="<?php echo $nama; ?>" placeholder="Nama Rekening" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label class="form-label" for="input-example-1">Tipe</label>
                        </div>
                        <div class="col-sm-9">
                            <select name="tipe" class="form-select">
                                <option value="H" <?php if($tipe=="H") echo "selected"; ?>>Header</option>
                                <option value="D" <?php if($tipe=="D") echo "selected"; ?>>Detail</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label class="form-label" for="input-example-1">Mata Uang</label>
                        </div>
                        <div class="col-sm-9">
                            <select name="currency" class="form-select">
                                <?php
                                $query = newQuery("get_results","SELECT * FROM tb_currency ORDER BY IDCurrency ASC");
                                if($query){
                                    foreach($query as $data){
                                        ?><option value="<?php echo $data->IDCurrency; ?>" <?php if($currency==$data->IDCurrency) echo "selected"; ?>><?php echo  strtoupper($data->Nama); ?></option><?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-footer">
                        <input type="hidden" name="posisi" id="posisi" value="<?php echo $posisi; ?>"/>
                        <a href="<?php echo PRSONPATH; ?>data-master-rekening/" class="btn btn-link modal-close-trigger">Kembali</a>
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
                //$('#kode').val(option2);
                if(option=="1-" || option=="5-" || option=="6-" || option=="8-"){
                    $('#posisi').val("Debet");
                } else {
                    $('#posisi').val("Kredit");
                }
           }); 
        });
    </script>
<?php include "pages/footer.php"; ?>