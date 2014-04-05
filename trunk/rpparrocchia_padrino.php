<?php
session_start();
require('accesso_db.inc');
ConnettiDB();

// controllo di avere una stringa da cercare
if(isset($_POST['queryString'])) {
	// contro sql injection
  $queryString = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['queryString']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
	if(strlen($queryString) >0) {
     $query =mysqli_query($GLOBALS["___mysqli_ston"], "select IdParrocchia, Parrocchia FROM tblparrocchie WHERE Parrocchia like '%".$queryString."%' ORDER BY Parrocchia LIMIT 15");
		if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
			echo("rpc_parrocchie: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
			exit();
		}  
		if($query) {
      while ($row = mysqli_fetch_object($query)) {
              echo '<li onClick="fill_parrocchia_padrino(\''.htmlentities($row->IdParrocchia).'|'.(htmlentities($row->Parrocchia)).'\');">'.stripslashes(htmlentities($row->Parrocchia)).'</li>';
      }
		} else {
      echo ("ERRORE: la ricerca nel database non ha dato risultati.");
		}
	} // There is a queryString.
 } else {
	 	echo ("<script type=\"text/javascript\">\n");
    echo ("alert(\"L\'accesso a questa pagina non deve essere fatto direttamente.\");\n");
    echo ("</script>\n");
 }
?>