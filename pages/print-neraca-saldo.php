<?php
$bulan = $this->validasi->validInput($_GET['bulan']);
$tahun = $this->validasi->validInput($_GET['tahun']);
$jenis_transaksi = $this->validasi->validInput($_GET['jenis_transaksi']);

$tanggal = $this->validasi->validInput($_GET['tanggal']);
$exp = explode("/", $tanggal);
$tgl = $exp[0];
$bulan = $exp[1];
$tahun = $exp[2];

function getSaldoAwal($bulan,$tahun,$idRekening,$tgl){
    $dataRekening = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDRekening='$idRekening'");
    $saldoAwal = newQuery("get_row","SELECT * FROM tb_saldo_awal WHERE IDRekening='$idRekening' and Tahun='$tahun'");
    
    if($saldoAwal) $saldoAwal=$saldoAwal->SaldoAwal; else $saldoAwal=0;
    $kredit=0;
    $debet=0;
    
    $dbulan = intval($bulan)+1;
    if($dbulan<10) $dbulan = '0'.$dbulan;
    $debet = newQuery("get_var","SELECT SUM(Debet) FROM tb_jurnal_detail WHERE IDRekening='$idRekening' AND DATE_FORMAT(Tanggal,'%Y-%m-%d') <= '$tahun-$bulan-$tgl' AND DATE_FORMAT(Tanggal,'%Y') >= '$tahun'");
    if(!$debet) $debet=0;
    $kredit = newQuery("get_var","SELECT SUM(Kredit) FROM tb_jurnal_detail WHERE IDRekening='$idRekening' AND DATE_FORMAT(Tanggal,'%Y-%m-%d') <= '$tahun-$bulan-$tgl' AND DATE_FORMAT(Tanggal,'%Y') >= '$tahun'");
    if(!$kredit) $kredit=0;
    
    if($dataRekening->Posisi=='Debet'){
        $closing = $saldoAwal+$debet-$kredit;
    } else {
        $closing = $saldoAwal-$debet+$kredit;
    }
    return $closing;
}

$totalDebet = 0;
$totalKredit = 0;
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content=""/>
    <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543"/>
    <title>Lintas Daya Accounting</title>
    <link rel="icon" type="image/png" href="<?php echo PRSONTEMPPATH; ?>dist/img/favicon.png"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"/>
    <link rel="stylesheet" href="<?php echo PRSONTEMPPATH; ?>css/print-style.css" media="all"/>
