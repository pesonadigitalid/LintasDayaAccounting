<?php
session_start();
$id = $_GET['id'];
$bulan = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember");
$periode = "Periode : ".$bulan[date("m")]." ".date("Y");

$jurnal = newQuery("get_row","SELECT *, DATE_FORMAT(TanggalKwitansi,'%d/%m/%Y') AS TanggalKwitansiID, DATE_FORMAT(TanggalPembayaran,'%d/%m/%Y') AS TanggalPembayaranID FROM tb_jurnal WHERE NoJurnal='".$this->action."'");
$jurnalDetail = newQuery("get_results","SELECT * FROM tb_jurnal_detail WHERE IDJurnal='".$jurnal->IDJurnal."'");
$pelanggan = newQuery("get_row","SELECT
    `tb_pelanggan`.`NamaPelanggan`
FROM
    `lintasdayadb`.`tb_pelanggan`
    INNER JOIN `lintasdayadb`.`tb_proyek` 
        ON (`tb_pelanggan`.`IDPelanggan` = `tb_proyek`.`IDClient`)
    INNER JOIN `lintasdayadb`.`tb_proyek_invoice` 
        ON (`tb_proyek`.`IDProyek` = `tb_proyek_invoice`.`IDProyek`) WHERE `tb_proyek_invoice`.`IDInvoice`='".$jurnal->NoRef."'");

function terbilang ($angka) {
    $angka = (float)$angka;
    $bilangan = array('','Satu','Dua','Tiga','Empat','Lima','Enam','Tujuh','Delapan','Sembilan','Sepuluh','Sebelas');
    if ($angka < 12) {
        return $bilangan[$angka];
    } else if ($angka < 20) {
        return $bilangan[$angka - 10] . ' Belas';
    } else if ($angka < 100) {
        $hasil_bagi = (int)($angka / 10);
        $hasil_mod = $angka % 10;
        return trim(sprintf('%s Puluh %s', $bilangan[$hasil_bagi], $bilangan[$hasil_mod]));
    } else if ($angka < 200) { return sprintf('Seratus %s', terbilang($angka - 100));
    } else if ($angka < 1000) { $hasil_bagi = (int)($angka / 100); $hasil_mod = $angka % 100; return trim(sprintf('%s Ratus %s', $bilangan[$hasil_bagi], terbilang($hasil_mod)));
    } else if ($angka < 2000) { return trim(sprintf('Seribu %s', terbilang($angka - 1000)));
    } else if ($angka < 1000000) { $hasil_bagi = (int)($angka / 1000); $hasil_mod = $angka % 1000; return sprintf('%s Ribu %s', terbilang($hasil_bagi), terbilang($hasil_mod));
    } else if ($angka < 1000000000) { $hasil_bagi = (int)($angka / 1000000); $hasil_mod = $angka % 1000000; return trim(sprintf('%s Juta %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
    } else if ($angka < 1000000000000) { $hasil_bagi = (int)($angka / 1000000000); $hasil_mod = fmod($angka, 1000000000); return trim(sprintf('%s Milyar %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
    } else if ($angka < 1000000000000000) { $hasil_bagi = $angka / 1000000000000; $hasil_mod = fmod($angka, 1000000000000); return trim(sprintf('%s Triliun %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
    } else {
        return 'Data Salah';
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="description" content=""/>
        <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543"/>
        
        <title>Lintas Daya Smart Office - Smart office for smart people</title>
        
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"/>
        <link rel="stylesheet" href="<?php echo PRSONTEMPPATH; ?>css/print-style2.css" media="all"/>
        <link rel="stylesheet" href="<?php echo PRSONTEMPPATH; ?>css/font-awesome.min.css" media="all"/>
    </head>
    <body>
        <table>
            <tr>
                <td width="30%" class="middle title-company" align="center" style="border: 2px solid #000; height: 50px;">
                    <h1>CV. LINTAS DAYA</h1>
                </td>
                <td width="40%" align="center" class="title-kwitansi">
                    <h2 class="underline">KWITANSI</h2>
                    <h2>RECEIPT</h2>
                </td>
                <td width="30%" class="bottom">
                    <div class="info-container">
                        <label class="n-info">
                            <p><span class="n-divider">No</span><span><em><strong>Number</strong></em></span></p>
                        </label>
                        <label class="titik-dua">:</label>
                        <label class="v-info"><?php echo $jurnal->NoKwitansi; ?></label>
                    </div>
                    <div class="info-container">
                        <label class="n-info">
                            <p><span class="n-divider">Tanggal</span><span><em><strong>Date</strong></em></span></p>
                        </label>
                        <label class="titik-dua">:</label>
                        <label class="v-info"><?php echo $jurnal->TanggalKwitansiID; ?></label>
                    </div>
                </td>
            </tr>
        </table>
        <div class="data-container-kwitansi">
            <table>
                <tr>
                    <td style="width: 120px !important;"><p><span class="n-divider">Sudah terima dari</span><span><em><strong>Received From</strong></em></span></p></td>
                    <td class="nopadding" style="width: 10px;">:</td>
                    <td><strong><?php echo $pelanggan->NamaPelanggan; ?></strong></td>
                </tr>
                <tr>
                    <td class="nopaddingtop" style="width: 120px !important;"><p><span class="n-divider">Banyaknya</span><span><em><strong>Amount</strong></em></span></p></td>
                    <td class="nopadding" style="width: 10px;">:</td>
                    <td class="nopaddingtop">Rp. <?php echo number_format($jurnal->Debet,2); ?></td>
                </tr>
            </table>
            <div class="divider-black"></div>
            <table>
                <tr class="bigheight">
                    <td style="width: 120px !important;"><p><span class="n-divider">Untuk Pembayaran</span><span><em><strong>For Payment</strong></em></span></p></td>
                    <td class="nopadding" style="width: 10px;">:</td>
                    <td><?php echo terbilang($jurnal->Debet); ?> Rupiah</td>
                </tr>
            </table>
            <div class="divider-black"></div>
            <table>
                <tr>
                    <td width="50%">
                        <div class="side-price">
                            <div class="side-price2">
                                <h4>Rp. <?php echo number_format($jurnal->Debet,2); ?></h4>
                            </div>
                        </div>
                        <div class="pilihan-container">
                            <span class="sub-pil">
                                <span class="border-check"></span> <div class="check-container"><?php if($jurnal->TipePembayaran=="CASH"){ ?><i class="fa fa-check"></i><?php } ?>&nbsp;</div> CASH
                            </span>
                            <span class="sub-pil">
                                <span class="border-check"></span> <div class="check-container"><?php if($jurnal->TipePembayaran=="TRANSFER"){ ?><i class="fa fa-check"></i><?php } ?>&nbsp;</div> TRANSFER BANK
                            </span>
                            <span class="sub-pil">
                                <span class="border-check"></span> <div class="check-container"><?php if($jurnal->TipePembayaran=="CEK/BG"){ ?><i class="fa fa-check"></i><?php } ?>&nbsp;</div> CHEQUE / BG
                            </span>
                        </div>
                        <div class="info-container2">
                            <label class="n-info">
                                <p><span class="n-divider">BANK</span><span><em><strong>Bank</strong></em></span></p>
                            </label>
                            <label class="titik-dua">:</label>
                            <label class="v-info"><?php echo $jurnal->Bank; ?></label>
                        </div>
                        <div class="info-container2">
                            <label class="n-info">
                                <p><span class="n-divider">Nomor</span><span><em><strong>Number</strong></em></span></p>
                            </label>
                            <label class="titik-dua">:</label>
                            <label class="v-info"><?php echo $jurnal->NoBank; ?></label>
                        </div>
                        <div class="info-container2">
                            <label class="n-info">
                                <p><span class="n-divider">Tanggal</span><span><em><strong>Date</strong></em></span></p>
                            </label>
                            <label class="titik-dua">:</label>
                            <label class="v-info"><?php echo $jurnal->TanggalPembayaranID; ?></label>
                        </div>
                    </td>
                    <td width="30%" style="padding-left: 10%;padding-right:10%;text-align: center;">
                        Denpasar, <?php echo date("d")." ".$bulan[date("m")]." ".date("Y"); ?><br /><br /><br /><br /><br /><br />(Ir. Lukito Pramono)</td>
                    </td>
                </tr>
            </table>
            <div class="divider-black" style="margin-bottom: 0 !important;"></div>
            <table>
                <tr>
                    <td colspan="2" align="center" style="padding: 5px !important; border-bottom: 1px solid #000;"><strong>Kwitansi ini akan dianggap Sah, Setelah pembayaran dengan Bilyet Giro/Cheque tsb. dapat diuangkan.</strong></td>
                </tr>
                <tr>
                    <td colspan="2" align="center" style="padding: 5px !important;"><em>This receipt will be cleared after Bilyet/Cheque can be cleared.</em></td>
                </tr>
            </table>
        </div>
        <script type="text/javascript">
            window.onload = function () { window.print(); }
        </script>
    </body>
</html>
</body>
</html>