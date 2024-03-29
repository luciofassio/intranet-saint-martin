<?php
require('accesso_db.inc');
require ('bar128.php');							// Our Library of Barcode Functions

ob_clear;
$id = $_GET["id"];
$prezzo_iscrizione = "";
$copie = 0;
ConnettiDB();
if(($_POST["anno"] == "" || $_POST["gruppi"] == "") && $id == "") {
	echo "<strong>Deve essere selezionato il gruppo e l'anno di abbonamento</strong>";
	exit();
}
if($_POST["copie"] != "" && !is_numeric($_POST["copie"])) {
	echo "<strong>Il numero delle copie deve essere numerico</strong>";
	exit();
} else {
	if($_POST["copie"] != "") {
		$copie = $_POST["copie"];
	}
}
if ($id <> "") {
	$_POST["anno"] = date("Y");
}
?>

<html>
<head>
<title>Trattamento dei dati personali</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" >

<style>

/* REGOLE 'SELETTORI' */
body {
	font-family: arial;
	font-size: 9pt;
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
	font-size: 10pt;
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
	height: 85%;
	text-align: left;
	padding:0px 0px 0px 0px;
	margin: 0 auto;
}

#intestazione_pagina {
	text-align: left;
  border-bottom: 3px dotted grey;
  padding-bottom: 7px;
  padding-top: 30px;
  /*background: #E0E0E0;
  -moz-border-radius: 14px; */
}

#mybarcode {
  position: relative;
  top: -120px;
  float: right;
}

#anagrafica{
  
}

#pagina {
	text-align: left;
}

/* REGOLE CLASSI */
.autorizzazioni {
  width: 100%;
  border-top: 1px dotted grey;
  border-bottom: 1px dotted grey;
}

.bordino {
	border:1px dotted grey;
}

.interlinea {
	font-size: 8pt;
  text-align: justify;	
	line-height: 1.2em;
}

table.autorizzazioni td {
  font-size: 8pt;
  text-align: justify;	
	line-height: 1.2em;
}

.quadrato{
  border:1px solid black;
  width: 20px;
  height: 10px;
}

td.larghezzacella {
  width: 90px;
}

.tabella_anagrafica {
  /*margin-top: -80px;*/
  border: 1px dotted grey;
  border-spacing: 0.5em 1em;
}

table.rubrica {
  width: 250px;
  margin-top: -10px;
  float: right;
  border: 1px dotted grey;
  border-spacing: 0.5em 1em;
}

table.rubrica td.cellarubrica{
  border-bottom: 1px dotted grey;
}

table.rubrica th.intestazionerubrica{
  background: #E0E0E0;
  border-bottom: 1px dotted grey;
  border-top: 1px dotted grey;
}

p.spazio_riservato {
  width:55%;
  font-weight: bold;
  font-variant: small-caps;
  font-size: 7.2pt;
  margin-top: -10px;
  margin-bottom:10px;
  border-bottom:1.5px dotted grey;
  padding-bottom:0.2em;
}


</style>
</head>
<body>

<?php
$idPersona;
$nome;
$cognome;
$indirizzo;
$sesso;
$data_nascita;
$luogo_nascita;
$email;
$classe;
$sezione; 
$parrocchia;
$telefono_casa;
$cellulare_ragazzo;
$cellulare_mamma;
$cellulare_padre;
$tipomodulo;
$modulo;


