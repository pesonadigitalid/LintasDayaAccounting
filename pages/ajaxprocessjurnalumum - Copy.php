<?php
include_once "../ajax-loader.php";
$idjurnal = $validasi->validInput($_POST['idjurnal']);
$notransaksi = $validasi->validInput($_POST['notransaksi']);
$no_bukti = $validasi->validInput($_POST['no_bukti']);
$keterangan = $validasi->validInput($_POST['keterangan']);

$tgl_transaksi = $validasi->validInput($_POST['tgl_transaksi']);

$cartArray = $validasi->validInput($_POST['cartArray']);
$balanceDebet = $validasi->validInput($_POST['balanceDebet']);
$balanceKredit = $validasi->validInput($_POST['balanceKredit']);

$cartArray = json_decode($cartArray);
$exp = explode("/",$tgl_transaksi);
$tanggal = $exp[2]."-".$exp[1]."-".$exp[0];
$idTanggal = $exp[2].$exp[1].$exp[0];

$query = $db->query("UPDATE tb_jurnal SET NoBukti='$no_bukti', NoRef='', Tanggal='$tanggal', Debet='$balanceDebet', Kredit='$balanceKredit', ModifiedBy='1', DateModified=NOW(), Keterangan='$keterangan' WHERE IDJurnal='$idjurnal'");
$tanggalSaldoAwal = $db->get_var("SELECT TanggalSaldoAwal FROM tb_master_rekening ORDER BY TanggalSaldoAwal DESC");
if($tanggalSaldoAwal>$tanggal){
    echo json_encode(array("status"=>"2","msg"=>"Anda tidak dapat melakukan penyimpanan pada tanggal tersebut. Saldo Awal anda tidak efektif per tanggal tersebut"));
}
//REPOSITION CLOSING BALANCE
$query = $db->get_results("SELECT * FROM tb_jurnal_detail WHERE IDJurnal='$idjurnal'");
if($query){
    foreach($query as $data){
        $dR = $db->get_row("SELECT * FROM tb_master_rekening WHERE IDRekening='".$data->IDRekening."'");
        
        $diff = $data->Debet-$data->Kredit;
        
        $qRest = $db->get_results("SELECT * FROM tb_jurnal_detail WHERE Tanggal>='".$data->Tanggal."' AND IDRekening='".$data->IDRekening."' ORDER BY Tanggal ASC, IDJurnal ASC");
        if($qRest){
            foreach($qRest as $dRest){
                if($dRest->Tanggal==$data->Tanggal && $dRest->IDJurnalDetail<$data->IDJurnalDetail){
                   // DO NOTHING
                } else
                    $db->query("UPDATE tb_jurnal_detail SET Closing=(Closing-$diff) WHERE IDJurnalDetail='".$dRest->IDJurnalDetail."'");
            }
        }
    }
    $db->query("DELETE FROM tb_jurnal_detail WHERE IDJurnal='$idjurnal'");
}
//REPOSITION CLOSING BALANCE LB KURS
$query = $db->get_results("SELECT * FROM tb_jurnal_detail WHERE JurnalRef='$notransaksi'");
if($query){
    foreach($query as $data){
        $dR = $db->get_row("SELECT * FROM tb_master_rekening WHERE IDRekening='".$data->IDRekening."'");
        $diff = $data->Debet-$data->Kredit;
        $qRest = $db->get_results("SELECT * FROM tb_jurnal_detail WHERE Tanggal>='".$data->Tanggal."' AND IDRekening='".$data->IDRekening."' ORDER BY Tanggal ASC, IDJurnal ASC");
        if($qRest){
            foreach($qRest as $dRest){
                if($dRest->Tanggal==$data->Tanggal && $dRest->IDJurnalDetail<$data->IDJurnalDetail){
                   // DO NOTHING
                } else
                    $db->query("UPDATE tb_jurnal_detail SET Closing=(Closing-$diff) WHERE IDJurnalDetail='".$dRest->IDJurnalDetail."'");
            }
        }
    }
    $db->query("DELETE FROM tb_jurnal_detail WHERE JurnalRef='$notransaksi'");
}
foreach($cartArray as $data){
    if(isset($data)){
        $jurnalTerakhir = $db->get_row("SELECT * FROM tb_jurnal_detail WHERE IDRekening='".$data->IDRekening."' AND Tanggal<='$tanggal' ORDER BY IDJurnalDetail DESC");
        $dataRekening = $db->get_row("SELECT * FROM tb_master_rekening WHERE IDRekening='".$data->IDRekening."'");
        if(!$jurnalTerakhir){
            $closing = $dataRekening->SaldoAwal;
            $closing2 = $dataRekening->SaldoAwal;
            $lastKurs = $dataRekening->Kurs;
        } else {
            $closing = $jurnalTerakhir->Closing;
            $closing2 = $jurnalTerakhir->Closing;
            $lastKurs = $jurnalTerakhir->Kurs;
        }
        
        if($data->Currency!=$dataRekening->IDCurrency){
            if($dataRekening->IDCurrency=="1"){
                $debet = $data->Debet*$data->Kurs;
                $kredit = $data->Kredit*$data->Kurs;
            } else {
                $debet = $data->Debet/$data->Kurs;
                $kredit = $data->Kredit/$data->Kurs;
            }
            $currency = $dataRekening->IDCurrency;
        } else {
            $debet = $data->Debet;
            $kredit = $data->Kredit;
            $currency = $data->Currency;
        }
        
        if($debet>0){
            $closing += $debet;
        }
        if($kredit>0){
            $closing -= $kredit;
        }
        
        $db->query("INSERT INTO tb_jurnal_detail SET IDJurnal='$idjurnal', IDRekening='".$data->IDRekening."', Tanggal='$tanggal', Debet='".$debet."', Kredit='".$kredit."', Closing='$closing', MataUang='".$currency."', Kurs='".$data->Kurs."', Keterangan='".$keterangan."'");
        
        if($dataRekening->IDCurrency>1){
            if($data->Kurs!=$lastKurs){
                $jurnalTerakhir = $db->get_row("SELECT * FROM tb_jurnal_detail WHERE IDRekening='104' AND Tanggal<='$tanggal' ORDER BY IDJurnalDetail DESC");
                $dataRekening = $db->get_row("SELECT * FROM tb_master_rekening WHERE IDRekening='104'");
                if(!$jurnalTerakhir){
                    $closingKurs = $dataRekening->SaldoAwal;
                } else {
                    $closingKurs = $jurnalTerakhir->Closing;
                }
                $diffKurs = ($closing2*$data->Kurs)-($closing2*$lastKurs);
                if($diffKurs>0){
                    $debetKurs = $diffKurs;
                    $kreditKurs = 0;
                } else {
                    $debetKurs = 0;
                    $kreditKurs = abs($diffKurs);
                }
                if($debetKurs>0){
                    $closingKurs += $debetKurs;
                }
                if($kreditKurs>0){
                    $closingKurs -= $kreditKurs;
                }
                /* INSERT INTO REKENING LB KURS */
                $db->query("INSERT INTO tb_jurnal_detail SET IDJurnal='0', IDRekening='104', Tanggal='$tanggal', JurnalRef='$notransaksi', Debet='".$debetKurs."', Kredit='".$kreditKurs."', Closing='$closingKurs', MataUang='1', Kurs='0', Keterangan='KONVERSI LABA RUGI KURS'");
                
                //RECALCULATE CLOSING BALANCE LB KURS
                $diff = $debetKurs-$kreditKurs;
                $qRest = $db->get_results("SELECT * FROM tb_jurnal_detail WHERE Tanggal>='$tanggal' AND IDRekening='104' AND JurnalRef!='$notransaksi' ORDER BY Tanggal ASC, IDJurnal ASC");
                if($qRest){
                    foreach($qRest as $dRest){
                        if($dRest->Tanggal==$tanggal && $dRest->IDJurnal<$idjurnal){
                            //DO NOTHING
                        } else
                        $db->query("UPDATE tb_jurnal_detail SET Closing=(Closing+$diff) WHERE IDJurnalDetail='".$dRest->IDJurnalDetail."'");
                    }
                }
            }
        }

        //RECALCULATE CLOSING BALANCE
        $dR = $db->get_row("SELECT * FROM tb_master_rekening WHERE IDRekening='".$data->IDRekening."'");
        $diff = $debet-$kredit;
        $qRest = $db->get_results("SELECT * FROM tb_jurnal_detail WHERE Tanggal>='$tanggal' AND IDRekening='".$data->IDRekening."' AND IDJurnal!='$idjurnal' ORDER BY Tanggal ASC, IDJurnal ASC");
        if($qRest){
            foreach($qRest as $dRest){
                if($dRest->Tanggal==$tanggal && $dRest->IDJurnal<$idjurnal){
                    //DO NOTHING
                } else
                $db->query("UPDATE tb_jurnal_detail SET Closing=(Closing+$diff) WHERE IDJurnalDetail='".$dRest->IDJurnalDetail."'");
            }
        }
    }
}
echo json_encode(array("status"=>"1","msg"=>$notransaksi));