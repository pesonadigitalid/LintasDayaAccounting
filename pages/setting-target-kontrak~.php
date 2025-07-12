<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
            <?php
            $tahun = $_GET['tahun'];
            $bulanList = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember");
            if($_POST['submitk']){
                
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
                        ?>
                        <tr class="all">
                            <td><?php echo $i; ?></td>
                            <td><strong> <?php echo $bulanList[$bln]." ".$tahun; ?></strong></td>
                            <td>
                                <input type="text" name="targetnilai[]" class="form-input price small-input" value="<?php if($targetnilai[$i]!="") echo $targetnilai[$i]; else echo number_format($SaldoAwal); ?>"/>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </body>
            </table>
            </form>
        </section>
    </section>
<?php include "pages/footer.php"; ?>