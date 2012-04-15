--************************************
-- AGGIORNAMENTO TABELLE DATABASE
-- dopo 1 gennaio 2012
--************************************

-- Aggiunge il campo DataRestituzione nella tabella tblgruppisacramenti
ALTER TABLE `db_oratorio`.`tblgruppisacramenti` ADD COLUMN `DataRestituzione` DATE DEFAULT NULL AFTER `GruppoSacramento`;

-- Salva il dato nel nuovo campo DataRestituzione
INSERT INTO `tblgruppisacramenti` (`SCR`,`GruppoSacramento`,`DataRestituzione`)
      VALUES (1,`2012-05-20 11:00:00`,`2012-05-02`);
      

