<?php include "pages/header.php"; ?>
    <section class="section bg-grey">
        <?php if($_SESSION["jabatan"]==3){ ?>
        <section id="overview" class="grid-hero container">
            <?php
            $totalProyek = newQuery("get_var","SELECT COUNT(*) FROM tb_proyek WHERE SisaPembayaran>0 $cond");
            $grandTotalProyek = newQuery("get_var","SELECT SUM(GrandTotal) FROM tb_proyek WHERE SisaPembayaran>0 $cond");
            $totalTerbayar = newQuery("get_var","SELECT SUM(JumlahPembayaran) FROM tb_proyek WHERE SisaPembayaran>0 $cond");
            $totalPiutang = newQuery("get_var","SELECT SUM(SisaPembayaran) FROM tb_proyek WHERE SisaPembayaran>0 $cond");
            ?>
            <div class="widgetMainContainer">
                <div class="columns">
                    <div class="column col-3">
                      <div class="widged-bar bg-success">
                        <div class="widgetContainer">
                          <p class="widgedTitle">Total Proyek Aktif <i class="fa fa-chevron-right"></i></p>
                          <p class="widgedValue"><?php echo $totalProyek; ?></p>
                          <i class="fa fa-gear bg-icon"></i>
                        </div>
                      </div>
                    </div>
                    <div class="column col-3">
                      <div class="widged-bar bg-primary">
                        <div class="widgetContainer">
                          <p class="widgedTitle">Grand Total Nilai Proyek <i class="fa fa-chevron-right"></i></p>
                          <p class="widgedValue">Rp. <?php echo number_format($grandTotalProyek,2); ?></p>
                          <i class="fa fa-dollar bg-icon"></i>
                        </div>
                      </div>
                    </div>
                    <div class="column col-3">
                      <div class="widged-bar bg-danger">
                        <div class="widgetContainer">
                          <p class="widgedTitle">Total Terbayar <i class="fa fa-chevron-right"></i></p>
                          <p class="widgedValue">Rp. <?php echo number_format($totalTerbayar,2); ?></p>
                          <i class="fa fa-envelope bg-icon"></i>
                        </div>
                      </div>
                    </div>
                    <div class="column col-3">
                      <div class="widged-bar bg-warning">
                        <div class="widgetContainer">
                          <p class="widgedTitle">Total Piutang <i class="fa fa-chevron-right"></i></p>
                          <p class="widgedValue">Rp. <?php echo number_format($totalPiutang,2); ?></p>
                          <i class="fa fa-send bg-icon"></i>
                        </div>
                      </div>
                    </div>
                </div>
            </div>
            <div class="columns" style="margin-bottom: 50px;">
                <div class="column col-8">
                    <div id="chartContainer" style="width:100%; height:500px;"></div>
                </div>
                <div class="column col-4">
                    <table class="table report-table" style="margin-top:0">
                    <tr><th colspan="4">BG JATUH TEMPO</th></tr>
                    <tr>
                        <th>TANGGAL</th>
                        <th>ACCOUNT</th>
                        <th>KETERANGAN</th>
                        <th>JUMLAH</th>
                    </tr>
                    <?php
                    $query = newQuery("get_results","SELECT *, DATE_FORMAT(BGJatuhTempo,'%d/%m/%Y') AS BGJatuhTempoID FROM tb_jurnal WHERE BGJatuhTempo>='".date("Y-m-d")."' AND NoBG!='' ORDER BY BGJatuhTempo ASC LIMIT 0,10");
                    if($query){
                        foreach($query as $data){
                            $detail = newQuery("get_row","SELECT a.*, b.NamaRekening FROM tb_jurnal_detail a WHERE a.IDJurnal='".$data->IDJurnal."' AND a.Kredit>0 AND a.IDRekening=b.IDRekening ORDER BY a.IDRekening");
                            ?>
                            <tr>
                                <td><?php echo $data->BGJatuhTempoID; ?></td>
                                <td><?php echo $detail->NamaRekening; ?></td>
                                <td><?php echo $data->Keterangan; ?></td>
                                <td><?php echo $data->Kredit; ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="4">Tidak Ada BG Jatuh Tempo untuk saat ini.</td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                </div>
            </div>
            <?php
            function selectTotalBiayaDate($date){
                $biaya = 0;
                $query = newQuery("get_results","SELECT * FROM `tb_master_rekening` WHERE IDParent='73' ORDER BY NamaRekening ASC");
                if($query){
                    foreach($query as $data){
                        if($data->Tipe=="D"){
                            $queryDetail = newQuery("get_results","SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.Tanggal='$date' AND a.`IDRekening`='".$data->IDRekening."' ORDER BY Tanggal ASC, JurnalRef ASC");
                            if($queryDetail){
                                foreach($queryDetail as $dataDetail){
                                    $biaya += $dataDetail->Debet;
                                }
                            }
                        } else {
                            $querySub = newQuery("get_results","SELECT * FROM `tb_master_rekening` WHERE IDParent='".$data->IDRekening."' ORDER BY NamaRekening ASC");
                            if($querySub){
                                foreach($querySub as $dataSub){
                                    if($dataSub->Tipe=="D"){
                                        $queryDetail = newQuery("get_results","SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.Tanggal='$date' AND a.`IDRekening`='".$dataSub->IDRekening."' ORDER BY Tanggal ASC, JurnalRef ASC");
                                        if($queryDetail){
                                            foreach($queryDetail as $dataDetail){
                                                $biaya += $dataDetail->Debet;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                return $biaya;
            }
            
            function selectTotalPendapatanDate($date){
                $pendapatan = 0;
                $query = newQuery("get_results","SELECT * FROM `tb_master_rekening` WHERE IDParent='63' ORDER BY NamaRekening ASC");
                if($query){
                    foreach($query as $data){
                        if($data->Tipe=="D"){
                            $queryDetail = newQuery("get_results","SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.Tanggal='$date' AND a.`IDRekening`='".$data->IDRekening."' ORDER BY Tanggal ASC, JurnalRef ASC");
                            if($queryDetail){
                                foreach($queryDetail as $dataDetail){
                                    $pendapatan += $dataDetail->Kredit;
                                }
                            }
                        } else {
                            $querySub = newQuery("get_results","SELECT * FROM `tb_master_rekening` WHERE IDParent='".$data->IDRekening."' ORDER BY NamaRekening ASC");
                            if($querySub){
                                foreach($querySub as $dataSub){
                                    if($dataSub->Tipe=="D"){
                                        $queryDetail = newQuery("get_results","SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.Tanggal='$date' AND a.`IDRekening`='".$dataSub->IDRekening."' ORDER BY Tanggal ASC, JurnalRef ASC");
                                        if($queryDetail){
                                            foreach($queryDetail as $dataDetail){
                                                $pendapatan += $dataDetail->Kredit;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                return $pendapatan;
            }
            ?>
            <script src="https://code.highcharts.com/highcharts.js"></script>
            <script src="https://code.highcharts.com/highcharts-3d.js"></script>
            <script src="https://code.highcharts.com/modules/exporting.js"></script>
            <script type="text/javascript" >
            $(function () {
                var categ=[
                            <?php 
                             $return = "";
                             for($i=1;$i<=cal_days_in_month(CAL_GREGORIAN, date("m"), date("Y"));$i++){
                                 $return .= "'".$i."/".date("m")."',";
                             }
                             echo substr($return,0,-1);
                             ?>
                         ];
                Highcharts.chart('chartContainer', {
                    
                     chart: {
                         type: 'area'
                     },
                     title: {
                         text: 'Pendapatan dan Pengeluaran'
                     },
                     xAxis: {
                         categories: categ,
                    tickmarkPlacement: 'on',
                    title: {
                      text: 'Tanggal',
                      style: {
                        fontWeight: 'bold'
                      },
                      formatter: function(){
                        return "<h3><b>" + this.value + "</b></h3>";  
                      }
                    },
                    min: 0.5,
                    max: categ.length-1.5,
                    startOnTick: false,
                    endOnTick: false,
                    minPadding: 0,
                    maxPadding: 0,
                    align: "left"       
                     },
                     yAxis: {
                         title: {
                             text: 'Nilai dalam Rupiah'
                         },
                         labels: {
                             formatter: function () {
                                 return this.value / 1000000 + 'jt';
                             }
                         }
                     },
                     plotOptions: {
                            series: {
                                fillOpacity: 0.3
                            }
                        },
                     tooltip: {
                         pointFormat: '{series.name} : <b>{point.y:,.0f}</b>'
                     },
                     series: [{
                         name: 'Pendapatan',
                         color: '#23a8b8',
                         data: [
                             <?php 
                             $return = "";
                             $prev = 0;
                             for($i=1;$i<=cal_days_in_month(CAL_GREGORIAN, date("m"), date("Y"));$i++){
                                 if($i<10) $tgl = "0".$i; else  $tgl = $i;
                                 $data = date("Y")."-".date("m")."-".$tgl;
                                 $val = selectTotalPendapatanDate($data);
                                 //if($val) $prev+=$val;
                                 $return .= $val.",";
                             }
                             echo substr($return,0,-1);
                             ?>
                             ]
                     },{
                         name: 'Pengeluaran',
                         color: '#e42626',
                         data: [
                             <?php 
                             $return = "";
                             $prev = 0;
                             for($i=1;$i<=cal_days_in_month(CAL_GREGORIAN, date("m"), date("Y"));$i++){
                                 if($i<10) $tgl = "0".$i; else  $tgl = $i;
                                 $data = date("Y")."-".date("m")."-".$tgl;
                                 $val = selectTotalBiayaDate($data);
                                 //if($val) $prev+=$val;
                                 $return .= $val.",";
                             }
                             echo substr($return,0,-1);
                             ?>
                             ]
                     }]
                });
            });
            </script>
        </section>
        <?php } ?>
    </section>
<?php include "pages/footer.php"; ?>