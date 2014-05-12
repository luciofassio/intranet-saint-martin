<?php
require('accesso_db.inc');
require ('bar128.php');							// Our Library of Barcode Functions

ob_clear;
ConnettiDB();
$id = $_GET["id"];
if($id == "") {
	echo "<strong>Deve essere selezionata un'iscrizione per stampare la ricevuta</trong>";
	exit();
}
$idevento = $_GET["idevento"];
if($idevento == "") {
	echo "<strong>Deve essere selezionato un evento per stampare la ricevuta</trong>";
	exit();
}
?>

<html>
<head>
<title>Ricevuta pagamento iscrizione</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" >

<style>

/* REGOLE 'SELETTORI' */
body {
	font-family: arial;
	font-size: 12pt;
	line-height: 0.69em;
}

h1 {
	font-size:x-large;
	font-weight: bold;
	text-align:center;
}

h2 {
	font-size:medium;
	text-align:center;
}

h3 {
   margin-bottom: -1px;
   text-align: center;
}

img {
  vertical-align: middle;
}

li {
	margin-top: 0.5em;	
	margin-bottom: 0.5em;	
}

p {
	margin-top: 0.5em;	
	margin-bottom: 0.5em;	
}

table {
	font-family: arial;
	font-size: 12pt;
	line-height: 1em;	
	width:100%;
}

ul {
	line-height: 1.15em;
	text-align: justify; 
}

/* REGOLE PER ID OGGETTI */

#contenutopagina {
	width: 100%;
	height: 810%;
	text-align: left;
	padding:0px 0px 0px 0px;
	margin: 0 auto;
}

#intestazione_pagina {
  text-align: left;
  padding-bottom: 7px;
  padding-top: 30px;
}

#mybarcode {
  position: relative;
  top: -120px;
  float: right;
}

#pagina {
	text-align: left;
}


.tabella_anagrafica {
  border: 1px dotted grey;
  border-spacing: 0.5em 1em;
}

</style>
</head>
<body>

<?php
$oggi = getdate();
$anno =  $oggi["year"];

$rstPersona = GetPersonaPrivacy($id);
if($rstPersona) {
	if(mysqli_num_rows($rstPersona) > 0) {
		$rowPersona = mysqli_fetch_object($rstPersona);
	}
}

$evento = GetNomeEventoByID($idevento);

$rstIscrizione = GetIscrizioneER($id, $idevento);
if($rstIscrizione) {
	if(mysqli_num_rows($rstIscrizione) > 0) {
		$rowIscrizione = mysqli_fetch_object($rstIscrizione);
?>

<div id="pagina">

    <div id="contenutopagina">
        
        <div id="intestazione_pagina">	
            <p style="font-size:large"><img src="./Immagini/logoratorio.png" id="logo_oratorio" width ="40" height="40" alt="logo oratorio" />
            <strong>Oratorio Saint Martin - Viale Europa, 1 - 11100 Aosta - Tel. 0165/554234 </strong></p>
	    </div>
        <h2>Ricevuta di pagamento iscrizione </h2>
        
        <div id="mybarcode">
            <?php 
                echo bar128(str_pad($id,13,"0",STR_PAD_LEFT ),140);
              ?> 
        </div>
        
        <div id="anagrafica">
        <table class="tabella_anagrafica" border="0">
            <tr>
		        <td width="10%">Nominativo:</td><td width="23%"><strong><?php echo $rowPersona->Cognome." ".$rowPersona->Nome; ?></strong></td>
                <td width="10%">Evento:</td><td width="23%"><strong><?php echo $evento ?></strong></td>
		        <td width="10%">Cena finale:</td><td width="23%"><strong><?php echo $rowIscrizione->CenaFinale ? "s&iacute;" : "no" ?></strong></td>
		    </tr>
            <tr>
		        <td width="10%">Indirizzo:</td><td width="23%"><strong><?php echo $rowPersona->Tipo_via." ".$rowPersona->Via." ".$rowPersona->numero_civico."<br/>".$rowPersona->CAP." ".$rowPersona->Citt?></strong></td>
                <td width="10%">Note:</td><td width="23%"><strong><?php echo $rowIscrizione->Note ?></strong></td>
		        <td width="10%">Ospiti:</td><td width="23%"><strong><?php echo $rowIscrizione->CenaFinaleOspiti > 0 ? $rowIscrizione->CenaFinaleOspiti : "nessuno" ?></strong></td>
                </tr>
	        <tr>
                <td width="10%">Data di nascita:</td><td width="23%"><strong><?php echo date_format(date_create($rowPersona->Data_di_nascita), "d/m/Y") ?></strong></td>
                <td width="10%">Importo:</td><td width="23%"><strong><?php echo "&euro; ".number_format($rowIscrizione->Pagamento, 2, ',', '.') ?></strong></td>
		        <td width="10%">Evento speciale:</td><td width="23%"><strong><?php echo $rowIscrizione->EventoSpeciale ? "s&iacute;" : "no" ?></strong></td>
                </tr>
		</table>
      <div style="float:left;margin-top:1em">
          <strong>Data:</strong> &nbsp; 
                <?php 
                    echo date("d/m/Y");
                ?>
      </div>
      
<?php
	}
}

function GetTesseratiByID($ID)
{
	$sql = "SELECT ID FROM Catechismi WHERE ID=%1\$s AND Cancellato=false";
	$sql = sprintf($sql, $ID);
	
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetTesseratiByID: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		exit();
	}  
    return $result;
}
?>
</body>
<!-- script type="text/javascript">
	window.print();
</script -->
</html>