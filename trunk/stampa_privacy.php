<?php
require('accesso_db.inc');
require ('bar128.php');							// Our Library of Barcode Functions

ob_clear;
$prezzo_iscrizione = "15,00 €";
if ($_GET["id"] != "") {
	ConnettiDB();
	$rstPersona = GetPersonaPrivacy($_GET["id"]);
	if($rstPersona) {
		if(mysql_num_rows($rstPersona) > 0) {
			$row = mysql_fetch_object($rstPersona);
			$nome = htmlentities($row->Nome);
			$cognome = htmlentities($row->Cognome);
			$indirizzo = htmlentities($row->Tipo_via)." ".htmlentities($row->Via).", ".htmlentities($row->numero_civico)." ".htmlentities($row->CAP)." ".htmlentities($row->Citt)." (".htmlentities($row->Provincia).")";
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
	$rstCellulareSMS = GetCellulareSMSByID($_GET["id"]);
	if($rstCellulareSMS) {
		if(mysql_num_rows($rstCellulareSMS) > 0) {
			$row = mysql_fetch_object($rstCellulareSMS);
			$cellulare = htmlentities($row->Prefisso."/".$row->Numero);
		}
	}
}
?>
<html>
<head>
<title>Trattamento dei dati personali</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
<style>
body {
	font-family: arial;
	font-size: 9pt;
	line-height: 0.95em;	
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
	line-height: 0.95em;	
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
	line-height: 0.95em;	
	width:100%;
	border:0px;
}
</style>
</head>
<body>
	<div id="pagina">
		<div id="contenutopagina">
			<h2>Oratorio "Saint Martin" Viale Europa, 1 Aosta Tel. 0165 55 42 34</h2>
			<h1>Tesseramento 2009-2010</h1>
			<hr/>
			<table>
			<tr rowspan="2">
				<td>Cognome:</td><td><?php echo $cognome ?></td><td align="right" colspan="2" rowspan="3"><?php echo bar128("1234567847"); ?></td>
			</tr>
			<tr>
				<td>Nome:</td><td><?php echo $nome ?></td>
			</tr>
			<tr>
				<td>Indirizzo:</td><td><?php echo $indirizzo ?></td>
			</tr>
			<tr>
				<td>Telefono di casa:</td><td><?php echo $telefono_casa ?></td><td>&nbsp;</td><td>&nbsp;</td>
			</tr>
			<tr>
				<td>E-mail:</td><td><?php echo $email ?></td><td>Sesso:</td><td><?php echo $sesso ?></td>
			</tr>
			<tr>
				<td>Data di nascita:</td><td><?php echo $data_nascita ?></td><td>Luogo di nascita:</td><td><?php echo $luogo_nascita ?></td>
			</tr>
			<tr>
				<td>Classe a scuola:</td><td><?php echo $classe."/".$sezione ?></td><td>&nbsp;</td><td>&nbsp;</td>
			</tr>
			<tr>
				<td>Parrocchia di provenienza:</td><td><?php echo $parrocchia ?></td><td>&nbsp;</td><td>&nbsp;</td>
			</tr>
			<tr>
				<td>Desidero essere informato via<br/>SMS delle iniziative dell'oratorio<br/>al seguente numero:</td><td><?php echo $cellulare ?></td><td>&nbsp;</td><td>&nbsp;</td>
			</tr>
			<tr>
				<td>Ho altri fratelli/sorelle iscritti?:</td><td>Sì - No</td><td>Verso la quota di €:</td><td><?php echo $prezzo_iscrizione ?></td>
			</tr>
			<tr>
				<td colspan="4">(*) La tessera dell'oratorio e la quota versata serviranno per la copertura assicurativa</td>
			</tr>
			</table>
			<hr/><br/>
			<p>Informativa</p>
			
			<p>In conformit&agrave; alla Legge
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
			<br/>
			<h2>Consenso</h2>
			
			<p>Premesso che - come
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
</body>
<script type="text/javascript">
	window.print();
</script>
</html>