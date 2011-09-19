﻿--************************************
-- Modifica della tabella `tblfunzioni`
--************************************

-- aggiunge il campo 'menu_padre'
ALTER TABLE `tblFunzioni` ADD COLUMN `menu_padre` INTEGER UNSIGNED NOT NULL AFTER `immagine_testo`;

-- aggiorna la sequenza della funzione uscita programma
UPDATE `tblFunzioni` SET `sequenza`=8 WHERE `id_funzione`=4;

-- aggiorna l'icona della funzione anagrafica
UPDATE `tblFunzioni` SET `immagine`='./Immagini/xanagrafica.png' WHERE `id_funzione`=1;


-- Inserisce i nuovi dati nella tabella tblfunzioni
INSERT INTO `tblFunzioni`
  (`id_funzione`,`sequenza`,`nome`,`url`,`immagine`,`immagine_testo`,`menu_padre`)

VALUES
  (8,7,'Utility','homepage.php?menu_padre=8','./Immagini/utility.png','Funzioni di utilità',0),
  (9,2,'Gestione Cresime','xsacramenti.php?scr=2','./Immagini/cresima2.gif','Gestione Cresime',8),
  (10,1,'Gestione Comunioni','xsacramenti.php?scr=1','./Immagini/pane_vino_2.jpg','Gestione Comunioni',8),
  (11,3,'SMS','xsms.php','./Immagini/SMS.png','Gestione e invio sms agli iscritti',8);

-- Aggiorna la tabella tblfunzionixoperatori
INSERT INTO `tblOperatoriXFunzioni`
  (`id_funzione`,`id_operatore`)

VALUES
  (8,50),
  (9,50),
  (10,50),
  (11,50),
  (8,132),
  (9,132),
  (10,132),
  (11,132),
  (8,147718),
  (9,147718),
  (10,147718),
  (11,147718);

-- ********************************
-- Crea la tabella `tblsacramenti`
-- ********************************
DROP TABLE IF EXISTS `tblsacramenti`;
CREATE TABLE `tblsacramenti` (
  `IDSacramenti` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `ID` INTEGER UNSIGNED NOT NULL COMMENT 'Identificativo candidato ai sacramenti',
  `DataBattesimo` DATE DEFAULT NULL,
  `ParrocchiaBattesimo` VARCHAR(255),
  `IndirizzoParrocchiaBattesimo` VARCHAR(255),
  `CertificatoBattesimo` INTEGER UNSIGNED NOT NULL DEFAULT 2,
  `NominativoPadrinoMadrina` VARCHAR(45),
  `ParrocchiaPadrinoMadrina` VARCHAR(255),
  `CertificatoIdoneita` TINYINT(1) UNSIGNED DEFAULT 0,
  `IdGruppo` INTEGER UNSIGNED NOT NULL,
  `ContributoVersato` TINYINT(1) UNSIGNED DEFAULT 0,
  `IscrizioneGratuita` TINYINT(1) UNSIGNED DEFAULT 0,
  `DataIscrizione` DATE NOT NULL,
  `SCR` INTEGER UNSIGNED NOT NULL COMMENT 'Tipo di sacramento: 1 comunione, 2 cresima',
  `Note` VARCHAR(4000),
  PRIMARY KEY (`IDSacramenti`)
)
ENGINE = InnoDB
DEFAULT CHARSET=latin1
COMMENT = 'tabella dati comunioni, cresime, eccetera';


-- Inserisce dati nella tabella `tblsacramenti`
INSERT INTO `tblsacramenti` (
  `ID`,
  `ParrocchiaBattesimo`,
  `DataBattesimo`,
  `CertificatoBattesimo`,
  `NominativoPadrinoMadrina`,
  `ParrocchiaPadrinoMadrina`,
  `CertificatoIdoneita`,
  `ContributoVersato`,
  `DataIscrizione`,
  `IdGruppo`,
  `SCR`
)

