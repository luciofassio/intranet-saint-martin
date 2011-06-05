<?php
// accesso al db
function ConnettiDB()
{
	require('saint_martin_db.inc');
	$link = mysql_connect($dbserver, $dbuser, $dbpassword);
	if (!$link) {
    	die('Errore di connessione: '.mysql_error()."<br/><br/>".$sql);
	}
	mysql_select_db($dbname, $link) 
		or die("Errore di apertura db: ".mysql_error()."<br/><br/>".$sql);
}

//:::::::::::::::::::
// provider dati
//:::::::::::::::::::
// Squadre 
/* per duplicare un set di squadre su un nuovo evento
INSERT INTO tblSquadre (IDEvento,NomeSquadra, PercorsoSquadra)
select 19 as IDEvento, NomeSquadra, PercorsoSquadra from tblSquadre where idevento=18
*/
function GetSquadre($IDEvento) {
	$sql = "SELECT * FROM tblSquadre WHERE IDEvento=%1\$s ORDER BY NomeSquadra";
	$sql = sprintf($sql, $IDEvento);
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("GetSquadre: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

function GetSquadraByID($ID, $IDEvento) {
	// ID		l'id dell'iscritto
	// ottiene il nome della squadra nel quale è iscritta la persona
	$sql = "SELECT NomeSquadra FROM tblIscrizioni INNER JOIN tblSquadre ON tblIscrizioni.IDSquadra = tblSquadre.IDSquadra WHERE ID=%1\$s AND tblIscrizioni.IDEvento=%2\$s";
	$sql = sprintf($sql, $ID, $IDEvento);
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("GetSquadraByID: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

function GetIscrittiSquadra($IDEvento,$IDSquadra) {
// ottiene l'elenco degli iscritti alla squadra indicata per l'evento indicato
	$sql = "SELECT Catechismi.ID, Cognome, Nome, tblClassi.Sigla FROM tblIscrizioni INNER JOIN Catechismi ON tblIscrizioni.ID = Catechismi.ID INNER JOIN tblClassi ON tblClassi.IDClasse=Catechismi.Classe WHERE tblIscrizioni.IDSquadra = %2\$s AND tblIscrizioni.IdEvento = %1\$s ORDER BY Cognome, Nome;";
	$sql = sprintf($sql, $IDEvento, $IDSquadra);
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("GetIscrittiSquadra: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
   return $result;
}

function GetNumIscrittiSquadra($IDEvento,$IDSquadra,$IDClasse) {
	// ID		l'id dell'iscritto
	// ottiene il numero di iscritti per squadra, evento e classe scolastica
	$sql  = "SELECT COUNT(tblIscrizioni.ID) AS iscritti ";
	$sql .= "FROM tblIscrizioni tblIscrizioni ";
   $sql .= "INNER JOIN Catechismi ON (tblIscrizioni.ID = Catechismi.ID) ";
	$sql .= "WHERE (tblIscrizioni.IDSquadra = %2\$s) ";
	$sql .= "AND (tblIscrizioni.IDEvento = %1\$s) ";
	$sql .= "AND (tblIscrizioni.IDRuolo NOT IN (3, 4, 5, 7)) ";
	$sql .= "AND (Catechismi.Classe = %3\$s) ";
	$sql .= "GROUP BY tblIscrizioni.IDSquadra";
	//ob_clean();
	//echo $sql;
	//exit();
	$sql = sprintf($sql, $IDEvento, $IDSquadra, $IDClasse);
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("GetNumIscrittiSquadra: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}
/*SELECT COUNT(tblIscrizioni.ID) AS iscritti
  FROM    saint_martin_db.tblIscrizioni tblIscrizioni
       INNER JOIN
          saint_martin_db.Catechismi Catechismi
       ON (tblIscrizioni.ID = Catechismi.ID)
 WHERE     (tblIscrizioni.IDSquadra = 51)
       AND(tblIscrizioni.IDEvento = 16)
       AND(tblIscrizioni.IDRuolo NOT IN (3, 4, 5, 7))
       AND(Catechismi.Classe = 3)
GROUP BY tblIscrizioni.IDSquadra */

function InsertIscrizioneSquadra($ID, $IDSquadra, $IDEvento) {
	$sql = "INSERT tblIscrizioni (ID, IDSquadra, IDEvento, Stampato, ForzaStampa) VALUES(%1\$s, %2\$s, %3\$s, 0, 0)";
	$sql = sprintf($sql, $ID, $IDSquadra, $IDEvento);
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("InsertIscrizioneSquadra: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
	return $result;
}

function UpdateIscrizioneSquadra($IDIscr, $IDSquadra) {
	$sql = "UPDATE tblIscrizioni SET IDSquadra = %2\$s WHERE IDIscrizione = %1\$s";
	$sql = sprintf($sql, $IDIscr, $IDSquadra);
	//ob_clean();
	//echo "sql=".$sql;
	//exit();
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("UpdateIscrizioneSquadra: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
	return $result;
}

// funzioni intranet
function GetApplicazioni($IDOperatore)
{
	$result = mysql_query("SELECT * FROM tblOperatoriXFunzioni inner join tblFunzioni on (tblOperatoriXFunzioni.id_funzione =  tblFunzioni.id_funzione) where id_operatore='".$IDOperatore."' ORDER BY sequenza");
	if (mysql_errno() <> 0) {
		throw new Exception("GetApplicazioni: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

function GetUtente($login, $password)
{
	$result = mysql_query("SELECT idoperatore, livello FROM tblOperatori WHERE login='".sql_quote($login)."' AND password='".sql_quote($password)."' AND attivo=1" );
	if (mysql_errno() <> 0) {
		throw new Exception("GetUtente: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

function GetOperatore($IDOperatore)
{
	$result = mysql_query("SELECT Cognome, Nome, login FROM Catechismi INNER JOIN tblOperatori ON ID=idoperatore WHERE ID=".$IDOperatore);
	if (mysql_errno() <> 0) {
		throw new Exception("GetOperatore: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

// angrafica catechismo
function GetCatechismi() {
	$result = mysql_query("SELECT Nome, Data_di_nascita FROM Catechismi");
	if (mysql_errno() <> 0) {
		throw new Exception("GetCatechismi: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

// fornisce l'ID della persona a partire dal bar code
function GetIDPersonaByBarCode($BarCode) {
	$sql = "SELECT ID FROM Catechismi WHERE ID='%1\$s'";
	$sql = sprintf($sql, (string)(int)$BarCode);
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("GetIDPersonaByBarCode: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    if ((mysql_num_rows($result) > 0)) {
		$rowID = mysql_fetch_object($result);
		return $rowID->ID; 
	} else {
		return "";	
	}
}

// tutti i dati della persona
function GetPersona($ID) {
	$result = mysql_query("SELECT * FROM Catechismi WHERE ID=".$ID);
	if (mysql_errno() <> 0) {
		throw new Exception("GetPersona: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

// persona con per disclaimer privacy
function GetPersonaPrivacy($ID) {
	$sql = "SELECT Cognome, Nome, Tipo_via, Via, numero_civico, CAP, Citt, Provincia,email,Sesso,Data_di_nascita,Luogo_di_nascita,tblClassi.Classe,tblSezioni.Sezione,Parrocchia_Provenienza,Prefisso,Numero,TelSMS FROM ((Catechismi LEFT OUTER JOIN tblTelefoni ON (Catechismi.ID = tblTelefoni.ID)) LEFT OUTER JOIN tblClassi ON (Catechismi.Classe = tblClassi.IDClasse)) LEFT OUTER JOIN tblSezioni ON (Catechismi.Sezione = tblSezioni.IDSezione) WHERE (tblTelefoni.IDTipoTelefono IN (1,2,3,4,5,6,7,null) OR tblTelefoni.IDTipoTelefono is null) AND Catechismi.ID = %1\$s ORDER BY tblTelefoni.IDTipoTelefono";
	$sql = sprintf($sql, $ID);
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("GetPersonaPrivacy: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

// parrocchia di provenienza per disclaimer privacy
function GetParrocchiaProvenienza($parrocchia_provenienza) {
	if ($parrocchia_provenienza=="" || $parrocchia_provenienza==null) {$parrocchia_provenienza=0;}
  $sql = "SELECT IdParrocchia,Parrocchia FROM tblparrocchie WHERE IdParrocchia=%1\$s ORDER BY Parrocchia";
	$sql = sprintf($sql, $parrocchia_provenienza);
	$result = mysql_query($sql);
	
  if (mysql_errno() <> 0) {
		throw new Exception("GetParrocchiaProvenienza: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    $row=mysql_fetch_object($result);
	  $parrocchia_provenienza=htmlentities($row->Parrocchia);
    return $parrocchia_provenienza;
}

// numero di cellulare personale di una persona
function GetCellularePersonaleID($ID,$idTipoTelefono) {
	$sql = "SELECT Prefisso, Numero FROM tblTelefoni t WHERE IDTipoTelefono = %1\$s AND ID = %2\$s";
	$sql = sprintf($sql, $idTipoTelefono, $ID);
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("GetCellularePersonaleID: ".mysql_errno().":".mysql_error()."\n\r".$sql);
		exit();
	}  
    return $result;
}

// numero di cellulare per SMS di una persona
function GetCellulareSMSByID($ID) {
	$sql = "SELECT Prefisso, Numero FROM tblTelefoni t WHERE TelSMS = 1 AND ID = %1\$s";
	$sql = sprintf($sql, $ID);
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("GetCellulareSMSByID: ".mysql_errno().":".mysql_error()."\n\r".$sql);
		exit();
	}  
    return $result;
}

// Ruoli
function GetRuoli()
{
	$result = mysql_query("SELECT 0 AS IDRuolo, 'Seleziona ruolo' AS Ruolo UNION SELECT * FROM tblRuoliER ORDER BY IDRuolo");
	if (mysql_errno() <> 0) {
		throw new Exception("GetRuoli: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

// Listino
/* per duplicare un listino NB: esiste già il metodo
INSERT INTO saint_martin_db.tblListino (IDEvento, IDRuolo, Iscrizione, Pranzo, Cena, CenaFinale, AbbonamentoPranzo, AbbonamentoCena, AbbonamentoPranzoCena, EventoSpecialeER)
select 17 as IDEvento, IDRuolo, Iscrizione, Pranzo, Cena, CenaFinale, AbbonamentoPranzo, AbbonamentoCena, AbbonamentoPranzoCena, EventoSpecialeER from tblListino where idevento=16
*/

// legge il listino completo dell'evento corrente
function GetListino($IDEventoCorrente)
{
	$sql = "SELECT Ruolo, Iscrizione, Pranzo, Cena, CenaFinale, AbbonamentoPranzo, AbbonamentoCena, AbbonamentoPranzoCena, EventoSpecialeER FROM tblListino INNER JOIN tblRuoliER ON tblListino.IDRuolo = tblRuoliER.IDRuolo WHERE IDEvento=%1\$s";
	$sql = sprintf($sql, $IDEventoCorrente);
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("GetListino: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

// legge il listino di un particolare ruolo
function GetListinoByRuoloER($IDRuolo, $IDEventoCorrente)
{
    //die('idruolo:'.$IDRuolo."<br/>IDEventoCorrente:".$IDEventoCorrente);
	$sql = "SELECT Iscrizione, Pranzo, Cena, CenaFinale, AbbonamentoPranzo, AbbonamentoCena, AbbonamentoPranzoCena, EventoSpecialeER FROM tblListino WHERE IDRuolo = %1\$s AND IDEvento = %2\$s";
	$sql = sprintf($sql, $IDRuolo, $IDEventoCorrente);
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("GetListinoByRuoloER: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

// duplica un listino esistente
function DuplicateListino($IDEventoOrigine, $IDEventoDestinazione)
{
	$sql = "insert into tbllistino (IDEvento,IDRuolo,Iscrizione,Pranzo,Cena,CenaFinale,AbbonamentoPranzo,AbbonamentoCena,AbbonamentoPranzoCena,EventoSpecialeER) select %2\$s,IDRuolo,Iscrizione,Pranzo,Cena,CenaFinale,AbbonamentoPranzo,AbbonamentoCena,AbbonamentoPranzoCena,EventoSpecialeER from tbllistino where idevento=%1\$s";
	$sql = sprintf($sql, $IDEventoOrigine, $IDEventoDestinazione);
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("DuplicateListino: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}


// Eventi
// restituisce tutti gli eventi
function GetEventi() {
	$result = mysql_query("SELECT * FROM tblEventi ORDER BY Data DESC LIMIT 8");
	if (mysql_errno() <> 0) {
		throw new Exception("GetEventi: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

function GetEventoCorrente() {
	$result = mysql_query("SELECT * FROM tblEventi WHERE IDEvento = (SELECT EventoCorrente FROM tblParametri)");
	if (mysql_errno() <> 0) {
		throw new Exception("GetEventoCorrente: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

function GetEventoByID($IDEvento) {
	$sql = "SELECT * FROM tblEventi WHERE IDEvento = %1\$s";
	$sql = sprintf($sql, $IDEvento);
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("GetEventoByID: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

function GetIDEventoCorrente() {
	$result = mysql_query("SELECT EventoCorrente FROM tblParametri");
	if (mysql_errno() <> 0) {
		throw new Exception("GetIDEventoCorrente: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

function SetEventoCorrente($IDEvento) {
	$sql = "UPDATE tblParametri SET EventoCorrente = %1\$s";
	$sql = sprintf($sql, $IDEvento);
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("SetEventoCorrente: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
	return $result;
}

// legge la descriozione dell'evento per chiave
function GetNomeEventoByID($IDEvento) {
	$sql = "SELECT NomeEvento FROM tblEventi WHERE IDEvento=%1\$s";
	$sql = sprintf($sql, $IDEvento);
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("GetNomeEventoByID.1: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
	$row = mysql_fetch_assoc($result);
	if (mysql_errno() <> 0) {
		throw new Exception("GetNomeEventoByID.2: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $row["NomeEvento"];
}
	
// legge la descriozione dell'evento corrente
function GetNomeEventoCorrente()
{
	$result = mysql_query("SELECT NomeEvento FROM tblEventi WHERE IDEvento = (SELECT EventoCorrente FROM tblParametri)");
	if (mysql_errno() <> 0) {
		throw new Exception("GetNomeEventoCorrente.1: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
	$row = mysql_fetch_assoc($result);
	if (mysql_errno() <> 0) {
		throw new Exception("GetNomeEventoCorrente.2: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $row["NomeEvento"];
}

// legge l'elenco degli eventi al quale la persona è iscritta
function GetEventiPartecipatiByID($ID) {
	$sql = "SELECT distinct IDEvento FROM tblPrenotazioni t where id=%1\$s";
	$sql = sprintf($sql, $ID);
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("GetEventiPartecipatiByID: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

// Iscrizioni
function GetIscrizioneER($ID, $IDEventoCorrente) {
	$sql = "SELECT * FROM tblIscrizioni WHERE ID=%1\$s AND IDEvento=%2\$s";
	$sql = sprintf($sql, $ID, $IDEventoCorrente);
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("GetIscrizioneER: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

function InsertIscrizioneER($ID, $IDEventoCorrente, $IDRuolo, $note, $AbbonamentoPranzo, $AbbonamentoCena, $CostoTotaleEuro, $CenaFinale, $NrOspiti, $EventoSpecialeER, $Pagamento) {
	if ($IDRuolo == "") $IDRuolo = "0";
	if ($AbbonamentoPranzo == "") $AbbonamentoPranzo = "0";
	if ($AbbonamentoCena == "") $AbbonamentoCena = "0";
	if ($CostoTotaleEuro == "") $CostoTotaleEuro = "0";
	if ($CenaFinale == "") $CenaFinale = "0";
	if ($NrOspiti == "") $NrOspiti = "0";
	if ($EventoSpecialeER == "") $EventoSpecialeER = "0";
	if ($Pagamento == "") $Pagamento = "0";
	$sql = "INSERT tblIscrizioni (ID, IDEvento, IDRUolo, Note, AbbonamentoPranzo, AbbonamentoCena, CostoComplessivo, CenaFinale, CenaFinaleOspiti, EventoSpecialeER, Pagamento) VALUES(%1\$s, %2\$s, %3\$s, '%4\$s', %5\$s, %6\$s, %7\$s, %8\$s, %9\$s, %10\$s, %11\$s)";
	$sql = sprintf($sql, $ID, $IDEventoCorrente, $IDRuolo, $note, $AbbonamentoPranzo, $AbbonamentoCena, str_replace(",", ".", $CostoTotaleEuro), $CenaFinale, str_replace(",", ".", $NrOspiti), $EventoSpecialeER, str_replace(",", ".", $Pagamento));
	//ob_clean();
	//echo $sql;
	//exit();
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("InsertIscrizioneER: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
	return $result;
}

function UpdateIscrizioneER($IDIscr, $IDRuolo, $note, $AbbonamentoPranzo, $AbbonamentoCena, $CostoTotaleEuro, $CenaFinale, $NrOspiti, $EventoSpecialeER, $Pagamento) {
	if ($IDRuolo == "") $IDRuolo = "0";
	if ($AbbonamentoPranzo == "") $AbbonamentoPranzo = "0";
	if ($AbbonamentoCena == "") $AbbonamentoCena = "0";
	if ($CostoTotaleEuro == "") $CostoTotaleEuro = "0";
	if ($CenaFinale == "") $CenaFinale = "0";
	if ($NrOspiti == "") $NrOspiti = "0";
	if ($EventoSpecialeER == "") $EventoSpecialeER = "0";
	if ($Pagamento == "") $Pagamento = "0";
	$sql = "UPDATE tblIscrizioni SET IDRUolo=%1\$s, Note='%2\$s', AbbonamentoPranzo=%4\$s, AbbonamentoCena=%5\$s, CostoComplessivo=%6\$s, CenaFinale=%7\$s, CenaFinaleOspiti=%8\$s, EventoSpecialeER=%9\$s, Pagamento=%10\$s WHERE IDIscrizione=%3\$s";
	$sql = sprintf($sql, $IDRuolo, $note, $IDIscr, $AbbonamentoPranzo, $AbbonamentoCena, str_replace(",", ".", $CostoTotaleEuro), $CenaFinale, str_replace(",", ".", $NrOspiti), $EventoSpecialeER, str_replace(",", ".", $Pagamento));
	//ob_clean();
	//echo "Pranzo: ".$AbbonamentoPranzo."<br/>";
	//echo $sql;
	//exit;
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("UpdateIscrizioneER: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
	return $result;
}

function DeleteIscrizioneER($IDIscr){
	$sql = "DELETE FROM tblIscrizioni WHERE IDIscrizione=%1\$s";
	$sql = sprintf($sql, $IDIscr);
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("DeleteIscrizioneER: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
	return $result;
}

// Prenotazioni
// legge il recordset delle prentoazioni dell'evento corrente per un utente specificato
// ID		codice utente
function GetPrenotazione($ID, $IDEventoCorrente) {
	$sql = "SELECT Data, Mattina, Pomeriggio, Sera, Pranzo, Cena, PranzoQuota, CenaQuota, PranzoGratis, CenaGratis FROM tblPrenotazioni t WHERE id=%1\$s AND IDEvento = %2\$s;";
	$sql = sprintf($sql, $ID, $IDEventoCorrente);
	$result = mysql_query($sql);
	switch (mysql_errno()) {
		case 0:
			break;
		default:
			throw new Exception("GetPrenotazione: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
			exit();
	}  
	return $result;
}

// inserisce le informazioni di prenotazione per una data specifica
// ID		codice utente
// Data	data dell'evento
function InsertPrenotazione($ID, $IDEventoCorrente, $Data, $Mattina, $Pomeriggio, $Sera, $Pranzo, $Cena, $PranzoQuota, $CenaQuota, $PranzoGratis, $CenaGratis) {
	$sql = "INSERT tblPrenotazioni (ID, IDEvento, Data, Mattina, Pomeriggio, Sera, Pranzo, Cena, PranzoQuota, CenaQuota, PranzoGratis, CenaGratis) VALUES(%1\$s, %2\$s, '%3\$s', %4\$s, %5\$s, %6\$s, %7\$s, %8\$s, %9\$s, %10\$s, %11\$s, %12\$s)";
	$sql = sprintf($sql, $ID, $IDEventoCorrente, convert($Data, 'd/m/Y', 'Y-m-d'), $Mattina, $Pomeriggio, $Sera, $Pranzo, $Cena, str_replace(",", ".", $PranzoQuota), str_replace(",", ".", $CenaQuota), $PranzoGratis, $CenaGratis);
	//ob_clean();
	//echo $sql;
	//exit();
	$result = mysql_query($sql);
	switch (mysql_errno()) {
		case 1062:   // chiave duplicata: aggiorno il record
			$sql = "UPDATE tblPrenotazioni SET Mattina=%4\$s, Pomeriggio=%5\$s, Sera=%6\$s, Pranzo=%7\$s, Cena=%8\$s, PranzoQuota=%9\$s, CenaQuota=%10\$s, PranzoGratis=%11\$s, CenaGratis=%12\$s WHERE ID=%1\$s AND IDEvento=%2\$s AND Data='%3\$s'";
			$sql = sprintf($sql, $ID, $IDEventoCorrente, convert($Data, 'd/m/Y', 'Y-m-d'), $Mattina, $Pomeriggio, $Sera, $Pranzo, $Cena, str_replace(",", ".", $PranzoQuota), str_replace(",", ".", $CenaQuota), $PranzoGratis, $CenaGratis);
			$result = mysql_query($sql);
			if (mysql_errno() <> 0) {
				throw new Exception("InsertPrenotazione.1: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
				exit();
			}  
			break;
		case 0:
			break;
		default:
			throw new Exception("InsertPrenotazione.2: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
			exit();
	}  
	return $result;
}

// elimina la prenotazione di un utenete a una certa data
function DeletePrenotazione($ID, $IDEventoCorrente, $Data)
{
	$sql = "DELETE FROM tblPrenotazioni WHERE ID = %1\$s AND IDEvento = %2\$s; AND Data = '%3\$s'";
	$sql = sprintf($sql, $ID, $IDEventoCorrente, convert($Data, 'd/m/Y', 'Y-m-d'));
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("DeletePrenotazione: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

// elimina tutte le prenotazioni di un utente
function DeletePrenotazioni($ID, $IDEventoCorrente) {
	$sql = "DELETE FROM tblPrenotazioni WHERE ID = %1\$s AND IDEvento = %2\$s;";
	$sql = sprintf($sql, $ID, $IDEventoCorrente);
	$result = mysql_query($sql);
	if (mysql_errno() <> 0) {
		throw new Exception("DeletePrenotazioni: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

// Classi 
function GetClassi() {
	$sql = "SELECT * FROM tblclassi";
	$result = mysql_query($sql);
	switch (mysql_errno()) {
		case 0:
			break;
		default:
			throw new Exception("GetClassi: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
			exit();
	}  
	return $result;
}

// Gruppi
function GetGruppi() {
	$sql = "SELECT 0 AS IDGruppo, 'Tutti i gruppi' AS Gruppo UNION SELECT * FROM tblgruppi";
	$result = mysql_query($sql);
	switch (mysql_errno()) {
		case 0:
			break;
		default:
			throw new Exception("GetGruppi: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
			exit();
	}  
	return $result;
}


// funzioni di servizio
function sql_quote( $value )
{
    if( get_magic_quotes_gpc() )
    {
          $value = stripslashes( $value );
    }
    //check if this function exists
    if( function_exists( "mysql_real_escape_string" ) )
    {
          $value = mysql_real_escape_string( $value );
    }
    //for PHP version < 4.3.0 use addslashes
    else
    {
          $value = addslashes( $value );
    }
    return $value;
}
?>