// devo fare moduli in bianco?
if ($copie > 0) {
	$nome = str_repeat("_",10);
	$cognome = str_repeat("_",10);
	$indirizzo = str_repeat("_",30);
	$sesso = str_repeat("_",2);
	$data_nascita = "__/__/_____";
	$luogo_nascita = str_repeat("_",30);
	$email = str_repeat("_",30);
	$classe = str_repeat("_",15);
	$sezione = str_repeat("_",10); 
	$parrocchia = str_repeat("_",30);
	$telefono_casa = str_repeat("&nbsp;",20);
	$cellulare_ragazzo = str_repeat("&nbsp;",20);
	$tipo_modulo[0]="dall'asilo alla 1<sup>a</sup> media";
	$tipo_modulo[1]="dalla 2<sup>a</sup> media alla 5<sup>a</sup> superiore";
	$tipo_modulo[2]="dopo le superiori &amp; adulti";
	$indice_classe[0]=7; //fino alla 1a media
	$indice_classe[1]=14;//fino alla 5a superiore 
	$indice_classe[2]=15;// per giovani e adulti
	
  for ($modulo=0;$modulo<3;$modulo++) {
      $tipomodulo=$tipo_modulo[$modulo];
      $IndiceClasse=$indice_classe[$modulo];
      for($p = 0;$p < $copie; $p++) {
		      StampaPagina();
	   }
	}
} else { 
	$tipomodulo="";
	
  if ($id <> "") {
		$rstTesserati = GetTesseratiByID($id);
	} else {
  		$rstTesserati = GetTesserati($_POST["anno"], $_POST["gruppi"]);
  	}
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
            
						if (strval($row->Data_di_nascita) != "0000-00-00 00:00:00" && strval($row->Data_di_nascita)!=null) {
              $data_nascita = htmlentities(date("d/m/Y", strtotime($row->Data_di_nascita)));
						} else {
							$data_nascita = "";
						}
						
            $luogo_nascita = htmlentities($row->Luogo_di_nascita);
						
            $email = htmlentities($row->{'email'});
						
            $classe = htmlentities($row->Classe);
            
            $IndiceClasse=$row->IndiceClasse;
						
            $sezione = htmlentities($row->Sezione); 
						
            $parrocchia = GetParrocchiaProvenienza($row->Parrocchia_Provenienza); 
						
            $telefono_casa="";
            if (($row->Prefisso).($row->Numero)!="") {
                $telefono_casa = htmlentities($row->Prefisso."/".$row->Numero); 
					  }
          }
				}
				
				// cellulare ragazzo
				$cellulare_ragazzo="";
        $rstCellularePersonaleID = GetCellularePersonaleID($idPersona,3);
				if($rstCellularePersonaleID) {
					if(mysqli_num_rows($rstCellularePersonaleID) > 0) {
						$row = mysqli_fetch_object($rstCellularePersonaleID);
						if (($row->Prefisso).($row->Numero)!="") {
              $cellulare_ragazzo = htmlentities($row->Prefisso."/".$row->Numero);
					  } 
          }
				}			
				
				// cellulare mamma
				$cellulare_mamma="";
				$rstCellularePersonaleID = GetCellularePersonaleID($idPersona,4);
				if($rstCellularePersonaleID) {
					if(mysqli_num_rows($rstCellularePersonaleID) > 0) {
						$row = mysqli_fetch_object($rstCellularePersonaleID);
            if (($row->Prefisso).($row->Numero)!="") {
              $cellulare_mamma = htmlentities($row->Prefisso."/".$row->Numero);
					  } 
					}
				}			
				
				// cellulare papà
				$cellulare_padre="";
        $rstCellularePersonaleID = GetCellularePersonaleID($idPersona,5);
				if($rstCellularePersonaleID) {
					if(mysqli_num_rows($rstCellularePersonaleID) > 0) {
						$row = mysqli_fetch_object($rstCellularePersonaleID);
						if (($row->Prefisso).($row->Numero)!="") {
              $cellulare_padre = htmlentities($row->Prefisso."/".$row->Numero);
					  } 
					}
				}
				
				$parentela=GetParentela($idPersona);
        StampaPagina();
      }				
	}
  }
}


