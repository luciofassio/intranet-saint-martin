<?php
session_start();
require('accesso_db.inc');
ConnettiDB();

// legge i post inviati dal modulo rubrica
if (isset($_POST['hdnIDxTel'])) {
    $id_utente=$_POST['hdnIDxTel'];
    $tipo_telefono=$_POST['tipo_phone'];
    
    $query="DELETE FROM tblTelefoni WHERE id=".$id_utente." AND idtipotelefono=".$tipo_telefono;
    $result=mysqli_query($GLOBALS["___mysqli_ston"], $query);
    if (mysqli_affected_rows($GLOBALS["___mysqli_ston"])>0) {
        echo ("<script type=\"text/javascript\">\n");
        echo ("alert(\"Il numero di telefono e\' stato cancellato dalla rubrica.\");\n");
        echo ("history.back();\n");
        echo ("</script>\n");
    }
} else {
	  echo ("<script type=\"text/javascript\">\n");
    echo ("alert(\"L\'accesso a questa pagina non deve essere fatto direttamente.\");\n");
    echo ("</script>\n");
}
?>