--
-- Definition of table `tblcapitolicontabilita`
--

DROP TABLE IF EXISTS `tblcapitolicontabilita`;
CREATE TABLE `tblcapitolicontabilita` (
  `IdCapitolo` INTEGER NOT NULL auto_increment,
  `SiglaCapitolo` VARCHAR(3) NOT NULL,
  `Capitolo` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`IdCapitolo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella per la gestione dei capitoli di contabilita';

--
-- Dumping data for table `tblcapitolicontabilita`
--

/*!40000 ALTER TABLE `tblcontabilita` DISABLE KEYS */;

INSERT INTO `tblcapitolicontabilita`
(`IdCapitolo`,`SiglaCapitolo`,`Capitolo`)

VALUES 
  (1,'RIS','Ristorazione'),
  (2,'MTC','Materiali Di Consumo'),
  (3,'SEG','Segreteria'),
  (4,'ABB','Abbigliamento'),
  (5,'IER','Iscrizioni Estate Ragazzi'),
  (6,'IOR','Iscrizioni Oratorio'),
  (7,'FMC','Farmacia'),
  (8,'SPE','Spettacoli'),
  (9,'CTB','Contributi');
  
/*!40000 ALTER TABLE `tblcapitolicontabilita` ENABLE KEYS */;

--
-- Definition of table `tblvocicontabilita`
--

DROP TABLE IF EXISTS tblvocicontabilita`;
CREATE TABLE `tblvocicontabilita` (
  `IdVoci` INTEGER NOT NULL auto_increment,
  `Voce` VARCHAR(45) NOT NULL,
  `IdCapitolo` INTEGER NOT NULL,
  `Movimentazione` VARCHAR(2) NOT NULL,
  PRIMARY KEY  (`IdVoci`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella per la gestione delle voci di contabilita';

--
-- Dumping data for table `tblvocicontabilita`
--

/*!40000 ALTER TABLE `tblvocicontabilita` DISABLE KEYS */;
INSERT INTO `tblvocicontabilita` (`IdVoci`,`Voce`,`IdCapitolo`,`Movimentazione`) VALUES 
 (1,'Toner',2,'U'),
 (2,'Risme Carta A4',2,'U'),
 (3,'Merende',1,'EU'),
 (4,'Cappellini',4,'U'),
 (5,'Magliette',4,'U'),
 (6,'Valori Bollati',3,'EU'),
 (7,'Pranzi Cene',1,'EU'),
 (8,'Bollette Telecom',3,'U'),
 (9,'Bollette Teletu',3,'U'),
 (10,'Sms (Mobyt)',3,'U'),
 (11,'Porta Pass',2,'U'),
 (12,'Collarini Porta Pass',2,'U'),
 (13,'Iscrizioni Estate Ragazzi',5,'EU'),
 (14,'Iscrizioni Oratorio',6,'EU'),
 (15,'Garze sterili',7,'U'),
 (16,'Service Audio Luci',8,'U'),
 (17,'Spettacoli Confezionati',8,'U'),
 (18,'Siae',9,'U'),
 (19,'Legge regionale Oratori',9,'E'),
 (20,'Cinque Per Mille',9,'E'),
 (21,'Progetti',9,'E'),
 (22,'Offerte',9,'E'),
 (23,'Risme Carta A3',2,'U');
/*!40000 ALTER TABLE `tblvocicontabilita` ENABLE KEYS */;


--
-- Definition of table `tblcontabilita`
--

DROP TABLE IF EXISTS tblvocicontabilita`;
CREATE TABLE `tblvocicontabilita` (
  `IdContabilita` INTEGER NOT NULL auto_increment,
  `DataOperazione` DATETIME,
  `Contabilita` VARCHAR(2),
  `IdCapitolo` INTEGER NOT NULL,
  `IdVoce` INTEGER NOT NULL,
  `Operazione` VARCHAR(1) NOT NULL,
  `Importo` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY  (`IdContabilita`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella per la gestione della contabilita';