VALUES
  (314,'Aosta - Saint Martin','1999-05-30',1,'Mammoliti Attilio','San Giorgio Morgeto',1,1,'2006-04-05',0,2),
  (148107,'Aosta - S. Orso','1999-11-30',1,'Albace Peppino','Aosta - Saint Martin',0,1,'2006-04-05',0,2),
  (148145,'Aosta - S. Stefano','1993-05-15',1,'Livecchi Alessandra','Aosta - S. Stefano',1,1,'2006-04-05',0,2),
  (147929,'Aosta - S. Orso','1994-04-02',1,'Ballarini Claudio','S. Pierre',1,1,'2006-04-05',0,2),
  (33,'Aosta - Saint Martin','1999-11-30',1,'Nones Alessandra','Aosta - Immacolata',1,1,'2006-04-05',0,2),
  (147960,'Aosta - Immacolata','1994-02-27',1,'Serra Giuseppina','Aosta - Immacolata',1,1,'2006-04-05',0,2),
  (148111,'Aosta - Immacolata','1999-11-30',1,'Libertino Italia','Aosta - Immacolata',1,1,'2006-04-05',0,2),
  (84,'Aosta - Saint Martin','1999-11-30',1,'Barailler Corinne','Aosta - Saint Martin',0,1,'2006-04-05',0,2),
  (147483,'Aosta - Saint Martin','1994-09-11',1,'Collomb Marco','La Thuile',1,1,'2006-04-05',0,2),
  (112,'S.ta Maria Di Gesù (CT)','1993-07-03',1,'Giurdanella Patrizia','Maria Madre Di Misericordia - TO',1,1,'2006-04-05',0,2),
  (148109,'Aosta - S. Orso','1994-04-24',1,'Giovinazzo Vittoria','Aosta - Saint Martin',0,1,'2006-04-05',0,2),
  (147944,'Aosta - S. Stefano','1999-11-30',1,'De Gattis Patrik','Aosta - Immacolata',1,1,'2006-04-05',0,2),
  (205,'Aosta - Saint Martin','1993-04-25',1,'Molinari Carolina','Aosta - Saint Martin',0,1,'2006-04-12',0,2),
  (211,'Aosta - Immacolata','1999-11-30',1,'Verduci Giuseppe','Aosta - Saint Martin',0,1,'2006-04-12',0,2),
  (147407,'Aosta - Immacolata','1994-05-22',1,'Mossuto Carolina','Aosta - Saint Martin',0,1,'2006-04-12',0,2),
  (246,'Aosta - Saint Martin','1994-04-17',1,'Bottaro Bartolomeo','Aosta - Saint Martin',0,1,'2006-04-12',0,2),
  (326,'S. Pierre','1994-04-03',1,'Vierin Emma','Charvensod',1,1,'2006-04-12',0,2),
  (349,'Aosta - Immacolata','1993-05-09',1,'Pedico Nadia','S.Bernardo Abate - Rivoli',1,1,'2006-04-12',0,2),
  (136,'Aosta - Saint Martin','1993-08-01',1,'Cerrato Marco','Aosta - S. Orso',1,1,'2006-04-12',0,2),
  (341,'Aosta - Saint Martin','1993-03-07',1,'Chantal Joly','Arnad',1,1,'2006-04-12',0,2),
  (180,'Comiso','1993-09-11',1,'Lo Monaco Margherita','Aosta - Saint Martin',0,1,'2006-04-12',0,2),
  (148138,'Aosta - S. Orso','1994-02-26',1,'Poggio Alessandra','S.Maria Alla Fontana - Milano',1,1,'2006-04-12',0,2),
  (148110,'Aosta - S. Orso','1993-06-13',1,'Giovinazzo Cristina','Maria SS. Assunta - S.G. Morgeto',1,1,'2006-04-12',0,2),
  (147981,'Aosta - Saint Martin','1993-04-10',1,'Cerise Manuela','Aosta - S. Anselmo',1,1,'2006-04-12',0,2),
  (148112,'Porossan - Aosta','1999-11-30',1,'Guerrisi Sabina','Aosta - S. Anselmo',1,1,'2006-04-12',0,2),
  (313,'Aosta - Saint Martin','1994-08-28',1,'Guerrisi Carmela','Porossan - Aosta',1,0,'2006-04-12',0,2),
  (251,'Aosta - Saint Martin','1994-05-29',1,'Polimeni Alessandro','Aosta - Saint Martin',0,1,'2006-04-12',0,2),
  (147995,'Savona','1994-05-29',1,'Frimaire Daniele','Aosta - Saint Martin',0,0,'2006-04-12',0,2),
  (148108,'B.V. Di Lourdes - Napoli','1993-06-20',1,'Melillo Amerigo','Napoli',1,1,'2006-04-12',0,2),
  (331,'Aosta - Saint Martin','2002-03-31',1,null,null,0,1,'2006-04-19',0,2),
  (147959,'Aosta - S. Stefano','1993-08-01',1,'Chabloz Sabrina','Antey - S. André',1,1,'2006-04-20',0,2),
  (302,'Sant\'Ilario Nus','1994-07-17',1,'Brunel Roberta','Aosta - Saint Martin',0,1,'2007-03-07',0,2),
  (70,'SS Marco E Caterina - 72020 Cellino S Marco - BR','1996-09-08',1,'Licastri Tiziana','S Caterina ',1,0,'2008-03-26',0,2),
  (147413,'Aosta - Immacolata','1995-03-19',1,'Alliegro Amedeo','San Michele Arcangelo - Piano Di Sorrento NA',1,0,'2008-03-26',0,2),
  (148104,'Aosta - Immacolata','1995-04-23',1,'Fazari Concetta','San Lorenzo - Via S. Orso 14 - AOSTA',1,0,'2008-03-26',0,2),
  (148098,'Aosta - Saint Martin','1996-01-21',1,'Blanc Silvana','Pollein',1,0,'2008-03-26',0,2),
  (116,'Nus','1995-07-23',1,'Sansotta Renato','San Maurizio - Sarre',1,0,'2008-03-26',0,2),
  (118,'Aosta - Saint Martin','1994-12-01',1,'susanna Sara','Aosta - S. Stefano',1,0,'2008-03-26',0,2),
  (125,'Aosta - Saint Martin','1995-06-18',1,'Amaranto Sara','Sarre',1,0,'2008-03-26',0,2),
  (148035,'Aosta - Saint Martin','1995-02-02',1,'Paramatti Alessandro','Aosta - Saint Martin',0,0,'2008-03-26',0,2),
  (128,'Aosta - Saint Martin','1995-07-30',1,'Careri Andrea','S. Marcel',1,0,'2008-03-26',0,2),
  (133,'Aosta - Saint Martin','1995-09-17',1,'Angeloni Nadia','Aosta - Cattedrale',1,0,'2008-03-26',0,2),
  (148274,'Jovençan','1996-05-12',1,'Baleani Fabiana','San Carlo Borromeo - Osimo',1,0,'2008-03-26',0,2),
  (143,'S. Rhemy En Bosses','1995-09-10',1,'Chenal Rita','Aosta - S. Orso',1,0,'2008-03-26',0,2),
  (144,'Aosta - Saint Martin','1995-04-30',1,'Manduano Matteo','S. Caterina Da Siena - Torino',1,0,'2008-03-26',0,2),
  (148227,'Saint\'Eustachio - Chesallet Di SARRE','1999-11-30',1,'Preti Lorella','Aosta - Saint Martin',0,0,'2008-03-26',0,2),
  (147573,'Saint Maurice - Sarre','1996-03-10',1,'Verducci Roberta','Aosta - Saint Martin',0,0,'2008-03-26',0,2),
  (169,'Aosta - Saint Martin','1995-08-27',1,'Bramato Carmela','Aosta - Saint Martin',0,0,'2008-03-26',0,2),
  (148222,'Aosta - Cattedrale','1996-04-06',1,'Fazari Maria Annunziata','Aosta - Saint Martin',0,0,'2008-03-26',0,2),
  (1109,'Aosta - Immacolata','1999-11-30',1,'Fassoni Stefano','Aosta - S. Stefano',1,0,'2008-03-26',0,2),
  (147582,'San Pietro - Chatillon','1996-09-15',1,'BATISTA Joanny','Aosta - Saint Martin',0,0,'2008-03-26',0,2),
  (148437,'Aosta - Saint Martin','1999-11-30',1,'Real Aline','Aosta - S. Stefano',1,0,'2008-03-26',0,2),
  (147902,'Aosta - S. Orso','1995-05-07',1,'Vanzetti Cinzia Vittoria','Santo Stefano - Gressan',1,0,'2008-03-26',0,2),
  (148421,'Gesù Nazareno - Torino','1994-12-01',1,'Chierici Enrico','Gesù Nazareno - 10138 Torino',1,0,'2008-03-26',0,2),
  (147409,'Aosta - S. Orso','1998-05-23',1,'Montemezzo Andrea','Charvensod',1,0,'2008-03-26',0,2),
  (148031,'Aosta - Saint Martin','1996-04-21',1,'Bertucco Simone','Aosta - Saint Martin',0,0,'2008-03-26',0,2),
  (148441,'Aosta - S. Stefano','2003-12-02',1,'Renda Roberto','Aosta - Saint Martin',0,0,'2008-03-26',0,2),
  (147691,'Aosta - S. Orso','1995-09-03',1,'Passuello Silvana','Aosta - S. Orso',1,0,'2008-03-26',0,2),
  (148220,'Aosta - Saint Martin','1996-02-25',1,'Bianco Felicia','Aosta - Saint Martin',0,0,'2008-04-02',0,2),
  (238,'Aosta - Saint Martin','1996-02-25',1,'Patriarca Giuseppe','Aosta - Saint Martin',0,0,'2008-04-02',0,2),
  (148219,'Aosta - Saint Martin','1996-02-25',1,'Patriarca Giuseppe','Aosta - Saint Martin',0,0,'2008-04-02',0,2),
  (1114,'Aosta - S. Stefano','1999-11-30',1,'Carotenuto Andrea Luca','Aosta - Immacolata',1,0,'2008-04-02',0,2),
  (148417,'Aosta - Immacolata','1995-07-09',1,'Porcu Tecla','Morgex',1,0,'2008-04-02',0,2),
  (148422,'Sant\'Eustachio Chesallet - Sarre','1996-04-28',1,'Voyat Susanna',' Sant\'Ilario - Nus',1,0,'2008-04-02',0,2),
  (268,'S. Vincent','1996-08-12',1,'Farina Nadia','S. Vincent',1,0,'2008-04-02',0,2),
  (148223,'Charvensod','1996-04-06',1,'Rodà Massimo','Aosta - S. Anselmo',1,0,'2008-04-02',0,2),
  (148419,' San Lorenzo - Pré S. Didier','1995-05-13',1,'Bissoli Adriano','Santa Maria Di Lourdes - Milano',1,0,'2008-04-02',0,2),
  (1124,'Sant\'Eustachio Di Chesallet - Sarre','1995-07-16',1,'Cossard Alessandro','Aosta - Immacolata',1,0,'2008-04-02',0,2),
  (147854,'Saint Pierre','1995-07-30',1,'Turco Renata','Aosta - Saint Martin',0,0,'2008-04-02',0,2),
  (148011,'Aosta - Immacolata','1995-09-10',1,'Vacca Francesca','S. Christophe',1,0,'2008-04-02',0,2),
  (148224,'Aosta - Saint Martin','1999-11-30',1,'Sordi Luca','Aosta - Saint Martin',0,0,'2008-04-02',0,2),
  (148206,'San Maurizio - Sarre','1996-03-24',1,'Sowes Sami','San Maurizio - Sarre',1,0,'2008-04-02',0,2),
  (308,'Aosta - S. Stefano','1995-07-16',1,'Graizzaro Gianni','Gressoney - La - Trinité',1,0,'2008-04-02',0,2),
  (309,'Aosta - Saint Martin','1999-11-30',1,'Minerdo Nadia','S. Pierre',1,0,'2008-04-02',0,2),
  (148226,'Aosta - Saint Martin','1995-05-28',1,'Guarda alessandra','Gressan',1,0,'2008-04-02',0,2),
  (315,'Aosta - Saint Martin','1995-06-25',1,'Rey Grazia','San Maurizio - Sarre',1,0,'2008-04-02',0,2),
  (148438,'Aosta - Saint Martin','1995-02-02',1,'Gallucci Maria Nicoletta','S. Christophe',1,0,'2008-04-02',0,2),
  (1110,'Aosta - S. Stefano','1996-08-18',1,'Quaglia Elisabetta','San Maurizio - Sarre',1,0,'2008-04-02',0,2),
  (148140,'Aosta - Immacolata','1999-11-30',1,'Tripodi Morena','Aosta - Saint Martin',0,0,'2008-04-02',0,2),
  (147659,'Aosta - Saint Martin','1995-04-15',1,'Longo Claudia','Aosta - S. Orso',1,0,'2008-04-02',0,2),
  (147904,'Aosta - Saint Martin','1996-03-10',1,'Greco roberta','Quart',1,0,'2008-04-02',0,2),
  (147575,'Santa Colomba - Charvensod','1999-11-30',1,'Isabella Donatella','San Brizio - Avise',1,0,'2008-04-02',0,2),
  (148418,'San Maurizio - Sarre','1995-07-09',1,'Rosa Giovanna','Santo Stefano - Gressan',1,0,'2008-04-02',0,2),
  (148155,'Aosta - Immacolata','1999-11-30',1,'D\'Amico Nadia','Aosta - Cattedrale',0,0,'2008-04-23',0,2),
  (147955,'S.Maria Delle Grazie A Porchiano- 80147 Napoli','1992-04-26',1,'Calienno Mariarosaria','S.Francesco Alla Rizzottaglia NOVARA',1,0,'2008-05-29',0,2),
  (147975,'S.Maria Delle Grazie A Porchiano NA','1990-04-22',1,'Parente Giovanna','Maria SS. Addolorata - Napoli',1,0,'2008-05-29',0,2),
  (148218,'Aosta - Saint Martin','1996-02-08',1,'Albace Valerio','Aosta - Saint Martin',0,0,'2009-04-29',0,2),
  (10707,'Aosta - Saint Martin','1995-12-06',1,'Bastianelli Barbara','Aosta - Saint Martin',0,0,'2009-04-29',0,2),
  (147952,'SS. Annunziata - Napoli','1996-09-15',1,'Di Marino Michele','San Francesco Alla Rizzottaglia - Novara',1,1,'2009-04-29',0,2),
  (147426,'Aosta - Saint Martin','1996-09-29',1,'Rosset Claudia','Aosta - Saint Martin',0,0,'2009-04-29',0,2),
  (148252,'Aosta - S. Stefano','1996-04-06',1,'Del Vecchio Givoanni',null,0,0,'2009-04-29',0,2),
  (10696,'Aosta - Saint Martin','1996-02-08',1,'Frand Genisot Piergiorgio','Aosta - Saint Martin',0,0,'2009-04-29',0,2),
  (17328,'Gressan','1996-02-08',1,'Panozzo Chiara','Charvensod',1,0,'2009-04-29',0,2),
  (148568,'Aosta - Immacolata','1996-07-14',1,'Fossà Giovanni','S. Christophe',1,0,'2009-04-29',0,2),
  (10711,'Aosta - Saint Martin','1996-03-03',1,'Paramatti Sandro','Aosta - Saint Martin',0,0,'2009-04-29',0,2),
  (148423,'Aosta - Saint Martin','1996-06-16',1,'Gomerio Daniele','Sarre',1,0,'2009-04-29',0,2),
  (17326,'Aosta - S. Orso','1997-03-16',1,'Marzi Carlo','Aosta - S. Orso',1,0,'2009-04-29',0,2),
  (148425,'Sacro Cuore - Quartu Sant\'elena','1997-04-19',1,'Rosa Riccardo','Santo Saba - Roma',1,0,'2009-04-29',0,2),
  (148253,'Sarre','1999-11-30',1,'Ascenzi Lucas','Sarre',1,0,'2009-04-29',0,2),
  (10700,'Aosta - Cattedrale','1997-03-16',1,'Pirozzi Michele','Aosta - S. Orso',1,0,'2009-04-29',0,2),
  (10714,'Aosta - Saint Martin','1999-11-30',1,'Menel Nicholas','Aosta - Saint Martin',0,0,'2009-04-29',0,2),
  (147863,'Gressan','1999-11-30',1,'Zaccaria Gianfranco','Sant\'Andrea - Roma',1,0,'2009-04-29',0,2),
  (147399,'Aosta - Saint Martin','1997-05-18',1,'Corso Giovanni','Aosta - S. Stefano',1,0,'2009-04-29',0,2),
  (147584,'Aosta - Saint Martin','1997-01-05',1,'Caccamo Giulio','Aosta - Saint Martin',1,0,'2009-04-29',0,2),
  (10716,'Aosta - Saint Martin','1997-04-13',1,'Lavit Luca','Aosta - S. Orso',1,0,'2009-04-29',0,2),
  (10706,'Aosta - Saint Martin','2002-03-30',1,'Maguet Luigi','S. Vincent',1,0,'2009-04-29',0,2),
  (10717,'Aosta - Saint Martin','1996-09-30',1,'Revel Claudio','Pré S. Didier',1,0,'2009-04-29',0,2),
  (10693,'Aosta - S. Orso','1996-05-05',1,'Amato Silvana','Gignod',1,0,'2009-05-06',0,2),
  (23,'Aosta - Saint Martin','1996-06-02',1,'Romeo Francesco','Aosta - S. Anselmo',1,0,'2009-05-06',0,2),
  (147950,'Porossan','1996-01-09',1,'Sartoris Roberto','Fenis',1,0,'2009-05-06',0,2),
  (147432,'Aosta - Saint Martin','1997-03-09',1,'Rubini Stefania','Roisan',1,0,'2009-05-06',0,2),
  (17327,'Aosta - Saint Martin','1997-03-29',1,'Caccamo Giovanni','Aosta - Saint Martin',0,0,'2009-05-06',0,2),
  (10695,'Aosta - Saint Martin','1996-03-31',1,'Presta Giuseppe','Sarre',1,0,'2009-05-06',0,2),
  (10710,'Torgnon','1996-06-26',1,'Gal Letizia','Torgnon',1,0,'2009-05-06',0,2),
  (148412,'Aosta - Saint Martin','1996-05-12',1,'Ricchitelli Angela','Aosta - Immacolata',1,0,'2009-05-06',0,2),
  (148121,'Lamezia Terme S.Maria Immacolata','1996-07-14',1,'Mazza Pamela','Aosta - Immacolata',1,0,'2009-05-06',0,2),
  (147865,'Aosta - Immacolata','1999-11-30',1,'Baietta Maria Adele','Pont - S. Martin',1,0,'2009-05-06',0,2),
  (10712,'Aosta - S. Stefano','1999-11-30',1,'Verduccio Maria Caterina','Aosta - S. Orso',1,0,'2009-05-06',0,2),
  (10713,'Aosta - Saint Martin','1997-04-13',1,'Davisod Giuliano','Aosta - Saint Martin',0,0,'2009-05-06',0,2),
  (148410,'Aosta - S. Anselmo','2003-03-27',1,'Porretta Alessandra','Saint Christophe',1,0,'2009-05-06',0,2),
  (10701,'Aosta - Saint Martin','1995-12-06',1,'Bethaz Stephanie','Charvensod',1,0,'2009-05-06',0,2),
  (10702,'Aosta - Saint Martin','1999-11-30',1,'Meacci Fabrizio','Torino',1,0,'2009-05-06',0,2),
  (148496,'Aosta - Saint Martin','1996-04-06',1,'Besanzini lorenzo','Aosta - Cattedrale',1,0,'2009-05-06',0,2),
  (10704,'Aosta - Saint Martin','1998-06-14',1,'Ratto Maria Rosa','Sarre',0,0,'2009-05-06',0,2),
  (10705,'Sarre','1997-03-02',1,'Valente Patrizia','Aosta - S. Stefano',1,0,'2009-05-06',0,2),
  (148411,'Sarre','1997-03-30',1,'Preti Valentina','Sarre',1,0,'2009-05-06',0,2),
  (148181,'Aosta - Immacolata','1999-11-30',1,'Gagliano Arturo','Aosta - Saint Martin',0,0,'2009-05-06',0,2),
  (148569,'San Lorenzo - Aosta','1996-08-25',1,'Piromalli Luis','San Maurizio - Fenis',1,0,'2009-05-13',0,2),
  (1113,'Aosta - Saint Martin','1997-04-27',1,'Blanc Stefano','Quart',1,0,'2009-05-13',0,2),
  (147583,'Signayes','1997-05-18',1,'Bertassi Margherita',null,0,0,'2009-05-13',0,2),
  (148462,'Cappella Ospedale Civile SS Annunziata - Cosenza','1996-01-13',1,'Lucanto Giovanni','Quart',1,0,'2009-05-13',0,2),
  (148623,'Aosta - Saint Martin','1996-07-28',1,'Notari Nello','Villeneuve',1,0,'2009-06-03',0,2),
  (10697,'Aosta - Saint Martin','1997-04-05',1,'Mulé Ippolita','Aosta - Saint Martin',0,0,'2009-06-10',0,2),
  (148142,'Beata V. Del S. Rosario Di Pompei','1999-01-31',1,'Ratti Massimiliano','Santi Cosma E Damiano',1,0,'2010-03-10',0,2),
  (148626,'Aosta - Immacolata','1999-11-30',1,'Mei Christian','Aosta - Saint Martin',0,0,'2010-03-10',0,2),
  (148628,'Porossan- Madonna Delle Nevi','2001-06-27',1,'Giovinazzo Luigi','Aosta - S. Stefano',1,0,'2010-03-10',0,2),
  (147400,'Aosta - Saint Martin','1999-11-30',1,'Cotellucci Andrea','Aosta - Saint Martin',0,0,'2010-03-10',0,2),
  (148078,'Aosta - S. Stefano','1999-04-03',1,'Franceschini Gianluca','Aosta - Saint Martin',0,0,'2010-03-10',0,2),
  (147898,'Aosta - Saint Martin','1999-11-30',1,'Sandretto Laura',null,1,0,'2010-03-10',0,2),
  (147886,'Aosta - Saint Martin','1998-07-19',1,'Zecchi Alberto','S. Christophe',1,0,'2010-03-10',0,2),
  (148079,'Aosta - Saint Martin','1999-11-30',1,'Bergamaschi Fabrizio','S. Agostino Vescovo - SPESSA PV',1,0,'2010-03-10',0,2),
  (148027,'Aosta - Saint Martin','1999-04-11',1,'Mastrogiuseppe Rosalba',null,1,0,'2010-03-10',0,2),
  (148629,'S. Nicola Mola Di Bari - BA','1998-07-05',1,'Panella Michele','S. Nicola Mola Di Bari - BA',1,0,'2010-03-10',0,2),
  (148630,'Aosta - Saint Martin','1999-11-30',1,'Mammoliti Virgilia','Aosta - Saint Martin',0,0,'2010-03-10',0,2),
  (147967,'M.SS.Assunta - S.G.Morgeto RC','1998-08-09',1,'Caruso Andrea','Porossan',1,0,'2010-03-10',0,2),
  (148117,'Aosta - Saint Martin','1999-02-14',1,'Voulaz Stefano','Aosta - S. Stefano',1,0,'2010-03-10',0,2),
  (148631,'Aosta - Saint Martin','1998-05-31',1,'Sacco Giuseppina Liliana','Aosta - Saint Martin',0,0,'2010-03-10',0,2),
  (147965,'Aosta - Saint Martin','1999-06-27',1,'Bois Stefano','Valgrisenche',1,0,'2010-03-10',0,2),
  (148081,'Mater Ecclesia - Sassari','1999-11-30',1,'Masaroli Mario','Aosta - Saint Martin',0,0,'2010-03-10',0,2),
  (147998,'Aosta - Cattedrale','2001-07-01',1,'Peloso luigi','Aosta - Immacolata',1,0,'2010-03-10',0,2),
  (148324,'Aosta - Cattedrale','1998-04-25',1,'Porliod Giorgia','Aosta',1,0,'2010-03-10',0,2),
  (148131,'Aosta - Saint Martin','1998-08-02',1,'Ruffoni Cristina','S. Bartolomeo Ap. - Milano',1,0,'2010-03-10',0,2),
  (148003,'Aosta - Saint Martin','1998-03-28',1,'Canale Vittorio',null,1,0,'2010-03-10',0,2),
  (147977,'Rhèmes S. Georges','2003-05-25',1,'Frassy Livia','Aosta - Saint Martin',0,0,'2010-03-10',0,2),
  (148082,'Santo Stefano Aosta','1998-04-12',1,'Giuliano Silvia','Aosta - Saint Martin',0,0,'2010-03-10',0,2),
  (148620,'Aosta - S. Orso','1999-11-30',1,'Manavella Roberto','Aosta - S. Orso',1,0,'2010-03-10',0,2),
  (148589,'M.SS.Assunta -S.G. Morgeto RC Morgeto','1999-08-01',1,'Guerrisi Laura','M.SS.Assunta -S.G. Morgeto RC',1,0,'2010-03-10',0,2),
  (148632,'S.Leone Vescovo - ORDONA - Foggia','1999-11-30',1,'Raco Domenico','S.Giorgio Morgeto RC',1,0,'2010-03-10',0,2),
  (148232,'Aosta - Saint Martin','2000-08-20',1,'Fini Antonella',null,0,0,'2010-03-10',0,2),
  (147969,'Aosta - Saint Martin','1999-06-20',1,'Dal Follo Arianna','Aosta - Immacolata',1,0,'2010-03-10',0,2),
  (148128,'Gressan Santo Stefano','1999-01-03',1,'Morabito Rosa',null,1,0,'2010-03-10',0,2),
  (147406,'San Maurizio Sarre','1999-11-30',1,'Carlotto Simone','Aosta - Saint Martin',0,0,'2010-04-14',0,2),
  (147963,'Aosta - Saint Martin','1997-12-04',1,'Mosconi Paolo','Aosta - Immacolata',1,0,'2010-03-10',0,2),
  (147973,'Aosta - Saint Martin','1999-11-30',1,'Bozzuto Lino','S. Marcel',1,0,'2010-03-10',0,2),
  (147972,'Aosta - Saint Martin','1999-11-30',1,'Vecchiarina Angela','S. Marcel',1,0,'2010-03-10',0,2),
  (148076,'S.Eustachio - Chesallet - SARRE','1998-02-06',1,'Sergi Giovanna','Aosta - Saint Martin',0,0,'2010-03-10',0,2),
  (147993,'Aosta - Saint Martin','1998-03-28',1,'Vitulo Francesca','S.ta Maria Di Caravaggio - Pavia',1,0,'2010-03-10',0,2),
  (148159,'Aosta - Immacolata','1999-02-28',1,'De Vecchi Laura','Aosta - Immacolata',1,0,'2010-03-10',0,2),
  (147440,'Aosta - Saint Martin','1999-01-24',1,'Cosentino Maria (Rappre. da Tedesco Gessica)','Maria SS. Assunta - S.G.Morgeto RC',1,0,'2010-03-10',0,2),
  (148092,'Sta Marina Vergine-Polistena','1998-08-30',1,'Borgese Marcella','S.G.Battista - Carmagnola TO',1,0,'2010-03-10',0,2),
  (148633,'Sant\'Eustachio Chesallet - SARRE','1997-12-04',1,'Pampagnin Fernando','Aosta - Saint Martin',0,0,'2010-03-10',0,2),
  (148105,'Aosta - Saint Martin','1999-03-13',1,'Zoja Mariachiara','Santo Stefano - Gressan',0,0,'2010-03-10',0,2),
  (148096,'S. Rhemy En Bosses AO','1999-11-30',1,'Danna Emeric','Aosta - Saint Martin',1,0,'2010-03-10',0,2),
  (147688,'S. M. Arcangelo - Belvedere M.mo 87021 Cosenza','1998-03-29',1,'Belmonte Anna Patrizia','Aosta - Saint Martin',0,0,'2010-03-10',0,2),
  (147949,'Aosta - Saint Martin','1999-11-30',1,'Buffa Pier Antonio','S. Cuore Di Gesù - Ivrea',1,0,'2010-03-10',0,2),
  (148156,'Aosta - Immacolata','1999-11-30',1,'Bellini Daniele','Aosta - Immacolata',1,0,'2010-03-10',0,2),
  (148314,'Aosta - Saint Martin','1997-04-19',1,'Mambrilla Tullio','Aosta - S. Stefano',1,0,'2010-03-10',0,2),
  (148594,'La Salle','1997-06-01',1,'Chierici Simone','Aosta - Saint Martin',1,0,'2010-03-10',0,2),
  (147592,'Chesallet','1999-11-30',1,'Cappai Loredana',' St Hilaire - Gignod',1,0,'2010-03-10',0,2),
  (147982,'Madonna Di Fatima - Pinerolo','1998-03-21',1,'Magro Alessia','Aosta - Saint Martin',0,0,'2010-03-10',0,2),
  (147594,'Aosta - Saint Martin','1998-03-22',1,'Lumicisi Alessandro','Aosta - S. Orso',1,0,'2010-03-10',0,2),
  (148074,'Sant\'Eustachio -Sarre','1998-03-01',1,'Ratti Eleonora','Aosta - Saint Martin',1,0,'2010-03-10',0,2),
  (148300,'Aosta - Immacolata','2005-01-09',1,'Signoroni Paolo','B.V.Imm. E S. Grato - Argentera Di Rivarolo Can.se',1,0,'2010-03-10',0,2),
  (147591,'Aosta - Saint Martin','1998-06-28',1,'Conchatre Noela','Santo Stefano Di ALLEIN - Aosta',1,0,'2010-03-10',0,2),
  (148483,'Gesu Nazareno - 10138 Torino','1999-11-30',1,'Micheletti Angelo','Gesù Nazareno - 10138 Torino',1,0,'2010-03-10',0,2),
  (148296,'Aosta - Saint Martin','1999-11-30',1,'Mognol Giorgia','Aosta - Saint Martin',0,0,'2010-03-10',0,2),
  (148072,'Aosta - Saint Martin','2003-08-23',1,'Ambroggio Silvia','Aosta - Saint Martin',0,0,'2010-03-10',0,2),
  (148100,'Lorh Am Main St. Michael - Germania','1999-11-30',1,'Nicolosi Alessia','Lorh Am Main St. Michael - Germania',1,0,'2010-03-10',0,2),
  (148634,'Aosta - Saint Martin','1997-07-20',1,'Canale Vittorio','Aosta - S. Stefano',1,0,'2010-03-10',0,2),
  (148295,'Aosta - Immacolata','1999-11-30',1,'Scopacasa Domenico','Aosta - Saint Martin',0,0,'2010-03-10',0,2),
  (148592,'Aosta - Immacolata','1997-04-27',1,'Cavalieri Gilberto','Aosta - Immacolata',1,0,'2010-03-10',0,2),
  (148097,'Aosta - S. Orso','1998-05-10',1,'Cote Christine','Sant\'Eustachio - Chesallet - 11010 SARRE',1,0,'2010-03-10',0,2),
  (147599,'Aosta - Cattedrale','1999-03-28',1,'Brogna Antonio','Aosta - Saint Martin',0,0,'2010-03-10',0,2),
  (148171,'Maria SS. Assunta - 89017 S.G.Morgeto','1998-01-04',1,'Napoli Antonio','Maria SS. Maria SS. Assunta - 89017 S.G.Morgeto',1,0,'2010-03-10',0,2),
  (148095,'Aosta - Saint Martin','1997-08-24',1,'Vuillermoz Elisa','Sant\'Eustachio - Chesallet - SARRE',1,0,'2010-03-10',0,2),
  (148016,'Aosta - Immacolata','1997-06-08',1,'Giovinazzo Giuseppina','S.ta Maria Maggiore - 10046 Poirini To',1,0,'2010-03-10',0,2),
  (148484,'Aosta - Immacolata','1998-07-12',1,'Agostino Simona','Santa Caterina - Brissogne',1,0,'2010-03-10',0,2),
  (148514,'Ordinariato Milit.IT - Caserma Cavour Torino','1999-11-30',1,'Sciancalepore Maria Rita',null,0,0,'1999-11-30',0,2),
  (148593,'S. Nicola In Excenex','1997-09-14',1,'Pivot Enrica','Aosta - S. Anselmo',1,0,'2010-03-10',0,2),
  (148595,'Aosta - Immacolata','1997-07-27',1,'Ciglio Stefania','San Maurizio - 11010 SARRE',1,0,'2010-03-10',0,2),
  (148174,'Aosta - Saint Martin','1999-11-30',1,'Rosalba Mastrogiuseppe','Aosta - S. Orso',1,0,'2010-03-10',0,2),
  (147958,'Aosta - S. Stefano','1999-11-30',1,'Viganò Paolo','Aosta - Saint Martin',0,0,'2010-03-10',0,2),
  (148635,'Aosta - Saint Martin','1997-05-04',1,'Polimeni Antonietta','Sant\'Eustachio - Chesallet - SARRE',1,0,'2010-03-17',0,2),
  (148584,'S.Maria Della Neve - Sangineto (CS)','1999-01-03',1,'Grosso Luca','Aosta - Saint Martin',0,0,'2010-03-17',0,2),
  (148597,'Santo Stefano - Gressan','1997-02-07',1,'Patrizia Forciniti','Aosta - Maria Immacolata',1,0,'2010-03-17',0,2),
  (148073,'Aosta - S. Orso','1996-12-05',1,'Dalla Libera Simona','Sant\'Ilario - 11020 NUS',1,0,'2010-03-17',0,2),
  (147397,'Aosta - Immacolata','1998-03-08',1,'Albertocchi Lucia','Aosta - S. Stefano',1,0,'2010-03-17',0,2),
  (148598,'Aosta - S. Stefano','1997-08-17',1,'Cesolari Maurizio','Aosta - S. Stefano',1,0,'2010-03-17',0,2),
  (147434,'Aosta - Saint Martin','1998-05-10',1,'Condò Annunziata','Aosta - Saint Martin',1,0,'2010-03-17',0,2),
  (147589,'Aosta - Immacolata','1998-04-19',1,'Marcato Simonetta','Aosta - S. Orso',1,0,'2010-03-17',0,2),
  (148599,'Santa Colomba - Charvensod AO','1998-01-04',1,'Verduci Antonella','Aosta - Immacolata',1,0,'2010-03-17',0,2),
  (147595,'Aosta - Saint Martin','1997-01-09',1,'Piccinno Roberta','S. Anna Beinasco - TO',1,0,'2010-03-17',0,2),
  (147957,'Aosta - S. Orso','1997-06-22',1,'Paternoster Raffaele','Livorno',1,0,'2010-03-17',0,2),
  (148600,'Aosta - S. Stefano','1999-11-30',1,'Le Cause Annamaria','Aosta - Saint Martin',1,0,'2010-03-17',0,2),
  (147590,'Aosta - Saint Martin','1998-09-20',1,'Farina Nadia','S. Vincent',1,0,'2010-03-17',0,2),
  (148636,'San Pancrazio - Giardini Naxos Taormina','1997-08-10',1,'Cammaroso Rosa Maria','San Pancrazio - Giardini Naxos Taormina',1,0,'2010-03-17',0,2),
  (148637,'Aosta - Saint Martin','1997-06-22',1,'Diego Cuaz','Aosta - Immacolata',1,0,'2010-03-17',0,2),
  (148172,'Aosta - Immacolata','1998-08-02',1,'Sorace Antonio','Sant\'Ilario - Gignod',1,0,'2010-03-17',0,2),
  (147596,'Aosta - S. Anselmo','1999-11-30',1,'Arbizzi Rosanna','Aosta - S. Orso',1,0,'2010-03-17',0,2),
  (148022,'S. Christophe - AO','1998-08-02',1,'Vencato Luana','S. Christophe - AO',0,0,'2010-03-17',0,2),
  (148638,'Verres S. Egidio','1998-07-19',1,'Mammoliti Michele','Aosta - S. Orso',1,0,'2010-03-17',0,2),
  (148639,'Aosta - S. Orso','1998-04-12',1,'Raco Luigi','Aosta - S. Anselmo',1,0,'2010-03-17',0,2),
  (147971,'S.M.del Carmelo -98029 Scaletta Zanclea (ME)','1999-04-09',1,'Liberto Daniela','S.M.del Carmelo -98029 Scaletta Zanclea (ME)',1,0,'2010-04-14',0,2),
  (148596,'Aosta - Immacolata','1998-03-29',1,'Nicolis Claudio','Aosta - Immacolata',1,0,'2010-04-21',0,2),
  (148667,'Pollein','2004-03-07',1,'Cenacchi Maria Luigia','Aosta - Saint Martin',0,0,'2010-05-19',0,2),
  (148668,'Cristo RE - Aymavilles','1999-11-30',1,'Brillo Rita','Aosta - S. Anselmo',1,0,'2010-05-19',0,2),
  (148113,'San Maurizio - Sarre','1994-09-11',1,'Bozon Lucia','San Maurizio - Sarre',1,1,'2007-03-14',0,2),
  (148254,'SS.Astanzio E Antoniano Torrevecchia Pia (Pavia)','1999-11-30',1,'Chiesa Alessandro','S. Antonio Di Padova Di Corsico - MI',1,1,'2007-03-14',0,2),
  (1076,'Sant\'Eustachio - Chesallet','1995-04-30',1,'Sbicego Hermes','Sant\'Eustachio - Chesallet - Sarre',1,1,'2007-03-14',0,2),
  (64,'Aosta - Saint Martin','1994-07-12',1,'Tornesi Marco','Chiesa SS. Annunziata - Sabaudia LT',1,1,'2007-03-14',0,2),
  (66,'Aosta - S. Stefano','1999-11-30',1,'Brendolan Gian Luca','San Michele Di Cavaglià',1,0,'2007-03-14',0,2),
  (96,'Aosta - Saint Martin','1995-07-02',1,'Coquillard Gabriele','Aosta - Saint Martin',1,1,'2007-03-14',0,2),
  (99,'Aosta - Saint Martin','1995-04-02',1,'Giovinazzo Maria Stella','Aosta - SS Maria Immacolata',1,1,'2007-03-14',0,2),
  (103,'Aosta - Saint Martin','2003-05-18',1,'Cremaschi Augusto','S. Christophe',1,1,'2007-03-14',0,2),
  (350,' San Vittore - Roisan','1994-09-18',1,'Usel Deborah','Valgrisenche',1,1,'2007-03-14',0,2),
  (148213,'Aosta - Saint Martin','1995-05-14',1,'Marini Fabio','Aosta -SS Maria Immacolata',1,1,'2007-03-14',0,2),
  (148265,'Santo Stefano - Aosta','1994-07-24',1,'De Marco Andrea','Santo Stefano - Gressan',1,0,'2007-03-20',0,2),
  (148266,'Sant\'Orso - Cogne','1996-04-06',1,'Ester Bertacchi','Novarasco San Benedetto',1,1,'2007-03-20',0,2),
  (148256,'Aosta - Saint Martin','1993-12-09',1,'Comé Irma','Brissogne - Santa Caterina',1,1,'2007-03-20',0,2),
  (148255,'Saint Pierre','1999-11-30',1,'Ragno Salvatore','Reggio Emilia',1,1,'2007-03-20',0,2),
  (148258,'Aosta - Saint Martin','1995-01-22',1,'Canale Zaira','Aosta - Saint Martin',0,1,'2007-03-20',0,2),
  (148262,'Santo Stefano - Aosta','1995-02-12',1,'Florio roberto','M. Immacolata - Aosta',1,1,'2007-03-20',0,2),
  (147570,'Aosta - Saint Martin','1995-06-25',1,'Anile Maria','Santa Colomba - Charvensod',1,1,'2007-03-20',0,2),
  (148146,'San Nicola In Excenex - Aosta','1995-05-28',1,'Scarfò Filomena','Saint Martin De Corléans - Aosta',1,1,'2007-03-21',0,2),
  (1671,'Aosta - Saint Martin','1994-09-17',1,'Bieler Peter','San Maurizio - Brusson',1,1,'2007-03-21',0,2),
  (147571,'Aosta -San G. Battista - Cattedrale','1994-04-02',1,'Filetti Laura',null,0,1,'2007-03-21',0,2),
  (157,'Santo Stefano - Aosta','1999-11-30',1,'Villot Sabrina','San Maurizio - Sarre',0,1,'2007-03-21',0,2),
  (148089,'Aosta - Immacolata','1995-05-28',1,'Grosso Francesco','Aosta - Saint Martin',0,1,'2007-03-21',0,2),
  (163,' San Nicola -Excenex','1999-11-30',1,'Coccalotto Bruna','Aosta - Saint Martin',1,1,'2007-03-21',0,2),
  (1082,'Aosta - Saint Martin','1993-12-09',1,'Magnani Stefano','Sant\'Eustachio - Chesallet Sarre',1,1,'2007-03-22',0,2),
  (147953,'San Giuseppe - Ramacca (CT)','1997-07-26',1,'Failla Salvatore Gaetano','San Domenico Savio - Scordia (CT)',1,1,'2007-03-22',0,2),
  (148212,'Aosta - Saint Martin','1999-11-30',1,'Nigra Piero','San Pantaleone - Courmayeur',1,1,'2007-03-22',0,2),
  (148259,'Chatillon - San Pietro','1994-02-04',1,'Framarin Renato','Aosta - S. Stefano',1,1,'2007-03-22',0,2),
  (148217,'Aosta -SS Maria Immacolata','1999-11-30',1,'Picciavani Monica','S. Christophe',1,1,'2007-03-22',0,2),
  (148090,'Aosta - Saint Martin','1995-03-05',1,'Cecchini Renzo','Aosta - S. Orso',1,1,'2007-03-22',0,2),
  (1063,'Aosta - Saint Martin','1994-06-12',1,'Mirabello Fabio','Aosta - Saint Martin',1,1,'2007-03-22',0,2),
  (259,'Aosta - Saint Martin','1995-03-26',1,'Di Turo Francesco Paolo','San Maurizio - Sarre',1,1,'2007-03-22',0,2),
  (147352,'Aosta - Saint Martin','1994-02-08',1,'Machet Francesca','San Lorenzo - Chambave',1,1,'2007-03-22',0,2),
  (267,'Melito P.S. (R.Calabria)','1995-07-16',1,'Pugliese Antonino','S. Ippolito - Bardonecchia - TO',1,1,'2007-03-22',0,2),
  (282,'Aosta - Saint Martin','1994-09-15',1,'Scagliotti Luca','Aosta - Saint Martin',0,1,'2007-03-22',0,2),
  (147411,'Aosta -SS Maria Immacolata','1999-11-30',1,'Sciardi Alberto','Aosta - Saint Martin',0,1,'2007-03-22',0,2),
  (10833,'Aosta - Saint Martin','1994-02-08',1,'Fosson Jacques','Aosta - Saint Martin',1,1,'2007-03-22',0,2),
  (148267,'Della Natività Di Maria Vergine - G.Lido TE','1993-06-27',1,'Bossi Laura','Sant\'Anna - Piacenza',1,1,'2007-03-28',0,2),
  (148214,'Aosta - S. Orso','1999-11-30',1,'Grassi Antonella','Aosta - Saint Martin',0,0,'2008-03-26',0,2),
  (148420,'Aosta - S. Orso','1995-02-03',1,'Albace Joseph','Aosta - S. Orso',1,0,'2008-03-26',0,2),
  (147828,'S. Marcel','1995-08-13',1,'Milloz Nicole','Aosta - Cattedrale',1,0,'2008-03-26',0,2),
  (15,'Aosta - Saint Martin','2003-03-30',1,'Aresca Stefania','Aosta - Saint Martin',0,0,'2008-03-26',0,2),
  (147579,'Aosta - Saint Martin','1997-03-29',1,'PATORNO Gabriella Giovanna','Aosta - Cattedrale',1,0,'2008-03-26',0,2),
  (147576,'SS. Grato E Policarpo - Camandona - BI','1995-07-16',1,'Bonino Cecilia','Aosta - Saint Martin',1,0,'2008-03-26',0,2),
  (47,'Aosta - Saint Martin','1995-09-24',1,'Diémoz Amato','Aosta - Saint Martin',0,0,'2008-03-26',0,2),
  (148165,'Aosta - Immacolata','1995-01-05',1,'Biazzetti Alessia','Quart',1,0,'2008-03-26',0,2),
  (148148,'Aosta - Saint Martin','2000-03-19',0,null,null,0,0,'2011-03-29',0,2),
  (148408,'Santa Marina Vergine','2000-07-02',0,null,null,0,0,'2011-05-11',0,2),
  (148093,'Aosta - Saint Martin','1999-11-30',0,null,null,0,0,'2011-05-11',0,2),
  (148718,null,null,0,null,null,0,0,'2011-05-11',0,2),
  (148724,null,null,0,null,null,0,0,'2011-05-11',0,2),
  (148114,null,null,0,null,null,0,0,'2011-05-11',0,2),
  (148403,null,null,0,null,null,0,0,'2011-05-11',0,2),
  (148404,'Aosta - Immacolata',null,0,null,null,0,0,'2011-05-11',0,2),
  (148402,'Santa Margherita - Torino',null,0,'Merivot Christine',null,0,0,'2011-05-11',0,2),
  (148731,'San Nicola In Excenex - Aosta','2000-05-06',1,'Caregaro Luca','Aosta - S. Anselmo',1,0,'2011-05-11',0,2),
  (148725,null,null,0,null,null,0,0,'2011-05-11',0,2),
  (148391,'SS. Salvatore E S. Giovanni In Laterano Roma','1999-09-18',1,null,null,0,0,'2011-05-11',0,2),
  (148475,null,null,0,null,null,0,0,'2011-05-11',0,2),
  (148127,'San Rocco -Lillianes','1999-01-16',1,'Don Albino Linty Blanchet',null,1,0,'2011-05-11',0,2),
  (148020,'Aosta - S. Orso',null,0,null,null,0,0,'2011-05-11',0,2),
  (147926,'Aosta - Saint Martin','2000-05-28',0,'Criaco Santa',null,0,0,'2011-05-11',0,2),
  (148289,'Aosta - Immacolata',null,0,null,null,0,0,'2011-05-11',0,2),
  (148732,'M. Immacolata','1999-03-14',1,'Salvato Matteo','Aosta - Saint Martin',0,0,'2011-05-11',0,2),
  (148102,'San Bernardo Di Signayes - Gignod','1999-11-30',1,null,null,0,0,'2011-05-11',0,2),
  (148586,null,null,0,null,null,0,0,'2011-05-11',0,2),
  (148468,'Saint Martin','2000-04-09',1,'Ricchitelli Angela','Aosta - Immacolata',1,0,'2011-05-11',0,2),
  (148065,'Aosta - Immacolata',null,0,null,null,0,0,'2011-05-11',0,2),
  (148398,'Aosta - Saint Martin','1999-08-29',0,'Ferrari Clara',null,0,0,'2011-05-11',0,2),
  (148397,'Aosta - Saint Martin','2000-04-29',0,'Franzoso Marco',null,0,0,'2011-05-11',0,2),
  (148729,null,null,0,null,null,0,0,'2011-05-11',0,2),
  (147915,'San Maurizio - Sarre','1999-11-30',1,'Sorace Alessia','Pollein',1,0,'2011-05-11',0,2),
  (148726,'Aosta - Saint Martin','1999-06-27',0,null,null,0,0,'2011-05-11',0,2),
  (148260,'Nus',null,0,'Ascani Michela',null,0,0,'2011-05-11',0,2),
  (148390,'Santa Prisca - Roma','2000-04-25',0,'Rebaudo Marlene',null,0,0,'2011-05-11',0,2),
  (148084,'Aosta - S. Stefano','1999-07-11',1,'Cozza Giovanni','Quart',1,0,'2011-05-11',0,2),
  (147858,'Aosta - Saint Martin','1999-09-19',0,'Gullone Ylenia',null,0,0,'2011-05-11',0,2),
  (148388,'Aosta - S. Orso',null,0,null,null,0,0,'2011-05-11',0,2),
  (148719,null,null,0,'Meggiolaro Egizia',null,0,0,'2011-05-11',0,2),
  (148394,'Aosta - Saint Martin','1999-09-26',0,null,null,0,0,'2011-05-11',0,2),
  (148395,'Gressan',null,0,'Perret Jean-Paul','Charvensod',1,0,'2011-05-11',0,2),
  (148154,'San Maurizio Sarre','1999-06-13',1,'Peaquin Gianluca',null,0,0,'2011-05-11',0,2),
  (148727,null,null,0,null,null,0,0,'2011-05-11',0,2),
  (148728,'Santo Stefano - Aosta','2000-03-12',1,'Impérial Solange',null,0,0,'2011-05-11',0,2),
  (148389,'Aosta - Saint Martin','1999-04-03',0,null,null,0,0,'2011-05-11',0,2),
  (148139,'Aosta - Saint Martin','2000-05-28',0,null,null,0,0,'2011-05-11',0,2),
  (148733,'San Biagio - Doues','1999-09-12',1,'Mazzocchi Emanuele','Aosta - Immacolata',1,0,'2011-05-11',0,2),
  (148587,'Mercenasco - To',null,0,'Bertelli Mara','San Maurizio - Sarre',1,0,'2011-05-11',0,2),
  (148115,'Aosta - Immacolata',null,0,null,null,0,0,'2011-05-11',0,2),
  (148509,'San Maurizio - Sarre',null,0,'Micheletti Anna Pia',null,0,0,'2011-05-11',0,2),
  (148407,'Aosta - Saint Martin','2000-04-09',0,'Ricci Arianna','Sant\'Eustachio Chesallet - SARRE',1,0,'2011-05-11',0,2),
  (148088,'San Maurizio - Sarre','1999-09-18',0,'Renda Roberto',null,0,0,'2011-05-11',0,2),
  (148177,null,null,0,null,null,0,0,'2011-05-11',0,2),
  (148086,'Sant\'Orso - Aosta','1999-06-06',0,'Tripoli Domenico',null,0,0,'2011-05-11',0,2),
  (148730,'Saint Martin Aosta','1999-11-30',1,'Vaccaro Loris','Saint-Vincent',1,0,'2011-05-11',0,2),
  (148734,null,null,0,null,null,0,0,'2011-05-11',0,2),
  (148229,null,null,0,null,null,0,0,'2011-05-11',0,2),
  (148721,null,null,0,null,null,0,0,'2011-05-11',0,2),
  (148400,'Aosta - S. Orso',null,0,null,null,0,0,'2011-05-11',0,2),
  (148136,'Aosta - Saint Martin','2000-05-28',0,null,null,0,0,'2011-05-11',0,2),
  (148103,'Aosta - Saint Martin','1999-02-21',0,null,null,0,0,'2011-05-11',0,2),
  (148722,'Santo Stefano - Aosta','1999-11-30',1,'Erik Montegrandi','Aosta - Cattedrale',1,0,'2011-05-11',0,2),
  (148393,null,null,0,null,null,0,0,'2011-05-11',0,2),
  (148150,'Aosta - Saint Martin','2000-06-04',0,'Mondardini Giorgio',null,0,0,'2011-05-11',0,2),
  (148723,'Sant\'Orso - Aosta','1999-09-05',1,'Tallon Cinzia',null,0,0,'2011-05-11',0,2),
  (148149,'Aosta - Saint Martin','2000-04-22',0,'Porliod Giorgia Melissa',null,0,0,'2011-05-11',0,2),
  (148083,'Aosta - Saint Martin','1999-11-30',0,null,null,0,0,'2011-05-11',0,2),
  (148720,null,null,0,null,null,0,0,'2011-05-11',0,2),
  (147876,'San Caprasio - Aulla','1999-06-27',1,'Piumatti Giovanni','Aosta - Cattedrale',1,0,'2011-05-11',0,2),
  (148401,'Aosta - S. Stefano',null,0,'Hilary Olivotte','Aosta - Saint Martin',0,0,'2011-05-11',0,2),
  (148406,'Aosta - S. Stefano',null,0,null,null,0,0,'2011-05-11',0,2),
  (148099,'Aosta - Saint Martin','2000-06-11',0,'Adriane Suellen Rodrigues Da Silva',null,0,0,'2011-05-11',0,2),
  (148134,'Aosta - Saint Martin','1999-11-30',0,null,null,0,0,'2011-05-11',0,2),
  (148405,null,null,0,null,null,0,0,'2011-05-11',0,2),
  (148392,'Sarre',null,0,null,null,0,0,'2011-05-11',0,2),
  (148588,null,null,0,null,null,0,0,'2011-05-11',0,2),
  (148101,'Quart','1999-11-30',1,'Del Maschio Roberto',null,0,0,'2011-05-11',0,2),
  (148085,'Aosta - Saint Martin','1999-11-30',1,'Cordivani Paola S. Christophe',null,0.00,1,'1999-11-30',0,2);


-- *************************************
-- Crea la tabella `tblgruppisacramenti`
-- *************************************
DROP TABLE IF EXISTS `tblgruppisacramenti`;
CREATE TABLE `tblgruppisacramenti` (
  `IDGruppoSacramento` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `SCR` INTEGER UNSIGNED NOT NULL COMMENT 'Tipo di sacramento: 1 comunione, 2 cresima',
  `GruppoSacramento` DATETIME DEFAULT NULL,
  PRIMARY KEY(`IDGruppoSacramento`)
)
ENGINE = InnoDB
DEFAULT CHARSET = latin1
COMMENT = 'tabella per gestire la sessione del sacramento';

-- Inserisce dati nella tabella `tblgruppisacramenti`
INSERT INTO `tblgruppisacramenti`
  (`IDGruppoSacramento`,`SCR`,`GruppoSacramento`)

VALUES
  (1,2,'2011-10-23 11:00:00'),
  (2,2,'2011-10-23 15:30:00');

--
--
--