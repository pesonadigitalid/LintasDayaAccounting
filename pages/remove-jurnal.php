<?php
$dataJurnal = newQuery("get_row","SELECT * FROM tb_jurnal WHERE NoJurnal='".$this->action."'");
if($dataJurnal){
    $idjurnal = $dataJurnal->IDJurnal;

    if($dataJurnal->Tipe=="1"){
        newQuery("query","UPDATE tb_proyek_invoice SET Sisa=(Sisa+".$dataJurnal->Debet.") WHERE IDInvoice='".$dataJurnal->NoRef."'");
        newQuery("query","UPDATE tb_proyek SET JumlahPembayaran=(JumlahPembayaran-".$dataJurnal->Debet."), SisaPembayaran=(GrandTotal-JumlahPembayaran) WHERE IDProyek='".$dataJurnal->IDProyek."'");
    } else if($dataJurnal->Tipe=="3" || $dataJurnal->Tipe=="5"){
        newQuery("query","UPDATE tb_po SET Sisa=(Sisa+".$dataJurnal->Debet."), TotalPembayaran=(TotalPembayaran-".$dataJurnal->Debet.") WHERE IDPO='".$dataJurnal->NoRef."'");
    }
    
    $query = newQuery("get_results","SELECT * FROM tb_jurnal_detail WHERE IDJurnal='$idjurnal'");
    if($query){
        foreach($query as $data){
            $dR = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDRekening='".$data->IDRekening."'");
            $diff = $data->Debet-$data->Kredit;
            
            $qRest = newQuery("get_results","SELECT * FROM tb_jurnal_detail WHERE IDJurnalDetail>'".$data->IDJurnalDetail."' AND IDRekening='".$data->IDRekening."'");
            if($qRest){
                foreach($qRest as $dRest){
                    //newQuery("query","UPDATE tb_jurnal_detail SET Closing=(Closing-$diff) WHERE IDJurnalDetail='".$dRest->IDJurnalDetail."'");
                }
            }
        }
        newQuery("query","DELETE FROM tb_jurnal_detail WHERE IDJurnal='$idjurnal'");
    }
    
    newQuery("query","DELETE FROM tb_jurnal WHERE IDJurnal='$idjurnal'");

    //Reset Pembayaran Penjualan MMS
    if($dataJurnal->Tipe=="4"){
        $no_ref = $dataJurnal->NoRef;
        $totalPembayaran = newQuery("get_var","SELECT SUM(Debet) FROM tb_jurnal WHERE NoRef='$no_ref' AND Tipe='4'");
        if(!$totalPembayaran) $totalPembayaran = 0;

        $d = newQuery("get_row","SELECT * FROM tb_penjualan_invoice WHERE IDInvoice='$no_ref'");
        $sisa = $d->GrandTotal - $totalPembayaran;
        newQuery("query","UPDATE tb_penjualan_invoice SET Sisa='$sisa' WHERE IDInvoice='$no_ref'");

        $totalPembayaran2 = newQuery("get_var","SELECT SUM(Debet) FROM tb_jurnal WHERE NoRef IN (SELECT IDInvoice FROM tb_penjualan_invoice WHERE IDPenjualan='".$d->IDPenjualan."') AND Tipe='4'");
        if(!$totalPembayaran2) $totalPembayaran2 = 0;

        $r = newQuery("get_row","SELECT * FROM tb_penjualan WHERE IDPenjualan='".$d->IDPenjualan."'");
        $sisa = $r->GrandTotal - $totalPembayaran2;
        newQuery("query","UPDATE tb_penjualan SET TotalPembayaran='$totalPembayaran2', Sisa='$sisa' WHERE IDPenjualan='".$r->IDPenjualan."'");
    }
}
header("location: ".PRSONPATH."history-jurnal/?Remove=".$this->action);