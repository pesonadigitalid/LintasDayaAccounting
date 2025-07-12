<?php
$query=newQuery("get_results","SELECT DISTINCT(IDRekening) FROM tb_jurnal_detail");
if($query){
    foreach($query as $data){
        /*
        $jurnalTerakhir = $db->get_row("SELECT * FROM tb_jurnal_detail WHERE IDRekening='".$data->IDRekening."' AND Tanggal<='$tanggal' ORDER BY IDJurnalDetail DESC");
        
        if(!$jurnalTerakhir){
            $closing = $dataRekening->SaldoAwal;
        } else {
            $closing = $jurnalTerakhir->Closing;
        }
        */
        $dataRekening = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDRekening='".$data->IDRekening."'");
        $closing = $dataRekening->SaldoAwal;
        
        $qRest = newQuery("get_results","SELECT * FROM tb_jurnal_detail WHERE IDRekening='".$data->IDRekening."' ORDER BY Tanggal ASC, IDJurnalDetail ASC");
        if($qRest){
            foreach($qRest as $dRest){
                $diff = $dRest->Debet-$dRest->Kredit;
                $closing = $closing+$diff;
                newQuery("query","UPDATE tb_jurnal_detail SET Closing='$closing' WHERE IDJurnalDetail='".$dRest->IDJurnalDetail."'");
            }
        }
    }
}
echo "OK";