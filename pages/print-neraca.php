<?php
$bulan = $this->validasi->validInput($_GET['bulan']);
$tahun = $this->validasi->validInput($_GET['tahun']);
$jenis_transaksi = $this->validasi->validInput($_GET['jenis_transaksi']);
if($jenis_transaksi!="Semua Rekening"){
    $cond = " AND a.IDRekening='$jenis_transaksi'";
}
$tanggal = $tahun."-".$bulan."-01";
$tanggalID = "01/".$bulan."/".$tahun;
$periode = $tahun."-".$bulan;
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
    <table>
        <tr>
            <td width="50%" class="bottom">
                <h1>CV. LINTAS DAYA</h1>
                <p style="margin-top: 5px;">JL. Tukad Citarum I, No. 10, Renon, Perum Surya Graha Asih, Kota Denpasar, Bali<br />Phone. (0361) 238055, Fax. -</p>
                </td>
            <td width="50%" align="right" class="bottom">
                Tanggal Cetak : <?php echo date("d/m/Y"); ?>
            </td>
        </tr>
    </table>
    <div class="laporanTitle">
        <h1 class="underline">** NERACA **</h1>Periode : <?php echo $this->fungsi->changeMonthNameID($bulan)." ".$tahun; ?>
    </div>
    <table class="tabelList2 noBorder" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td style="text-align: left;"></td>
                <td width="100"></td>
            </tr>
            <tr>
                <td><strong>Aktiva</strong></td>
                <td></td>
            </tr>
            <?php
            /* AKTIVA */
            function getSaldoAwal($bulan,$tahun,$idRekening){
                $dataRekening = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDRekening='$idRekening'");
                $saldoAwal = newQuery("get_row","SELECT * FROM tb_saldo_awal WHERE IDRekening='$idRekening' and Tahun='$tahun'");
                
                if($saldoAwal) $saldoAwal=$saldoAwal->SaldoAwal; else $saldoAwal=0;
                $kredit=0;
                $debet=0;
                
                $dbulan = intval($bulan)+1;
                if($dbulan<10) $dbulan = '0'.$dbulan;
                $debet = newQuery("get_var","SELECT SUM(Debet) FROM tb_jurnal_detail WHERE IDRekening='$idRekening' AND DATE_FORMAT(Tanggal,'%Y-%m') < '$tahun-$dbulan' AND DATE_FORMAT(Tanggal,'%Y') >= '$tahun'");
                if(!$debet) $debet=0;
                $kredit = newQuery("get_var","SELECT SUM(Kredit) FROM tb_jurnal_detail WHERE IDRekening='$idRekening' AND DATE_FORMAT(Tanggal,'%Y-%m') < '$tahun-$dbulan' AND DATE_FORMAT(Tanggal,'%Y') >= '$tahun'");
                if(!$kredit) $kredit=0;
                
                if($dataRekening->Posisi=='Debet'){
                    $closing = $saldoAwal+$debet-$kredit;
                } else {
                    $closing = $saldoAwal-$debet+$kredit;
                }
                return $closing;
            }
            
            $data = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE KodeRekening='1-0000'");
            $totalAktiva = 0;
            if($data){
                $query2 = newQuery("get_results","SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.IDParent='".$data->IDRekening."' ORDER BY a.KodeRekening ASC");
                if($query2){
                    foreach($query2 as $data2){
                        if($data2->Tipe=="D"){
                            $dataRekening = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDRekening='".$data2->IDRekening."'");
                            $closing = getSaldoAwal($bulan,$tahun,$data2->IDRekening);
                            $kurs = 1;
                            if($data3->IDCurrency>1){ $closing=$closing*$kurs; }
                            if($closing!=0){
                                $dDebet = $closing;
                                $totalAktiva += $dDebet;
                                ?>
                                <tr>
                                    <td style="padding-left: 20px;"><?php echo (($data2->KodeRekening." ".$data2->NamaRekening)); ?></td>
                                    <td style="text-align: right;"><?php if($dDebet>=0) echo number_format($dDebet,2); else echo "(".number_format(abs($dDebet),2).")"; ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            $query3 = newQuery("get_results","SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.IDParent='".$data2->IDRekening."' ORDER BY a.KodeRekening ASC");
                            if($query3){
                                foreach($query3 as $data3){
                                    if($data3->Tipe=="D"){
                                        $dataRekening = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDRekening='".$data3->IDRekening."'");
                                        $closing = getSaldoAwal($bulan,$tahun,$data3->IDRekening);
                                        $kurs = 1;
                                        if($data3->IDCurrency>1){ $closing=$closing*$kurs; }
                                        if($closing!=0){
                                            $dDebet = $closing;
                                            $totalAktiva += $dDebet;
                                            ?>
                                            <tr>
                                                <td style="padding-left: 20px;"><?php echo (($data3->KodeRekening." ".$data3->NamaRekening)); ?></td>
                                                <td style="text-align: right;"><?php if($dDebet>=0) echo number_format($dDebet,2); else echo "(".number_format(abs($dDebet),2).")"; ?></td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        $total4 = 0;
                                        $query4 = newQuery("get_results","SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.IDParent='".$data3->IDRekening."' ORDER BY a.KodeRekening ASC");
                                        if($query4){
                                            foreach($query4 as $data4){
                                                if($data4->Tipe=="D"){
                                                    $dataRekening = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDRekening='".$data4->IDRekening."'");
                                                    $closing = getSaldoAwal($bulan,$tahun,$data4->IDRekening);
                                                    $kurs = 1;
                                                    if($data4->IDCurrency>1){ $closing=$closing*$kurs; }
                                                    if($closing!=0){
                                                        $dDebet = $closing;
                                                        $totalAktiva += $dDebet;
                                                        ?>
                                                        <tr>
                                                            <td style="padding-left: 20px;"><?php echo (($data4->KodeRekening." ".$data4->NamaRekening)); ?></td>
                                                            <td style="text-align: right;"><?php if($dDebet>=0) echo number_format($dDebet,2); else echo "(".number_format(abs($dDebet),2).")"; ?></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                } else {
                                                    $query5 = newQuery("get_results","SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.IDParent='".$data4->IDRekening."' ORDER BY a.KodeRekening ASC");
                                                    if($query5){
                                                        foreach($query5 as $data5){
                                                            if($data5->Tipe=="D"){
                                                                $dataRekening = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDRekening='".$data5->IDRekening."'");
                                                                $closing = getSaldoAwal($bulan,$tahun,$data5->IDRekening);
                                                                $kurs = 1;
                                                                if($data5->IDCurrency>1){ $closing=$closing*$kurs; }
                                                                if($closing!=0){
                                                                    $dDebet = $closing;
                                                                    $totalAktiva += $dDebet;
                                                                    ?>
                                                                    <tr>
                                                                        <td style="padding-left: 20px;"><?php echo (($data5->KodeRekening." ".$data5->NamaRekening)); ?></td>
                                                                        <td style="text-align: right;"><?php if($dDebet>=0) echo number_format($dDebet,2); else echo "(".number_format(abs($dDebet),2).")"; ?></td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                            } else {
                                                                
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
            }
            ?>
            <tr>
                <td><strong>Total Aktiva</strong></td>
                <td style="text-align: right;"><div style="border-top: solid 1px #000;padding-top: 5px;"><?php if($totalAktiva>=0) echo number_format($totalAktiva,2); else echo "(".number_format(abs($totalAktiva),2).")"; ?></div></td>
            </tr>
            <tr>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td><strong>Kewajiban</strong></td>
                <td></td>
            </tr>
            <?php
            /* Kewajiban */
            $data = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE KodeRekening='2-0000'");
            $totalKewajiban = 0;
            if($data){
                $query2 = newQuery("get_results","SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.IDParent='".$data->IDRekening."' ORDER BY a.KodeRekening ASC");
                if($query2){
                    foreach($query2 as $data2){
                        if($data2->Tipe=="D"){
                            $dataRekening = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDRekening='".$data2->IDRekening."'");
                            $closing = getSaldoAwal($bulan,$tahun,$data2->IDRekening);
                            $kurs = 1;
                            if($data3->IDCurrency>1){ $closing=$closing*$kurs; }
                            if($closing!=0){
                                $dDebet = $closing;
                                $totalKewajiban += $dDebet;
                                ?>
                                <tr>
                                    <td style="padding-left: 20px;"><?php echo (($data2->KodeRekening." ".$data2->NamaRekening)); ?></td>
                                    <td style="text-align: right;"><?php if($dDebet>=0) echo number_format($dDebet,2); else echo "(".number_format(abs($dDebet),2).")"; ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            $query3 = newQuery("get_results","SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.IDParent='".$data2->IDRekening."' ORDER BY a.KodeRekening ASC");
                            if($query3){
                                foreach($query3 as $data3){
                                    if($data3->Tipe=="D"){
                                        $dataRekening = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDRekening='".$data3->IDRekening."'");
                                        $closing = getSaldoAwal($bulan,$tahun,$data3->IDRekening);
                                        $kurs = 1;
                                        if($data3->IDCurrency>1){ $closing=$closing*$kurs; }
                                        if($closing!=0){
                                            $dDebet = $closing;
                                            $totalKewajiban += $dDebet;
                                            ?>
                                            <tr>
                                                <td style="padding-left: 20px;"><?php echo (($data3->KodeRekening." ".$data3->NamaRekening)); ?></td>
                                                <td style="text-align: right;"><?php if($dDebet>=0) echo number_format($dDebet,2); else echo "(".number_format(abs($dDebet),2).")"; ?></td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        $total4 = 0;
                                        $query4 = newQuery("get_results","SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.IDParent='".$data3->IDRekening."' ORDER BY a.KodeRekening ASC");
                                        if($query4){
                                            foreach($query4 as $data4){
                                                if($data4->Tipe=="D"){
                                                    $dataRekening = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDRekening='".$data4->IDRekening."'");
                                                    $closing = getSaldoAwal($bulan,$tahun,$data4->IDRekening);
                                                    $kurs = 1;
                                                    if($data4->IDCurrency>1){ $closing=$closing*$kurs; }
                                                    if($closing!=0){
                                                        $dDebet = $closing;
                                                        $totalKewajiban += $dDebet;
                                                        ?>
                                                        <tr>
                                                            <td style="padding-left: 20px;"><?php echo (($data4->KodeRekening." ".$data4->NamaRekening)); ?></td>
                                                            <td style="text-align: right;"><?php if($dDebet>=0) echo number_format($dDebet,2); else echo "(".number_format(abs($dDebet),2).")"; ?></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                } else {
                                                    $query5 = newQuery("get_results","SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.IDParent='".$data4->IDRekening."' ORDER BY a.KodeRekening ASC");
                                                    if($query5){
                                                        foreach($query5 as $data5){
                                                            if($data5->Tipe=="D"){
                                                                $dataRekening = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDRekening='".$data5->IDRekening."'");
                                                                $closing = getSaldoAwal($bulan,$tahun,$data5->IDRekening);
                                                                $kurs = 1;
                                                                if($data5->IDCurrency>1){ $closing=$closing*$kurs; }
                                                                if($closing!=0){
                                                                    $dDebet = $closing;
                                                                    $totalKewajiban += $dDebet;
                                                                    ?>
                                                                    <tr>
                                                                        <td style="padding-left: 20px;"><?php echo (($data5->KodeRekening." ".$data5->NamaRekening)); ?></td>
                                                                        <td style="text-align: right;"><?php if($dDebet>=0) echo number_format($dDebet,2); else echo "(".number_format(abs($dDebet),2).")"; ?></td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                            } else {
                                                                
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
            }
            ?>
            <tr>
                <td><strong>Total Kewajiban</strong></td>
                <td style="text-align: right;"><div style="border-top: solid 1px #000;padding-top: 5px;"><?php if($totalKewajiban>=0) echo number_format($totalKewajiban,2); else echo "(".number_format(abs($totalKewajiban),2).")"; ?></div></td>
            </tr>
            
            <tr>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td><strong>Modal</strong></td>
                <td></td>
            </tr>
            <?php
            /* Modal */
            $data = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE KodeRekening='3-0000'");
            $totalModal = 0;
            if($data){
                $query2 = newQuery("get_results","SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.IDParent='".$data->IDRekening."' ORDER BY a.KodeRekening ASC");
                if($query2){
                    foreach($query2 as $data2){
                        if($data2->Tipe=="D"){
                            $dataRekening = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDRekening='".$data2->IDRekening."'");
                            $closing = getSaldoAwal($bulan,$tahun,$data2->IDRekening);
                            $kurs = 1;
                            if($data3->IDCurrency>1){ $closing=$closing*$kurs; }
                            if($closing!=0){
                                $dDebet = $closing;
                                $totalModal += $dDebet;
                                ?>
                                <tr>
                                    <td style="padding-left: 20px;"><?php echo (($data2->KodeRekening." ".$data2->NamaRekening)); ?></td>
                                    <td style="text-align: right;"><?php if($dDebet>=0) echo number_format($dDebet,2); else echo "(".number_format(abs($dDebet),2).")"; ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            $query3 = newQuery("get_results","SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.IDParent='".$data2->IDRekening."' ORDER BY a.KodeRekening ASC");
                            if($query3){
                                foreach($query3 as $data3){
                                    if($data3->Tipe=="D"){
                                        $dataRekening = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDRekening='".$data3->IDRekening."'");
                                        $closing = getSaldoAwal($bulan,$tahun,$data3->IDRekening);
                                        $kurs = 1;
                                        if($data3->IDCurrency>1){ $closing=$closing*$kurs; }
                                        if($closing!=0){
                                            $dDebet = $closing;
                                            $totalModal += $dDebet;
                                            ?>
                                            <tr>
                                                <td style="padding-left: 20px;"><?php echo (($data3->KodeRekening." ".$data3->NamaRekening)); ?></td>
                                                <td style="text-align: right;"><?php if($dDebet>=0) echo number_format($dDebet,2); else echo "(".number_format(abs($dDebet),2).")"; ?></td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        $total4 = 0;
                                        $query4 = newQuery("get_results","SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.IDParent='".$data3->IDRekening."' ORDER BY a.KodeRekening ASC");
                                        if($query4){
                                            foreach($query4 as $data4){
                                                if($data4->Tipe=="D"){
                                                    $dataRekening = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDRekening='".$data4->IDRekening."'");
                                                    
                                                    $closing = getSaldoAwal($bulan,$tahun,$data4->IDRekening);
                                                    $kurs = 1;
                                                    if($data4->IDCurrency>1){ $closing=$closing*$kurs; }
                                                    if($closing!=0){
                                                        $dDebet = $closing;
                                                        $totalModal += $dDebet;
                                                        ?>
                                                        <tr>
                                                            <td style="padding-left: 20px;"><?php echo (($data4->KodeRekening." ".$data4->NamaRekening)); ?></td>
                                                            <td style="text-align: right;"><?php if($dDebet>=0) echo number_format($dDebet,2); else echo "(".number_format(abs($dDebet),2).")"; ?></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                } else {
                                                    $query5 = newQuery("get_results","SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.IDParent='".$data4->IDRekening."' ORDER BY a.KodeRekening ASC");
                                                    if($query5){
                                                        foreach($query5 as $data5){
                                                            if($data5->Tipe=="D"){
                                                                $dataRekening = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDRekening='".$data5->IDRekening."'");
                                                                $closing = getSaldoAwal($bulan,$tahun,$data5->IDRekening);
                                                                $kurs = 1;
                                                                if($data5->IDCurrency>1){ $closing=$closing*$kurs; }
                                                                if($closing!=0){
                                                                    $dDebet = $closing;
                                                                    $totalModal += $dDebet;
                                                                    ?>
                                                                    <tr>
                                                                        <td style="padding-left: 20px;"><?php echo (($data5->KodeRekening." ".$data5->NamaRekening)); ?></td>
                                                                        <td style="text-align: right;"><?php if($dDebet>=0) echo number_format($dDebet,2); else echo "(".number_format(abs($dDebet),2).")"; ?></td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                            } else {
                                                                
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
            }
            ?>
            <tr>
                <td><strong>Total Modal</strong></td>
                <td style="text-align: right;"><div style="border-top: solid 1px #000;padding-top: 5px;"><?php if($totalModal>=0) echo number_format($totalModal,2); else echo "(".number_format(abs($totalModal),2).")"; ?></div></td>
            </tr>
            <?php $total = $totalModal+$totalKewajiban; ?>
            <tr>
                <td><strong>Total Kewajiban dan Modal</strong></td>
                <td style="text-align: right;"><div style="border-top: solid 1px #000;padding-top: 5px;"><?php if($total>=0) echo number_format($total,2); else echo "(".number_format(abs($total),2).")"; ?></div></td>
            </tr>
        </tbody>
    </table>
    <script type="text/javascript">
        window.onload = function () { window.print(); }
    </script>
</body>
</html>