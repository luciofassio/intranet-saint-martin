<?php
require('accesso_db.inc');
ob_start (); 		// set buffer on
echo "1";
exit();
ConnettiDB();
// controllo di avere i dati in post
if(isset($_POST['IDSquadra']) && isset($_POST['IDEvento']) && isset($_POST['IDClasse'])) {
	// contro sql injection
	$IDSquadra = mysql_real_escape_string($_POST['IDSquadra']);
	$IDEvento = mysql_real_escape_string($_POST['IDEvento']);
	$IDClasse = mysql_real_escape_string($_POST['IDClasse']);
	//$IDSquadra = "74";
	//$IDEvento = "19";
	//$IDClasse = "8";
	if(strlen($IDSquadra) > 0 && strlen($IDEvento) > 0 && strlen($IDClasse) > 0) {
		$query = GetNumIscrittiSquadra($IDEvento, $IDSquadra, $IDClasse);
		while ($row = mysql_fetch_object($query)) {
 			echo $row->iscritti;
		}
	} 
} else {
	echo 'Questa pagina non deve essere acceduta direttamente.';
}
?>