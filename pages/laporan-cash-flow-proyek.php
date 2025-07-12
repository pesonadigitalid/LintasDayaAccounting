<?php
$proyek = $this->validasi->validInput($_GET['proyek']);
$status = 1;
?>
<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
             <div class="columns">
             <div class="column col-9">
            <h5>Laporan Cash Flow Proyek<small>Laporan Cash Flow Per Project</small></h5>
            <?php if(isset($notif)){ ?>
                <div class="toast toast-<?php echo $notif['class']; ?>">
                    <button class="btn btn-clear float-right"></button>
                    <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
                </div>
            <?php } ?>
            <form method="GET" action="" class="form-horizontal form-bordered" style="padding: 0px 0;border-top:solid 1px #d3d3d3;border-bottom:solid 1px #d3d3d3;">
                <div class="columns">
                    <div class="column col-2">
                        <label class="form-label" for="input-example-1">Proyek :</label>
                    </div>
                    <div class="column col-7">
                        <select name="proyek" class="form-select" style="width: 100%">
                            <option value="">Pilih Proyek</option>
                            <?php
                            $query = newQuery("get_results","SELECT * FROM tb_proyek ORDER BY Tahun DESC, KodeProyek ASC");
                            if($query){
                                foreach($query as $data){
                                    ?><option value="<?php echo $data->IDProyek; ?>" <?php if($proyek==$data->IDProyek) echo "selected"; ?>><?php echo $data->KodeProyek." / ".$data->Tahun." / ".$data->NamaProyek; ?></option><?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="column col-3">
                        <button type="submit" name="filterbutton" value="1" class="btn btn-success">Filter</button>
                        <?php if($proyek!=""){ ?>
                        <a href="<?php echo PRSONPATH."print-cash-flow/?proyek=$proyek"; ?>" class="btn btn-danger"><i class="fa fa-print"></i> Print</a>
                        <?php } ?>
                    </div>
                </div>
            </form>
            <?php if($proyek!=""){ ?>
            <table class="table report-table">
                <tr>
                    <th width="100">Tanggal</th>
                    <th width="120">Account</th>
                    <th>Keterangan</th>
                    <th width="120" style="text-align: center;">Debet</th>
                    <th width="120" style="text-align: center;">Kredit</th>
                    <th width="120" style="text-align: center;">Saldo</th>
                </tr>
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
                ?>
            </table>
            <?php } ?>
            </div>
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
        </section>
    </section>
<?php include "pages/footer.php"; ?>