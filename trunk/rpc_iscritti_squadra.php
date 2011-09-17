<?php
require('accesso_db.inc');
ob_start (); 		// set buffer on
ConnettiDB();
// controllo di avere una stringa da cercare
if(isset($_POST['IDSquadra']) && isset($_POST['IDEvento'])) {
	// contro sql injection
	$IDSquadra = mysql_real_escape_string($_POST['IDSquadra']);
	$IDEvento = mysql_real_escape_string($_POST['IDEvento']);
	//$IDSquadra = mysql_real_escape_string($_GET['IDSquadra']);
	//$IDEvento = mysql_real_escape_string($_GET['IDEvento']);
	if(strlen($IDSquadra) > 0 && strlen($IDEvento) > 0) {
		$sql = "SELECT Catechismi.ID, Cognome, Nome FROM tblIscrizioni INNER JOIN Catechismi ON tblIscrizioni.ID = Catechismi.ID WHERE tblIscrizioni.IDSquadra = %1\$s AND tblIscrizioni.IdEvento= %2\$s ORDER BY Cognome, Nome;";
		$sql = sprintf($sql, $IDSquadra, $IDEvento);
		$query = mysql_query($sql);
		if (mysql_errno() == 0) {
			if (! mysql_num_rows($query)) {
				echo "0|";	
			} else {
				echo mysql_num_rows($query)."|";
				while ($row = mysql_fetch_object($query)) {
					echo "<option value=".$row->ID." class=\"listaiscritti\">".htmlentities($row->Cognome)." ".htmlentities($row->Nome)."</option>";
				}
			}
		} else {
			echo("rpc_iscritti_squadra: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
			exit();
		}  
	} // nessun dato in input
} else {
	echo 'Questa pagina non deve essere acceduta direttamente.';
}
?>