function StampaPagina()
{
global $copie;
global $idPersona;
global $nome;
global $cognome;
global $indirizzo;
global $sesso;
global $data_nascita;
global $luogo_nascita;
global $email;
global $classe;
global $IndiceClasse;
global $sezione; 
global $parrocchia;
global $telefono_casa;
global $cellulare_ragazzo;
global $cellulare_mamma;
global $cellulare_padre;
global $parentela;
global $tipomodulo;
global $modulo;
?>

	
<div id="pagina">

    <div id="contenutopagina">
        
        <div id="intestazione_pagina">	
            <p class="spazio_riservato">
                <span style="font-weight:normal; font-variant:normal;">
                    riservato segreteria oratorio:
                </span>

                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                noigest &nbsp;&nbsp;
                
                <span class="quadrato">&nbsp;&nbsp;&nbsp;</span>
                
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                gestione oratorio &nbsp;&nbsp;
                
                <span class="quadrato">&nbsp;&nbsp;&nbsp;</span>
            </p>
            
            <img src="./Immagini/logoratorio.png" id="logo_oratorio" width ="40" height="40" alt="logo oratorio" />
            <strong>Oratorio Saint Martin - Viale Europa, 1 - 11100 Aosta - Tel. 0165/554234 </strong>
		    </div>
        <?php 
            if ($copie==0) {
        ?>
                <h2>ISCRIZIONE <?php echo $_POST["anno"]." - ".($_POST["anno"] + 1) ?></h2>
        <?php 
            } else {  
        ?>
            <h2>ISCRIZIONE ANNO <?php
                echo str_repeat("_",5);
            }
        ?></h2>
        
        <div id="mybarcode">
            <?php 
                if ($copie ==0) {
                    echo bar128(str_pad($idPersona,13,"0",STR_PAD_LEFT ),140);
                } else {
                    echo "<span style='font-weight:bold;background:black;padding:0.2em;color:white;'>".$tipomodulo."</span>";
                    echo "<style> .tabella_anagrafica {margin-top:-10px;} </style>";
                }
              ?> 
        </div>
        
        <div id="anagrafica">
        <table class="tabella_anagrafica" border=0>
            <tr>
				        <td>
                    Nominativo: <strong><?php echo $cognome." ".$nome; ?></strong>
                </td>
               
                <td rowspan=6>
                
                    <table class="rubrica">
                        <tr>
                            <th class="intestazionerubrica">SMS</th>
                            <th class="intestazionerubrica">RUBRICA</th>
                        </tr>
                        
                        <tr>
                            <td class="quadrato">&nbsp;</td>
                            <td class="cellarubrica">Casa: <strong><?php echo $telefono_casa; ?></strong></td>
                        </tr>
                        
                        <tr>
                            <td class="quadrato">&nbsp;</td>
                            <td class="cellarubrica"><?php 
                                if ($IndiceClasse==16) {
                                    echo "Cellulare: ";
                                } elseif($IndiceClasse < 7) {
                                    echo "Cell. bimbo/a: ";
                                }
                                  else {
                                    echo "Cell. ragazzo/a: ";
                                }
                            ?>
                            <strong><?php echo $cellulare_ragazzo; ?></strong></td>
                        </tr>
                        
                        <tr>
                            <td class="quadrato">&nbsp;</td>
                            <td class="cellarubrica">Cell. mamma: <strong><?php echo $cellulare_mamma; ?></strong></td>
                        </tr>
                        
                        <tr>
                            <td class="quadrato">&nbsp;</td>
                            <td class="cellarubrica">Cell. pap&agrave;: <strong><?php echo $cellulare_padre; ?></strong></td>
                        </tr>
                        
                        <tr>
                            <td class="quadrato">&nbsp;</td>
                            <td class="cellarubrica">Altro:</td>
                        </tr>
                    </table>
                </td>
            </tr>
			
			      <tr>
			         <td>Indirizzo: <strong><?php echo $indirizzo ?></strong></td>
			      </tr>
			
            <tr>
				        <td>
                    Sesso: <strong><?php echo $sesso ?></strong>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Data di nascita: <strong><?php echo $data_nascita ?></strong>
                </td>
			     </tr>
			
          <tr>
		    	      <td>Luogo di nascita: <strong><?php echo $luogo_nascita ?></strong></td>
			    </tr>
			
			   <tr>				
                <td>E-mail: <strong><?php echo $email ?></strong></td>
			   </tr>
			
			   <tr>
				    <?php if ($IndiceClasse>14) {?>
                <td>Gruppo (giovane/adulto)  : <strong><?php echo $classe ?></strong></td>
            <?php 
              } else {
            ?>
            <td>Classe a scuola: <strong><?php echo $classe ?></strong></td>
            <?php } ?>
			   </tr>
			   
         <tr>
				      <td>
                <?php 
                    if ($IndiceClasse<8) {
                        echo "Giorno di catechismo: ";
                    } elseif ($IndiceClasse>7 && $IndiceClasse<15) {
                        echo "Giorno del Gruppo: ";
                    } else {
                        echo "Giorno di formazione: ";
                    }
                ?>
                
                <strong> 
                    <?php 
                          if ($sezione=="**********") {
                              $sezione=null;
                          }                      
                          echo $sezione;
                    ?>
                </strong>
             </td>    
      
          <tr>     
              <td colspan="2">
                Parrocchia di provenienza: <strong><?php echo $parrocchia ?></strong>
             </td>
			   </tr>
			
			<tr>
				<?php 
            if ($parentela) {
                echo "<td class=\"bordino\" colspan=\"2\">Fratelli/Sorelle: <strong>".$parentela."</strong></td>";
            } else {
        ?>
        <td class="bordino" colspan="2">Ho altri fratelli/sorelle iscritti all'Oratorio? &nbsp;<strong>Sì - No</strong>&nbsp;&nbsp;&nbsp;&nbsp;Come si chiamano? 
        <br /> <br />Che classe fanno?
        <?php } ?>
			</tr>

			<tr>
			   <td colspan="2">
            <table class="tabella_quota" border =0>
			         <tr>
			         	<td class="larghezzacella">Verso la quota di <sup>(*)</sup></td>
			            <td class="quadrato">&nbsp;</td>
			            <td class="larghezzacella"><strong>&nbsp;&nbsp;20 &euro;</strong></td>
                   	<td class="quadrato">&nbsp;</td>
                   	<td class="larghezzacella"><strong>&nbsp;&nbsp;8 &euro;</strong> (2° figlio)</td>
                   	<td class="quadrato">&nbsp;</td>
                   	<td class="larghezzacella"><strong>&nbsp;&nbsp;Iscrizione<br />&nbsp;&nbsp;gratuita</strong><br />&nbsp;&nbsp;(dal 3° figlio)</td>
							<td class="quadrato">&nbsp;</td>							
							<td class="larghezzacella"><strong>&nbsp;&nbsp;Altro &euro;</strong> ______ </td>			         
			         </tr>
            </table>
          </td>
      </tr>
			</table>
			
      <br />
      <sup>(*) La tessera dell'oratorio e la quota versata serviranno per la copertura assicurativa</sup>
			
      <br /><br />
      
			<!-- TABELLA AUTORIZZAZIONI -->
      <table class="autorizzazioni">
          <tr>
              <td class="interlinea" style="border-bottom:1px dotted grey;">
                  <strong>
                      Autorizzo mio figlio/a a partecipare alle attivit&agrave;
                      fuori dal territorio parrocchiale <br />sollevando l'oratorio Saint-Martin  
                      e la parrocchia da ogni responsabilit&agrave
                      <br />&nbsp;
                  </strong>
                  
              </td>
              
              <td class="quadrato">&nbsp;</td>
              
              <td>
                  <strong>
                      <span style="font-size:9pt;">&nbsp;&nbsp;Sì</span>
                  </strong>
              </td>
              
              <td class="quadrato">&nbsp;</td>
              
              <td>
                  <strong>
                      <span style="font-size:9pt;">&nbsp;&nbsp;No</span>
                  </strong>
              </td>
          </tr>
          <tr>
              <td>
                  <strong>
                      Autorizzo la pubblicazione, sul sito della parrocchia (<span style="font-style: italic;">www.parrocchiasaintmartin.it</span>)
                      <br /> e su eventuali social network, di video e fotografie prodotti durante le attivit&agrave; 
                      <br />sollevando l'oratorio Saint-Martin e la parrocchia da ogni responsabilit&agrave;
                  </strong>
              </td>
              
              <td class="quadrato">&nbsp;</td>
              
              <td>
                  <strong>
                      <span style="font-size:9pt;">&nbsp;&nbsp;Sì</span>
                  </strong>
              </td>
              
              <td class="quadrato">&nbsp;</td>
              
              <td>
                  <strong>
                      <span style="font-size:9pt;">&nbsp;&nbsp;No</span>
                  </strong>
              </td>
          </tr>
      </table>
      <p style="text-align:right;padding-top:20px;">Firma _______________________________________ </p>
      </div>
            
      <div id="informativa">
      <p><h3>INFORMATIVA</h3></p>
			
			<p class="interlinea">In conformit&agrave; al D. L.
			30/06/2003, nr. 196, riguardante la tutela delle persone e di altri
			soggetti rispetto al trattamento dei dati personali, si informa
			che:</p>
			
			<ul>
			<li>
			<p class="interlinea">I dati
			personali raccolti con la presente scheda di adesione verranno
			trattati per esclusive finalit&agrave;
			associative/gestionali, statistiche e promozionali, mediante
			elaborazione con criteri prefissati.</p>
			</li>
			<li>
			<p class="interlinea">L'acquisizione
			dei dati personali &egrave; presupposto indispensabile per
			l'instaurazione del contratto associativo e lo
			svolgimento dei rapporti cui la stessa acquisizione &egrave;
			finalizzata.</p>
			</li>
			<li>
			<p class="interlinea">I dati
			raccolti saranno comunicati per motivi associativi e assicurativi a
			NOI Associazione nazionale, regionale e territoriale,
			all'intermediario assicurativo e a eventuali
			associazioni ed enti con cui NOI Associazione
			stabilir&agrave; accordi e convenzioni.</p>
			</li>
			<li>
			<p class="interlinea">I dati
			raccolti non saranno mai, in nessun caso, comunicati, diffusi o
			messi a disposizione di enti diversi da quelli
			indicati.</p>
			</li>
			<li>
			<p class="interlinea">L'associazione
			e i suoi genitori hanno diritto a ottenere senza ritardo:
			</p>
			</li>
			<ol style="list-style-type:lower-latin">
				<li>
				<p class="interlinea">La
				conferma dell'esistenza dei dati personali che
				li riguardano, la comunicazione in forma intelligibile dei medesimi
				dati e della loro origine, nonch&eacute; della logica su cui
				si basa il trattamento.</p>
				</li>
				<li>
				<p class="interlinea">La
				cancellazione, la trasformazione in forma anonima, il blocco dei
				dati trattati in violazione della legge.</p>
				</li>
				<li>
				<p class="interlinea">L'aggiornamento e la rettifica o
				l'integrazione dei dati.</p>
				</li>
			</ol>
			<li><p class="interlinea">Titolare
			del trattamento &egrave; l'associazione
			evidenziata nell'intestazione di questa
			informativa, da cui si evince anche la sede.</p>
			</li>
			<li>
			<p class="interlinea">Responsabile del
			trattamento &egrave; la medesima associazione nella persona
			del suo presidente.</p>
			</li>
			</ul>
			
			<h3>CONSENSO</h3>
			
			<p class="interlinea">Premesso che - come
			rappresentato nell'informativa che mi
			&egrave; stata fornita ai sensi della Legge 675/1996 - le
			operazioni di tesseramento richiedono il trattamento dei dati
			personali di mio/a figlio/a e la loro comunicazione a NOI
			Associazione nazionale, regionale e territoriale,
			all'intermediario assicurativo e a eventuali
			associazioni e enti con cui NOI Associazione stabilir&agrave;
			accordi e convenzioni. Con la sottoscrizione
			del presente documento esprimo il mio consenso al trattamento e
			alle comunicazioni indicate dei dati raccolti con questa scheda di
			adesione.</p>
			<br /><br />
			
      <div style="float:left">
          <strong> Data:</strong> &nbsp; 
                <?php 
                    if ($copie == 0) {
                        echo date("d/m/Y");
                    } else {
                        echo "__/__/_____";
                    }
                ?>
      </div>
      
      <div style="float:right;">
          <?php 
              if ($copie>0) {
                  if ($modulo<2) {
                      echo "<strong>Firma del genitore:</strong> ".str_repeat("_",40);
                  } else {
                      echo "<strong>Firma:</strong> ".str_repeat("_",40);
                  }
              } else {
                    $gg_oggi=date("d");
                    $mm_oggi=date("m");
                    $yy_oggi=date("Y");
                    $gg_nascita=substr($data_nascita,0,2);
                    $mm_nascita=substr($data_nascita,3,2);
                    $yy_nascita=substr($data_nascita,6,4);
              
                    if ($yy_oggi-$yy_nascita==18) {
                        if ((int)($mm_nascita.$gg_nascita)>(int)($mm_oggi.$gg_oggi)) {
                            echo "<strong>Firma del genitore:</strong> ".str_repeat("_",40);
                        } else {
                            echo "<strong>Firma:</strong> ".str_repeat("_",40);
                        }
                    } elseif ($yy_oggi-$yy_nascita<18) {
                          echo "<strong>Firma del genitore:</strong> ".str_repeat("_",40);
                    } else {
                          echo "<strong>Firma:</strong> ".str_repeat("_",40);
                    } 
              }
          ?>
      </div>
		</div>
	</div> 
	<p style="page-break-after: always;" /> <!-- margin-top:460px;" / -->
<?php
    return;
}

