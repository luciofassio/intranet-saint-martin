<?php
require('accesso_db.inc');
require('business_layer.inc');
ConnettiDB();
// controllo di avere una stringa da cercare
// il post viene fatto da Jquery
if(isset($_POST['IDRuolo']) && isset($_POST['IDEvento'])) {
	// contro sql injection
	$IDRuolo = mysql_real_escape_string($_POST['IDRuolo']);
	$IDEvento = mysql_real_escape_string($_POST['IDEvento']);
	if (strlen($IDRuolo) > 0 && is_numeric($IDRuolo) && strlen($IDEvento) > 0 && is_numeric($IDEvento)) {
		echo CaricaListino($IDRuolo, $IDEvento);
	} else {
		echo "Errore: il ruolo o l'evento non sono presenti o non sono numerici";
	}
} else {
	echo "Errore: il ruolo o l'evento non sono presenti";
}
?>