<?php
$tahun = $this->validasi->validInput($_GET['tahun']);
$departement = $this->validasi->validInput($_GET['departement']);
$tipe = $this->validasi->validInput($_GET['tipe']);
$tanggal = $this->validasi->validInput($_GET['tanggal']);
if ($tanggal == "") $tanggal = date("Y-m-d");

if ($tanggal != "") {
    $condTanggal = " AND a.Tanggal<='$tanggal' ";
    $condTanggal2 = " AND b.Tanggal<='$tanggal' ";
    $condTanggal3 = " AND Tanggal<='$tanggal' ";
}

$exp = explode("-", $tanggal);
$tanggalID = $exp[2] . "/" . $exp[1] . "/" . $exp[0];

$dep = newQuery("get_row", "SELECT * FROM tb_departement WHERE IDDepartement='$departement'");
$bulanList = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember");

$periode = "Departement: " . $dep->NamaDepartement . ". Tahun: " . $tahun . ". Periode Laporan: " . $tanggalID;

$db =  new ezSQL_mysql(YGDBUSER, YGDBPASS, YGDBNAME, YGDBHOST);

$totalProyek = newQuery("get_var", "SELECT COUNT(*) FROM tb_proyek WHERE Tahun='$tahun' AND IDDepartement='$departement' ORDER BY KodeProyek ASC");
if (!$totalProyek) $totalProyek = 0;
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="" />
    <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543" />
    <title>Lintas Daya Accounting</title>
    <link rel="icon" type="image/png" href="<?php echo PRSONTEMPPATH; ?>dist/img/favicon.png" />
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
    <link rel="stylesheet" href="<?php echo PRSONTEMPPATH; ?>css/print-style-acc.css" media="all" />
    <style type="text/css" media="all">
        body {
            font-size: 10px !important;
        }
    </style>
</head>

