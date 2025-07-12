<?php include "pages/header.php"; ?>
    <section class="section section-body bg-grey">
        <section id="overview" class="grid-hero container">
            <div class="col-sm-12">
                <?php                
                if($this->action!=""){
                    $data = newQuery("get_row","SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal WHERE NoJurnal='".$this->action."'");
                    if($data){
                        $id_jurnal = $data->IDJurnal;
                        $no_bukti = $data->NoBukti;
                        $no_ref = $data->NoRef;
                        $tanggal = $data->TanggalID;
                        $keterangan = $data->Keterangan;
                    }
                }      
                if($tanggal=="") $tanggal=date("d/m/Y");          
                ?>
                <h5>Jurnal Transaksi<small>Pengelolaan jurnal transaksi</small></h5>
                <?php if(isset($notif)){ ?>
                    <div class="toast toast-<?php echo $notif['class']; ?>">
                        <button class="btn btn-clear float-right"></button>
                        <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
                    </div>
                <?php } ?>
                <form method="POST" action="">
                    <div class="columns">            
                        <div class="column col-3">
                            <label class="form-label" for="input-example-1">No. Jurnal</label>
                            <input class="form-input" type="text" name="no_jurnal" placeholder="No. Jurnal" value="<?php echo $this->action; ?>" readonly=""/>
                        </div>                 
                        <div class="column col-3">
                            <label class="form-label" for="input-example-1">No. Bukti</label>
                            <input class="form-input" type="text" name="no_bukti" id="no_bukti" placeholder="No. Bukti Faktur" value="<?php echo $no_bukti; ?>"/>
                        </div>         
                        <div class="column col-3">
                            <label class="form-label" for="input-example-1">Tanggal</label>
                            <input class="form-input input-calendar" id="tanggal" name="tanggal" type="text" value="<?php echo $tanggal; ?>"/>
                        </div>         
                        <div class="column col-3">
                            <label class="form-label" for="input-example-1">Issued By</label>
                            <input class="form-input" type="text" name="issued_by" placeholder="Issued By" value="Administrator" readonly=""/>
                        </div>
                    </div>
                    <div class="form-bordered2">
                        <div class="columns">   
                            <div class="column col-12">
                                <label class="form-label" for="input-example-1">Keterangan</label>
                                <input class="form-input" type="text" name="keterangan" id="keterangan" value="<?php echo $keterangan; ?>" readonly="" placeholder="Keterangan" />
                            </div>
                        </div>
                    </div>
                    <table class="table new-table" id="tablecart" style="margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th width="30">No</th>
                                <th width="100">Kode</th>
                                <th>Rekening</th>
                                <th width="80">Cur.</th>
                                <th width="125">Debet</th>
                                <th width="125">Kredit</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        <body>
                            <tr>
                                <td class="spacer" colspan="7"></td>
                            </tr>
                           <tr>
                                <td colspan="7"><strong>Tidak ada data yang dapat ditampilkan...</strong></td>
                            </tr>
                        </body>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="nobg right"><strong>Balance:</strong></td>
                                <td class="nobg pointerbg"><strong class="nominal_balance" id="balanceDebet">0</strong></td>
                                <td class="nobg pointerbg"><strong class="nominal_balance" id="balanceKredit">0</strong></td>
                                <td class="nobg"></td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="form-footer" style="margin-top: 10px;">
                        <a href="<?php echo PRSONPATH; ?>jurnal-umum-baru/" class="btn btn-primary" style="float: left;margin-right: 10px;" onclick="return confirm('Anda yakin ingin menambahkan jurnal baru?');"><i class="fa fa-plus"></i> Input Jurnal Baru</a>
                        <a href="<?php echo PRSONPATH; ?>remove-jurnal/<?php echo $this->action; ?>" class="btn btn-danger" style="float: left;" onclick="return confirm('Anda yakin ingin menghapus jurnal ini?');"><i class="fa fa-remove"></i> Hapus Jurnal</a>
                        <a href="<?php echo PRSONPATH; ?>history-jurnal/" class="btn btn-link modal-close-trigger">Kembali</a>
                        <button type="button" name="submit" value="1" class="btn btn-primary" onclick="doSubmit();"><i class="fa fa-save"></i> Simpan Jurnal</button>
                    </div>
                </form>
            </div>
        </section>
    </section>
    <div id="modalWindow" class="modal">
        <div class="modal-overlay"></div>
        <div class="modal-container">
            <div class="modal-body">
                <p>Proses transaksi. Mohon menunggu...</p>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo PRSONTEMPPATH; ?>scripts/pikaday.js"></script>
    <link href="<?php echo PRSONTEMPPATH; ?>css/select2.min.css" rel="stylesheet" />
    <script src="<?php echo PRSONTEMPPATH; ?>scripts/select2.min.js"></script>
    <script type="text/javascript">
        var pickerDefault = new Pikaday({
            field: document.getElementById('tanggal'),
            format: 'DD/MM/YYYY',
        });
        var noUrut=1;
        var KursArray=[];
        var KursNm=[];
        var cartArray=[];
        var displayCartArray=[];
        var balanceDebet=0;
        var balanceKredit=0;
        
        <?php
        $query = newQuery("get_results","SELECT * FROM tb_currency ORDER BY IDCurrency ASC");
        if($query){
            foreach($query as $data){
                ?>KursArray['<?php echo $data->IDCurrency; ?>'] = "<?php echo $data->Kurs; ?>";KursNm['<?php echo $data->IDCurrency; ?>'] = "<?php echo $data->Nama; ?>";<?php
            }
        }
        ?>
        
        <?php
        $queryJurnal = newQuery("get_results","SELECT * FROM tb_jurnal_detail WHERE IDJurnal='".$id_jurnal."'");
        if($queryJurnal){
            foreach($queryJurnal as $dataJurnal){
                $dataRekening = newQuery("get_row","SELECT * FROM tb_master_rekening WHERE IDRekening='".$dataJurnal->IDRekening."'");
                ?>
                cartArray[noUrut] = {NoUrut: noUrut, IDRekening: "<?php echo $dataJurnal->IDRekening; ?>" , KodeRekening: "<?php echo $dataRekening->KodeRekening; ?>", NamaRekening: "<?php echo $dataRekening->NamaRekening; ?>", Posisi: "<?php echo $dataRekening->Posisi; ?>", Keterangan: "<?php echo $dataJurnal->Keterangan; ?>", Currency: "<?php echo $dataJurnal->MataUang; ?>", Kredit: "<?php echo $dataJurnal->Kredit; ?>", Debet: "<?php echo $dataJurnal->Debet; ?>", Kurs: parseFloat(KursArray[<?php echo $dataJurnal->MataUang; ?>]), MataUang: KursNm[<?php echo $dataJurnal->MataUang; ?>], Pos: "<?php echo $dataJurnal->Pos; ?>" };
                noUrut += 1;
                <?php
            }
        }
        ?>
        
        $(document).ready(function(){
            $('.price').focus(function(){
               $(this).val($(this).val().toString().replace(/,/g,"")); 
            });
            $('.price').focusout(function(){
               $(this).val(numberWithCommas($(this).val())); 
            });
            
            displayCart();
            console.log(cartArray);
        });
        function numberWithCommas(x) {
            var x = x.toString().replace(/,/g,"");
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        function displayCart(){
            displayCartArray = cartArray.filter(function(){return true;});
            displayCartArray = displayCartArray.sort(sortFunction);
            var displayresult = "";
            var i=0;
            balanceDebet=0;
            balanceKredit=0;
            displayCartArray.forEach(function(entry) {
                i++;
                if(entry["Pos"]=="Debet"){
                    Kredit = numberWithCommas(entry["Kredit"]);
                    Debet = '<input type="text" class="form-input price small-input" id="debet'+entry["NoUrut"]+'" onfocus="inputfocus(\'debet'+entry["NoUrut"]+'\')" onfocusout="inputfocusout(\'debet'+entry["NoUrut"]+'\')" value="'+numberWithCommas(entry["Debet"])+'" onchange="updateRekening(\''+entry["NoUrut"]+'\',\'Debet\',this.value)"/>';
                } else {
                    Kredit = '<input type="text" class="form-input price small-input" id="kredit'+entry["NoUrut"]+'" onfocus="inputfocus(\'kredit'+entry["NoUrut"]+'\')" onfocusout="inputfocusout(\'kredit'+entry["NoUrut"]+'\')" value="'+numberWithCommas(entry["Kredit"])+'" onchange="updateRekening(\''+entry["NoUrut"]+'\',\'Kredit\',this.value)"/>';
                    Debet = numberWithCommas(entry["Debet"]);
                }
                
                KursDebet = 0;
                KursKredit = 0;
                if(entry["Currency"]!=1){
                    if(entry["Debet"]>0){
                        KursDebet = parseFloat(entry["Debet"])*parseFloat(entry["Kurs"]);
                        Debet += "<small>(IDR "+numberWithCommas(KursDebet)+")</small>";
                    }
                    if(entry["Kredit"]>0){
                        KursKredit = parseFloat(entry["Kredit"])*parseFloat(entry["Kurs"]);
                        Kredit += "<small>(IDR "+numberWithCommas(KursKredit)+")</small>";
                    }
                } else {
                    KursDebet = parseFloat(entry["Debet"]);
                    KursKredit = parseFloat(entry["Kredit"]);
                }
                balanceDebet+=KursDebet;
                balanceKredit+=KursKredit;
                displayresult += '<tr><td>'+i+'</td><td>'+entry["KodeRekening"]+'</td><td><strong>'+entry["NamaRekening"]+'</strong></td><td>'+entry["MataUang"]+'</td><td>'+Debet+'</td><td>'+Kredit+'</td><td></td></tr>';
            });
            if(i==0){
                displayresult += '<tr><td colspan="7"><strong>Belum ada data yang dapat ditampilkan</strong></td></tr>';
            }
            $('#tablecart tbody').html('<tr><td class="spacer" colspan="7"></td></tr>'+displayresult);
            $('#balanceDebet').html(numberWithCommas(balanceDebet));
            $('#balanceKredit').html(numberWithCommas(balanceKredit));
        }
        function inputfocus(a){
            $('#'+a).val($('#'+a).val().toString().replace(/,/g,"")); 
        }
        
        function inputfocusout(a){
            $('#'+a).val(numberWithCommas($('#'+a).val())); 
        }
        function updateRekening(a,b,c){
            cartArray[a][b] = c;
            displayCart();
        }
        function deleteItem(a){
            delete cartArray[a];
            displayCart();
            
            return false;
        }
        function sortFunction(a, b) {
            if (a['NoUrut'] === b['NoUrut']) {
                return 0;
            }
            else {
                return (a['NoUrut'] < b['NoUrut']) ? -1 : 1;
            }
        }
        function doSubmit(){
            if(balanceDebet==0 && balanceKredit==0){
                alert("Masukan minimal satu rekening sebelum anda melakukan penyimpanan jurnal! Atau Balance anda tidak boleh 0 !");
            } else {
                if(balanceDebet!=balanceKredit){
                    alert("Debet dan Kredit harus seimbang!");
                } else {
                    $('#modalWindow').addClass('active');
                    var notransaksi = "<?php echo $this->action; ?>";
                    var idjurnal = "<?php echo $id_jurnal; ?>";
                    var tgl_transaksi = $('#tanggal').val();
                    var no_bukti = $('#no_bukti').val();
                    var keterangan = $('#keterangan').val();
                    $.post("../pages/ajaxprocessjurnaltranskasi.php",{ idjurnal: idjurnal, tgl_transaksi: tgl_transaksi, no_bukti: no_bukti, notransaksi: notransaksi, cartArray: JSON.stringify(cartArray), balanceDebet: balanceDebet, balanceKredit: balanceKredit, keterangan: keterangan }).done(function(data){
                    $('#modalWindow').removeClass('active');
                    var results = jQuery.parseJSON(data.trim());
                    if(results.status=="2"){
                        alert(results.msg);
                    } else if(results.status=="1"){ 
                        alert("Jurnal "+results.msg+" berhasil disimpan!");
                    } else {
                        alert("Jurnal gagal disimpan! Silahkan coba kembali...");
                    }
                });
                }
            }
        }
    </script>
<?php include "pages/footer.php"; ?>