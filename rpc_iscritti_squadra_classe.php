<?php
require('accesso_db.inc');
ob_start (); 		// set buffer on
echo "1";
exit();
ConnettiDB();
// controllo di avere i dati in post
if(isset($_POST['IDSquadra']) && isset($_POST['IDEvento']) && isset($_POST['IDClasse'])) {
	// contro sql injection
	$IDSquadra = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['IDSquadra']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
	$IDEvento = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['IDEvento']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
	$IDClasse = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['IDClasse']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
	//$IDSquadra = "74";
	//$IDEvento = "19";
	//$IDClasse = "8";
	if(strlen($IDSquadra) > 0 && strlen($IDEvento) > 0 && strlen($IDClasse) > 0) {
			try {
				$query = GetNumIscrittiSquadra($IDEvento, $IDSquadra, $IDClasse);
			}
			catch (Exception $e) {
				echo($e->getMessage());				exit();
			}
		while ($row = mysqli_fetch_object($query)) {
 			echo $row->iscritti;
		}
	} 
} else {
	echo 'Questa pagina non deve essere acceduta direttamente.';
}
?>