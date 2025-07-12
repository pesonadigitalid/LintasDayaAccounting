<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
            <?php
            $tahun = $_GET['tahun'];
            $bulanList = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember");
            if($_POST['submitk']){
                $tnilai = $_POST['targetnilai'];
                
                foreach($tnilai as $key=>$val){
                    $bulan = $key+1;
                    $cekData = newQuery("get_row","SELECT * FROM tb_target_kontrak WHERE Bulan='$bulan' AND Tahun='$tahun'");
                    if($cekData){
                        newQuery("query","UPDATE tb_target_kontrak SET Tahun='$tahun', Bulan='$bulan', TargetNilai='".str_replace(",","",$val)."', CreatedBy='".$_SESSION['userIDAdmin']."', DateCreated=NOW() WHERE IDKontrak='".$cekData->IDKontrak."'");
                    } else {
                        newQuery("query","INSERT INTO tb_target_kontrak SET Tahun='$tahun', Bulan='$bulan', TargetNilai='".str_replace(",","",$val)."', CreatedBy='".$_SESSION['userIDAdmin']."', DateCreated=NOW()");
                    }
                    //echo $cekData->IDKontrak."/".$key."/".$bulan."/".$val."/".$cekData->TargetNilai."<br />";
                }
                $notif = array("class"=>"success","msg"=>"Target kontrak berhasil disimpan!");
            }
            ?>
            <div class="columns">
                <div class="column col-6">
                    <h5>Setting Target Kontrak Bulanan Lintas Daya<small>Pengelolaan target kontrak bulanan lintas daya</small></h5>
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
            <form method="POST" action="">
            <table class="table new-table">
                <thead>
                    <tr>
                        <th width="20">No.</th>
                        <th>Bulan / Tahun</th>
                        <th width="150">Target Nilai</th>
                    </tr>
                </thead>
                <body>
                    <tr>
                        <td class="spacer" colspan="6"></td>
                    </tr>
                    <?php
                    for($i=1;$i<=12;$i++){
                        if($i<10) $bln = "0".$i; else $bln = $i;
                        $targetNilai = newQuery("get_var","SELECT TargetNilai FROM tb_target_kontrak WHERE Tahun='$tahun' AND Bulan='$i'");
                        if($targetNilai) $targetNilai = $targetNilai; else $targetNilai=0;
                        ?>
                        <tr class="all">
                            <td><?php echo $i; ?></td>
                            <td><strong> <?php echo $bulanList[$bln]." ".$tahun; ?></strong></td>
                            <td>
                                <input type="text" name="targetnilai[]" class="form-input price small-input" value="<?php if($targetnilai[$i]!="") echo $targetnilai[$i]; else echo number_format($targetNilai); ?>"/>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </body>
            </table>
            <div class="columns">            
                <div class="column col-3" style="padding-top: 30px;">
                <button type="submit" name="submitk" value="1" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Target Kontrak</button>                      
            </div>
            </form>
        </section>
    </section>
    <script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/pikaday.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){ 
            $('.price').focus(function(){
               $(this).val($(this).val().toString().replace(/,/g,"")); 
            });
            
            $('.price').focusout(function(){
               $(this).val(numberWithCommas($(this).val())); 
            });
        });
        function numberWithCommas(x) {
            var x = x.toString().replace(/,/g,"");
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        
        function filtertahun(){
            window.location.href = '<?php echo PRSONPATH; ?>setting-target-kontrak/?tahun='+$('#tahunSaldoAwal').val();
        }
    </script>
<?php include "pages/footer.php"; ?>