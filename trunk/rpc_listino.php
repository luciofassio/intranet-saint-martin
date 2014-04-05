<?php
require('accesso_db.inc');
require('business_layer.inc');
ConnettiDB();
// controllo di avere una stringa da cercare
// il post viene fatto da Jquery
if(isset($_POST['IDRuolo']) && isset($_POST['IDEvento'])) {
	// contro sql injection
	$IDRuolo = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['IDRuolo']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
	$IDEvento = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['IDEvento']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
	if (strlen($IDRuolo) > 0 && is_numeric($IDRuolo) && strlen($IDEvento) > 0 && is_numeric($IDEvento)) {
		echo CaricaListino($IDRuolo, $IDEvento);
	} else {
		echo "Errore: il ruolo o l'evento non sono presenti o non sono numerici";
	}
} else {
	echo "Errore: il ruolo o l'evento non sono presenti";
}
?>