function GetTesserati($anno, $gruppo)
{
	$sql = "SELECT ID,Classe FROM Catechismi WHERE YEAR(DataTesseramento) = %1\$s AND Cancellato=false ";
	if($gruppo != 0) {
		$sql .= "AND Classe IN (SELECT IDClasse FROM tblClassi WHERE fkGruppo = %2\$s) ";
	}
	$sql .= "ORDER BY Classe,Cognome,Nome";
	$sql = sprintf($sql, $anno, $gruppo);
		
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
  if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetTesserati: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		exit();
	}  
    return $result;
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

function GetParentela($ID) {
  $parentela=null;
  $query="SELECT tblparentela.IdFamigliare,tblparentela.IdGradoParentela,Catechismi.Nome,tblClassi.Sigla 
          FROM tblparentela
          INNER JOIN Catechismi
          ON tblparentela.IdFamigliare=Catechismi.ID
          INNER JOIN tblClassi
          ON Catechismi.Classe=tblClassi.IDClasse
          WHERE tblparentela.IdFamiglia=(SELECT IdFamiglia FROM tblparentela WHERE IdFamigliare=".$ID.")
          AND IdGradoParentela>2 
          ORDER BY Catechismi.Nome";

    $result_parentela=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die("Ops! Problemi con questa query <br><br>".$query);
    
    if ($result_parentela) {
        while ($row=mysqli_fetch_object($result_parentela)) {
            if ($row->IdFamigliare!=$ID) {
                $parentela.=htmlentities($row->Nome)." (".$row->Sigla.") ";
            }
        }
    } else {
      return false;
    }
    
    return $parentela;

}
?>
</body>
<!-- script type="text/javascript">
	window.print();
</script -->
</html>