</head>
<body>
    <div class="laporanTitle" style="border-top: none;font-size: 12px;">
        <h1 style="font-size: 18px;padding-bottom:10px;">** NERACA SALDO **</h1>
        <h1 style="font-size: 18px;">CV. LINTAS DAYA</h1>
        <p>Periode Sampai Dengan : <?php echo $tanggal; ?></p>
    </div>

    <table class="tabelList8 border-solid" cellpadding="0" cellspacing="0" style="max-width: 700px;margin:0 auto;">
        <tr>
            <td width="50%">
                <table class="tabelList6 border-solid tabel-neraca" cellpadding="0" cellspacing="0" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="border-bottom" style="text-align: left;" width="40">No. Akun</th>
                            <th class="border-left border-bottom" style="text-align: left;">Nama Perkiraan</th>
                            <th class="border-left border-bottom" width="80">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            $kol = 0;
                            $query = newQuery("get_results","SELECT * FROM tb_master_rekening WHERE IDParent='0' AND Posisi='Debet' ORDER BY KodeRekening ASC");
                            if($query){
                                foreach($query as $data){
                                    $query2 = newQuery("get_results","SELECT * FROM tb_master_rekening WHERE IDParent='".$data->IDRekening."' ORDER BY KodeRekening ASC");
                                    if($query2){
                                        foreach($query2 as $data2){
                                            if($data2->Tipe=="D"){
                                                $saldo = getSaldoAwal($bulan,$tahun,$data2->IDRekening,$tgl);
                                                if($saldo!=0){
                                                    $totalDebet += $saldo;
                                                    $kol++;
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $data2->KodeRekening; ?></td>
                                                        <td class="border-left"><?php echo $data2->NamaRekening; ?></td>
                                                        <td class="border-left" style="text-align: right;"><?php echo number_format($saldo,2);?></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                            $query3 = newQuery("get_results","SELECT * FROM tb_master_rekening WHERE IDParent='".$data2->IDRekening."' ORDER BY KodeRekening ASC");
                                            if($query3){
                                                foreach($query3 as $data3){
                                                    if($data3->Tipe=="D"){
                                                        $saldo = getSaldoAwal($bulan,$tahun,$data3->IDRekening,$tgl);
                                                        if($saldo!=0){
                                                            $totalDebet += $saldo;
                                                            $kol++;
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $data3->KodeRekening; ?></td>
                                                                <td class="border-left"><?php echo $data3->NamaRekening; ?></td>
                                                                <td class="border-left" style="text-align: right;"><?php echo number_format($saldo,2);?></td>
                                                            </tr>
                                                            <?php
                                                        }
                                                    }
                                                    $query4 = newQuery("get_results","SELECT * FROM tb_master_rekening WHERE IDParent='".$data3->IDRekening."' ORDER BY KodeRekening ASC");
                                                    if($query4){
                                                        foreach($query4 as $data4){
                                                            if($data4->Tipe=="D"){
                                                                $saldo = getSaldoAwal($bulan,$tahun,$data4->IDRekening,$tgl);
                                                                if($saldo!=0){
                                                                    $totalDebet += $saldo;
                                                                    $kol++;
                                                                    ?>
                                                                    <tr>
                                                                        <td><?php echo $data4->KodeRekening; ?></td>
                                                                        <td class="border-left"><?php echo $data4->NamaRekening; ?></td>
                                                                        <td class="border-left" style="text-align: right;"><?php echo number_format($saldo,2);?></td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                            }
                                                            $query5 = newQuery("get_results","SELECT * FROM tb_master_rekening WHERE IDParent='".$data4->IDRekening."' ORDER BY KodeRekening ASC");
                                                            if($query5){
                                                                foreach($query5 as $data5){
                                                                    if($data5->Tipe=="D"){
                                                                        $saldo = getSaldoAwal($bulan,$tahun,$data5->IDRekening,$tgl);
                                                                        if($saldo!=0){
                                                                            $totalDebet += $saldo;
                                                                            $kol++;
                                                                            ?>
                                                                            <tr>
                                                                                <td><?php echo $data5->KodeRekening; ?></td>
                                                                                <td class="border-left"><?php echo $data5->NamaRekening; ?></td>
                                                                                <td class="border-left" style="text-align: right;"><?php echo number_format($saldo,2);?></td>
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
                                }
                            }
                            ?>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td>
                <table class="tabelList6 border-solid" cellpadding="0" cellspacing="0" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="border-bottom" style="text-align: left;" width="40">No. Akun</th>
                            <th class="border-left border-bottom" style="text-align: left;">Nama Perkiraan</th>
                            <th class="border-left border-bottom" width="80">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            $kol2 = 0;
                            $query = newQuery("get_results","SELECT * FROM tb_master_rekening WHERE IDParent='0' AND Posisi='Kredit' ORDER BY KodeRekening ASC");
                            if($query){
                                foreach($query as $data){
                                    $query2 = newQuery("get_results","SELECT * FROM tb_master_rekening WHERE IDParent='".$data->IDRekening."' ORDER BY KodeRekening ASC");
                                    if($query2){
                                        foreach($query2 as $data2){
                                            if($data2->Tipe=="D"){
                                                $saldo = getSaldoAwal($bulan,$tahun,$data2->IDRekening,$tgl);
                                                if($saldo!=0){
                                                    $totalKredit += $saldo;
                                                    $kol2++;
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $data2->KodeRekening; ?></td>
                                                        <td class="border-left"><?php echo $data2->NamaRekening; ?></td>
                                                        <td class="border-left" style="text-align: right;"><?php echo number_format($saldo,2);?></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                            $query3 = newQuery("get_results","SELECT * FROM tb_master_rekening WHERE IDParent='".$data2->IDRekening."' ORDER BY KodeRekening ASC");
                                            if($query3){
                                                foreach($query3 as $data3){
                                                    if($data3->Tipe=="D"){
                                                        $saldo = getSaldoAwal($bulan,$tahun,$data3->IDRekening,$tgl);
                                                        if($saldo!=0){
                                                            $totalKredit += $saldo;
                                                            $kol2++;
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $data3->KodeRekening; ?></td>
                                                                <td class="border-left"><?php echo $data3->NamaRekening; ?></td>
                                                                <td class="border-left" style="text-align: right;"><?php echo number_format($saldo,2);?></td>
                                                            </tr>
                                                            <?php
                                                        }
                                                    }
                                                    $query4 = newQuery("get_results","SELECT * FROM tb_master_rekening WHERE IDParent='".$data3->IDRekening."' ORDER BY KodeRekening ASC");
                                                    if($query4){
                                                        foreach($query4 as $data4){
                                                            if($data4->Tipe=="D"){
                                                                $saldo = getSaldoAwal($bulan,$tahun,$data4->IDRekening,$tgl);
                                                                if($saldo!=0){
                                                                    $totalKredit += $saldo;
                                                                    $kol2++;
                                                                    ?>
                                                                    <tr>
                                                                        <td><?php echo $data4->KodeRekening; ?></td>
                                                                        <td class="border-left"><?php echo $data4->NamaRekening; ?></td>
                                                                        <td class="border-left" style="text-align: right;"><?php echo number_format($saldo,2);?></td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                            }
                                                            $query5 = newQuery("get_results","SELECT * FROM tb_master_rekening WHERE IDParent='".$data4->IDRekening."' ORDER BY KodeRekening ASC");
                                                            if($query5){
                                                                foreach($query5 as $data5){
                                                                    if($data5->Tipe=="D"){
                                                                        $saldo = getSaldoAwal($bulan,$tahun,$data5->IDRekening,$tgl);
                                                                        if($saldo!=0){
                                                                            $totalKredit += $saldo;
                                                                            $kol2++;
                                                                            ?>
                                                                            <tr>
                                                                                <td><?php echo $data5->KodeRekening; ?></td>
                                                                                <td class="border-left"><?php echo $data5->NamaRekening; ?></td>
                                                                                <td class="border-left" style="text-align: right;"><?php echo number_format($saldo,2);?></td>
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
                                }
                            }
                            $sisakol = $kol-$kol2;
                            if($sisakol>0){
                                for($i=0;$i<$sisakol;$i++){
                                    ?>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td class="border-left"></td>
                                        <td class="border-left"></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table class="tabelList6 border-solid tabel-neraca" cellpadding="0" cellspacing="0" style="width: 100%">
                    <tbody>
                        <tr>
                            <th style="text-align: right;font-weight: bold;">Total Aktiva : </th>
                            <th class="border-left" style="text-align: right;" width="80"><?php echo number_format($totalDebet,2); ?></th>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td>
                <table class="tabelList6 border-solid tabel-neraca" cellpadding="0" cellspacing="0" style="width: 100%">
                    <tbody>
                        <tr>
                            <th style="text-align: right;font-weight: bold;">Total Pasiva (Kewajiban + Modal) : </th>
                            <th class="border-left" style="text-align: right;" width="80"><?php echo number_format($totalKredit,2); ?></th>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    <script type="text/javascript">
        window.onload = function () { window.print(); }
    </script>
</body>
</html>