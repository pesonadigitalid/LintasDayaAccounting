<?php
$bulan = date("m");
$tahun = date("Y");

function getSaldoAkhir($bulan,$tahun,$idRekening){
    $dataRekening = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDRekening='$idRekening'");
    $saldoAwal = newQuery("get_row","SELECT * FROM tb_saldo_awal WHERE IDRekening='$idRekening' and Tahun='$tahun'");
    
    if($saldoAwal) $saldoAwal=$saldoAwal->SaldoAwal; else $saldoAwal=0;
    $kredit=0;
    $debet=0;
    
    $debet = newQuery("get_var","SELECT SUM(Debet) FROM tb_jurnal_detail WHERE IDRekening='$idRekening' AND DATE_FORMAT(Tanggal,'%Y-%m') <= '$tahun-$bulan' AND DATE_FORMAT(Tanggal,'%Y') = '$tahun'");
    if(!$debet) $debet=0;
    $kredit = newQuery("get_var","SELECT SUM(Kredit) FROM tb_jurnal_detail WHERE IDRekening='$idRekening' AND DATE_FORMAT(Tanggal,'%Y-%m') <= '$tahun-$bulan' AND DATE_FORMAT(Tanggal,'%Y') = '$tahun'");
    if(!$kredit) $kredit=0;
    
    if($dataRekening->Posisi=='Debet'){
        $closing = $saldoAwal+$debet-$kredit;
    } else {
        $closing = $saldoAwal-$debet+$kredit;
    }
    return $closing;
}

if($_POST['submit']){
    $tahun = $this->validasi->validInput($_POST['tahun']);
    $tahun2 = $tahun+1;
    $query = newQuery("get_results","SELECT a.* FROM tb_master_rekening a WHERE a.Tipe='D' $cond ORDER BY a.KodeRekening ASC");
    if($query){
        foreach($query as $data){
            $dataRekening = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDRekening='".$data->IDRekening."'");
            $closing = getSaldoAkhir(12,$tahun,$data->IDRekening);
            $pref = substr($data->KodeRekening, 0, 1);
            if($pref=="1" || $pref=="5" ||  $pref=="6" ||  $pref=="8") $pos = "Debet";
            else $pos = "Kredit";
            $cek = newQuery("get_row","SELECT * FROM tb_saldo_awal WHERE IDRekening='$data->IDRekening' AND Tahun='$tahun2'");
            if($cek){
                newQuery("query","UPDATE tb_saldo_awal SET SaldoAwal='$closing', Posisi='".$pos."' WHERE IDSaldo='".$cek->IDSaldo."'");
            } else {
                newQuery("query","INSERT INTO tb_saldo_awal SET SaldoAwal='$closing', Posisi='".$pos."', IDRekening='$data->IDRekening', Tahun='$tahun2'");
            }
        }
    }
    $notif = array("class"=>"success","msg"=>"Tutup Buku Besar berhasil. Saldo terakhir telah dijadikan Saldo Awal untuk tahun $tahun2.");
}
?>
<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
            <div class="col-sm-7">
                <h5>Tutup Buku<small>Tutup Periode Buku Besar dan kalkulasi saldo awal tahun</small></h5>
                <?php if(isset($notif)){ ?>
                    <div class="toast toast-<?php echo $notif['class']; ?>">
                        <button class="btn btn-clear float-right"></button>
                        <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
                    </div>
                <?php } ?>
                <div class="form-header">Pilih tahun yang ingin ditutup:</div>
                <form method="POST" action="" class="form-horizontal form-bordered">
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label class="form-label" for="input-example-1">Tahun : </label>
                        </div>
                        <div class="col-sm-9">
                            <select name="tahun" class="form-select">
                                <?php for($i=2012;$i<=date("Y");$i++){ ?>
                                <option value="<?php echo $i; ?>" <?php if($tahun==$i) echo "selected"; ?>><?php echo $i; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-footer">
                        <button type="submit" name="submit" value="111" class="btn btn-danger">Proses Tutup Buku</button>
                    </div>
                </form>
            </div>
        </section>
    </section>
    <link href="<?php echo PRSONTEMPPATH; ?>css/select2.min.css" rel="stylesheet" />
    <script src="<?php echo PRSONTEMPPATH; ?>scripts/select2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $("#jenis_transaksi").select2();
        });
    </script>
<?php include "pages/footer.php"; ?>