<?php
session_start();
require('accesso_db.inc');
ConnettiDB();

// controllo di avere una stringa da cercare
if(isset($_POST['queryString'])) {
	// contro sql injection
  $queryString = mysql_real_escape_string($_POST['queryString']);
	if(strlen($queryString) >0) {
     $query =mysql_query("select idcomuni, comune, codicepostale, provincia from tblcomuni where comune like '".$queryString."%' ORDER BY comune  LIMIT 11");
		if($query) {
      while ($row = mysql_fetch_object($query)) {
              echo '<input type"hidden" id="mytown" value="'.htmlentities($row->idcomuni).'|'.htmlentities($row->comune).'|'.htmlentities($row->codicepostale).'|'.htmlentities($row->provincia).'\');">';
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
