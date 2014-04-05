<?php
require('accesso_db.inc');
ob_start (); 		// set buffer on
ConnettiDB();
// controllo di avere una stringa da cercare
if(isset($_POST['IDSquadra']) && isset($_POST['IDEvento'])) {
	// contro sql injection
	$IDEvento = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['IDEvento']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
	$IDSquadra = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['IDSquadra']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
	//$IDSquadra = mysql_real_escape_string($_GET['IDSquadra']);
	//$IDEvento = mysql_real_escape_string($_GET['IDEvento']);
	// mando il  numero totale degli iscritti e i nominativi
	if(strlen($IDSquadra) > 0 && strlen($IDEvento) > 0) {
		$sql = "SELECT Catechismi.ID, Cognome, Nome, SiglaRuolo FROM tblIscrizioni 
		        INNER JOIN Catechismi ON tblIscrizioni.ID = Catechismi.ID 
                INNER JOIN tblruolier ON tbliscrizioni.IDRuolo = tblruolier.IDRuolo
				WHERE tblIscrizioni.IdEvento= %1\$s AND tblIscrizioni.IDSquadra = %2\$s 
				ORDER BY Cognome, Nome;";
		$sql = sprintf($sql, $IDEvento, $IDSquadra);
		$query = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) == 0) {
			if (! mysqli_num_rows($query)) {
				echo "0|";	
			} else {
				echo mysqli_num_rows($query)."|";
				while ($row = mysqli_fetch_object($query)) {
					$nome = htmlentities($row->Cognome)." ".htmlentities($row->Nome);
					$nome .= str_pad($row->SiglaRuolo,29 - strlen($nome),".",STR_PAD_LEFT);
					$nome = str_replace(".", "&nbsp;", $nome);
					//echo "<option value=".$row->ID." class=\"listaiscritti\">".$nome."</option>";
					echo "<option value=".$row->ID." >".$nome."</option>";
				}
			}
		} else {
			echo("rpc_iscritti_squadra: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
			exit();
		}  
		// adesso mando il numero degli iscritti suddivisi per ruolo
		$sql = "SELECT tblruolier.Ruolo, COUNT(tbliscrizioni.IDRuolo) AS NumIscritti
                FROM saint_martin_db.tbliscrizioni tbliscrizioni
                INNER JOIN tblruolier ON tbliscrizioni.IDRuolo = tblruolier.IDRuolo
                WHERE tbliscrizioni.IDEvento = %1\$s AND tbliscrizioni.IDSquadra = %2\$s
                GROUP BY tbliscrizioni.IDRuolo";
		$sql = sprintf($sql, $IDEvento, $IDSquadra);
		$query = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) == 0) {
				while ($row = mysqli_fetch_object($query)) {
					$ruoli .= $row->Ruolo.":<br/>";
					$iscritti .= $row->NumIscritti."<br/>";
				}
				echo "|".$ruoli."|".$iscritti;
		} else {
			echo("rpc_iscritti_squadra: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
			exit();
		}  
	} // nessun dato in input
} else {
	echo 'Questa pagina non deve essere acceduta direttamente.';
}
?>