/*
SQLyog Ultimate v8.82 
MySQL - 5.5.39 : Database - pesonaaccounting
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`pesonaaccounting` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `pesonaaccounting`;

/*Table structure for table `tb_contact` */

DROP TABLE IF EXISTS `tb_contact`;

CREATE TABLE `tb_contact` (
  `IDContact` int(5) NOT NULL AUTO_INCREMENT,
  `Nama` varchar(50) DEFAULT NULL,
  `Alamat` text,
  `NoTelp` varchar(16) DEFAULT NULL,
  `IDCPKategori` int(5) DEFAULT NULL,
  `Status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`IDContact`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `tb_contact` */

/*Table structure for table `tb_cp_kategori` */

DROP TABLE IF EXISTS `tb_cp_kategori`;

CREATE TABLE `tb_cp_kategori` (
  `IDCPKategori` int(5) NOT NULL AUTO_INCREMENT,
  `Nama` varchar(50) DEFAULT NULL,
  `Status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`IDCPKategori`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `tb_cp_kategori` */

/*Table structure for table `tb_currency` */

DROP TABLE IF EXISTS `tb_currency`;

CREATE TABLE `tb_currency` (
  `IDCurrency` int(5) NOT NULL AUTO_INCREMENT,
  `Nama` varchar(20) DEFAULT NULL,
  `Kurs` double(15,2) DEFAULT NULL,
  `EfektifPerTanggal` date DEFAULT NULL,
  PRIMARY KEY (`IDCurrency`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `tb_currency` */

insert  into `tb_currency`(`IDCurrency`,`Nama`,`Kurs`,`EfektifPerTanggal`) values (1,'IDR',0.00,NULL);

/*Table structure for table `tb_jurnal` */

DROP TABLE IF EXISTS `tb_jurnal`;

CREATE TABLE `tb_jurnal` (
  `IDJurnal` int(15) NOT NULL AUTO_INCREMENT,
  `NoJurnal` varchar(20) DEFAULT NULL,
  `NoBukti` varchar(20) DEFAULT NULL,
  `NoRef` varchar(20) DEFAULT NULL,
  `Tanggal` date DEFAULT NULL,
  `Keterangan` varchar(200) DEFAULT NULL,
  `Debet` double(15,2) DEFAULT NULL,
  `Kredit` double(15,2) DEFAULT NULL,
  `Status` tinyint(1) DEFAULT '1' COMMENT '1=JurnalUmum;2=JurnalTransaksi',
  `CreatedBy` int(10) DEFAULT NULL,
  `DateCreated` datetime DEFAULT NULL,
  `ModifiedBy` int(10) DEFAULT NULL,
  `DateModified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`IDJurnal`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `tb_jurnal` */

insert  into `tb_jurnal`(`IDJurnal`,`NoJurnal`,`NoBukti`,`NoRef`,`Tanggal`,`Keterangan`,`Debet`,`Kredit`,`Status`,`CreatedBy`,`DateCreated`,`ModifiedBy`,`DateModified`) values (1,'01-20160800001','','','2016-08-30','Pembelian Kertas Print',50000.00,50000.00,1,1,'2016-08-30 18:31:57',1,'2016-08-30 19:17:50'),(2,'01-20160800002','','','2016-08-30','Pembelian Materai 6000',12000.00,12000.00,1,1,'2016-08-30 18:32:38',1,'2016-08-30 18:33:11'),(3,'01-20160800003','','','2016-08-30','Gaji karyawan bulanan',20000000.00,20000000.00,2,1,'2016-08-30 18:33:39',1,'2016-08-30 18:34:04'),(4,'01-20160800004','','','2016-08-29','Beli Jus',10000.00,10000.00,1,1,'2016-08-30 19:18:25',1,'2016-08-30 19:20:13');

/*Table structure for table `tb_jurnal_detail` */

DROP TABLE IF EXISTS `tb_jurnal_detail`;

CREATE TABLE `tb_jurnal_detail` (
  `IDJurnalDetail` bigint(15) NOT NULL AUTO_INCREMENT,
  `IDJurnal` int(15) DEFAULT NULL,
  `IDRekening` int(15) DEFAULT NULL,
  `JurnalRef` varchar(20) DEFAULT NULL,
  `Tanggal` date DEFAULT NULL,
  `Pos` enum('Debet','Kredit') DEFAULT NULL,
  `Keterangan` varchar(200) DEFAULT NULL,
  `Debet` double(15,2) DEFAULT NULL,
  `Kredit` double(15,2) DEFAULT NULL,
  `Closing` double(15,2) DEFAULT NULL,
  `MataUang` int(15) DEFAULT NULL,
  `Kurs` double(15,2) DEFAULT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`IDJurnalDetail`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

/*Data for the table `tb_jurnal_detail` */

insert  into `tb_jurnal_detail`(`IDJurnalDetail`,`IDJurnal`,`IDRekening`,`JurnalRef`,`Tanggal`,`Pos`,`Keterangan`,`Debet`,`Kredit`,`Closing`,`MataUang`,`Kurs`,`DateCreated`) values (22,1,80,NULL,'2016-08-30',NULL,'Pembelian Kertas Print',50000.00,0.00,62000.00,1,0.00,'2016-08-30 19:17:50'),(21,1,4,NULL,'2016-08-30',NULL,'Pembelian Kertas Print',0.00,50000.00,-10072000.00,1,0.00,'2016-08-30 19:17:50'),(3,2,4,NULL,'2016-08-30',NULL,'Pembelian Materai 6000',0.00,12000.00,9978000.00,1,0.00,'2016-08-30 18:33:11'),(4,2,80,NULL,'2016-08-30',NULL,'Pembelian Materai 6000',12000.00,0.00,12000.00,1,0.00,'2016-08-30 18:33:11'),(7,3,93,NULL,'2016-08-30','Debet','Gaji karyawan bulanan',20000000.00,0.00,20000000.00,1,0.00,'2016-08-30 18:34:04'),(8,3,4,NULL,'2016-08-30','Kredit','Gaji karyawan bulanan',0.00,20000000.00,-10022000.00,1,0.00,'2016-08-30 18:34:04'),(23,4,4,NULL,'2016-08-29',NULL,'Beli Jus',0.00,10000.00,9990000.00,1,0.00,'2016-08-30 19:20:13'),(24,4,79,NULL,'2016-08-29',NULL,'Beli Jus',10000.00,0.00,10000.00,1,0.00,'2016-08-30 19:20:13');

/*Table structure for table `tb_jurnal_detail_temp` */

DROP TABLE IF EXISTS `tb_jurnal_detail_temp`;

CREATE TABLE `tb_jurnal_detail_temp` (
  `IDJurnalDetail` int(15) NOT NULL AUTO_INCREMENT,
  `IDJurnal` int(15) DEFAULT NULL,
  `IDRekening` int(15) DEFAULT NULL,
  `Tanggal` date DEFAULT NULL,
  `Debet` double(15,2) DEFAULT NULL,
  `Kredit` double(15,2) DEFAULT NULL,
  `Closing` double(15,2) DEFAULT NULL,
  `MataUang` int(15) DEFAULT NULL,
  `Kurs` double(15,2) DEFAULT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`IDJurnalDetail`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `tb_jurnal_detail_temp` */

/*Table structure for table `tb_jurnal_transaksi` */

DROP TABLE IF EXISTS `tb_jurnal_transaksi`;

CREATE TABLE `tb_jurnal_transaksi` (
  `IDJurnalTransaksi` int(15) NOT NULL AUTO_INCREMENT,
  `IDJurnal` int(15) DEFAULT NULL,
  `IDTransaksi` int(15) DEFAULT NULL,
  `Tanggal` date DEFAULT NULL,
  `Jumlah` double(15,2) DEFAULT NULL,
  `MataUang` int(10) DEFAULT NULL,
  `Kurs` double(15,2) DEFAULT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`IDJurnalTransaksi`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `tb_jurnal_transaksi` */

/*Table structure for table `tb_master_rekening` */

DROP TABLE IF EXISTS `tb_master_rekening`;

CREATE TABLE `tb_master_rekening` (
  `IDRekening` int(10) NOT NULL AUTO_INCREMENT,
  `KodeRekening` varchar(10) NOT NULL,
  `NamaRekening` varchar(100) NOT NULL,
  `IDCurrency` int(10) NOT NULL,
  `IDParent` int(10) DEFAULT NULL,
  `Posisi` enum('Debet','Kredit') NOT NULL DEFAULT 'Debet',
  `Tipe` enum('H','D') NOT NULL DEFAULT 'D',
  `SaldoAwal` double(15,2) DEFAULT '0.00',
  `Kurs` double(15,2) DEFAULT '0.00',
  `TanggalSaldoAwal` date DEFAULT NULL,
  PRIMARY KEY (`IDRekening`)
) ENGINE=MyISAM AUTO_INCREMENT=105 DEFAULT CHARSET=latin1;

/*Data for the table `tb_master_rekening` */

insert  into `tb_master_rekening`(`IDRekening`,`KodeRekening`,`NamaRekening`,`IDCurrency`,`IDParent`,`Posisi`,`Tipe`,`SaldoAwal`,`Kurs`,`TanggalSaldoAwal`) values (1,'1-0000','AKTIVA',1,0,'Debet','H',0.00,0.00,NULL),(2,'1-1000','AKTIVA LANCAR',1,1,'Debet','H',0.00,0.00,NULL),(3,'1-1100','KAS & BANK',1,2,'Debet','H',0.00,0.00,NULL),(4,'1-1110','KAS OPERASIONAL',1,3,'Debet','D',10000000.00,0.00,'2016-08-01'),(5,'1-1120','KAS KECIL',1,3,'Debet','D',0.00,0.00,'2016-08-01'),(6,'1-1130','BANK',1,3,'Debet','H',0.00,0.00,NULL),(7,'1-1131','BANK BCA',1,6,'Debet','D',0.00,0.00,'2016-08-01'),(8,'1-1132','BANK MANDIRI VALAS',1,6,'Debet','D',0.00,0.00,'2016-08-01'),(9,'1-1133','BANK BNI',1,6,'Debet','D',0.00,0.00,'2016-08-01'),(10,'1-1160','KAS BELUM DISETOR',1,3,'Debet','D',0.00,0.00,'2016-08-01'),(11,'1-1200','PIUTANG',1,1,'Debet','H',0.00,0.00,NULL),(12,'1-1210','PIUTANG DAGANG',1,11,'Debet','D',0.00,0.00,'2016-08-01'),(13,'1-1220','PIUTANG KARYAWAN',1,11,'Debet','D',0.00,0.00,'2016-08-01'),(14,'1-1230','PIUTANG KARTU KREDIT',1,11,'Debet','D',0.00,0.00,'2016-08-01'),(15,'1-1300','PERSEDIAAN',1,1,'Debet','H',0.00,0.00,NULL),(16,'1-1310','PERSEDIAAN BARANG DAGANGAN',1,15,'Debet','D',0.00,0.00,'2016-08-01'),(17,'1-1400','BIAYA DIBAYAR DIMUKA',1,1,'Debet','H',0.00,0.00,NULL),(18,'1-1410','PENGELUARAN DIBAYAR DIMUKA',1,17,'Debet','D',0.00,0.00,'2016-08-01'),(19,'1-1420','SEWA DIBAYAR DIMUKA',1,17,'Debet','D',0.00,0.00,'2016-08-01'),(20,'1-1430','ASURANSI DIBAYAR DIMUKA',1,17,'Debet','D',0.00,0.00,'2016-08-01'),(21,'1-1500','PAJAK',1,1,'Debet','H',0.00,0.00,NULL),(22,'1-1510','PAJAK MASUKAN',1,21,'Debet','D',0.00,0.00,'2016-08-01'),(23,'1-1520','PAJAK MASUKAN BELUM DITERIMA',1,21,'Debet','D',0.00,0.00,'2016-08-01'),(24,'1-1530','PAJAK DIBAYAR DIMUKA LAINNYA',1,21,'Debet','D',0.00,0.00,'2016-08-01'),(25,'1-2000','AKTIVA TETAP',1,1,'Debet','H',0.00,0.00,NULL),(26,'1-2100','TANAH',1,25,'Debet','D',0.00,0.00,'2016-08-01'),(27,'1-2210','BANGUNAN/GUDANG',1,25,'Debet','D',0.00,0.00,'2016-08-01'),(28,'1-2211','AKUMULASI PENYUSUTAN GEDUNG',1,25,'Debet','D',0.00,0.00,'2016-08-01'),(29,'1-2310','KENDARAAN',1,25,'Debet','D',0.00,0.00,'2016-08-01'),(30,'1-2311','AKUMULASI PENYUSUTAN KENDARAAN',1,25,'Debet','D',0.00,0.00,'2016-08-01'),(31,'1-2410','PERALATAN',1,25,'Debet','D',0.00,0.00,'2016-08-01'),(32,'1-2411','AKUMULASI PENYUSUTAN PERALATAN',1,25,'Debet','D',0.00,0.00,'2016-08-01'),(33,'1-2500','INVESTASI',1,25,'Debet','H',0.00,0.00,NULL),(34,'1-2510','DEPOSITO',1,33,'Debet','D',0.00,0.00,'2016-08-01'),(35,'1-2520','SAHAM',1,33,'Debet','D',0.00,0.00,'2016-08-01'),(36,'1-2530','INVESTASI LAINNYA',1,33,'Debet','D',0.00,0.00,'2016-08-01'),(37,'1-2800','ASET TIDAK BERWUJUD',1,1,'Debet','H',0.00,0.00,NULL),(38,'1-2810','TRADEMARK/MERK DAGANG',1,37,'Debet','D',0.00,0.00,'2016-08-01'),(39,'1-2820','GOODWILL',1,37,'Debet','D',0.00,0.00,'2016-08-01'),(40,'2-0000','KEWAJIBAN',1,0,'Kredit','H',0.00,0.00,NULL),(41,'2-1000','KEWAJIBAN LANCAR',1,40,'Kredit','H',0.00,0.00,NULL),(42,'2-1100','HUTANG OPERASIONAL',1,41,'Kredit','H',0.00,0.00,NULL),(43,'2-1110','HUTANG USAHA',1,42,'Kredit','D',0.00,0.00,'2016-08-01'),(44,'2-1111','HUTANG KARTU KREDIT',1,42,'Kredit','D',0.00,0.00,'2016-08-01'),(45,'2-1112','HUTANG KONSINYASI',1,42,'Kredit','D',0.00,0.00,'2016-08-01'),(46,'2-1200','PENDAPATAN DITERIMA DIMUKA',1,41,'Kredit','D',0.00,0.00,'2016-08-01'),(47,'2-1300','HUTANG PAJAK',1,41,'Kredit','H',0.00,0.00,NULL),(48,'2-1310','PAJAK KELUARAN',1,47,'Kredit','D',0.00,0.00,'2016-08-01'),(49,'2-1311','PAJAK KELUARAN BLM TERBIT',1,47,'Kredit','D',0.00,0.00,'2016-08-01'),(50,'2-1400','HUTANG GAJI',1,41,'Kredit','H',0.00,0.00,NULL),(51,'2-1410','HUTANG GAJI',1,50,'Kredit','D',0.00,0.00,'2016-08-01'),(52,'2-1420','HUTANG PPH 21',1,50,'Kredit','D',0.00,0.00,'2016-08-01'),(53,'2-1500','HUTANG BANK',1,41,'Kredit','H',0.00,0.00,NULL),(54,'2-1510','HUTANG BANK BCA',1,53,'Kredit','D',0.00,0.00,'2016-08-01'),(55,'2-2000','KEWAJIBAN TIDAK LANCAR',1,40,'Kredit','H',0.00,0.00,NULL),(56,'2-2100','HUTANG BANK TDK LANCAR',1,55,'Kredit','D',0.00,0.00,'2016-08-01'),(57,'3-0000','MODAL',1,0,'Kredit','H',0.00,0.00,NULL),(58,'3-1000','MODAL USAHA',1,57,'Kredit','D',10000000.00,0.00,'2016-08-01'),(59,'3-2000','MODAL SAHAM',1,57,'Kredit','D',0.00,0.00,'2016-08-01'),(60,'3-3998','LABA DITAHAN S/D TAHUN LALU',1,57,'Debet','D',0.00,0.00,'2016-08-01'),(61,'3-3999','LABA DITAHAN TAHUN BERJALAN',1,57,'Kredit','D',0.00,0.00,'2016-08-01'),(62,'3-9999','OPENING BALANCE',1,57,'Kredit','D',0.00,0.00,'2016-08-01'),(63,'4-0000','PENDAPATAN',1,0,'Kredit','H',0.00,0.00,NULL),(64,'4-1000','PENDAPATAN DAGANG',1,63,'Kredit','H',0.00,0.00,NULL),(65,'4-1100','PENDAPATAN JUAL',1,64,'Kredit','D',0.00,0.00,'2016-08-01'),(66,'4-1200','BIAYA PENJUALAN',1,64,'Kredit','D',0.00,0.00,'2016-08-01'),(67,'4-1300','POTONGAN PEMBELIAN',1,64,'Kredit','D',0.00,0.00,'2016-08-01'),(68,'4-1400','RETUR PENJUALAN',1,64,'Kredit','D',0.00,0.00,'2016-08-01'),(69,'4-2000','PENDAPATAN JASA',1,63,'Kredit','D',0.00,0.00,'2016-08-01'),(70,'5-0000','HPP',1,0,'Debet','H',0.00,0.00,NULL),(71,'5-1000','HARGA POKOK PENJUALAN',1,70,'Debet','D',0.00,0.00,'2016-08-01'),(72,'5-2000','PENGATURAN STOK',1,70,'Debet','D',0.00,0.00,'2016-08-01'),(73,'6-0000','BIAYA',1,0,'Debet','H',0.00,0.00,NULL),(74,'6-1000','BIAYA UMUM',1,73,'Debet','H',0.00,0.00,NULL),(75,'6-1100','BIAYA GAJI STAFF HARIAN',1,74,'Debet','D',0.00,0.00,'2016-08-01'),(76,'6-1200','BIAYA LISTRIK/AIR/TELP',1,74,'Debet','D',0.00,0.00,'2016-08-01'),(77,'6-1300','BIAYA BUNGA PINJAMAN',1,74,'Debet','D',0.00,0.00,'2016-08-01'),(78,'6-1400','BIAYA ASURANSI',1,74,'Debet','D',0.00,0.00,'2016-08-01'),(79,'6-1500','BIAYA ATK',1,74,'Debet','D',0.00,0.00,'2016-08-01'),(80,'6-1600','PERLENGKAPAN KANTOR',1,74,'Debet','D',0.00,0.00,'2016-08-01'),(81,'6-1700','ONGKOS ANGKUT PEMBELIAN',1,74,'Debet','D',0.00,0.00,'2016-08-01'),(82,'6-1800','BIAYA DENDA',1,74,'Debet','D',0.00,0.00,'2016-08-01'),(83,'6-1900','BIAYA SEWA',1,74,'Debet','D',0.00,0.00,'2016-08-01'),(84,'6-2000','BIAYA PEMASARAN',1,73,'Debet','H',0.00,0.00,NULL),(85,'6-2100','BIAYA IKLAN',1,84,'Debet','D',0.00,0.00,'2016-08-01'),(86,'6-2200','BIAYA PROMOSI',1,84,'Debet','D',0.00,0.00,'2016-08-01'),(87,'6-3000','BIAYA OPERASIONAL',1,73,'Debet','H',0.00,0.00,NULL),(88,'6-3100','BIAYA KIRIM',1,87,'Debet','D',0.00,0.00,'2016-08-01'),(89,'6-3200','BIAYA CETAK',1,87,'Debet','D',0.00,0.00,'2016-08-01'),(90,'6-3300','BIAYA SEWA KENDARAAN',1,87,'Debet','D',0.00,0.00,'2016-08-01'),(91,'6-3400','BIAYA PERAWATAN KENDARAAN',1,87,'Debet','D',0.00,0.00,'2016-08-01'),(92,'6-4000','BIAYA GAJI DAN UPAH',1,73,'Debet','H',0.00,0.00,NULL),(93,'6-4100','BIAYA GAJI DAN UPAH KARYAWAN',1,92,'Debet','D',0.00,0.00,'2016-08-01'),(94,'6-4200','BIAYA GAJI DAN UPAH LAINNYA',1,92,'Debet','D',0.00,0.00,'2016-08-01'),(95,'6-5000','BIAYA PENYUSUTAN ',1,73,'Debet','H',0.00,0.00,NULL),(96,'6-5100','PENYUSUTAN',1,95,'Debet','D',0.00,0.00,'2016-08-01'),(97,'7-0000','PENDAPATAN LAIN',1,0,'Kredit','H',0.00,0.00,NULL),(98,'7-1100','BUNGA',1,97,'Kredit','D',0.00,0.00,'2016-08-01'),(99,'7-1200','PENDAPATAN BUNGA DEPOSITO',1,97,'Kredit','D',0.00,0.00,'2016-08-01'),(100,'7-1300','PENDAPATAN DEVIDEN SAHAM',1,97,'Kredit','D',0.00,0.00,'2016-08-01'),(101,'8-0000','BIAYA LAIN',1,0,'Debet','H',0.00,0.00,NULL),(102,'8-1000','BEBAN BUNGA',1,101,'Debet','D',0.00,0.00,'2016-08-01'),(103,'8-2000','BIAYA PAJAK PENGHASILAN',1,102,'Debet','D',0.00,0.00,'2016-08-01'),(104,'7-1400','LABA/RUGI KURS',1,97,'Kredit','D',0.00,0.00,'2016-08-01');

/*Table structure for table `tb_master_transaksi` */

DROP TABLE IF EXISTS `tb_master_transaksi`;

CREATE TABLE `tb_master_transaksi` (
  `IDTransaksi` int(15) NOT NULL AUTO_INCREMENT,
  `KodeTransaksi` varchar(15) NOT NULL,
  `Keterangan` text,
  `Status` tinyint(1) DEFAULT NULL,
  `DateModified` datetime DEFAULT NULL,
  PRIMARY KEY (`IDTransaksi`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `tb_master_transaksi` */

insert  into `tb_master_transaksi`(`IDTransaksi`,`KodeTransaksi`,`Keterangan`,`Status`,`DateModified`) values (1,'TR001','Gaji karyawan bulanan',1,NULL);

/*Table structure for table `tb_master_transaksi_rekening` */

DROP TABLE IF EXISTS `tb_master_transaksi_rekening`;

CREATE TABLE `tb_master_transaksi_rekening` (
  `IDTransaksiRekening` int(10) NOT NULL AUTO_INCREMENT,
  `IDTransaksi` int(10) DEFAULT NULL,
  `IDRekening` int(10) DEFAULT NULL,
  `Posisi` enum('Debet','Kredit') DEFAULT NULL,
  PRIMARY KEY (`IDTransaksiRekening`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

/*Data for the table `tb_master_transaksi_rekening` */

insert  into `tb_master_transaksi_rekening`(`IDTransaksiRekening`,`IDTransaksi`,`IDRekening`,`Posisi`) values (11,2,4,'Debet'),(10,1,93,'Debet'),(9,1,4,'Kredit'),(12,2,8,'Kredit');

/*Table structure for table `tb_system_config` */

DROP TABLE IF EXISTS `tb_system_config`;

CREATE TABLE `tb_system_config` (
  `id_system_config` int(16) NOT NULL AUTO_INCREMENT,
  `label` varchar(32) DEFAULT NULL,
  `value` text,
  PRIMARY KEY (`id_system_config`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

/*Data for the table `tb_system_config` */

insert  into `tb_system_config`(`id_system_config`,`label`,`value`) values (1,'URL_SITE','http://localhost/pesonaaccounting/'),(2,'ALLOWED_IP','169.254.14.58'),(3,'LAST_SYNC','Percetakan Bali Post'),(4,'JAMBUKA','Jalan Kepundung, Denpasar, Bali'),(5,'ACTIVATIONCODE','S+syeyaGY3zzFTlcqxHcotrzdrpS+/a6HnZEl+0DhhdLE/J1ItCxW1Ig+BJfArNLNzaeFohnZFg='),(6,'VERSION','1.0.1');

/*Table structure for table `tb_user` */

DROP TABLE IF EXISTS `tb_user`;

CREATE TABLE `tb_user` (
  `id_user` int(16) NOT NULL AUTO_INCREMENT,
  `nama` varchar(64) DEFAULT NULL,
  `telp` varchar(16) DEFAULT NULL,
  `level` int(1) DEFAULT NULL,
  `bagian` tinyint(1) DEFAULT '0',
  `img_photo` varchar(200) DEFAULT NULL,
  `username_user` varchar(64) DEFAULT NULL,
  `password_user` varchar(64) DEFAULT NULL,
  `status` int(1) DEFAULT '1',
  PRIMARY KEY (`id_user`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `tb_user` */

insert  into `tb_user`(`id_user`,`nama`,`telp`,`level`,`bagian`,`img_photo`,`username_user`,`password_user`,`status`) values (1,'Administrator','123456',1,0,NULL,'admin','21232f297a57a5a743894a0e4a801fc3',1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
