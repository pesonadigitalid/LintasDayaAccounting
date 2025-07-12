<?php
$tanggal = date("d/m/Y"); 
if($_POST['submit']){
    $tanggal = $_POST['tanggal'];
    $exp = explode("/",$tanggal);
    $tanggal = $exp[2]."-".$exp[1]."-".$exp[0];
    $jenis_transaksi = $_POST['jenis_transaksi'];
    $dataLast = newQuery("get_row","SELECT * FROM tb_jurnal WHERE DATE_FORMAT(Tanggal,'%Y-%m')='".date("Y-m")."' ORDER BY NoJurnal DESC");
    if($dataLast) $last = substr($dataLast->NoJurnal,-5); else $last=0;
    do{
        $last++;
        if($last<10000 and $last>=1000)
            $last = "0".$last;
        else if($last<1000 and $last>=100)
            $last = "00".$last;
        else if($last<100 and $last>=10)
            $last = "000".$last;
        else if($last<10)
            $last = "0000".$last;
        $noTransaksi = "01-".date("Ym").$last;
        $checkNoTransaksi = newQuery("get_row","SELECT * FROM tb_jurnal WHERE NoJurnal='$noTransaksi'");
    } while($checkNoTransaksi);
    $dTransaksi = newQuery("get_row","SELECT * FROM tb_master_transaksi WHERE IDTransaksi='$jenis_transaksi'");
    $query = newQuery("query","INSERT INTO tb_jurnal SET NoJurnal='$noTransaksi', Status='2', Keterangan='".$dTransaksi->Keterangan."', Debet='0', Kredit='0', CreatedBy='1', DateCreated=NOW(), Tanggal='$tanggal'");
    $idjurnal = newQuery("get_var","SELECT LAST_INSERT_ID()");
    $qTransaksi = newQuery("get_results","SELECT a.*, b.IDCurrency FROM tb_master_transaksi_rekening a, tb_master_rekening b WHERE a.IDTransaksi='$jenis_transaksi' AND a.IDRekening=b.IDRekening");
    if($qTransaksi){
        foreach($qTransaksi as $dtTransaksi){
            $closing = newQuery("get_var","SELECT Closing FROM tb_jurnal_detail WHERE IDRekening='".$dtTransaksi->IDRekening."' ORDER BY IDJurnalDetail DESC");
            newQuery("query","INSERT INTO tb_jurnal_detail SET IDJurnal='$idjurnal', IDRekening='".$dtTransaksi->IDRekening."', Tanggal='$tanggal', Debet='0', Kredit='0', Closing='$closing', MataUang='".$dtTransaksi->IDCurrency."', Kurs='0', Keterangan='".$dTransaksi->Keterangan."', Pos='".$dtTransaksi->Posisi."'");
        }
    }
    header("location: ".PRSONPATH."jurnal-transaksi/".$noTransaksi);
}          
?>
<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
            <div class="col-sm-7">
                <h5>Input Jurnal Transaksi<small>Pengelolaan data jurnal transaksi</small></h5>
                <?php if(isset($notif)){ ?>
                    <div class="toast toast-<?php echo $notif['class']; ?>">
                        <button class="btn btn-clear float-right"></button>
                        <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
                    </div>
                <?php } ?>
                <div class="form-header">Formulir Jurnal Transaksi</div>
                <form method="POST" action="" class="form-horizontal form-bordered">
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label class="form-label" for="input-example-1">Tanggal</label>
                        </div>
                        <div class="col-sm-9">
                            <input class="form-input input-calendar" id="tanggal" name="tanggal" type="text" value="<?php echo $tanggal; ?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label class="form-label" for="input-example-1">Jenis Transaksi</label>
                        </div>
                        <div class="col-sm-9">
                            <select name="jenis_transaksi" id="jenis_transaksi" class="form-select" style="width: 100%;">
                                <?php
                                $query = newQuery("get_results","SELECT * FROM tb_master_transaksi WHERE STATUS='1' ORDER BY Keterangan ASC");
                                if($query){
                                    foreach($query as $data){
                                        ?><option value="<?php echo $data->IDTransaksi; ?>" <?php if($jenis_transaksi==$data->IDTransaksi) echo "selected"; ?>><?php echo $data->KodeTransaksi." - ".ucwords(strtolower($data->Keterangan)); ?></option><?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-footer">
                        <a href="<?php echo PRSONPATH; ?>history-jurnal/" class="btn btn-link modal-close-trigger">Kembali</a>
                        <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-save"></i> Selanjutnya</button>
                    </div>
                </form>
            </div>
        </section>
    </section>
    <script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/pikaday.js"></script>
    <link href="<?php echo PRSONTEMPPATH; ?>css/select2.min.css" rel="stylesheet" />
    <script src="<?php echo PRSONTEMPPATH; ?>scripts/select2.min.js"></script>
    <script type="text/javascript">
        var pickerDefault = new Pikaday({
            field: document.getElementById('tanggal'),
            format: 'DD/MM/YYYY',
        });
        
        $(document).ready(function(){
            $("#jenis_transaksi").select2();
        });
    </script>
<?php include "pages/footer.php"; ?>