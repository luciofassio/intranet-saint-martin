<?php
require('accesso_db.inc');
ob_start (); 		// set buffer on
ConnettiDB();
// controllo di avere una stringa da cercare
if(isset($_POST['IDSquadra']) && isset($_POST['IDEvento'])) {
	// contro sql injection
	$IDSquadra = mysql_real_escape_string($_POST['IDSquadra']);
	$IDEvento = mysql_real_escape_string($_POST['IDEvento']);
	if(strlen($IDSquadra) > 0 && strlen($IDEvento) > 0) {
		$query = mysql_query("SELECT Catechismi.ID, Cognome, Nome FROM tblIscrizioni INNER JOIN Catechismi ON tblIscrizioni.ID = Catechismi.ID WHERE tblIscrizioni.IDSquadra = ".$IDSquadra." AND tblIscrizioni.IdEvento=".$IDEvento." ORDER BY Cognome, Nome;");
		if (mysql_errno() == 0) {
			while ($row = mysql_fetch_object($query)) {
    			echo "<option value=".$row->ID." class=\"listaiscritti\">".htmlentities($row->Cognome)." ".htmlentities($row->Nome)."</option>";
			}
		} else {
			throw new Exception(mysql_errno().":".mysql_error());
			exit();
		}
	} // There is a queryString.
} else {
	echo 'Questa pagina non deve essere acceduta direttamente.';
}
?>