<?php
$proyek = $this->validasi->validInput($_GET['proyek']);
$tanggalDisplay = $this->fungsi->changeMonthNameID(date("m"))." ".date("Y");
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
        <h1 class="underline">** CASH FLOW PROYEK **</h1>Periode : <?php echo $tanggalDisplay; ?>
    </div>
    <table class="tabelList2" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="60">Tanggal</th>
                <th width="50">Account</th>
                <th>Keterangan</th>
                <th width="100" style="text-align: center;">Debet</th>
                <th width="100" style="text-align: center;">Kredit</th>
                <th width="100" style="text-align: center;">Saldo</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $minDate = newQuery("get_var","SELECT MIN(Tanggal) FROM tb_jurnal WHERE IDProyek='$proyek'");
            $minDate = date('Y-m-d', strtotime($minDate . ' -1 day'));
            $maxDate = newQuery("get_var","SELECT MAX(Tanggal) FROM tb_jurnal WHERE IDProyek='$proyek'");
            $nilaiProyek = newQuery("get_var","SELECT GrandTotal FROM tb_proyek WHERE IDProyek='$proyek'");
            $range = createDateRangeArray($minDate,$maxDate);
            $saldo = 0;
            foreach($range as $dataTanggal){
                
                $exp = explode("-",$dataTanggal);
                $tgl = $exp[2]."/".$exp[1]."/".$exp[0];
                
                $query = newQuery("get_results","SELECT * FROM `tb_master_rekening` WHERE IDParent='63' ORDER BY NamaRekening ASC");
                if($query){
                    foreach($query as $data){
                        if($data->Tipe=="D"){
                            $queryDetail = newQuery("get_results","SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND a.Tanggal='$dataTanggal' AND a.`IDRekening`='".$data->IDRekening."' ORDER BY Tanggal ASC, JurnalRef ASC");
                            if($queryDetail){
                              foreach($queryDetail as $dataDetail){
                                  $saldo += $dataDetail->Kredit;
                                  ?>
                                    <tr>
                                        <td><?php echo $tgl; ?></td>
                                        <td><?php echo $data->KodeRekening; ?></td>
                                        <td><?php echo $dataDetail->Keterangan; ?></td>
                                        <td class="saldo">Rp. <?php echo number_format($dataDetail->Kredit,2); ?></td>
                                        <td class="saldo">0</td>
                                        <td class="saldo"><strong>Rp. <?php echo number_format($saldo,2); ?></strong></td>
                                    </tr>
                                  <?php
                              }
                            }
                        } else {
                            $querySub = newQuery("get_results","SELECT * FROM `tb_master_rekening` WHERE IDParent='".$data->IDRekening."' ORDER BY NamaRekening ASC");
                            if($querySub){
                                foreach($querySub as $dataSub){
                                    if($dataSub->Tipe=="D"){
                                        $queryDetail = newQuery("get_results","SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND a.Tanggal='$dataTanggal' AND a.`IDRekening`='".$dataSub->IDRekening."' ORDER BY Tanggal ASC, JurnalRef ASC");
                                        if($queryDetail){
                                            foreach($queryDetail as $dataDetail){
                                                $saldo += $dataDetail->Kredit;
                                                ?>
                                                <tr>
                                                    <td><?php echo $tgl; ?></td>
                                                    <td><?php echo $data->KodeRekening; ?></td>
                                                    <td><?php echo $dataDetail->Keterangan; ?></td>
                                                    <td class="saldo">Rp. <?php echo number_format($dataDetail->Kredit,2); ?></td>
                                                    <td class="saldo">0</td>
                                                    <td class="saldo"><strong>Rp. <?php echo number_format($saldo,2); ?></strong></td>
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
                
                $query = newQuery("get_results","SELECT * FROM `tb_master_rekening` WHERE IDParent='73' OR IDParent='101' ORDER BY NamaRekening ASC");
              if($query){
                  foreach($query as $data){
                      if($data->Tipe=="D"){
                          $queryDetail = newQuery("get_results","SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND a.Tanggal='$dataTanggal' AND a.`IDRekening`='".$data->IDRekening."' ORDER BY Tanggal ASC, JurnalRef ASC");
                          if($queryDetail){
                              foreach($queryDetail as $dataDetail){
                                  $saldo -= $dataDetail->Debet;
                                  ?>
                                  <tr>
                                    <td><?php echo $tgl; ?></td>
                                    <td><?php echo $data->KodeRekening; ?></td>
                                    <td><?php echo $dataDetail->Keterangan; ?></td>
                                    <td class="saldo">0</td>
                                    <td class="saldo">Rp. <?php echo number_format($dataDetail->Debet,2); ?></td>
                                    <td class="saldo"><strong>Rp. <?php echo number_format($saldo,2); ?></strong></td>
                                </tr>
                                  <?php
                              }
                          }
                      } else {
                          $querySub = newQuery("get_results","SELECT * FROM `tb_master_rekening` WHERE IDParent='".$data->IDRekening."' ORDER BY NamaRekening ASC");
                          if($querySub){
                              foreach($querySub as $dataSub){
                                  if($dataSub->Tipe=="D"){
                                      $queryDetail = newQuery("get_results","SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDProyek`='$proyek' AND a.Tanggal='$dataTanggal' AND a.`IDRekening`='".$dataSub->IDRekening."' ORDER BY Tanggal ASC, JurnalRef ASC");
                                      if($queryDetail){
                                          foreach($queryDetail as $dataDetail){
                                              $saldo -= $dataDetail->Debet;
                                              ?>
                                              <tr>
                                                <td><?php echo $tgl; ?></td>
                                                <td><?php echo $data->KodeRekening; ?></td>
                                                <td><?php echo $dataDetail->Keterangan; ?></td>
                                                <td class="saldo">0</td>
                                                <td class="saldo">Rp. <?php echo number_format($dataDetail->Debet,2); ?></td>
                                                <td class="saldo"><strong>Rp. <?php echo number_format($saldo,2); ?></strong></td>
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
            ?>
              <tr>
                <td colspan="5" style="text-align: right;"><strong>SALDO AKHIR : </strong></td>
                <td class="saldo"><strong>Rp. <?php echo number_format($saldo,2); ?></strong></td>
            </tr>
            <?php
            function createDateRangeArray($strDateFrom,$strDateTo)
            {
                $aryRange=array();
            
                $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
                $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));
            
                if ($iDateTo>=$iDateFrom)
                {
                    array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
                    while ($iDateFrom<$iDateTo)
                    {
                        $iDateFrom+=86400; // add 24 hours
                        array_push($aryRange,date('Y-m-d',$iDateFrom));
                    }
                }
                return $aryRange;
            }
            ?>
        </tbody>
    </table>
    
    <script type="text/javascript">
        window.onload = function () { window.print(); }
    </script>
</body>
</html>