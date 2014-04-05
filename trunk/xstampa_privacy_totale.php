<?php
require('accesso_db.inc');
require ('bar128.php');							// Our Library of Barcode Functions

ob_clear;
$prezzo_iscrizione = "";
ConnettiDB();
$rstTesserati = GetTesserati("2009");
?>

<html>
<head>
<title>Trattamento dei dati personali</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
<style>
body {
	font-family: arial;
	font-size: 9pt;
	line-height: 0.99em;	
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

ul {
	line-height: 1.15em;
	text-align: justify; 
}

li {
	margin-top: 0.5em;	
	margin-bottom: 0.5em;	
}

p {
	margin-top: 0.5em;	
	margin-bottom: 0.5em;	
}

#pagina {
	text-align: center;
	margin-top: 10px;
}

#contenutopagina {
	width: 90%;
	height: 85%;
	text-align: left;
	padding:0px 0px 0px 0px;
	margin: 0 auto;
}

table {
	font-family: arial;
	font-size: 10pt;
	line-height: 1em;	
	width:100%;
	border:0px;
}

.bordino {
	border:1px dotted grey;
}

.interlinea {
	text-align: justify;	
	line-height: 1.2em;
}

</style>
</head>
<body>

<?php
if($rstTesserati) {
	if(mysqli_num_rows($rstTesserati) > 0) {
		while ($rowTesserati = mysqli_fetch_object($rstTesserati)){
			$idPersona = $rowTesserati->ID;
			$rstPersona = GetPersonaPrivacy($idPersona);
			if($rstPersona) {
				if(mysqli_num_rows($rstPersona) > 0) {
					$row = mysqli_fetch_object($rstPersona);
					
					$nome = htmlentities($row->Nome);
					
					$cognome = htmlentities($row->Cognome);
					
					if (htmlentities($row->Tipo_via) =="" || htmlentities($row->Via) == "") {
						$indirizzo="";
					} else {
						$indirizzo = htmlentities($row->Tipo_via)." ".htmlentities($row->Via).", ".htmlentities($row->numero_civico)." - ".htmlentities($row->CAP)." ".htmlentities($row->Citt)." (".htmlentities($row->Provincia).")";
					}	
									
					$sesso = $row->Sesso;
					
					if (strval($row->Data_di_nascita) != "0000-00-00 00:00:00") {
						$data_nascita = htmlentities(date("d/m/Y", strtotime($row->Data_di_nascita)));
					} else {
						$data_nascita = "";
					}
					
					$luogo_nascita = htmlentities($row->Luogo_di_nascita);
					
					$email = htmlentities($row->{'email'});
					
					$classe = htmlentities($row->Classe);
					
					$sezione = htmlentities($row->Sezione); 
					
					$parrocchia = htmlentities($row->Parrocchia_del_Battesimo); 
					
					$telefono_casa = htmlentities($row->Prefisso."/".$row->Numero); 
				}
			}
			
			
			$rstCellularePersonaleID = GetCellularePersonaleID($idPersona);
			if($rstCellularePersonaleID) {
				if(mysqli_num_rows($rstCellularePersonaleID) > 0) {
					$row = mysqli_fetch_object($rstCellularePersonaleID);
					$cellulare_personale = htmlentities($row->Prefisso."/".$row->Numero);
				}
			}			
			
			
			$rstCellulareSMS = GetCellulareSMSByID($idPersona);
			if($rstCellulareSMS) {
				if(mysqli_num_rows($rstCellulareSMS) > 0) {
					$row = mysqli_fetch_object($rstCellulareSMS);
					$cellulare = htmlentities($row->Prefisso."/".$row->Numero);
				}
			}
?>
			
	<div id="pagina">
		<div id="contenutopagina">
			<h2>Oratorio "Saint Martin" Viale Europa, 1 Aosta Tel. 0165 55 42 34</h2>
			<hr/>
			<h1>Tesseramento 2009-2010</h1>
			<table>
			<tr rowspan="3">
				<td>Cognome: <strong><?php echo $cognome ?></strong></td>
				<td>Nome: <strong><?php echo $nome ?></strong></td>
				<td align="right" colspan="2" rowspan="3"><?php echo bar128(str_pad($rowTesserati->ID,13,"0",STR_PAD_LEFT ),140); ?></td>
			</tr>
			
			<tr>
				<td colspan="2">Indirizzo: <strong><?php echo $indirizzo ?></strong></td>
			</tr>
			<tr>
				<td colspan="2">Telefono di casa: <strong><?php echo $telefono_casa ?></strong></td>
			</tr>
			<tr>
				<td colspan="4">Numero di cellulare: <strong><?php echo $cellulare_personale ?></strong></td>
			</tr>
			<tr>				
				<td>E-mail: <strong><?php echo $email ?></strong></td>
				<td colspan="3">Sesso: <strong><?php echo $sesso ?></strong></td>
			</tr>
			<tr>
				<td>Data di nascita: <strong><?php echo $data_nascita ?></strong></td>
				<td colspan="3">Luogo di nascita: <strong><?php echo $luogo_nascita ?></strong></td>
			</tr>
			<tr>
				<td colspan="4">Classe a scuola: <strong><?php echo $classe."</strong> / Sez. Parrocchia: <strong>".$sezione."</strong>" ?></td>
			</tr>
			<tr>
				<td colspan="4">Parrocchia di provenienza: <strong><?php echo $parrocchia ?></strong></td>
			</tr>
			<tr>
				<td colspan="4">Desidero essere informato via SMS delle iniziative dell'oratorio al seguente numero: <strong><?php echo $cellulare ?></strong></td>
			</tr>
			<tr>
				<td class="bordino" colspan="2">Ho altri fratelli/sorelle iscritti?: &nbsp;<strong>Sì - No</strong>&nbsp;&nbsp;&nbsp;&nbsp;Nr.:_____<br /><br />Classe frequentata fratello/i: </td>
				<td align="right">Verso la quota di:</td>
				<td><ul style="list-style-type:square;"><li><strong>20 &euro;</strong></li><li><strong>8 &euro;</strong> (2° figlio)</li> <li><strong>Iscrizione gratuita</strong> <br />(dal 3° figlio)</li></ul> <?php echo $prezzo_iscrizione ?></td>
			</tr>
			<tr>
				<td colspan="4">(*) La tessera dell'oratorio e la quota versata serviranno per la copertura assicurativa</td>
			</tr>
			</table>
			<hr/><br/>
			<p><h2>Informativa</h2></p>
			
			<p class="interlinea">In conformit&agrave; alla Legge
			31/12/1996, nr. 675, riguardante la tutela delle persone e di altri
			soggetti rispetto al trattamento dei dati personali, si informa
			che:</p>
			
			<ul>
			<li>
			<p>I dati
			personali raccolti con la presente scheda di adesione verranno
			trattati per esclusive finalit&agrave;
			associative/gestionali, statistiche e promozionali, mediante
			elaborazione con criteri prefissati.</p>
			</li>
			<li>
			<p>L'acquisizione
			dei dati personali &egrave; presupposto indispensabile per
			l'instaurazione del contratto associativo e lo
			svolgimento dei rapporti cui la stessa acquisizione &egrave;
			finalizzata.</p>
			</li>
			<li>
			<p>I dati
			raccolti saranno comunicati per motivi associativi e assicurativi a
			NOI Associazione nazionale, regionale e territoriale,
			all'intermediario assicurativo e a eventuali
			associazioni ed enti con cui NOI Associazione
			stabilir&agrave; accordi e convenzioni.</p>
			</li>
			<li>
			<p>I dati
			raccolti non saranno mai, in nessun caso, comunicati, diffusi o
			messi a disposizione di enti diversi da quelli
			indicati.</p>
			</li>
			<li>
			<p>L'associazione
			e i suoi genitori hanno diritto a ottenere senza ritardo:
			</p>
			</li>
			<ol style="list-style-type:lower-latin">
				<li>
				<p>La
				conferma dell'esistenza dei dati personali che
				li riguardano, la comunicazione in forma intelligibile dei medesimi
				dati e della loro origine, nonch&egrave; della logica su cui
				si basa il trattamento.</p>
				</li>
				<li>
				<p>La
				cancellazione, la trasformazione in forma anonima, il blocco dei
				dati trattati in violazione della legge.</p>
				</li>
				<li>
				<p>L'aggiornamento e la rettifica o
				l'integrazione dei dati.</p>
				</li>
			</ol>
			<li><p>Titolare
			del trattamento &egrave; l'associazione
			evidenziata nell'intestazione di questa
			informativa, da cui si evince anche la sede.</p>
			</li>
			<li>
			<p>Responsabile del
			trattamento &egrave; la medesima associazione nella persona
			del suo presidente.</p>
			</li>
			</ul>
			
			<h2>Consenso</h2>
			
			<p class="interlinea">Premesso che - come
			rappresentato nell'informativa che mi
			&egrave; stata fornita ai sensi della Legge 675/1996, le
			operazioni di tesseramento richiedono il trattamento dei dati
			personali di mio/a figlio/a e la loro comunicazione a NOI
			Associazione nazionale, regionale e territoriale,
			all'intermediario assicurativo e a eventuali
			associazioni e enti con cui NOI Associazione stabilir&agrave;
			accordi e convenzioni - con la sottoscrizione
			del presente documento esprimo il mio consenso al trattamento e
			alle comunicazioni indicate dei dati raccolti con questa scheda di
			adesione.</p>
			<br/><br/>
			<div style="float:left">Data: <?php echo date("d/m/Y"); ?></div><div style="float:right">Firma del genitore: _________________________________</div>
		</div>
	</div>
	<p style="page-break-after: always;margin-top:300px" />
<?php
		}
	}
}

function GetTesserati($anno)
{
	$sql = "SELECT ID FROM Catechismi WHERE YEAR(DataTesseramento) = %1\$s AND Classe > 14 ORDER BY Classe,Cognome,Nome";
	$sql = sprintf($sql, $anno);
	
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetTesserati: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
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