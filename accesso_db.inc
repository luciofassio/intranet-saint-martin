<?php
// accesso al db
function ConnettiDB()
{
	require('saint_martin_db.inc');
	$link = ($GLOBALS["___mysqli_ston"] = mysqli_connect($dbserver,  $dbuser,  $dbpassword));
	if (!$link) {
    	die('Errore di connessione: '.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
	}
	((bool)mysqli_query( $link, "USE $dbname")) 
		or die("Errore di apertura db: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
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
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetSquadre: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

function GetSquadraByID($ID, $IDEvento) {
	// ID		l'id dell'iscritto
	// ottiene il nome della squadra nel quale è iscritta la persona
	$sql = "SELECT NomeSquadra FROM tblIscrizioni INNER JOIN tblSquadre ON tblIscrizioni.IDSquadra = tblSquadre.IDSquadra WHERE ID=%1\$s AND tblIscrizioni.IDEvento=%2\$s";
	$sql = sprintf($sql, $ID, $IDEvento);
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetSquadraByID: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

function GetIscrittiSquadra($IDEvento,$IDSquadra) {
// ottiene l'elenco degli iscritti alla squadra indicata per l'evento indicato
	$sql = "SELECT Catechismi.ID, Cognome, Nome, tblClassi.Sigla FROM tblIscrizioni INNER JOIN Catechismi ON tblIscrizioni.ID = Catechismi.ID INNER JOIN tblClassi ON tblClassi.IDClasse=Catechismi.Classe WHERE tblIscrizioni.IDSquadra = %2\$s AND tblIscrizioni.IdEvento = %1\$s ORDER BY Cognome, Nome;";
	$sql = sprintf($sql, $IDEvento, $IDSquadra);
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetIscrittiSquadra: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
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
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetNumIscrittiSquadra: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
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
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("InsertIscrizioneSquadra: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
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
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("UpdateIscrizioneSquadra: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
	return $result;
}

// funzioni intranet
function GetApplicazioni($IDOperatore,$menu_padre)
{
  $result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM tblOperatoriXFunzioni inner join tblFunzioni on (tblOperatoriXFunzioni.id_funzione =  tblFunzioni.id_funzione) where id_operatore='".$IDOperatore."' AND menu_padre=".$menu_padre." ORDER BY sequenza");
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetApplicazioni: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

function GetUtente($login, $password)
{
	$result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT idoperatore, livello FROM tblOperatori WHERE login='".sql_quote($login)."' AND password='".sql_quote($password)."' AND attivo=1" );
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetUtente: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

function GetOperatore($IDOperatore)
{
	$result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT Cognome, Nome, login FROM Catechismi INNER JOIN tblOperatori ON ID=idoperatore WHERE ID=".$IDOperatore);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetOperatore: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

// angrafica catechismo
function GetCatechismi() {
	$result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT Nome, Data_di_nascita FROM Catechismi");
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetCatechismi: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

// fornisce l'ID della persona a partire dal bar code
function GetIDPersonaByBarCode($BarCode) {
	$sql = "SELECT ID FROM Catechismi WHERE ID='%1\$s'";
	$sql = sprintf($sql, (string)(int)$BarCode);
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetIDPersonaByBarCode: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
    if ((mysqli_num_rows($result) > 0)) {
		$rowID = mysqli_fetch_object($result);
		return $rowID->ID; 
	} else {
		return "";	
	}
}

// tutti i dati della persona
function GetPersona($ID) {
	$result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM Catechismi WHERE ID=".$ID);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetPersona: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

// persona con per disclaimer privacy
function GetPersonaPrivacy($ID) {
	$sql = "SELECT Cognome, Nome, Tipo_via, Via, numero_civico, CAP, Citt, Provincia,email,Sesso,Data_di_nascita,Luogo_di_nascita,Catechismi.Classe AS IndiceClasse,tblClassi.Classe,tblSezioni.Sezione,Parrocchia_Provenienza,Prefisso,Numero,TelSMS FROM ((Catechismi LEFT OUTER JOIN tblTelefoni ON (Catechismi.ID = tblTelefoni.ID)) LEFT OUTER JOIN tblClassi ON (Catechismi.Classe = tblClassi.IDClasse)) LEFT OUTER JOIN tblSezioni ON (Catechismi.Sezione = tblSezioni.IDSezione) WHERE (tblTelefoni.IDTipoTelefono IN (1,2,3,4,5,6,7,null) OR tblTelefoni.IDTipoTelefono is null) AND Catechismi.ID = %1\$s ORDER BY tblTelefoni.IDTipoTelefono";
	$sql = sprintf($sql, $ID);
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetPersonaPrivacy: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

// parrocchia di provenienza per disclaimer privacy
function GetParrocchiaProvenienza($parrocchia_provenienza) {
	if ($parrocchia_provenienza=="" || $parrocchia_provenienza==null) {$parrocchia_provenienza=0;}
  $sql = "SELECT IdParrocchia,Parrocchia FROM tblparrocchie WHERE IdParrocchia=%1\$s ORDER BY Parrocchia";
	$sql = sprintf($sql, $parrocchia_provenienza);
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	
  if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetParrocchiaProvenienza: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
    $row=mysqli_fetch_object($result);
	  $parrocchia_provenienza=htmlentities($row->Parrocchia);
    return $parrocchia_provenienza;
}

// numero di cellulare personale di una persona
function GetCellularePersonaleID($ID,$idTipoTelefono) {
	$sql = "SELECT Prefisso, Numero FROM tblTelefoni t WHERE IDTipoTelefono = %1\$s AND ID = %2\$s";
	$sql = sprintf($sql, $idTipoTelefono, $ID);
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetCellularePersonaleID: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."\n\r".$sql);
		exit();
	}  
    return $result;
}

// numero di cellulare per SMS di una persona
function GetCellulareSMSByID($ID) {
	$sql = "SELECT Prefisso, Numero FROM tblTelefoni t WHERE TelSMS = 1 AND ID = %1\$s";
	$sql = sprintf($sql, $ID);
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetCellulareSMSByID: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."\n\r".$sql);
		exit();
	}  
    return $result;
}

// Ruoli
function GetRuoli()
{
	$result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT 0 AS IDRuolo, 'Seleziona ruolo' AS Ruolo UNION SELECT IDRuolo, Ruolo FROM tblRuoliER ORDER BY IDRuolo");
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetRuoli: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
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
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetListino: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
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
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetListinoByRuoloER: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

// duplica un listino esistente
function DuplicateListino($IDEventoOrigine, $IDEventoDestinazione)
{
	$sql = "insert into tbllistino (IDEvento,IDRuolo,Iscrizione,Pranzo,Cena,CenaFinale,AbbonamentoPranzo,AbbonamentoCena,AbbonamentoPranzoCena,EventoSpecialeER) select %2\$s,IDRuolo,Iscrizione,Pranzo,Cena,CenaFinale,AbbonamentoPranzo,AbbonamentoCena,AbbonamentoPranzoCena,EventoSpecialeER from tbllistino where idevento=%1\$s";
	$sql = sprintf($sql, $IDEventoOrigine, $IDEventoDestinazione);
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("DuplicateListino: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}


// Eventi
// restituisce tutti gli eventi
function GetEventi() {
	$result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM tblEventi ORDER BY Data DESC LIMIT 8");
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetEventi: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

function GetEventoCorrente() {
	$result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM tblEventi WHERE IDEvento = (SELECT EventoCorrente FROM tblParametri)");
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetEventoCorrente: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

function GetEventoByID($IDEvento) {
	$sql = "SELECT * FROM tblEventi WHERE IDEvento = %1\$s";
	$sql = sprintf($sql, $IDEvento);
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetEventoByID: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

function GetIDEventoCorrente() {
	$result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT EventoCorrente FROM tblParametri");
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetIDEventoCorrente: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

function SetEventoCorrente($IDEvento) {
	$sql = "UPDATE tblParametri SET EventoCorrente = %1\$s";
	$sql = sprintf($sql, $IDEvento);
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("SetEventoCorrente: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
	return $result;
}

// legge la descriozione dell'evento per chiave
function GetNomeEventoByID($IDEvento) {
	$sql = "SELECT NomeEvento FROM tblEventi WHERE IDEvento=%1\$s";
	$sql = sprintf($sql, $IDEvento);
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetNomeEventoByID.1: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
	$row = mysqli_fetch_assoc($result);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetNomeEventoByID.2: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
    return $row["NomeEvento"];
}
	
// legge la descriozione dell'evento corrente
function GetNomeEventoCorrente()
{
	$result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT NomeEvento FROM tblEventi WHERE IDEvento = (SELECT EventoCorrente FROM tblParametri)");
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetNomeEventoCorrente.1: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
	$row = mysqli_fetch_assoc($result);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetNomeEventoCorrente.2: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
    return $row["NomeEvento"];
}

function InsertEvento($evento, $data_evento, $durata) {
	$sql = "INSERT tblEventi (NomeEvento, Data, Durata) VALUES('%1\$s', '%2\$s', %3\$s)";
	$sql = sprintf($sql, str_replace("'", "''", $evento), convert($data_evento, 'd/m/Y', 'Y-m-d'), $durata);
	//ob_clean();
	//echo $sql;
	//exit();
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("InsertEvento: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
	return $result;
}

// legge l'elenco degli eventi al quale la persona è iscritta
function GetEventiPartecipatiByID($ID) {
	$sql = "SELECT distinct IDEvento FROM tblPrenotazioni t where id=%1\$s";
	$sql = sprintf($sql, $ID);
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetEventiPartecipatiByID: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

// Iscrizioni
function GetIscrizioneER($ID, $IDEventoCorrente) {
	$sql = "SELECT * FROM tblIscrizioni WHERE ID=%1\$s AND IDEvento=%2\$s";
	$sql = sprintf($sql, $ID, $IDEventoCorrente);
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetIscrizioneER: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
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
	$sql = sprintf($sql, $ID, $IDEventoCorrente, $IDRuolo, ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $note) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")), $AbbonamentoPranzo, $AbbonamentoCena, str_replace(",", ".", $CostoTotaleEuro), $CenaFinale, str_replace(",", ".", $NrOspiti), $EventoSpecialeER, str_replace(",", ".", $Pagamento));
	//ob_clean();
	//echo $sql;
	//exit();
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("InsertIscrizioneER: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
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
	$sql = sprintf($sql, $IDRuolo, ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $note) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")), $IDIscr, $AbbonamentoPranzo, $AbbonamentoCena, str_replace(",", ".", $CostoTotaleEuro), $CenaFinale, str_replace(",", ".", $NrOspiti), $EventoSpecialeER, str_replace(",", ".", $Pagamento));
	//ob_clean();
	//echo "Pranzo: ".$AbbonamentoPranzo."<br/>";
	//echo $sql;
	//exit;
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("UpdateIscrizioneER: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
	return $result;
}

function DeleteIscrizioneER($IDIscr){
	$sql = "DELETE FROM tblIscrizioni WHERE IDIscrizione=%1\$s";
	$sql = sprintf($sql, $IDIscr);
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("DeleteIscrizioneER: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
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
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	switch (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false))) {
		case 0:
			break;
		default:
			throw new Exception("GetPrenotazione: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
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
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	switch (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false))) {
		case 1062:   // chiave duplicata: aggiorno il record
			$sql = "UPDATE tblPrenotazioni SET Mattina=%4\$s, Pomeriggio=%5\$s, Sera=%6\$s, Pranzo=%7\$s, Cena=%8\$s, PranzoQuota=%9\$s, CenaQuota=%10\$s, PranzoGratis=%11\$s, CenaGratis=%12\$s WHERE ID=%1\$s AND IDEvento=%2\$s AND Data='%3\$s'";
			$sql = sprintf($sql, $ID, $IDEventoCorrente, convert($Data, 'd/m/Y', 'Y-m-d'), $Mattina, $Pomeriggio, $Sera, $Pranzo, $Cena, str_replace(",", ".", $PranzoQuota), str_replace(",", ".", $CenaQuota), $PranzoGratis, $CenaGratis);
			$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
				throw new Exception("InsertPrenotazione.1: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
				exit();
			}  
			break;
		case 0:
			break;
		default:
			throw new Exception("InsertPrenotazione.2: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
			exit();
	}  
	return $result;
}

// elimina la prenotazione di un utenete a una certa data
function DeletePrenotazione($ID, $IDEventoCorrente, $Data)
{
	$sql = "DELETE FROM tblPrenotazioni WHERE ID = %1\$s AND IDEvento = %2\$s; AND Data = '%3\$s'";
	$sql = sprintf($sql, $ID, $IDEventoCorrente, convert($Data, 'd/m/Y', 'Y-m-d'));
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("DeletePrenotazione: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

// elimina tutte le prenotazioni di un utente
function DeletePrenotazioni($ID, $IDEventoCorrente) {
	$sql = "DELETE FROM tblPrenotazioni WHERE ID = %1\$s AND IDEvento = %2\$s;";
	$sql = sprintf($sql, $ID, $IDEventoCorrente);
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("DeletePrenotazioni: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
		exit();
	}  
    return $result;
}

// Classi 
function GetClassi() {
	$sql = "SELECT * FROM tblclassi";
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	switch (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false))) {
		case 0:
			break;
		default:
			throw new Exception("GetClassi: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
			exit();
	}  
	return $result;
}

// Gruppi
function GetGruppi() {
	$sql = "SELECT 0 AS IDGruppo, 'Tutti i gruppi' AS Gruppo UNION SELECT * FROM tblgruppi";
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	switch (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false))) {
		case 0:
			break;
		default:
			throw new Exception("GetGruppi: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
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
    if( function_exists( "mysqli_real_escape_string" ) )
    {
          $value = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $value ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
    }
    //for PHP version < 4.3.0 use addslashes
    else
    {
          $value = addslashes( $value );
    }
    return $value;
}
?>
