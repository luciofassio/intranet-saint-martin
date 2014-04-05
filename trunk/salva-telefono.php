<?php
session_start();
require('accesso_db.inc');
ConnettiDB();

// legge i post inviati dal modulo rubrica
if (isset($_POST['hdnIDxTel'])) {
    $id_utente=$_POST['hdnIDxTel'];
    $prefisso_internazionale=$_POST['prefisso_intnaz'];
    $prefisso_nazionale=$_POST['prefisso_naz'];
    $numero_phone=$_POST['numero_phone'];
    $tipo_telefono=$_POST['tipo_phone'];
    $change_sms=$_POST['change_sms'];
    $change_tel=$_POST['change_tel'];
    $change_tipo_value=$_POST['change_tipo'];
    
    if (isset($_POST['chkSpedizioneSms'])){
        $sms=$_POST['chkSpedizioneSms'];
    } else {
        $sms=0;
    }
    
    if ($change_tel=="true") {
          // controlla lo status dell'abilitazione alla ricezione degli sms.
          // La pone a false su tutti i numeri di telefono per impedire che ci sia più di un numero abilitato)
          if ($change_sms=="true") {
            $query="UPDATE tblTelefoni SET telsms=0 WHERE id=".$id_utente;
            $result=mysqli_query($GLOBALS["___mysqli_ston"], $query);
          }
         
         // aggiorna il numero di telefono dell'utente selezionato
         if ($change_tipo_value==$tipo_telefono) { 
            $query="UPDATE tblTelefoni SET prefisso='".$prefisso_nazionale."', numero='".$numero_phone."', telsms=".$sms." WHERE id=".$id_utente." AND idtipotelefono=".$tipo_telefono;
        } else {
            $query="UPDATE tblTelefoni SET prefisso='".$prefisso_nazionale."', numero='".$numero_phone."', telsms=".$sms.", idtipotelefono=".$change_tipo_value." WHERE id=".$id_utente." AND idtipotelefono=".$tipo_telefono;
        }
        
        $result=mysqli_query($GLOBALS["___mysqli_ston"], $query);
        // controlla che l'aggiornamento sia avvenuto con successo
        if (mysqli_affected_rows($GLOBALS["___mysqli_ston"]) > 0) {
            echo ("<script type=\"text/javascript\">\n");
            echo ("alert(\"L\'aggiornamento del numero di telefono è avvenuto con successo.\");\n");
            echo ("history.back();\n");
            //echo ("window.open('anagrafica.php')");
            echo ("</script>\n");
        } else {
            echo ("<script type=\"text/javascript\">\n");
            echo ("alert(\"Attenzione! L\'aggiornamento del numero è fallito. Qualcosa è andato storto. Riprovare, se il problema persiste contattare gli amministratori del sistema.\");\n");
            echo ("history.back();\n");
            echo ("</script>\n");
        }
    } else {
    
    //controlla che il numero di telefono non sia già inserito in archivio
    $query="SELECT idtelefono, id, prefisso, numero, idtipotelefono,telsms FROM tblTelefoni WHERE id=".$id_utente." AND prefisso=".$prefisso_nazionale." AND numero=".$numero_phone;
    $result=mysqli_query($GLOBALS["___mysqli_ston"], $query);
    
    if (mysqli_num_rows($result)>0) {
      echo ("<script type=\"text/javascript\">\n");
      echo ("alert(\"Attenzione! Il numero che stai cercando di inserire e\' gia\' presente in rubrica.\");\n");
      echo ("history.back();\n");
      echo ("</script>\n");
    } else {
          // pone a false le abilitazioni agli sms di tutti i numeri di telefono dell'utente selezionato
          // per impedire che ci sia più di un numero abilitato)
          if ($change_sms=="true") {
            $query="UPDATE tblTelefoni SET telsms=0 WHERE id=".$id_utente;
            $result=mysqli_query($GLOBALS["___mysqli_ston"], $query);
          }
          // salva (finalmente) il numero di telefono in archivio
          $query="INSERT INTO tblTelefoni (id,prefisso,numero,idtipotelefono,telsms) VALUES (".$id_utente.",'".$prefisso_nazionale."','".$numero_phone."',".$tipo_telefono.",".$sms.")";
	        $result=mysqli_query($GLOBALS["___mysqli_ston"], $query);
	        if (mysqli_affected_rows($GLOBALS["___mysqli_ston"])>0) {
            /* echo ("<script type=\"text/javascript\">\n");
            echo ("alert(\"Il numero di telefono e\' stato inserito in rubrica.\");\n");
            echo ("history.back();\n");
            echo ("</script>\n"); */
          }
    }
  }
} else {
	  echo ("<script type=\"text/javascript\">\n");
    echo ("alert(\"L\'accesso a questa pagina non deve essere fatto direttamente.\");\n");
    echo ("</script>\n");
}
?>