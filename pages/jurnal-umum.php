<?php include "pages/header.php"; ?>
<section class="section section-body bg-grey">
    <section id="overview" class="grid-hero container">
        <div class="col-sm-12">
            <?php
            if ($_POST['submitrekening']) {
                $rekening = $_POST['rekening'];
                $keterangan = ucfirst(strtolower($_POST['keterangan']));
                $currency = $_POST['currency'];
                $nominal = $_POST['nominal'];

                if ($rekening == "" || $keterangan == "" || $currency == "" || $nominal == "") {
                    $notif = array("class" => "danger", "msg" => "Lengkapi data anda terlebih dahulu!");
                } else {

                    $cek = newQuery("get_row", "SELECT * FROM tb_master_transaksi WHERE KodeTransaksi='$kode' AND IDTransaksi!='" . $this->action . "'");
                    if ($cek) {
                        $notif = array("class" => "danger", "msg" => "Data tidak dapat disimpan! Kode Transaksi telah digunakan!");
                    } else {
                        if ($this->action != "") {
                            $sql = "UPDATE tb_master_transaksi SET KodeTransaksi='$kode', Keterangan='$nama', IDRekeningDebet='$rekdebet', IDRekeningKredit='$rekkredit', Status='$status' WHERE IDTransaksi='" . $this->action . "'";
                        } else {
                            $sql = "INSERT INTO tb_master_transaksi SET KodeTransaksi='$kode', Keterangan='$nama', IDRekeningDebet='$rekdebet', IDRekeningKredit='$rekkredit', Status='$status'";
                        }
                        $query = newQuery("query", $sql);
                        if ($query) {
                            $notif = array("class" => "success", "msg" => "Data berhasil disimpan!");
                            unset($kode, $nama, $rekdebet, $rekkredit, $status);
                        } else {
                            $notif = array("class" => "danger", "msg" => "Data gagal disimpan. Silahkan coba kembali nanti!");
                        }
                    }
                }
            }

            if ($this->action != "") {
                $data = newQuery("get_row", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID, DATE_FORMAT(BGJatuhTempo,'%d/%m/%Y') AS BGJatuhTempoID, DATE_FORMAT(TanggalKwitansi,'%d/%m/%Y') AS TanggalKwitansiID, DATE_FORMAT(TanggalPembayaran,'%d/%m/%Y') AS TanggalPembayaranID FROM tb_jurnal WHERE NoJurnal='" . $this->action . "'");
                if ($data) {
                    $user = newQuery("get_var", "SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->CreatedBy . "'");
                    $id_jurnal = $data->IDJurnal;
                    $no_bukti = $data->NoBukti;
                    $no_ref = $data->NoRef;
                    $tanggal = $data->TanggalID;
                    $keterangan = $data->Keterangan;

                    $id_proyek = $data->IDProyek;
                    $departement = $data->IDDepartement;
                    $kategori = $data->Tipe;

                    if ($kategori == "3") {
                        $jenis_po = newQuery("get_var", "SELECT JenisPO FROM tb_po WHERE IDPO='$no_ref'");
                    }

                    $no_bg = $data->NoBG;
                    if ($data->BGJatuhTempoID != "00/00/0000")
                        $tanggal_jatuh_tempo = $data->BGJatuhTempoID;


                    $no_kwitansi = $data->NoKwitansi;
                    $metode_pembayaran = $data->TipePembayaran;
                    $bank = $data->Bank;
                    $no_rek = $data->NoBank;
                    $jurnal_ppn_invoice = $data->JurnalPPNInvoice;

                    if ($data->TanggalKwitansiID != "00/00/0000")
                        $tanggal_kwitansi = $data->TanggalKwitansiID;
                    if ($data->TanggalPembayaranID != "00/00/0000")
                        $tanggal_bayar = $data->TanggalPembayaranID;

                    $sisa = newQuery("get_var", "SELECT Sisa FROM tb_proyek_invoice WHERE IDInvoice='$no_ref'");
                }
            }
            if ($tanggal == "") $tanggal = date("d/m/Y");

            if ($_SESSION["locked"] != '') {
                $departement = $_SESSION["departement"];
            }
            ?>
            <h5>Jurnal Umum<small>Pengelolaan jurnal umum</small></h5>
            <?php if (isset($notif)) { ?>
                <div class="toast toast-<?php echo $notif['class']; ?>">
                    <button class="btn btn-clear float-right"></button>
                    <i class="fa fa-warning"></i> <?php echo $notif['msg']; ?>
                </div>
            <?php } ?>
            <form method="POST" action="">
                <div class="columns">
                    <div class="column col-3">
                        <label class="form-label" for="input-example-1">Departement</label>
                        <select name="departement" id="departement" class="form-select" style="width: 100%;" onchange="getSelectValue()" <?php echo $_SESSION["locked"]; ?>>
                            <option value="0">Umum</option>
                            <?php
                            $query = newQuery("get_results", "SELECT * FROM tb_departement ORDER BY NamaDepartement ASC");
                            if ($query) {
                                foreach ($query as $data) {
                            ?><option value="<?php echo $data->IDDepartement; ?>" <?php if ($departement == $data->IDDepartement) echo "selected"; ?>><?php echo $data->NamaDepartement; ?></option><?php
                                                                                                                                                                                                }
                                                                                                                                                                                            }
                                                                                                                                                                                                    ?>
                        </select>
                    </div>
                    <div class="column col-3">
                        <label class="form-label" for="input-example-1">Proyek</label>
                        <select name="id_proyek" id="id_proyek" class="form-select" style="width: 100%;" onchange="getSelectValue()">
                            <option value="0">Umum</option>
                            <?php
                            $query = newQuery("get_results", "SELECT * FROM tb_proyek ORDER BY Tahun DESC, KodeProyek ASC");
                            if ($query) {
                                foreach ($query as $data) {
                            ?><option style="display: none;" value="<?php echo $data->IDProyek; ?>" class="departement departement<?php echo $data->IDDepartement; ?>" <?php if ($id_proyek == $data->IDProyek) echo "selected"; ?>><?php echo $data->KodeProyek . " / " . $data->Tahun . " / " . $data->NamaProyek; ?></option><?php
                                                                                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                                                                                                ?>
                        </select>
                    </div>
                    <div class="column col-3">
                        <label class="form-label" for="input-example-1">Kategori</label>
                        <select name="kategori" id="kategori" class="form-select" style="width: 100%;" onchange="changeTipeJurnal()">
                            <option value="0" <?php if ($kategori == "0") echo "selected"; ?> class="value0">Jurnal Umum</option>
                            <option value="1" <?php if ($kategori == "1") echo "selected"; ?> class="value1">Pendapatan Proyek</option>
                            <option value="8" <?php if ($kategori == "8") echo "selected"; ?> class="value8">Jurnal Invoice Proyek (PPN)</option>
                            <!-- <option value="2" <?php if ($kategori == "2") echo "selected"; ?>>Pendapatan Lain</option> -->
                            <option value="3" <?php if ($kategori == "3") echo "selected"; ?> class="value3">Pembayaran PO Proyek</option>
                            <option value="4" <?php if ($kategori == "4") echo "selected"; ?> class="value4">Pendapatan MMS</option>
                            <option value="5" <?php if ($kategori == "5") echo "selected"; ?> class="value5">Pembayaran PO MMS</option>
                            <option value="6" <?php if ($kategori == "6") echo "selected"; ?> class="value6">Hutang PO Proyek</option>
                            <option value="7" <?php if ($kategori == "7") echo "selected"; ?> class="value7">Hutang PO MMS</option>
                            <!-- <option value="4" <?php if ($kategori == "4") echo "selected"; ?>>Pembayaran PO Umum (SYS)</option> -->
                            <!-- <option value="5" <?php if ($kategori == "5") echo "selected"; ?>>Pembayaran Lain</option> -->
                        </select>
                    </div>
                    <div class="column col-3 refContainer" style="<?php if ($kategori != "1" && $kategori != "3" && $kategori != "4") echo "display: none;"; ?>">
                        <label class="form-label" for="input-example-1">No Invoice System</label>
                        <select name="no_ref" id="no_ref" class="form-select" style="width: 100%;"></select>
                    </div>
                    <div class="column col-3 nominalContainer">
                        <label class="form-label" for="input-example-1">Nominal Sisa Hutang/Piutang</label>
                        <input class="form-input" type="text" id="nominal_ref" name="nominal_ref" placeholder="Nominal Sisa Hutang/Piutang" value="<?php echo number_format($sisa, 0); ?>" readonly="" />
                    </div>
                </div>
                <div class="form-bordered2">
                    <div class="columns">
                        <div class="column col-3">
                            <label class="form-label" for="input-example-1">No. Jurnal</label>
                            <input class="form-input" type="text" name="no_jurnal" placeholder="No. Jurnal" value="<?php echo $this->action; ?>" readonly="" />
                        </div>
                        <div class="column col-3">
                            <label class="form-label" for="input-example-1">No. Bukti Manual</label>
                            <input class="form-input" type="text" name="no_bukti" id="no_bukti" placeholder="No. Bukti Manual" value="<?php echo $no_bukti; ?>" />
                        </div>
                        <div class="column col-3">
                            <label class="form-label" for="input-example-1">Tanggal</label>
                            <input class="form-input input-calendar" id="tanggal" name="tanggal" type="text" value="<?php echo $tanggal; ?>" />
                        </div>
                        <div class="column col-3">
                            <label class="form-label" for="input-example-1">Issued By</label>
                            <input class="form-input" type="text" name="issued_by" placeholder="Issued By" value="<?php echo $user; ?>" readonly="" />
                        </div>
                    </div>
                </div>
                <div class="form-bordered2">
                    <div class="columns">
                        <div class="column col-12">
                            <label class="form-label" for="input-example-1">Keterangan</label>
                            <input class="form-input" type="text" name="keterangan" id="keterangan" value="<?php echo $keterangan; ?>" placeholder="Keterangan" />
                        </div>
                    </div>
                </div>
                <div class="form-bordered2">
                    <div class="columns">
                        <div class="column col-6">
                            <label class="form-label" for="input-example-1">Rekening Perkiraan</label>
                            <select name="rekening" id="rekening" class="form-select" style="width: 100%;text-transform: uppercase;">
                                <?php
                                $query = newQuery("get_results", "SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.Tipe='D' ORDER BY KodeRekening ASC");
                                if ($query) {
                                    foreach ($query as $data) {
                                        if ($this->fungsi->authAccessRekening($data->Posisi, $data->KodeRekening) == 1) {
                                ?><option value="<?php echo $data->IDRekening; ?>" data-posisi="<?php echo $data->Posisi; ?>" <?php if ($rekdebet == $data->IDRekening) echo "selected"; ?>><?php echo $data->KodeRekening . " - " . ucwords(strtolower($data->NamaRekening)); ?></option><?php
                                                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                                                            ?>
                            </select>
                        </div>
                        <div class="column col-1">
                            <label class="form-label" for="input-example-1">Cur.</label>
                            <select name="currency" id="currency" class="form-select" style="width: 100%;">
                                <?php
                                $query = newQuery("get_results", "SELECT * FROM tb_currency ORDER BY IDCurrency ASC");
                                if ($query) {
                                    foreach ($query as $data) {
                                ?><option value="<?php echo $data->IDCurrency; ?>" <?php if ($currency == $data->IDCurrency) echo "selected"; ?>><?php echo  strtoupper($data->Nama); ?></option><?php
                                                                                                                                                                                                }
                                                                                                                                                                                            }
                                                                                                                                                                                                    ?>
                            </select>
                        </div>
                        <div class="column col-2">
                            <label class="form-label" for="input-example-1">Debet</label>
                            <input class="form-input price" type="text" name="debet" id="debet" placeholder="Debet" />
                        </div>
                        <div class="column col-2">
                            <label class="form-label" for="input-example-1">Kredit</label>
                            <input class="form-input price" type="text" name="kredit" id="kredit" placeholder="Kredit" />
                        </div>
                        <div class="column col-1">
                            <label class="form-label" for="input-example-1">&nbsp;</label>
                            <button type="button" name="submitrekening" value="1" class="btn btn-success" onclick="addToCart()"><strong>Post</strong></button>
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
                            <th width="50">Aksi</th>
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
                <div class="form-bordered2">
                    <div class="columns">
                        <div class="column col-3">
                            <div class="kwitansi" id="kwitansi">
                                <div class="form-control">
                                    <label class="form-label" for="input-example-1">No Kwitansi</label>
                                    <input class="form-input" type="text" name="no_kwitansi" id="no_kwitansi" placeholder="No. Kwitansi" maxlength="20" value="<?php echo $no_kwitansi; ?>" />
                                </div>
                                <div class="form-control">
                                    <label class="form-label" for="input-example-1">Tanggal Kwitansi</label>
                                    <input class="form-input" type="text" name="tanggal_kwitansi" id="tanggal_kwitansi" placeholder="Tanggal Kwitansi" value="<?php echo $tanggal_kwitansi; ?>" />
                                </div>
                                <div class="form-control">
                                    <label class="form-label" for="input-example-1">Metode Pembayaran</label>
                                    <select class="form-input" name="metode_pembayaran" id="metode_pembayaran">
                                        <option class="CASH">CASH</option>
                                        <option class="TRANSFER">TRANSFER</option>
                                        <option class="CEK/BG">CEK/BG</option>
                                    </select>
                                </div>
                                <div class="form-control">
                                    <label class="form-label" for="input-example-1">Bank</label>
                                    <input class="form-input" type="text" name="bank" id="bank" placeholder="Nama Bank" value="<?php echo $bank; ?>" />
                                </div>
                                <div class="form-control">
                                    <label class="form-label" for="input-example-1">No Rekening</label>
                                    <input class="form-input" type="text" name="no_rek" id="no_rek" placeholder="No. Rekening" value="<?php echo $no_rek; ?>" />
                                </div>
                                <div class="form-control">
                                    <label class="form-label" for="input-example-1">Tanggal Pembayaran</label>
                                    <input class="form-input" type="text" name="tanggal_bayar" id="tanggal_bayar" placeholder="Tanggal Pembayaran" value="<?php echo $tanggal_bayar; ?>" />
                                </div>
                            </div>
                            <div class="jenis_po_con" id="jenis_po_con" style="<?php if ($kategori != "1" && $kategori != "3" && $kategori != "4") echo "display: none;"; ?>">
                                <div class="form-control">
                                    <label class="form-label" for="input-example-1">Jenis PO</label>
                                    <select class="form-input" name="jenis_po" id="jenis_po" style="padding-top: 6px;">
                                        <option value="1">PO MATERIAL</option>
                                        <option value="2">PO TENAGA/SUBKON</option>
                                        <option value="3">PO OVERHEAD</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-control" style="margin-top:20px">
                                <label class="form-label" for="input-example-1">Attribute Jurnal</label>
                                <input type="checkbox" id="jurnal_ppn_invoice" name="jurnal_ppn_invoice" value="1" <?php if ($jurnal_ppn_invoice == "1") echo "checked=''"; ?> /> Set sebagai Jurnal PPN Invoice
                            </div>
                        </div>
                        <div class="column col-3">
                        </div>
                        <div class="column col-3">
                            <label class="form-label" for="input-example-1">No. BG</label>
                            <input class="form-input" type="text" name="no_bg" id="no_bg" placeholder="No. BG" value="<?php echo $no_bg; ?>" />
                        </div>
                        <div class="column col-3">
                            <label class="form-label" for="input-example-1">Tanggal Jatuh Tempo</label>
                            <input class="form-input input-calendar" type="text" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" placeholder="Tanggal Jatuh Tempo" value="<?php echo $tanggal_jatuh_tempo; ?>" />
                        </div>
                    </div>
                </div>
                <div class="form-footer" style="margin-top: 10px;">
                    <a href="<?php echo PRSONPATH; ?>jurnal-umum-baru/" class="btn btn-primary" style="float: left;margin-right: 10px;" onclick="return confirm('Anda yakin ingin menambahkan jurnal baru?');"><i class="fa fa-plus"></i> Input Jurnal Baru</a>
                    <a href="<?php echo PRSONPATH; ?>remove-jurnal/<?php echo $this->action; ?>" class="btn btn-danger" style="float: left;" onclick="return confirm('Anda yakin ingin menghapus jurnal ini?');"><i class="fa fa-remove"></i> Hapus Jurnal</a>
                    <a href="<?php echo PRSONPATH; ?>history-jurnal/" class="btn btn-link modal-close-trigger">Kembali</a>
                    <button type="button" name="submit" value="1" class="btn btn-primary" onclick="doSubmit('');"><i class="fa fa-save"></i> <strong>Simpan Jurnal</strong></button>
                    <button type="button" name="submit" value="1" class="btn btn-success" onclick="doSubmit('baru');"><i class="fa fa-save"></i> <strong>Simpan Tambah Baru</strong></button>
                    <a id="printKwitansi" href="<?php echo PRSONPATH; ?>print-kwitansi/<?php echo $this->action; ?>" class="btn btn-danger" target="_blank" style="display: none;"><i class="fa fa-print"></i> <strong>Print Kwitansi</strong></a>
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
    var pickerDefault = new Pikaday({
        field: document.getElementById('tanggal_jatuh_tempo'),
        format: 'DD/MM/YYYY',
    });
    var pickerDefault = new Pikaday({
        field: document.getElementById('tanggal_kwitansi'),
        format: 'DD/MM/YYYY',
    });
    var pickerDefault = new Pikaday({
        field: document.getElementById('tanggal_bayar'),
        format: 'DD/MM/YYYY',
    });
    var noUrut = 1;
    var KursArray = [];
    var KursNm = [];
    var cartArray = [];
    var displayCartArray = [];
    var balanceDebet = 0;
    var balanceKredit = 0;
    var noRef = "<?php echo $no_ref; ?>";
    var jenisPO = "<?php echo $jenis_po; ?>";

    <?php
    $query = newQuery("get_results", "SELECT * FROM tb_currency ORDER BY IDCurrency ASC");
    if ($query) {
        foreach ($query as $data) {
    ?>KursArray['<?php echo $data->IDCurrency; ?>'] = "<?php echo $data->Kurs; ?>";
    KursNm['<?php echo $data->IDCurrency; ?>'] = "<?php echo $data->Nama; ?>";
    <?php
        }
    }
    ?>

    <?php
    $queryJurnal = newQuery("get_results", "SELECT * FROM tb_jurnal_detail WHERE IDJurnal='" . $id_jurnal . "'");
    if ($queryJurnal) {
        foreach ($queryJurnal as $dataJurnal) {
            $dataRekening = newQuery("get_row", "SELECT * FROM tb_master_rekening WHERE IDRekening='" . $dataJurnal->IDRekening . "'");
    ?>
            cartArray[noUrut] = {
                NoUrut: noUrut,
                IDRekening: "<?php echo $dataJurnal->IDRekening; ?>",
                KodeRekening: "<?php echo $dataRekening->KodeRekening; ?>",
                NamaRekening: "<?php echo $dataRekening->NamaRekening; ?>",
                Posisi: "<?php echo $dataRekening->Posisi; ?>",
                Keterangan: "<?php echo $dataJurnal->Keterangan; ?>",
                Currency: "<?php echo $dataJurnal->MataUang; ?>",
                Kredit: "<?php echo $dataJurnal->Kredit; ?>",
                Debet: "<?php echo $dataJurnal->Debet; ?>",
                Kurs: parseFloat("<?php echo $dataJurnal->Kurs; ?>"),
                MataUang: KursNm[<?php echo $dataJurnal->MataUang; ?>]
            };
            noUrut += 1;
    <?php
        }
    }
    ?>

    $(document).ready(function() {
        <?php if ($_SESSION["locked"] != '') { ?>
            getSelectValue();
        <?php } ?>

        $('#parent').change(function() {
            var option = $('option:selected', this).attr('kode-parent');
            var option2 = $('option:selected', this).attr('kode-rekening');
            $('#kodeprefix').val(option);
            $('#kode').val(option2);
        });
        $('.price').focus(function() {
            $(this).val($(this).val().toString().replace(/,/g, ""));
        });
        $('.price').focusout(function() {
            $(this).val(numberWithCommas($(this).val()));
        });
        $("#rekening").select2();

        displayCart();
        console.log(cartArray);

        $('.nominalContainer').hide();
        changeTipeJurnal();

        $('#no_ref').change(function() {
            var nominal = $('#no_ref option:selected').attr("nominal");
            var cat = $('#no_ref option:selected').attr("cat");
            var text = $('#no_ref option:selected').text();
            var kategori = $('#kategori').val();
            var jenis_po = $('#no_ref option:selected').attr("jenis_po");
            if (cat == "Debet")
                $('#debet').val(nominal);
            else
                $('#kredit').val(nominal);

            if (kategori === '3' || kategori === '6' || kategori === '7') {
                var res = text.split(" / ");
                var newText = res[1] + " " + res[0];
                $('#keterangan').val(newText);
            } else if (kategori === '4') {
                var res = text.split(" / ");
                var newText = "Pelanggan: " + res[2] + "; No. Invoice: " + res[0] + "; No. SPB: " + res[1];
                $('#keterangan').val(newText);
            }

            if (kategori === '3') {
                $('#jenis_po').val(jenis_po);
            }
        });

        $('#departement').change(function() {
            $('#id_proyek').val("0").trigger('change');
            $('#no_ref').val("0").trigger('change');
            $('#kategori').val("0").trigger('change');
            ShowProyek();
        });

        function ShowProyek() {
            var id = $('#departement').val();
            $('.departement').hide();
            $('.departement' + id).show();
            //getSelectValue();
        }

        ShowProyek();

        //$('#id_proyek').select2({dropdownAutoWidth : true});
    });

    function numberWithCommas(x) {
        var x = x.toString().replace(/,/g, "");
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function addToCart() {
        var IDRekening = $('#rekening').val();
        var Rekening = $('#rekening option:selected').text();
        var Arr = Rekening.split(' - ');
        var KodeRekening = Arr[0];
        var NamaRekening = Arr[1];
        var Posisi = $('#rekening option:selected').attr("data-posisi");
        var Keterangan = $('#keterangan').val();
        var Currency = $('#currency').val();
        var Debet = $('#debet').val().toString().replace(/,/g, "");
        if (Debet == "") Debet = 0;
        var Kredit = $('#kredit').val().toString().replace(/,/g, "");
        if (Kredit == "") Kredit = 0;

        if (Keterangan == "" || (Debet == "" && Kredit == "")) {
            alert("Mohon lengkapi data anda terlebih dahulu! Termasuk keterangan jurnal!");
        } else {
            cartArray[noUrut] = {
                NoUrut: noUrut,
                IDRekening: IDRekening,
                KodeRekening: KodeRekening,
                NamaRekening: NamaRekening,
                Posisi: Posisi,
                Keterangan: Keterangan,
                Currency: Currency,
                Kredit: Kredit,
                Debet: Debet,
                Kurs: parseFloat(KursArray[Currency]),
                MataUang: KursNm[Currency]
            };
            noUrut += 1;
            if (noUrut == 2) $('#tablecart tbody').html('');

            console.log(cartArray);

            displayCart();
            $('#debet').val('');
            $('#kredit').val('');
            document.getElementById("keterangan").focus();
        }
    }

    function displayCart() {
        displayCartArray = cartArray.filter(function() {
            return true;
        });
        displayCartArray = displayCartArray.sort(sortFunction);
        var displayresult = "";
        var i = 0;
        balanceDebet = 0;
        balanceKredit = 0;
        displayCartArray.forEach(function(entry) {
            i++;
            Kredit = '<input type="text" class="form-input price small-input" id="kredit' + entry["NoUrut"] + '" onfocus="inputfocus(\'kredit' + entry["NoUrut"] + '\')" onfocusout="inputfocusout(\'kredit' + entry["NoUrut"] + '\')" value="' + numberWithCommas(entry["Kredit"]) + '" onchange="updateRekening(\'' + entry["NoUrut"] + '\',\'Kredit\',this.value)"/>';
            Debet = '<input type="text" class="form-input price small-input" id="debet' + entry["NoUrut"] + '" onfocus="inputfocus(\'debet' + entry["NoUrut"] + '\')" onfocusout="inputfocusout(\'debet' + entry["NoUrut"] + '\')" value="' + numberWithCommas(entry["Debet"]) + '" onchange="updateRekening(\'' + entry["NoUrut"] + '\',\'Debet\',this.value)"/>';
            KursDebet = 0;
            KursKredit = 0;
            if (entry["Currency"] != 1) {
                if (entry["Debet"] > 0) {
                    KursDebet = parseFloat(entry["Debet"]) * parseFloat(entry["Kurs"]);
                    Debet += "<small>(IDR " + numberWithCommas(KursDebet) + ")</small>";
                }
                if (entry["Kredit"] > 0) {
                    KursKredit = parseFloat(entry["Kredit"]) * parseFloat(entry["Kurs"]);
                    Kredit += "<small>(IDR " + numberWithCommas(KursKredit) + ")</small>";
                }
            } else {
                KursDebet = parseFloat(entry["Debet"]);
                KursKredit = parseFloat(entry["Kredit"]);
            }
            balanceDebet += KursDebet;
            balanceKredit += KursKredit;
            displayresult += '<tr><td>' + i + '</td><td>' + entry["KodeRekening"] + '</td><td><strong>' + entry["NamaRekening"] + '</strong></td><td>' + entry["MataUang"] + '</td><td>' + Debet + '</td><td>' + Kredit + '</td><td><a href="" onclick="return deleteItem(' + entry["NoUrut"] + ')" class="btn btn-danger btn-sm"><i class="fa fa-remove"></i></a></td></tr>';
        });
        if (i == 0) {
            displayresult += '<tr><td colspan="7"><strong>Belum ada data yang dapat ditampilkan</strong></td></tr>';
        }
        $('#tablecart tbody').html('<tr><td class="spacer" colspan="7"></td></tr>' + displayresult);
        $('#balanceDebet').html(numberWithCommas(balanceDebet));
        $('#balanceKredit').html(numberWithCommas(balanceKredit));
    }

    function inputfocus(a) {
        $('#' + a).val($('#' + a).val().toString().replace(/,/g, ""));
    }

    function inputfocusout(a) {
        $('#' + a).val(numberWithCommas($('#' + a).val()));
    }

    function updateRekening(a, b, c) {
        cartArray[a][b] = c;
        displayCart();
    }

    function deleteItem(a) {
        delete cartArray[a];
        displayCart();

        return false;
    }

    function sortFunction(a, b) {
        if (a['NoUrut'] === b['NoUrut']) {
            return 0;
        } else {
            return (a['NoUrut'] < b['NoUrut']) ? -1 : 1;
        }
    }

    function doSubmit(a) {
        if (balanceDebet == 0 && balanceKredit == 0) {
            alert("Masukan minimal satu rekening sebelum anda melakukan penyimpanan jurnal! Atau Balance anda tidak boleh 0 !");
        } else {
            if (balanceDebet != balanceKredit) {
                alert("Debet dan Kredit harus seimbang!");
            } else {
                $('#modalWindow').addClass('active');
                var notransaksi = "<?php echo $this->action; ?>";
                var idjurnal = "<?php echo $id_jurnal; ?>";
                var departement = $('#departement').val();
                var tgl_transaksi = $('#tanggal').val();
                var no_bukti = $('#no_bukti').val();
                var keterangan = $('#keterangan').val();

                var no_bg = $('#no_bg').val();
                var tanggal_jatuh_tempo = $('#tanggal_jatuh_tempo').val();
                var id_proyek = $('#id_proyek').val();
                var kategori = $('#kategori').val();
                var no_bukti_invoice = $('#no_bukti_invoice').val();
                var no_bukti_po = $('#no_bukti_po').val();
                var no_ref = $('#no_ref').val();

                var no_kwitansi = $('#no_kwitansi').val();
                var tanggal_kwitansi = $('#tanggal_kwitansi').val();
                var metode_pembayaran = $('#metode_pembayaran').val();
                var bank = $('#bank').val();
                var no_rek = $('#no_rek').val();
                var tanggal_bayar = $('#tanggal_bayar').val();

                var jenis_po = $('#jenis_po').val();
                var jurnal_ppn_invoice = $('#jurnal_ppn_invoice').is(':checked') ? 1 : 0;

                $.post("../pages/ajaxprocessjurnalumum.php", {
                    idjurnal: idjurnal,
                    tgl_transaksi: tgl_transaksi,
                    no_bukti: no_bukti,
                    notransaksi: notransaksi,
                    cartArray: JSON.stringify(cartArray),
                    balanceDebet: balanceDebet,
                    balanceKredit: balanceKredit,
                    keterangan: keterangan,
                    no_bg: no_bg,
                    tanggal_jatuh_tempo: tanggal_jatuh_tempo,
                    id_proyek: id_proyek,
                    kategori: kategori,
                    no_bukti_invoice: no_bukti_invoice,
                    no_bukti_po: no_bukti_po,
                    no_ref: no_ref,
                    departement: departement,
                    no_kwitansi: no_kwitansi,
                    tanggal_kwitansi: tanggal_kwitansi,
                    metode_pembayaran: metode_pembayaran,
                    bank: bank,
                    no_rek: no_rek,
                    tanggal_bayar: tanggal_bayar,
                    jenis_po: jenis_po,
                    jurnal_ppn_invoice: jurnal_ppn_invoice
                }).done(function(data) {
                    $('#modalWindow').removeClass('active');
                    var results = jQuery.parseJSON(data.trim());
                    if (results.status == "2") {
                        alert(results.msg);
                    } else if (results.status == "1") {
                        alert("Jurnal " + results.msg + " berhasil disimpan!");
                        if (a === 'baru') {
                            var exp = tgl_transaksi.split("/");
                            window.location.href = "<?PHP echo PRSONPATH; ?>jurnal-umum-baru/?tanggal=" + exp[0] + "&bulan=" + exp[01] + "&tahun=" + exp[2];
                        }
                    } else {
                        alert("Jurnal gagal disimpan! Silahkan coba kembali...");
                    }
                });
            }
        }
    }

    function changeTipeJurnal() {
        var kategori = $('#kategori').val();
        if (kategori == "1" || kategori == "8" || kategori == "3" || kategori == "4" || kategori == "5" || kategori == "6" || kategori == "7") {
            $('.refContainer').show();
            //$('.nominalContainer').show();
            $('#no_ref').empty();
            getSelectValue();
        } else {
            $('.refContainer').hide();
            $('.nominalContainer').hide();
        }

        if (kategori == "1" || kategori == "8") {
            // $('#printKwitansi').show();
            // $('#kwitansi').show();
            $('#printKwitansi').hide();
            $('#kwitansi').hide();
        } else {
            $('#printKwitansi').hide();
            $('#kwitansi').hide();
        }

        if (kategori == "3") {
            $('#jenis_po_con').show();
        } else {
            $('#jenis_po_con').show();
        }
    }

    function getSelectValue() {
        var departement = $('#departement').val();
        var proyek = $('#id_proyek').val();
        //alert(departement);
        if (departement === '5' || departement === '1' || departement === '3' || (departement === '0' && proyek > 0)) {
            $('.value1').show();
            $('.value8').show();
            $('.value3').show();
            $('.value6').show();
        } else {
            $('.value1').hide();
            $('.value8').hide();
            $('.value3').hide();
            $('.value3').show();
        }

        if (departement === '4') {
            $('.value4').show();
            $('.value5').show();
            $('.value7').show();
        } else {
            $('.value4').hide();
            $('.value5').hide();
            $('.value7').hide();
        }

        var kategori = $('#kategori').val();
        $.get("../pages/ajaxgetjurnaloption.php?tipe=" + kategori + "&proyek=" + proyek + "&noRef=" + noRef, function(data) {
            var data = JSON.parse(data);
            console.log(data);
            var items = data.option;
            if (kategori == "1" || kategori == "8" || kategori == "3" || kategori == "4" || kategori == "5" || kategori == "6" || kategori == "7") {
                if (data.option.length > 0) {
                    $.each(items, function(i, item) {
                        $('#no_ref').append($('<option>', {
                            value: item.id,
                            text: item.val,
                            nominal: item.nominal,
                            cat: item.tipe,
                            jenis_po: item.jenis_po
                        }));
                    });
                    $('#no_ref').val(noRef);
                    $('#jenis_po').val(jenisPO);
                }
                $('#no_ref').select2();
            }
        });
    }

    function formatDesign(item) {
        var selectionText = item.text.split("<br/>");
        var $returnString = selectionText[0] + "</br>" + selectionText[1];
        return $returnString;
    };
</script>
<?php include "pages/footer.php"; ?>