<?php
require('accesso_db.inc');
ob_start (); 		// set buffer on
ConnettiDB();
// controllo di avere una stringa da cercare
if(isset($_POST['IDSquadra']) && isset($_POST['IDEvento'])) {
	// contro sql injection
	$IDEvento = mysql_real_escape_string($_POST['IDEvento']);
	$IDSquadra = mysql_real_escape_string($_POST['IDSquadra']);
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
		$query = mysql_query($sql);
		if (mysql_errno() == 0) {
			if (! mysql_num_rows($query)) {
				echo "0|";	
			} else {
				echo mysql_num_rows($query)."|";
				while ($row = mysql_fetch_object($query)) {
					$nome = htmlentities($row->Cognome)." ".htmlentities($row->Nome);
					$nome .= str_pad($row->SiglaRuolo,29 - strlen($nome),".",STR_PAD_LEFT);
					$nome = str_replace(".", "&nbsp;", $nome);
					//echo "<option value=".$row->ID." class=\"listaiscritti\">".$nome."</option>";
					echo "<option value=".$row->ID." >".$nome."</option>";
				}
			}
		} else {
			echo("rpc_iscritti_squadra: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
			exit();
		}  
		// adesso mando il numero degli iscritti suddivisi per ruolo
		$sql = "SELECT tblruolier.Ruolo, COUNT(tbliscrizioni.IDRuolo) AS NumIscritti
                FROM saint_martin_db.tbliscrizioni tbliscrizioni
                INNER JOIN tblruolier ON tbliscrizioni.IDRuolo = tblruolier.IDRuolo
                WHERE tbliscrizioni.IDEvento = %1\$s AND tbliscrizioni.IDSquadra = %2\$s
                GROUP BY tbliscrizioni.IDRuolo";
		$sql = sprintf($sql, $IDEvento, $IDSquadra);
		$query = mysql_query($sql);
		if (mysql_errno() == 0) {
				while ($row = mysql_fetch_object($query)) {
					$ruoli .= $row->Ruolo.":<br/>";
					$iscritti .= $row->NumIscritti."<br/>";
				}
				echo "|".$ruoli."|".$iscritti;
		} else {
			echo("rpc_iscritti_squadra: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
			exit();
		}  
	} // nessun dato in input
} else {
	echo 'Questa pagina non deve essere acceduta direttamente.';
}
?>