<?php
session_start();
require('accesso_db.inc');
ConnettiDB();

// controllo di avere una stringa da cercare
if(isset($_POST['queryString'])) {
	// contro sql injection
  $queryString = mysql_real_escape_string($_POST['queryString']);
	if(strlen($queryString) >0) {
     $query =mysql_query("select IdParrocchia, Parrocchia FROM tblparrocchie WHERE Parrocchia like '".$queryString."%' ORDER BY Parrocchia LIMIT 5");
		if($query) {
      while ($row = mysql_fetch_object($query)) {
              echo '<li onClick="fill_parrocchie(\''.htmlentities($row->IdParrocchia).'|'.htmlentities($row->Parrocchia).'\');">'.htmlentities($row->Parrocchia).'</li>';
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