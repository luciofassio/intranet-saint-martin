<?php
//
// Business layer
//
// carico gli eventi nella ddll
// $evento			ID evento da selezionare nella ddl
// $arrEventiPersona	elenco degli ID evento ai quali la persona è già iscritta, da evidenziare nella ddl
function CaricaEventi($evento, $arrEventiPersona) {
	$rstEventi = GetEventi();
	if (isset($rstEventi)) {
		$eventi = "";
		while ($rowEventi = mysql_fetch_object($rstEventi)) {
			$eventi .= "<option value=\"".$rowEventi->IDEvento."\"";
			if (isset($arrEventiPersona)) {
				// evidenzio gli eventi già prenotati
				foreach ($arrEventiPersona as $ev) {
					if ($rowEventi->IDEvento == $ev["IDEvento"] ) {
						$eventi .= " style=\"background-color:lightgreen\" ";
					}
				}
			}
			// seleziono l'evento attuale
			if ($rowEventi->IDEvento == $evento) {
				$eventi .= " selected ";
			}
			$eventi .= ">".$rowEventi->NomeEvento."</option>";
		}
	}
	return $eventi;
}

// carico il ruolo nella ddl
function CaricaRuoli($ruolo) {
	$rstRuoli = GetRuoli();
	if ($rstRuoli) {
		$rowRuoli = mysql_fetch_object($rstRuoli);
		$ruoli = "";
		while ($rowRuoli) {
			$ruoli .= "<option value=\"".$rowRuoli->IDRuolo."\"";
			if ($rowRuoli->IDRuolo == $ruolo) {
				$ruoli .= " selected ";
			}
			$ruoli .= ">".$rowRuoli->Ruolo."</option>";
			$rowRuoli = mysql_fetch_object($rstRuoli);
		}
	}
	return $ruoli;
}

// carico le classi in una ddl
function CaricaClassi($classe) {
	$rstClassi = GetClassi();
	if ($rstClassi) {
		$rowClassi = mysql_fetch_object($rstClassi);
		$Classi = "";
		while ($rowClassi) {
			$Classi .= "<option value=\"".$rowClassi->IDClasse."\"";
			if ($rowClassi->IDClasse == $classe) {
				$Classi .= " selected ";
			}
			$Classi .= ">".$rowClassi->Classe."</option>";
			$rowClassi = mysql_fetch_object($rstClassi);
		}
	}
	return $Classi;
}

// carico i gruppi di classi in una ddl
function CaricaGruppi($gruppo) {
	$rstGruppi = GetGruppi();
	if ($rstGruppi) {
		$rowGruppi = mysql_fetch_object($rstGruppi);
		$Gruppi = "";
		while ($rowGruppi) {
			$Gruppi .= "<option value=\"".$rowGruppi->IDGruppo."\"";
			if ($rowGruppi->IDGruppo == $gruppo) {
				$Gruppi .= " selected=\"selected\" ";
			}
			$Gruppi .= ">".$rowGruppi->Gruppo."</option>";
			$rowGruppi = mysql_fetch_object($rstGruppi);
		}
	}
	return $Gruppi;
}

function CaricaListino($IDRuolo, $IDEvento) {
	$rstListino = GetListinoByRuoloER($IDRuolo, $IDEvento);
	if($rstListino) {
		$rowListino = mysql_fetch_object($rstListino);
		$listino = "";
		while ($rowListino) {
			$listino .= "<span id=\"prezzoIscrizione\" style=\"float:right\" >".number_format($rowListino->Iscrizione, 2, ',', '.')."</span><br/>";
			$listino .= "<span id=\"prezzoPranzo\" style=\"float:right\" >".number_format($rowListino->Pranzo, 2, ',', '.')."</span><br/>";
			$listino .= "<span id=\"prezzoCena\" style=\"float:right\" >".number_format($rowListino->Cena, 2, ',', '.')."</span><br/>";
			$listino .= "<span id=\"prezzoCenaFinale\" style=\"float:right\" >".number_format($rowListino->CenaFinale, 2, ',', '.')."</span><br/>";
			$listino .= "<span id=\"prezzoAbbPranzo\" style=\"float:right\" >".number_format($rowListino->AbbonamentoPranzo, 2, ',', '.')."</span><br/>";
			$listino .= "<span id=\"prezzoAbbCena\" style=\"float:right\" >".number_format($rowListino->AbbonamentoCena, 2, ',', '.')."</span><br/>";
			$listino .= "<span id=\"prezzoAbbPranzoCena\" style=\"float:right\" >".number_format($rowListino->AbbonamentoPranzoCena, 2, ',', '.')."</span><br/>";
			$listino .= "<span id=\"prezzoEventoSpecialeER\" style=\"float:right\" >".number_format($rowListino->EventoSpecialeER, 2, ',', '.')."</span><br/>";
			$rowListino = mysql_fetch_object($rstListino);
		}
	}
	return $listino;
}
?>