<body class="center">
    <?php
    if ($totalProyek == 0) echo "Tidak ada proyek untuk saat ini";
    else {
        $limit = 10;
        $TotalAll = array();
        for ($start = 0; $start <= $totalProyek; $start += $limit) {
    ?>
            <div class="newPage">
                <?php if ($tipe == "1") { ?>
                    <h1 class="blue">Proyeksi Project Per Departement</h1>
                    <h3 class="red"><?php echo $periode; ?></h3>
                    <table class="tbLabaRugi" style="width: auto">

                        <tr>
                            <td style="font-weight: bold;" class="red">Proyek</td>
                            <?php
                            $ProyekArray = array();
                            $ProyekNamaArray = array();
                            $KontrakArray = array();

                            $PendapatanArray = array();
                            $DPPArray = array();
                            $PPH2Array = array();
                            $PPH10Array = array();
                            $TotalPajakArray = array();

                            $query = newQuery("get_results", "SELECT * FROM tb_proyek WHERE Tahun='$tahun' AND IDDepartement='$departement' ORDER BY KodeProyek ASC LIMIT $start,$limit");
                            if ($query) {
                                foreach ($query as $data) {
                            ?><td width="90" style="text-align: center;border-bottom: solid 1px #333;padding-bottom: 5px;"><?php echo $data->NamaProyek; ?></td><?php
                                                                                                                                                            array_push($ProyekArray, $data->IDProyek);
                                                                                                                                                            array_push($ProyekNamaArray, $data->KodeProyek);
                                                                                                                                                            array_push($KontrakArray, $data->GrandTotal);

                                                                                                                                                            $id = $data->IDProyek;

                                                                                                                                                            $pendapatan2 = 0;

                                                                                                                                                            $ppn10 = 0;
                                                                                                                                                            $pph2 = 0;
                                                                                                                                                            $dpp = 0;
                                                                                                                                                            $totalPajak = 0;

                                                                                                                                                            $q = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_proyek_invoice WHERE IDProyek='$id' $condTanggal3 ORDER BY IDProyek ASC");
                                                                                                                                                            if ($q) {

                                                                                                                                                                foreach ($q as $d) {
                                                                                                                                                                    if (($d->Sisa > 0 && $d->Sisa < 1) || $d->Sisa < 0) $sisa = 0;
                                                                                                                                                                    else  $sisa = $d->Sisa;

                                                                                                                                                                    //Pajak
                                                                                                                                                                    $temp_dpp = 0;
                                                                                                                                                                    $temp_ppn = 0;
                                                                                                                                                                    $temp_pph = 0;
                                                                                                                                                                    if ($d->PPNPersen > 0) {
                                                                                                                                                                        $temp_dpp = $d->Jumlah;
                                                                                                                                                                        $temp_ppn = $d->PPN;
                                                                                                                                                                        $temp_pph = round($temp_dpp * 0.02, 2);
                                                                                                                                                                    }

                                                                                                                                                                    $pendapatan2 += ($d->GrandTotal - $d->Sisa);

                                                                                                                                                                    $dpp += $temp_dpp;
                                                                                                                                                                    $pph2 += $temp_pph;
                                                                                                                                                                    $ppn10 += $temp_ppn;
                                                                                                                                                                }
                                                                                                                                                            }

                                                                                                                                                            array_push($PendapatanArray, $pendapatan2);
                                                                                                                                                            array_push($DPPArray, $dpp);
                                                                                                                                                            array_push($PPH2Array, $pph2);
                                                                                                                                                            array_push($PPH10Array, $ppn10);
                                                                                                                                                            array_push($TotalPajakArray, ($pph2 + $ppn10));

                                                                                                                                                            $TotalAll['Kontrak'] += $data->GrandTotal;
                                                                                                                                                            $TotalAll['Pendapatan'] += $pendapatan2;
                                                                                                                                                            $TotalAll['DPP'] += $dpp;
                                                                                                                                                            $TotalAll['PPH2'] += $pph2;
                                                                                                                                                            $TotalAll['PPN10'] += $ppn10;
                                                                                                                                                            $TotalAll['TotalPajak'] += ($pph2 + $ppn10);
                                                                                                                                                        }
                                                                                                                                                    }
                                                                                                                                                            ?>
                            <td width="90" style="text-align: center;border-bottom: solid 1px #333;padding-bottom: 5px;"></td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;"></td>
                            <?php
                            for ($i = 0; $i < count($ProyekNamaArray); $i++) {
                            ?><td style="text-align: center;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;" class="red"><?php echo $ProyekNamaArray[$i]; ?></td><?php
                                                                                                                                                                            }
                                                                                                                                                                                ?><td style="text-align: center;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;" class="red">Total</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;" class="red">Penerimaan</td>
                            <?php
                            for ($i = 0; $i < count($ProyekArray); $i++) {
                            ?><td style="text-align: right;">Rupiah</td><?php
                                                            }
                                                                ?>
                            <td style="text-align: right;">Rupiah</td>
                        </tr>
                        <tr>
                            <td class="labelHeader">Kontrak</td>
                            <?php
                            for ($i = 0; $i < count($KontrakArray); $i++) {
                            ?><td style="text-align: center;text-align:right;"><?php echo number_format($KontrakArray[$i], 2); ?></td><?php
                                                                                                                            }
                                                                                                                                ?>
                            <td style="text-align: center;text-align:right;"><?php echo number_format($TotalAll['Kontrak'], 2); ?></td>
                        </tr>
                        <tr>
                            <td class="labelHeader">Total Pendapatan</td>
                            <?php
                            foreach ($PendapatanArray as $value) {
                            ?><td style="text-align: center;text-align:right;border-bottom: solid 1px #333;padding-bottom: 5px;"><?php echo number_format($value, 2); ?></td><?php
                                                                                                                                                                }
                                                                                                                                                                    ?>
                            <td style="text-align: center;text-align:right;border-bottom: solid 1px #333;padding-bottom: 5px;"><?php echo number_format($TotalAll['Pendapatan'], 2); ?></td>
                        </tr>
                        <tr>
                            <td class="labelHeader">DPP</td>
                            <?php
                            foreach ($DPPArray as $value) {
                            ?><td style="text-align: right;"><?php echo number_format($value, 2); ?></td><?php
                                                                                            }
                                                                                                ?>
                            <td style="text-align: center;text-align:right;"><?php echo number_format($TotalAll['DPP'], 2); ?></td>
                        </tr>
                        <tr>
                            <td class="labelHeader">PPN 10% & 11%</td>
                            <?php
                            foreach ($PPH10Array as $value) {
                            ?><td style="text-align: right;"><?php echo number_format($value, 2); ?></td><?php
                                                                                            }
                                                                                                ?>
                            <td style="text-align: center;text-align:right;"><?php echo number_format($TotalAll['PPN10'], 2); ?></td>
                        </tr>
                        <tr>
                            <td class="labelHeader">PPH 2%</td>
                            <?php
                            foreach ($PPH2Array as $value) {
                            ?><td style="text-align: center;text-align:right;border-bottom: solid 1px #333;padding-bottom: 5px;"><?php echo number_format($value, 2); ?></td><?php
                                                                                                                                                                }
                                                                                                                                                                    ?>
                            <td style="text-align: center;text-align:right;border-bottom: solid 1px #333;padding-bottom: 5px;"><?php echo number_format($TotalAll['PPH2'], 2); ?></td>
                        </tr>
                        <tr>
                            <td class="labelHeader">Total Pajak</td>
                            <?php
                            foreach ($TotalPajakArray as $value) {
                            ?><td style="text-align: right;font-weight: bold;"><?php echo number_format($value, 2); ?></td><?php
                                                                                                                }
                                                                                                                    ?>
                            <td style="text-align: right;font-weight: bold;"><?php echo number_format($TotalAll['TotalPajak'], 2); ?></td>
                        </tr>
                        <tr>
                            <td class="labelHeader"></td>
                            <td style="text-align: right;"></td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;" class="red">Pengeluaran</td>
                            <?php
                            for ($i = 0; $i < count($ProyekArray); $i++) {
                            ?><td style="text-align: right;"></td><?php
                                                        }
                                                            ?>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="labelHeader">Biaya Material</td>
                            <?php
                            $BiayaMaterialArray = array();
                            foreach ($ProyekArray as $id) {
                                $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDProyek='$id' AND a.JenisPO='1' AND a.IsAccidental='0' AND a.IDSupplier=b.IDSupplier $condTanggal");
                                $material1 = 0;
                                if ($query) {
                                    foreach ($query as $data) {
                                        $material1 += $data->GrandTotal;
                                    }
                                }

                                $query = $db->get_results("SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal a, tb_jurnal_detail b WHERE a.IDJurnal=b.IDJurnal AND b.IDRekening='45' AND b.Debet>0 AND a.IDProyek='$id' AND a.NoRef='' $condTanggal");
                                if ($query) {
                                    foreach ($query as $data) {
                                        if ($data->NoRef == '') {
                                            $cek = $db->get_row("SELECT * FROM tb_jurnal_detail WHERE IDJurnal='" . $data->IDJurnal . "' AND IDRekening!='" . $data->IDRekening . "'");
                                            if ($cek->IDRekening != '132') {
                                                $material1 += $data->Debet;
                                            }
                                        }
                                    }
                                }
                            ?><td style="text-align: right;"><?php echo number_format($material1, 2); ?></td><?php
                                                                                                    array_push($BiayaMaterialArray, $material1);
                                                                                                    $TotalAll['BiayaMaterial'] += $material1;
                                                                                                }
                                                                                                    ?>
                            <td style="text-align: center;text-align:right;"><?php echo number_format($TotalAll['BiayaMaterial'], 2); ?></td>
                        </tr>
                        <tr>
                            <td class="labelHeader">Pengiriman Stok Gudang</td>
                            <?php
                            $PengirimanArray = array();
                            foreach ($ProyekArray as $id) {
                                $pengiriman1 = 0;

                                $query = $db->get_results("SELECT a.*, DATE_FORMAT(b.Tanggal,'%d/%m/%Y') AS TanggalID, b.IDPengiriman FROM tb_pengiriman_detail a, tb_pengiriman b WHERE a.NoPengiriman=b.NoPengiriman AND b.IDProyek='$id' AND StokFrom='0' $condTanggal2 GROUP BY a.NoPengiriman");
                                if ($query) {
                                    foreach ($query as $data) {
                                        $grandTotal = $db->get_var("SELECT SUM(SubTotal) FROM tb_pengiriman_detail a, tb_pengiriman b WHERE a.NoPengiriman=b.NoPengiriman AND b.IDProyek='$id' AND a.StokFrom='0' AND a.NoPengiriman='" . $data->NoPengiriman . "'");
                                        if (!$grandTotal) $grandTotal = 0;
                                        $pengiriman1 += $grandTotal;
                                    }
                                }

                            ?><td style="text-align: right;"><?php echo number_format($pengiriman1, 2); ?></td><?php
                                                                                                        array_push($PengirimanArray, $pengiriman1);
                                                                                                        $TotalAll['Pengiriman'] += $pengiriman1;
                                                                                                    }
                                                                                                        ?>
                            <td style="text-align: center;text-align:right;"><?php echo number_format($TotalAll['Pengiriman'], 2); ?></td>
                        </tr>
                        <tr>
                            <td class="labelHeader">Biaya Tenaga/Subkon</td>
                            <?php
                            $TenagaArray = array();
                            foreach ($ProyekArray as $id) {
                                $tenaga1 = 0;

                                $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDProyek='$id' AND a.JenisPO='2' AND a.IsAccidental='0' AND a.IDSupplier=b.IDSupplier $condTanggal");
                                if ($query) {
                                    foreach ($query as $data) {
                                        if (($data->Sisa > 0 && $data->Sisa < 1) || $data->Sisa < 0) $sisa = 0;
                                        else  $sisa = $data->Sisa;

                                        $tenaga1 += $data->GrandTotal;
                                    }
                                }

                                $query = $db->get_results("SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal a, tb_jurnal_detail b WHERE a.IDJurnal=b.IDJurnal AND b.IDRekening='45' AND b.Debet>0 AND a.IDProyek='$id'  AND a.NoRef='' $condTanggal");
                                if ($query) {
                                    foreach ($query as $data) {
                                        if ($data->NoRef == '') {
                                            $cek = $db->get_row("SELECT * FROM tb_jurnal_detail WHERE IDJurnal='" . $data->IDJurnal . "' AND IDRekening!='" . $data->IDRekening . "'");
                                            if ($cek->IDRekening != '138' && $cek->IDRekening != '139') {
                                                $tenaga1 += $data->Debet;
                                            }
                                        }
                                    }
                                }

                            ?><td style="text-align: right;"><?php echo number_format($tenaga1, 2); ?></td><?php
                                                                                                    array_push($TenagaArray, $tenaga1);
                                                                                                    $TotalAll['Tenaga'] += $tenaga1;
                                                                                                }
                                                                                                    ?>
                            <td style="text-align: center;text-align:right;"><?php echo number_format($TotalAll['Tenaga'], 2); ?></td>
                        </tr>
                        <tr>
                            <td class="labelHeader">Biaya Overhead</td>
                            <?php
                            $OverheadArray = array();
                            foreach ($ProyekArray as $id) {
                                $overhead1 = 0;

                                $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDProyek='$id' AND a.JenisPO='3' AND a.IsAccidental='0' AND a.IDSupplier=b.IDSupplier $condTanggal");
                                if ($query) {
                                    foreach ($query as $data) {
                                        if (($data->Sisa > 0 && $data->Sisa < 1) || $data->Sisa < 0) $sisa = 0;
                                        else  $sisa = $data->Sisa;
                                        $overhead1 += $data->GrandTotal;
                                    }
                                }

                                $query = $db->get_results("SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal a, tb_jurnal_detail b WHERE a.IDJurnal=b.IDJurnal AND (b.IDRekening IN (SELECT IDRekening FROM tb_master_rekening WHERE IDParent='73' OR IDParent='101' OR IDParent IN (SELECT IDRekening FROM tb_master_rekening WHERE IDParent='73' OR IDParent='101')) OR b.IDRekening = '138') AND b.Debet>0 AND a.IDProyek='$id' AND Tipe='0' $condTanggal");
                                if ($query) {
                                    foreach ($query as $data) {
                                        $overhead1 += $data->Debet;
                                    }
                                }

                            ?><td style="text-align: center;text-align:right;"><?php echo number_format($overhead1, 2); ?></td><?php
                                                                                                                                                                        array_push($OverheadArray, $overhead1);
                                                                                                                                                                        $TotalAll['Overhead'] += $overhead1;
                                                                                                                                                                    }
                                                                                                                                                                        ?>
                            <td style="text-align: center;text-align:right;"><?php echo number_format($TotalAll['Overhead'], 2); ?></td>
                        </tr>
                        <tr>
                            <td class="labelHeader">Biaya Accidental</td>
                            <?php
                            $AccidentalArray = array();
                            foreach ($ProyekArray as $id) {
                                $accidental1 = 0;

                                $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDProyek='$id' AND a.IsAccidental='1' AND a.IDSupplier=b.IDSupplier $condTanggal");
                                if ($query) {
                                    foreach ($query as $data) {
                                        $accidental1 += $data->GrandTotal;
                                    }
                                }

                            ?><td style="text-align: center;text-align:right;border-bottom: solid 1px #333;padding-bottom: 5px;"><?php echo number_format($accidental1, 2); ?></td><?php
                                                                                                                                                                            array_push($AccidentalArray, $accidental1);
                                                                                                                                                                            $TotalAll['Accidental'] += $accidental1;
                                                                                                                                                                        }
                                                                                                                                                                            ?>
                            <td style="text-align: center;text-align:right;border-bottom: solid 1px #333;padding-bottom: 5px;"><?php echo number_format($TotalAll['Accidental'], 2); ?></td>
                        </tr>
                        <tr>
                            <td class="labelHeader">Total Pengeluaran</td>
                            <?php
                            $TotalPengeluaranArray = array();
                            foreach ($ProyekArray as $key => $id) {
                                $totalPengeluaran = $BiayaMaterialArray[$key] + $PengirimanArray[$key] + $TenagaArray[$key] + $OverheadArray[$key] + $AccidentalArray[$key];


                            ?><td style="text-align: center;text-align:right;border-bottom: solid 1px #333;padding-bottom: 5px;font-weight: bold;"><?php echo number_format($totalPengeluaran, 2); ?></td><?php
                                                                                                                                                                                                    array_push($TotalPengeluaranArray, $totalPengeluaran);
                                                                                                                                                                                                }
                                                                                                                                                                                                    ?>
                            <td style="text-align: center;text-align:right;border-bottom: solid 1px #333;padding-bottom: 5px;font-weight: bold;"><?php echo number_format($TotalAll['BiayaMaterial'] + $TotalAll['Pengiriman'] + $TotalAll['Tenaga'] + $TotalAll['Overhead'] + $TotalAll['Accidental'], 2); ?></td>
                        </tr>
                        <tr>
                            <td class="labelHeader">Retur Barang</td>
                            <?php
                            $ReturnArray = array();
                            foreach ($ProyekArray as $id) {
                                $return1 = 0;

                                $query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_audit WHERE IDProyek='$id' $condTanggal3 ORDER BY Tanggal ASC, IDAudit ASC");
                                if ($query) {
                                    foreach ($query as $data) {
                                        $return1 += $data->GrandTotal;
                                    }
                                }

                            ?><td style="text-align: center;text-align:right;border-bottom: solid 1px #333;padding-bottom: 5px;"><?php echo number_format($return1, 2); ?></td><?php
                                                                                                                                                                        array_push($ReturnArray, $return1);
                                                                                                                                                                        $TotalAll['Retur'] += $return1;
                                                                                                                                                                    }
                                                                                                                                                                        ?>
                            <td style="text-align: center;text-align:right;border-bottom: solid 1px #333;padding-bottom: 5px;"><?php echo number_format($TotalAll['Retur'], 2); ?></td>
                        </tr>
                        <tr>
                            <td class="labelHeader">Laba/Rugi</td>
                            <?php
                            $LabaRugiArray = array();
                            foreach ($ProyekArray as $key => $id) {
                                $labaRugi = ($PendapatanArray[$key] - $TotalPajakArray[$key]) - $TotalPengeluaranArray[$key] + $ReturnArray[$key];

                            ?><td style="text-align: center;text-align:right;border-bottom: solid 1px #333;padding-bottom: 5px;font-weight: bold;"><?php echo number_format($labaRugi, 2); ?></td><?php
                                                                                                                                                                                            array_push($LabaRugiArray, $labaRugi);
                                                                                                                                                                                        }
                                                                                                                                                                                            ?>
                            <td style="text-align: center;text-align:right;border-bottom: solid 1px #333;padding-bottom: 5px;font-weight: bold;"><?php echo number_format($TotalAll['Pendapatan'] - $TotalAll['TotalPajak'] - ($TotalAll['BiayaMaterial'] + $TotalAll['Pengiriman'] + $TotalAll['Tenaga'] + $TotalAll['Overhead'] + $TotalAll['Accidental']) + $TotalAll['Retur'], 2); ?></td>
                        </tr>
                    </table>
                <?php } ?>

                <?php if ($tipe == "2") { ?>
                    <?php
                    // REPEAT DETAIL PROYEKSI
                    $ProyekArray = array();
                    $query = newQuery("get_results", "SELECT * FROM tb_proyek WHERE Tahun='$tahun' AND IDDepartement='$departement' ORDER BY KodeProyek ASC LIMIT $start,$limit");
                    if ($query) {
                        foreach ($query as $data) {
                            array_push($ProyekArray, $data->IDProyek);
                        }
                    }
                    foreach ($ProyekArray as $key => $id) {
                        $dataProyek = newQuery("get_row", "SELECT * FROM tb_proyek WHERE IDProyek='" . $id . "'");
                        $cond = $condTanggal;
                        $cond2 = $condTanggal2;
                        $cond3 = $condTanggal3;
                    ?>
                        <div class="newPage">
                            <h1 class="blue" style="line-height: 1.6em">Proyeksi Project <?php echo $dataProyek->NamaProyek; ?></h1>
                            <h3 class="red">Kode Proyek: <?php echo $dataProyek->KodeProyek; ?>, Tahun: <?php echo $dataProyek->Tahun; ?></h3>
                            <table class="tbLabaRugi" style="width: 800px">

                                <tr>
                                    <td style="font-weight: bold;" class="red">Proyeksi</td>
                                    <td width="90" style="text-align: center;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;" class="red">Grand Total</td>
                                    <td width="90" style="text-align: center;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;" class="red">Terbayar</td>
                                    <td width="90" style="text-align: center;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;" class="red">Sisa</td>
                                </tr>
                                <tr>
                                    <td class="labelHeader">Penerimaan</td>
                                    <td colspan="3">
                                </tr>
                                <?php
                                $Pendapatan1 = 0;
                                $Pendapatan2 = 0;
                                $Pendapatan3 = 0;
                                $DPP = 0;
                                $PPH2 = 0;
                                $PPH10 = 0;
                                $TotalPajak = 0;
                                $query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_proyek_invoice WHERE IDProyek='$id' $cond3 ORDER BY IDProyek ASC");
                                if ($query) {
                                    foreach ($query as $data) {
                                        if (($data->Sisa > 0 && $data->Sisa < 1) || $data->Sisa < 0) $sisa = 0;
                                        else  $sisa = $data->Sisa;

                                        //Pajak
                                        $temp_dpp = 0;
                                        $temp_ppn = 0;
                                        $temp_pph = 0;
                                        if ($data->PPNPersen > 0) {
                                            $temp_dpp = $data->Jumlah;
                                            $temp_ppn = $data->PPN;
                                            $temp_pph = round($temp_dpp * 0.02, 2);
                                        }
                                        $Pendapatan1 += $data->GrandTotal;
                                        $Pendapatan2 += ($data->GrandTotal - $data->Sisa);
                                        $Pendapatan3 += $sisa;

                                        $DPP += $temp_dpp;
                                        $PPH2 += $temp_pph;
                                        $PPH10 += $temp_ppn;
                                        $TotalPajak += ($temp_pph + $temp_ppn);
                                ?>
                                        <tr>
                                            <td class="labelHeader2 deep1" style="font-weight: normal;"><?php echo $data->NoInv . " / " . $data->TanggalID . " / " . $data->Keterangan; ?></td>
                                            <td style="text-align: right;"><?php echo number_format($Pendapatan1, 2); ?></td>
                                            <td style="text-align: right;"><?php echo number_format($Pendapatan2, 2); ?></td>
                                            <td style="text-align: right;"><?php echo number_format($Pendapatan3, 2); ?></td>
                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                                <tr>
                                    <td class="labelHeader">Total Pendapatan</td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Pendapatan1, 2); ?></td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Pendapatan2, 2); ?></td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Pendapatan3, 2); ?></td>
                                </tr>
                                <tr>
                                    <td class="labelHeader">DPP</td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($DPP, 2); ?></td>
                                    <td style="text-align: right;"></td>
                                    <td style="text-align: right;"></td>
                                </tr>
                                <tr>
                                    <td class="labelHeader">PPN 10% & 11%</td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($PPH10, 2); ?></td>
                                    <td style="text-align: right;"></td>
                                    <td style="text-align: right;"></td>
                                </tr>
                                <tr>
                                    <td class="labelHeader">PPH 2%</td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($PPH2, 2); ?></td>
                                    <td style="text-align: right;"></td>
                                    <td style="text-align: right;"></td>
                                </tr>
                                <tr>
                                    <td class="labelHeader">Total Pajak</td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($TotalPajak, 2); ?></td>
                                    <td style="text-align: right;"></td>
                                    <td style="text-align: right;"></td>
                                </tr>
                                <tr>
                                    <td class="labelHeader"></td>
                                    <td style="text-align: right;"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;" class="red">Pengeluaran</td>
                                    <td colspan="3"></td>
                                </tr>
                                <tr>
                                    <td class="labelHeader">Biaya Material</td>
                                    <td colspan="3"></td>
                                </tr>
                                <?php
                                $Material1 = 0;
                                $Material2 = 0;
                                $Material3 = 0;

                                $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDProyek='$id' AND a.JenisPO='1' AND a.IsAccidental='0' AND a.IDSupplier=b.IDSupplier $cond $urut");
                                if ($query) {
                                    foreach ($query as $data) {
                                        $supplier = $db->get_row("SELECT * FROM tb_supplier WHERE IDSupplier='" . $data->IDSupplier . "'");
                                        if ($supplier) $supplier = $supplier->NamaPerusahaan;
                                        else $supplier = "-";
                                        if (($data->Sisa > 0 && $data->Sisa < 1) || $data->Sisa < 0) $sisa = 0;
                                        else  $sisa = $data->Sisa;

                                ?>
                                        <tr>
                                            <td class="labelHeader2 deep1" style="font-weight: normal;"><?php echo $data->NoPo . " / " . $data->TanggalID . " / " . $supplier; ?></td>
                                            <td style="text-align: right;"><?php echo number_format($data->GrandTotal, 2); ?></td>
                                            <td style="text-align: right;"><?php echo number_format($data->TotalPembayaran, 2); ?></td>
                                            <td style="text-align: right;"><?php echo number_format($sisa, 2); ?></td>
                                        </tr>
                                        <?php

                                        $Material1 += $data->GrandTotal;
                                        $Material2 += $data->TotalPembayaran;
                                        $Material3 += $sisa;
                                    }
                                }

                                $query = $db->get_results("SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal a, tb_jurnal_detail b WHERE a.IDJurnal=b.IDJurnal AND b.IDRekening='45' AND b.Debet>0 AND a.IDProyek='$id' $cond AND a.NoRef=''");
                                if ($query) {
                                    foreach ($query as $data) {
                                        if ($data->NoRef == '') {
                                            $cek = $db->get_row("SELECT * FROM tb_jurnal_detail WHERE IDJurnal='" . $data->IDJurnal . "' AND IDRekening!='" . $data->IDRekening . "'");
                                            if ($cek->IDRekening != '132') {

                                        ?>
                                                <tr>
                                                    <td class="labelHeader2 deep1" style="font-weight: normal;"><?php echo $data->NoBukti . " / " . $data->TanggalID . " / " . $data->Keterangan; ?></td>
                                                    <td style="text-align: right;"><?php echo number_format($data->Debet, 2); ?></td>
                                                    <td style="text-align: right;"><?php echo number_format($data->Debet, 2); ?></td>
                                                    <td style="text-align: right;"><?php echo number_format(0, 2); ?></td>
                                                </tr>
                                <?php

                                                $Material1 += $data->Debet;
                                                $Material2 += $data->Debet;
                                                $Material3 += 0;
                                            }
                                        }
                                    }
                                }
                                ?>
                                <tr>
                                    <td class="labelHeader">Total B. Material</td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Material1, 2); ?></td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Material2, 2); ?></td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Material3, 2); ?></td>
                                </tr>
                                <tr>
                                    <td class="labelHeader">Pengiriman Stok Gudang</td>
                                    <td colspan="3"></td>
                                </tr>
                                <?php
                                $Pengiriman1 = 0;
                                $Pengiriman2 = 0;
                                $Pengiriman3 = 0;

                                $query = $db->get_results("SELECT a.*, DATE_FORMAT(b.Tanggal,'%d/%m/%Y') AS TanggalID, b.IDPengiriman FROM tb_pengiriman_detail a, tb_pengiriman b WHERE a.NoPengiriman=b.NoPengiriman AND b.IDProyek='$id' AND StokFrom='0' $cond2 GROUP BY a.NoPengiriman");
                                if ($query) {
                                    foreach ($query as $data) {
                                        $grandTotal = $db->get_var("SELECT SUM(SubTotal) FROM tb_pengiriman_detail a, tb_pengiriman b WHERE a.NoPengiriman=b.NoPengiriman AND b.IDProyek='$id' AND a.StokFrom='0' AND a.NoPengiriman='" . $data->NoPengiriman . "'");
                                        if (!$grandTotal) $grandTotal = 0;

                                ?>
                                        <tr>
                                            <td class="labelHeader2 deep1" style="font-weight: normal;"><?php echo $data->NoPengiriman . " / " . $data->TanggalID . " / STOK GUDANG"; ?></td>
                                            <td style="text-align: right;"><?php echo number_format($grandTotal, 2); ?></td>
                                            <td style="text-align: right;"><?php echo number_format($grandTotal, 2); ?></td>
                                            <td style="text-align: right;"><?php echo number_format(0, 2); ?></td>
                                        </tr>
                                <?php

                                        $Pengiriman1 += $grandTotal;
                                        $Pengiriman2 += $grandTotal;
                                        $Pengiriman3 += 0;
                                    }
                                }
                                ?>
                                <tr>
                                    <td class="labelHeader">Total P. Stok Gudang</td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Pengiriman1, 2); ?></td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Pengiriman2, 2); ?></td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Pengiriman3, 2); ?></td>
                                </tr>
                                <tr>
                                    <td class="labelHeader">Biaya Tenaga/Subkon</td>
                                    <td colspan="3"></td>
                                </tr>
                                <?php
                                $Tenaga1 = 0;
                                $Tenaga2 = 0;
                                $Tenaga3 = 0;

                                $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDProyek='$id' AND a.JenisPO='2' AND a.IsAccidental='0' AND a.IDSupplier=b.IDSupplier $cond $urut");
                                if ($query) {
                                    foreach ($query as $data) {
                                        $supplier = $db->get_row("SELECT * FROM tb_supplier WHERE IDSupplier='" . $data->IDSupplier . "'");
                                        if ($supplier) $supplier = $supplier->NamaPerusahaan;
                                        else $supplier = "-";
                                        if (($data->Sisa > 0 && $data->Sisa < 1) || $data->Sisa < 0) $sisa = 0;
                                        else  $sisa = $data->Sisa;

                                ?>
                                        <tr>
                                            <td class="labelHeader2 deep1" style="font-weight: normal;"><?php echo $data->NoPo . " / " . $data->TanggalID . " / " . $supplier; ?></td>
                                            <td style="text-align: right;"><?php echo number_format($data->GrandTotal, 2); ?></td>
                                            <td style="text-align: right;"><?php echo number_format($data->TotalPembayaran, 2); ?></td>
                                            <td style="text-align: right;"><?php echo number_format($sisa, 2); ?></td>
                                        </tr>
                                        <?php

                                        $Tenaga1 += $data->GrandTotal;
                                        $Tenaga2 += $data->TotalPembayaran;
                                        $Tenaga3 += $sisa;
                                    }
                                }

                                $query = $db->get_results("SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal a, tb_jurnal_detail b WHERE a.IDJurnal=b.IDJurnal AND b.IDRekening='45' AND b.Debet>0 AND a.IDProyek='$id' $cond AND a.NoRef=''");
                                if ($query) {
                                    foreach ($query as $data) {
                                        if ($data->NoRef == '') {
                                            $cek = $db->get_row("SELECT * FROM tb_jurnal_detail WHERE IDJurnal='" . $data->IDJurnal . "' AND IDRekening!='" . $data->IDRekening . "'");
                                            if ($cek->IDRekening != '138' && $cek->IDRekening != '139') {

                                        ?>
                                                <tr>
                                                    <td class="labelHeader2 deep1" style="font-weight: normal;"><?php echo $data->NoBukti . " / " . $data->TanggalID . " / " . $data->Keterangan; ?></td>
                                                    <td style="text-align: right;"><?php echo number_format($data->Debet, 2); ?></td>
                                                    <td style="text-align: right;"><?php echo number_format($data->Debet, 2); ?></td>
                                                    <td style="text-align: right;"><?php echo number_format(0, 2); ?></td>
                                                </tr>
                                <?php

                                                $Tenaga1 += $data->Debet;
                                                $Tenaga2 += $data->Debet;
                                                $Tenaga3 += 0;
                                            }
                                        }
                                    }
                                }
                                ?>
                                <tr>
                                    <td class="labelHeader">Total B. Tenaga</td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Tenaga1, 2); ?></td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Tenaga2, 2); ?></td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Tenaga3, 2); ?></td>
                                </tr>
                                <tr>
                                    <td class="labelHeader">Biaya Overhead</td>
                                    <td colspan="3"></td>
                                </tr>
                                <?php
                                $Overhead1 = 0;
                                $Overhead2 = 0;
                                $Overhead3 = 0;

                                $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDProyek='$id' AND a.JenisPO='3' AND a.IsAccidental='0' AND a.IDSupplier=b.IDSupplier $cond $urut");
                                if ($query) {
                                    foreach ($query as $data) {
                                        $supplier = $db->get_row("SELECT * FROM tb_supplier WHERE IDSupplier='" . $data->IDSupplier . "'");
                                        if ($supplier) $supplier = $supplier->NamaPerusahaan;
                                        else $supplier = "-";
                                        if (($data->Sisa > 0 && $data->Sisa < 1) || $data->Sisa < 0) $sisa = 0;
                                        else  $sisa = $data->Sisa;

                                ?>
                                        <tr>
                                            <td class="labelHeader2 deep1" style="font-weight: normal;"><?php echo $data->NoPo . " / " . $data->TanggalID . " / " . $supplier; ?></td>
                                            <td style="text-align: right;"><?php echo number_format($data->GrandTotal, 2); ?></td>
                                            <td style="text-align: right;"><?php echo number_format($data->TotalPembayaran, 2); ?></td>
                                            <td style="text-align: right;"><?php echo number_format($sisa, 2); ?></td>
                                        </tr>
                                    <?php

                                        $Overhead1 += $data->GrandTotal;
                                        $Overhead2 += $data->TotalPembayaran;
                                        $Overhead3 += $sisa;
                                    }
                                }

                                $query = $db->get_results("SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal a, tb_jurnal_detail b WHERE a.IDJurnal=b.IDJurnal AND (b.IDRekening IN (SELECT IDRekening FROM tb_master_rekening WHERE IDParent='73' OR IDParent='101' OR IDParent IN (SELECT IDRekening FROM tb_master_rekening WHERE IDParent='73' OR IDParent='101')) OR b.IDRekening = '138') AND b.Debet>0 AND a.IDProyek='$id' AND Tipe='0' $cond");
                                if ($query) {
                                    foreach ($query as $data) {

                                    ?>
                                        <tr>
                                            <td class="labelHeader2 deep1" style="font-weight: normal;"><?php echo $data->NoBukti . " / " . $data->TanggalID . " / " . $data->Keterangan; ?></td>
                                            <td style="text-align: right;"><?php echo number_format($data->Debet, 2); ?></td>
                                            <td style="text-align: right;"><?php echo number_format($data->Debet, 2); ?></td>
                                            <td style="text-align: right;"><?php echo number_format(0, 2); ?></td>
                                        </tr>
                                <?php

                                        $Overhead1 += $data->Debet;
                                        $Overhead2 += $data->Debet;
                                        $Overhead3 += 0;
                                    }
                                }
                                ?>
                                <tr>
                                    <td class="labelHeader">Total B. Overhead</td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Overhead1, 2); ?></td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Overhead2, 2); ?></td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Overhead3, 2); ?></td>
                                </tr>


                                <tr>
                                    <td class="labelHeader">Biaya Accident</td>
                                    <td colspan="3"></td>
                                </tr>
                                <?php
                                $accidental1 = 0;
                                $accidental2 = 0;
                                $accidental3 = 0;

                                $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDProyek='$id' AND a.IsAccidental='1' AND a.IDSupplier=b.IDSupplier $cond $urut");
                                if ($query) {
                                    foreach ($query as $data) {
                                        $supplier = $db->get_row("SELECT * FROM tb_supplier WHERE IDSupplier='" . $data->IDSupplier . "'");
                                        if ($supplier) $supplier = $supplier->NamaPerusahaan;
                                        else $supplier = "-";
                                        if (($data->Sisa > 0 && $data->Sisa < 1) || $data->Sisa < 0) $sisa = 0;
                                        else  $sisa = $data->Sisa;

                                ?>
                                        <tr>
                                            <td class="labelHeader2 deep1" style="font-weight: normal;"><?php echo $data->NoPo . " / " . $data->TanggalID . " / " . $supplier; ?></td>
                                            <td style="text-align: right;"><?php echo number_format($data->GrandTotal, 2); ?></td>
                                            <td style="text-align: right;"><?php echo number_format($data->TotalPembayaran, 2); ?></td>
                                            <td style="text-align: right;"><?php echo number_format($sisa, 2); ?></td>
                                        </tr>
                                <?php

                                        $accidental1 += $data->GrandTotal;
                                        $accidental2 += $data->TotalPembayaran;
                                        $accidental3 += $sisa;
                                    }
                                }
                                ?>
                                <tr>
                                    <td class="labelHeader">Total B. Accident</td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($accidental1, 2); ?></td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($accidental2, 2); ?></td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($accidental3, 2); ?></td>
                                </tr>

                                <tr>
                                    <td class="labelHeader">Total Pengeluaran</td>
                                    <?php
                                    $Pengeluaran1 = $Material1 + $Tenaga1 + $Overhead1 + $Pengiriman1 + $accidental1;
                                    $Pengeluaran2 = $Material2 + $Tenaga2 + $Overhead2 + $Pengiriman2 + $accidental2;
                                    $Pengeluaran3 = $Material3 + $Tenaga3 + $Overhead3 + $Pengiriman3 + $accidental3;
                                    ?>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Pengeluaran1, 2); ?></td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Pengeluaran2, 2); ?></td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Pengeluaran3, 2); ?></td>
                                </tr>
                                <tr>
                                    <td class="labelHeader">Retur Barang</td>
                                    <td colspan="3"></td>
                                </tr>
                                <?php
                                $Return1 = 0;
                                $Return2 = 0;
                                $Return3 = 0;

                                $query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_audit WHERE IDProyek='$id' ORDER BY Tanggal ASC, IDAudit ASC");
                                if ($query) {
                                    foreach ($query as $data) {
                                ?>
                                        <tr>
                                            <td class="labelHeader2 deep1" style="font-weight: normal;"><?php echo $data->NoAudit . " / " . $data->TanggalID . " / " . $data->Keterangan; ?></td>
                                            <td style="text-align: right;"><?php echo number_format($data->GrandTotal, 2); ?></td>
                                            <td style="text-align: right;"><?php echo number_format(0, 2); ?></td>
                                            <td style="text-align: right;"><?php echo number_format(0, 2); ?></td>
                                        </tr>
                                <?php

                                        $Return1 += $data->GrandTotal;
                                    }
                                }
                                ?>
                                <tr>
                                    <td class="labelHeader">Total Retur</td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Return1, 2); ?></td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format(0, 2); ?></td>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format(0, 2); ?></td>
                                </tr>
                                <tr>
                                    <td class="labelHeader">Laba/Rugi</td>
                                    <?php
                                    $Profit = ($Pendapatan2 - $TotalPajak) - $Pengeluaran1 + $Return1;
                                    ?>
                                    <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-top: 5px;"><?php echo number_format($Profit, 2); ?></td>
                                    <td style="text-align: right;"></td>
                                    <td style="text-align: right;"></td>
                                </tr>
                            </table>
                        </div>
                    <?php
                    }
                    ?>
                <?php } ?>
            </div>
    <?php
        }
    } ?>
    <script type="text/javascript">
        setTimeout(function() {
            window.print();
        }, 1500);
    </script>
</body>

</html>