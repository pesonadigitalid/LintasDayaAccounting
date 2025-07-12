<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
            <?php
            $tahun = $_GET['tahun'];
            $debet = newQuery("get_var","SELECT SUM(SaldoAwal) FROM tb_saldo_awal WHERE Posisi='Debet' AND Tahun='$tahun'");
            $kredit = newQuery("get_var","SELECT SUM(SaldoAwal) FROM tb_saldo_awal WHERE Posisi='Kredit' AND Tahun='$tahun'");
            $kurs = newQuery("get_row","SELECT * FROM tb_currency WHERE IDCurrency='2'");
            $tanggal_efektif2 = newQuery("get_var","SELECT DATE_FORMAT(TanggalSaldoAwal,'%d/%m/%Y') FROM tb_master_rekening ORDER BY TanggalSaldoAwal DESC");
            if($_POST['submitk']){
                $idr = $_POST['idr'];
                $saldo = $_POST['saldo'];
                $posisi = $_POST['posisi'];
                $cur = $_POST['cur'];
                $tanggal_efektif = $_POST['tanggal_efektif'];
                $tanggal_efektif2 = $_POST['tanggal_efektif'];
                $exp = explode("/",$tanggal_efektif);
                $tanggal_efektif = $exp[2]."-".$exp[1]."-".$exp[0];
                
                $debet = 0;
                $kredit = 0;
                /* CEK POSISI */
                foreach($idr as $key=>$value){
                    $val = str_replace(",","",$saldo[$key]);
                    if($posisi[$key]=="Debet"){
                        if($cur[$key]>1)
                            $debet += $val*$kurs->Kurs;
                        else
                            $debet += $val;
                    } else {
                        if($cur[$key]>1)
                            $kredit += $val*$kurs->Kurs;
                        else
                            $kredit += $val;
                    }
                }
                if($debet!=$kredit){
                    $notif = array("class"=>"danger","msg"=>"Saldo Awal tidak dapat disimpan karena tidak balance. Saldo Debet: <strong>".number_format($debet,2)."</strong>, Saldo Kredit: <strong>".number_format($kredit,2)."</strong>");
                } else {
                    foreach($idr as $key=>$value){
                    if($cur[$key]>1)
                        $kurs2 = $kurs->Kurs;
                    else
                        $kurs2 = 0;
                        $posisi = newQuery("get_var","SELECT Posisi FROM tb_master_rekening WHERE IDRekening='".$value."'");
                        $SaldoAwal = newQuery("get_row","SELECT * FROM tb_saldo_awal WHERE IDRekening='".$value."' AND Tahun='$tahun'");
                        if($SaldoAwal){
                            newQuery("query","UPDATE tb_saldo_awal SET SaldoAwal='".str_replace(",","",$saldo[$key])."', Posisi='$posisi', CreatedBy='".$_SESSION['userIDAdmin']."', DateCreated=NOW() WHERE IDRekening='".$value."' AND Tahun='$tahun'");
                        } else {
                            newQuery("query","INSERT INTO tb_saldo_awal SET Tahun='$tahun', SaldoAwal='".str_replace(",","",$saldo[$key])."', Posisi='$posisi', IDRekening='".$value."', CreatedBy='".$_SESSION['userIDAdmin']."', DateCreated=NOW()");
                        }
                        //newQuery("query","UPDATE tb_master_rekening SET SaldoAwal='".str_replace(",","",$saldo[$key])."', TanggalSaldoAwal='$tanggal_efektif', Kurs='$kurs2' WHERE IDRekening='".$value."'");
                    }
                    $notif = array("class"=>"success","msg"=>"Saldo Awal berhasil disimpan!");
                }
            }
            ?>
            <div class="columns">            
                <div class="column col-6">
                    <h5>Setting Saldo Awal Rekening<small>Pengelolaan saldo awal rekening perkiraan</small></h5>
                </div>
                <div class="column col-6" style="text-align: right; padding-top: 40px;">
                    <select name="tahunSaldoAwal" id="tahunSaldoAwal" style="height: 27px;width: 100px;">
                        <?php
                        for($i=2016;$i<=(date("Y")+1);$i++){
                            ?><option value="<?php echo $i; ?>" <?php if($tahun==$i) echo "selected"; ?>><?php echo $i; ?></option><?php  
                        }
                        ?>
                    </select>
                    <button type="button" onclick="filtertahun()">Filter</button>
                </div>
            </div>
            
            <?php if(isset($notif)){ ?>
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
            <form method="POST" action="">
            <table class="table new-table">
                <thead>
                    <tr>
                        <th width="150">No Rek.</th>
                        <th>Nama Perkiraan</th>
                        <th width="100">Mata Uang</th>
                        <th width="100">Normal</th>
                        <th width="50">Tipe</th>
                        <th width="150">Saldo Awal</th>
                    </tr>
                </thead>
                <body>
                    <tr>
                        <td class="spacer" colspan="6"></td>
                    </tr>
                    <?php
                    $query = newQuery("get_results","SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.Tipe='D' ORDER BY a.KodeRekening ASC");
                    if($query){
                        $i=0;
                        foreach($query as $data){
                            $SaldoAwal = newQuery("get_row","SELECT * FROM tb_saldo_awal WHERE IDRekening='".$data->IDRekening."' AND Tahun='$tahun'");
                            if($SaldoAwal) $SaldoAwal=$SaldoAwal->SaldoAwal; else $SaldoAwal=0;
                            ?>
                            <tr class="all <?php echo substr($data->KodeRekening,0,2)."0000"; ?>">
                                <td><?php echo $data->KodeRekening; ?></td>
                                <td><strong> <?php echo $data->NamaRekening; ?></strong></td>
                                <td><?php echo $data->Nama; ?></td>
                                <td><?php echo $data->Posisi; ?></td>
                                <td><?php echo $data->Tipe; ?></td>
                                <td>
                                    <input type="hidden" name="posisi[]" value="<?php echo $data->Posisi; ?>"/>
                                    <input type="hidden" name="cur[]" value="<?php echo $data->IDCurrency; ?>"/>
                                    <input type="text" name="saldo[]" class="form-input priceAmount price small-input" data-category="<?php echo $data->Posisi; ?>" value="<?php if($saldo[$i]!="") echo $saldo[$i]; else echo number_format($SaldoAwal,2); ?>"/><input type="hidden" name="idr[]" value="<?php echo $data->IDRekening; ?>"/>
                                </td>
                            </tr>
                            <?php
                            $i++;
                        }
                    }
                    ?>
                </body>
            </table>
            <p>
                <div class="columns">            
                        <div class="column col-3" style="padding-top: 30px;">
                        <button type="submit" name="submitk" value="1" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Saldo Awal</button>                        
                        <!--                        
                            <label class="form-label" for="input-example-1">Efektif Per Tanggal: </label>
                            <input class="form-input input-calendar" type="text" name="tanggal_efektif" id="tanggal_efektif" value="<?php echo $tanggal_efektif2; ?>"/>
                        -->
                        </div>
                        <div class="column col-3"></div>
                        <div class="column col-3">
                            <label class="form-label" for="input-example-1">Saldo Awal Debet: </label>
                            <input class="form-input debetAmount" readonly="" type="text" value="<?php echo number_format($debet,2); ?>" style="text-align: right;"/>
                        </div>
                        <div class="column col-3">
                            <label class="form-label" for="input-example-1">Saldo Awal Kredit: </label>
                            <input class="form-input creditAmount" readonly="" type="text" value="<?php echo number_format($kredit,2); ?>" style="text-align: right;"/>
                        </div>
                </div><br />
                
            </p>
            </form>
        </section>
    </section>
    <script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/pikaday.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){ 
            showRecord("1-0000"); 
            $('.price').focus(function(){
               $(this).val($(this).val().toString().replace(/,/g,"")); 
            });
            
            $('.price').focusout(function(){
               $(this).val(numberWithCommas($(this).val())); 
            });

            $('.priceAmount').change(function() {
                var Debet = 0;
                var Kredit = 0;
                $('.priceAmount').each(function(index, el) {
                    val = parseFloat($(el).val().toString().replace(/,/g,""));
                    if($(el).attr("data-category")==="Debet"){
                        Debet += val;
                    } else {
                        Kredit += val;
                    }
                });
                console.log(Debet,Kredit);
                $('.debetAmount').val(Debet);
                $('.creditAmount').val(Kredit);
            });
        });
        function showRecord(a){
            $('.all').hide();
            $('.'+a).fadeIn(400);
        }
        function numberWithCommas(x) {
            var x = x.toString().replace(/,/g,"");
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        
        function filtertahun(){
            window.location.href = '<?php echo PRSONPATH; ?>setting-saldo-awal/?tahun='+$('#tahunSaldoAwal').val();
        }
    </script>
<?php include "pages/footer.php"; ?>