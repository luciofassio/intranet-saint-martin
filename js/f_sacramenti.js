/* JavaScript Document
 * Oggetto:   Funzioni per pagina web xsacramenti
 * Creazione: Agosto 2011
 * Modifiche: ********
 * Autore:    Marco Fogliadini 
*/



//***************************************************************************************
// disattiva la scelta del gruppo quando si vuole stampare in ordine alfabetico gli iscritti
// di tutti i gruppi
function DisattivaScegliGruppi() {
    var stampa_elenco=document.getElementById("chkUnisciGruppi").checked;
    var gruppo=document.getElementById("stampa_gruppo");

    if (stampa_elenco) {
        gruppo.options[0].selected=true;
        gruppo.disabled=true;
       
    } else {
        gruppo.disabled=false;
    }
    
    return;
}

//***************************************************************************************
// setta il fuoco all'apertura della pagina
function CaricamentoPagina(sezione){
    switch (sezione) {
        case 'scheda':
          document.getElementById("campi_ricerca").style.visibility="hidden";
          document.getElementById("gruppi").style.visibility="hidden";
        
          // apre la scheda del candidato
          document.getElementById("scheda").style.visibility="visible";
          document.getElementById("data_battesimo").focus();
        break;
        
        case 'gruppi': //crea modifica gruppi 
            document.getElementById("campi_ricerca").style.visibility="hidden";
            document.getElementById("scheda").style.visibility="hidden";
             document.getElementById("stampa_documenti").style.visibility="hidden";
            document.getElementById("gruppi").style.visibility="visible";
            document.getElementById("sezione").value=sezione; //setta la sezione aperta
            document.getElementById("data_gruppo").focus();
        break;
        
        case 'elenco_documenti': // finestra elenco documenti
            document.getElementById("campi_ricerca").style.visibility="hidden";
            document.getElementById("scheda").style.visibility="hidden";
            document.getElementById("gruppi").style.visibility="hidden";
            document.getElementById("stampa_documenti").style.visibility="visible";
            document.getElementById("sezione").value=sezione; //setta la sezione aperta
            document.getElementById("azione").value=sezione;
        break;
        
        case 'modulo_precompilato': // finestra elenco documenti
            document.getElementById("campi_ricerca").style.visibility="hidden";
            document.getElementById("scheda").style.visibility="hidden";
            document.getElementById("gruppi").style.visibility="hidden";
            document.getElementById("stampa_documenti").style.visibility="visible";
            document.getElementById("sezione").value=sezione; //setta la sezione aperta
            document.getElementById("azione").value=sezione;
        break;
        
        default: // visualizza la sezione di ricerca e comandi
            document.getElementById("campi_ricerca").style.visibility="visible";
            document.getElementById("scheda").style.visibility="hidden";
            document.getElementById("gruppi").style.visibility="hidden";
    
            // setta il fuoco sul campo Cognome della Ricerca iscritti
            var campo=document.getElementById("txtCognome");
            campo.focus();
            campo.style.background="#FAF176";
        break;
    }
    return;
}

//***************************************************************************************
// serve per rilevare se l'utente ha premuto il tasto tab per spostarsi di campo e nasconde i suggest
function RilevaTab(obj,campo) {
    switch (obj.keyCode) {
        case 9: // tab
            $('#suggestions').hide();
            $('#suggestions_names').hide();
            //$('#suggestions_comuni').hide();
            $('#suggestions_parrocchie').hide();
            $('#suggestions_parrocchie_padrino').hide();
        break;
        
        case 13: // invio
            if (obj.keyCode==13) {
                if (campo=="txtBarCode") {
                    if (document.getElementById(campo).value==null || document.getElementById(campo).value=="") {
                        return;
                    }
                    document.getElementById("hdnID").value=document.getElementById(campo).value;
                    document.getElementById("sezione").value="scheda";
                    document.getElementById("CercaIscritti").submit();
                }
            }
        break;
    }
    
    return;
}

//***************************************************************************************
// modifica il colore e mette in minuscolo il testo digitato dall'utente
function ResetCampo(nome_campo,sfondo) {
    var campo = document.getElementById(nome_campo);
    campo.value=campo.value.toLowerCase();
    campo.style.color ="black";
    campo.style.background = sfondo;
    //campo.style.background ="#FAF176";
    $('#suggestions').hide();
    $('#suggestions_names').hide();
    return;
}    

//***************************************************************************************
// modifica colore e mette in maiuscolo il testo digitato dall'utente nel campo
function ControlloInput(nome_campo,bypass) {
        var campo = document.getElementById(nome_campo);
        if (bypass) {
            campo.style.color ="black";
            campo.style.background = "white"
            campo.style.border="1px dotted grey";
            return;
        }
        // controlla il campo       
        if (campo.value!="") {
            campo.value=campo.value.toUpperCase();
            campo.style.color ="#FAF176";
            campo.style.background ="green";
            campo.style.border="1px dotted grey";
        } else {
            campo.style.color ="black";
            campo.style.background = "white"
            campo.style.border="1px dotted grey";
        }
        return;
}  

//***************************************************************************************		
function fill(thisValue) { // per i cognomi della sezione ricerca
      if (thisValue != null) {
        modulo = document.getElementById("CercaIscritti");
        valori = thisValue.split('|');	
				$('#hdnID').val(valori[0]);
				$('#txtCognome').val(valori[1]);
				$('#txtNome').val(valori[2]);
				
				// costruisce il codice a barre a 13 cifre e lo stampa
				var barcode="0000000000000";
				barcode=barcode.slice(0,-valori[0].length)+valori[0];
        $('#txtBarCode').val(barcode);
        // $('#txtBarCode').val(valori[3]); dismesso perché utilizza il codice a barre del vecchio programma Access

				setTimeout("$('#suggestions').hide();", 200);
				document.getElementById("sezione").value="scheda";
        modulo.submit();
      } 
      
      ControlloInput('txtCognome');
      ControlloInput('txtNome');
      ControlloInput('txtBarCode');
      return;
}

//***************************************************************************************		
// autocomplete in ajax - suggest per i cognomi degli iscritti
function lookup(inputString) {
      var bottone = document.getElementById("caricaPersona");
      if(inputString.length == 0) {
				// disabilita il pulsante cerca iscritti
				bottone.disabled=true;
        // Hide the suggestion box.
				$('#suggestions').hide();
			} else {
				bottone.disabled=false;
        $.post("rpc.php", {queryString: ""+inputString+""}, function(data){
          if(data.length >0) {
            $('#suggestions').show();
						$('#autoSuggestionsList').html(data);
					} else {
            $('#suggestions').hide();
          }
				});
			}
			return;
} // lookup

//***************************************************************************************		
// autocomplete in ajax - suggest per i nomi degli iscritti
function lookup_names(inputString) {
      var bottone = document.getElementById("caricaPersona");
      if(inputString.length == 0) {
				// disabilita il pulsante cerca iscritti
				bottone.disabled=true;
        // Hide the suggestion box.
				$('#suggestions_names').hide();
			} else {
				bottone.disabled=false;
        $.post("rpc_names.php", {queryString: ""+inputString+""}, function(data){
          if(data.length >0) {
            $('#suggestions_names').show();
						$('#autoSuggestionsListNames').html(data);
					} else {
            $('#suggestions_names').hide();
          }
				});
			}
			return;
} // lookup

//***************************************************************************************		
function fill_names(thisValue) { // per i nomi della sezione ricerca
      if (thisValue != null) {
        modulo = document.getElementById("CercaIscritti");
        valori = thisValue.split('|');	
				$('#hdnID').val(valori[0]);
				$('#txtCognome').val(valori[1]);
        $('#txtNome').val(valori[2]);
				
				// costruisce il codice a barre a 13 cifre e lo stampa
				var barcode="0000000000000";
				barcode=barcode.slice(0,-valori[0].length)+valori[0];
        $('#txtBarCode').val(barcode);
        // $('#txtBarCode').val(valori[3]); dismesso perché utilizza il codice a barre del vecchio programma Access

				setTimeout("$('#suggestions_nanes').hide();", 200);
				document.getElementById("sezione").value="scheda";
        modulo.submit();
      } 
      
      ControlloInput('txtCognome');
      ControlloInput('txtNome');
      ControlloInput('txtBarCode');
      return;
}

//***************************************************************************************		
// funzione per invio codice a barre a PHP per la ricerca dell'iscritto
function InvioBarCode(tasto) {
    var BarCode =document.getElementById("txtBarCode").value;
    
    // controlla se nella stringa letta dal lettore ci sia il codice di 'invio' o se l'utente abbia premuto il tasto di invio
    // In caso affermativo manda la stringa a PHP
    if (tasto.keyCode==13) {
        document.getElementById("hdnID").value=BarCode;
       
        document.getElementById("CercaIscritti").submit();
    }
    
return;
}	

//***************************************************************************************		
// AZIONI PUNLSANTI
function AzioniPulsanti(comando) {
    var azione=new String();
    switch (comando) {
        case "salva": // salva la scheda del candidato
            azione="salva_scheda";
        break;
        
        case "elimina": // elimina la scheda del canddiato
            if (!confirm("Attenzione! Premendo 'Ok' cancellerai tutti i dati di questa scheda! Proseguo?")) {
                return;
            } else {
              azione="elimina_scheda";
            }
        break;
    
        case "chiudi": // chiude la scheda del candidato
            azione="chiudi_scheda";
        break;
        
        case "chiudi_gruppi":
            azione="chiudi_gruppi";
        break;
        
        case "elenco_documenti": // elenco documenti mancanti
            azione="elenco";
        break;
        
        case "elenco_totale": // elenco documenti mancanti
            azione="elenco";
        break;
        
        case "notifiche":
            azione="notifiche";
        break;
        
        case "modulo_precompilato":
            azione="modulo_precompilato";
        break;
        
    
    }

    //manda l'azione da svolgere a PHP
    document.getElementById("azione").value=azione;
    document.getElementById("CercaIscritti").submit();
    
    return;
}

//***************************************************************************************
//funzione per controllare l'inserimento corretto dell'ora
function ControlloOraInserita(oradafiltrare,campo,colore,sfondo) {
    var hh=new String();
    var mm=new String();

    var pattern1=/^([0-9]{4,4})+$/
    var pattern2=/^([0-9]{2,2})+\:+([0-9]{2,2})+$/;
    
    switch (oradafiltrare.length) {
        case 0:
            document.getElementById(campo).style.color=colore;
            document.getElementById(campo).style.background=sfondo;
            return;
        break;
        
        case 2:
            oradafiltrare+=":00";
        break;
    }    
    
    //controlla che siano stati inseriti solo numeri
    if (!pattern1.test(oradafiltrare)) { 
        if (!pattern2.test(oradafiltrare)) {
            alert ("Attenzione! L'ora che hai inserito e' errata!")
            return;
       }
    }
    
    //controllo dell'ora
    hh=oradafiltrare.substr(0,2);
    if (Number(hh)>23) {
        alert ("Attenzione! L'ora che hai inserito e' errata!")
        return;
    }
    
    //controllo dei minuti
    if (oradafiltrare.length==4) {
      mm=oradafiltrare.substr(2,2);
    } else {
      mm=oradafiltrare.substr(3,2);
    }
    
    if (Number(mm)>59) {
        alert ("Attenzione! L'ora che hai inserito e' errata!")
        return;
    }
    
    
    // ricostruzione della stringa e uscita
    document.getElementById(campo).value=hh+":"+mm;
    document.getElementById(campo).style.color=colore;
    document.getElementById(campo).style.background=sfondo;
    return;
}

//***************************************************************************************
// funzione per controllare l'inserimento corretto della data
function ControlloDataInserita(datadafiltrare,campo,dt,colore,sfondo) {

var gg;  //variabile per i giorni
var mm; // variabile per i mesi
var yy; // variabile per l'anno
var dateNow = new Date(); // assegna la data corrente (presa dal pc locale)
var yearNow = dateNow.getFullYear(); //assegna l'anno corente
var indice=0; // variabile di servizio

var nrgiorni = new Array();
	nrgiorni[0]=29; // febbraio bisestile
	nrgiorni[1]=31; // gennaio
	nrgiorni[2]=28; // febbraio
	nrgiorni[3]=31; // marzo
	nrgiorni[4]=30;	// aprile
	nrgiorni[5]=31; // maggio
	nrgiorni[6]=30; // giugno
	nrgiorni[7]=31; // luglio
	nrgiorni[8]=31; // agosto
	nrgiorni[9]=30; // settembre
	nrgiorni[10]=31; // ottobre
	nrgiorni[11]=30; // novembre
	nrgiorni[12]=31; // dicembre

if (datadafiltrare==null || datadafiltrare=="") { //se il campo è vuoto esce dalla funzione
  document.getElementById(campo).style.color=colore;
  document.getElementById(campo).style.background="white";
  return;
}

switch (datadafiltrare.length) { //analizza il formato della data e prepara le variabili gg, mm, yy
    case is = 6: //formato data breve
        gg = datadafiltrare.slice(0,2);
        mm = datadafiltrare.slice(2,4);
        yy = datadafiltrare.slice(4,6);
    break;    
 
    case is = 8: //formato data a 8 caratteri
        if (datadafiltrare.indexOf("/") >= 0 || datadafiltrare.indexOf("-")>=0) {  //con separatori
           gg = datadafiltrare.slice(0,2);
           mm = datadafiltrare.slice(3,5);
           yy = datadafiltrare.slice(6,8);
        } else { //data a 8 cifre senza separatori
           gg = datadafiltrare.slice(0,2);
           mm = datadafiltrare.slice(2,4);
           yy = datadafiltrare.slice(4,8);
        }
    break;
        
    case is = 10: //formato data completa
        gg = datadafiltrare.slice(0,2);
        mm = datadafiltrare.slice(3,5);
        yy = datadafiltrare.slice(6,10);
    break;

    default: //negli altri formati inseriti la routine genera un errore
        alert("Attenzione! La data che hai inserito e' errata. Sono validi i seguenti formati: 'ggmmaa','gg/mm/aa','ggmmaaaa' e 'gg/mm/aaaa'");
        document.getElementById(campo).focus();
        //datadafiltrare.focus();
        datadafiltrare="";
        return datadafiltrare;
}

//controlla che i dati di riferimento al giorno, al mese e all'anno siano dati numerici 
if (isNaN(gg) || isNaN(mm) || isNaN(yy)) {
	alert("Attenzione! La data che hai inserito e' errata.");
        document.getElementById(campo).focus();
        //datadafiltrare.focus();
        datadafiltrare="";
        return datadafiltrare;
}

//controlla che siano stati inseriti giusti tutti gli elementi che compongono la data
if (Number(gg) <= 0 || Number(mm) <= 0 || Number(mm) > 12 || Number(yy) < 0) {                     
        alert("Attenzione! La data che hai inserito e' errata.");
        document.getElementById(campo).focus();
        //datadafiltrare.focus();
        datadafiltrare="";
        return datadafiltrare;
}

//porta l'anno a quattro cifre calcolando il cambio del millennio
if (Number(yy) <= (Number(yearNow)-2000)) {                      
        yy = (Number(yy)+2000);                                          
    } else if (Number(yy) < 100 && Number(yy) > (Number(yearNow)-2000)){
        yy = (Number(yy)+1900);
    }

//controlla che l'anno non sia maggiore o uguale a quello corrente
//(dt e dn significano data tesseramento e data di nascita)
if (dt=="dn") {
    if (Number(yy) >= Number(yearNow)) {                         
        alert("Attenzione! L'anno che hai inserito e' errato!");
        document.getElementById(campo).focus;
        //datadafiltrare.focus();
        datadafiltrare="";
        return datadafiltrare;
    }
}
// controlla che l'anno inserito non sia bisestile e assegna il valore alla variabile indice
if ((Number(yy) % 4) == 0 && Number(mm)==2) {
	indice=0;
} else {
	indice=Number(mm);
}

// controlla che l'utente non abbia inserito un numero giorni del mese errato
if (Number(gg) > nrgiorni[indice]) {	
	alert("Attenzione! La data che hai inserito e' errata.");
    document.getElementById(campo).focus();
    //datadafiltrare.focus();
    datadafiltrare="";
    return datadafiltrare;
}
// ricompone e stampa la data filtrata
datadafiltrare=gg+"/"+mm+"/"+yy;
document.getElementById(campo).value=datadafiltrare;
document.getElementById(campo).style.color=colore;
document.getElementById(campo).style.background="white";
return;
}

//***************************************************************************************
// funzione per filtrare i dati inseriti dall'utente (cognome, nome, indirizzo, ecc.)
function FiltroStringa(stringa,campo,colore) {
    var indice =0;
    var carattere = new String();
    var vecchiocarattere = new String();
    var nuovastringa = new String();
    
    for (indice=0; indice < stringa.length; indice++) {
        carattere = stringa.charAt(indice);
        if (carattere.charCodeAt(0) != 32 || vecchiocarattere.charCodeAt(0) != 32) {
            if (indice == 0 
                              || vecchiocarattere.charCodeAt(0) == 32 // spazio
                              || vecchiocarattere.charCodeAt(0)==39   // apostrofo
                              || vecchiocarattere.charCodeAt(0)==45  // trattino -
                              || vecchiocarattere.charCodeAt(0)==40 //parentesi aperta
                )
            {
                nuovastringa += carattere.toUpperCase();
            } else {
                nuovastringa += carattere.toLowerCase();
            }
            vecchiocarattere = carattere;
        }
    }
    document.getElementById(campo).value=nuovastringa;
    document.getElementById(campo).style.background="white";
    document.getElementById(campo).style.color=colore;
    
    if (campo=='parrocchia_battesimo') {
      AssegnaBattesimo('blur');
    }
    
    return nuovastringa;
}
/**************************************************************************************************
 * nel caso in cui l'operatore abbia scelto per la parrocchia di battesimo Saint-Martin de Corléans
 * non serve far portare il certificato di battesimo in parrocchia... è già lì!!!
 **************************************************************************************************/ 
function AssegnaBattesimo(ctrl) {
    var parrocchia=document.getElementById("parrocchia_battesimo").value.toLowerCase();
    var optCertificatoBattesimo=document.getElementsByName("optBattesimo");
    var hdnIdParrocchia=document.getElementById('hdnIdParrocchia').value;
   
   switch (ctrl) {
      case 'blur':
          if (parrocchia=="") {
              optCertificatoBattesimo[1].checked=true;
          }
          if (parrocchia.indexOf('martin') <1 && parrocchia.indexOf('aosta') <1 && optCertificatoBattesimo[2].checked==true) {
              optCertificatoBattesimo[1].checked=true;
          }
      break;
      
      case 'fill':
          if (parrocchia.indexOf('martin') >0 && parrocchia.indexOf('aosta') >0 || hdnIdParrocchia==1) {
              optCertificatoBattesimo[2].checked=true; //seleziona in automatico il campo in parrocchia
          } else {
              optCertificatoBattesimo[1].checked=true;
          }
      break;
      
      case 'opt':
          if ((parrocchia.indexOf('martin') ==0 && parrocchia.indexOf('aosta') ==0) || hdnIdParrocchia!=1 || parrocchia=="") {
              alert("Attenzione! Questa opzione e' selezionabile \n soltanto se il battesimo e' stato celebrato a Saint-Martin de Corleans"); //seleziona in automatico il campo in parrocchia
              optCertificatoBattesimo[1].checked=true;
          }
      break;
   }
    return;
}

//***************************************************************************************
// conta i caratteri in un dato campo
function ContaCaratteri(campo,stringa,caratteri){
    if (stringa.length > caratteri){
       document.getElementById(campo).value=stringa.substr(0,caratteri);
        
    }
    return stringa;
}

//***************************************************************************************		
// suggest ajax per le parrocchie
function fill_parrocchie(thisValue) {
    if (thisValue != null) {
        var valori = thisValue.split('|');
        $('#hdnIdParrocchia').val(valori[0]);
				$('#parrocchia_battesimo').val(valori[1]);
				setTimeout("$('#suggestions_parrocchie').hide();", 200);
      } 
      AssegnaBattesimo('fill');
      $('#parrocchia_battesimo').focus();
}

//***************************************************************************************		
// suggest ajax per le parrocchie
function fill_parrocchia_padrino(thisValue) {
    if (thisValue != null) {
        var valori = thisValue.split('|');
        $('#hdnIdParrocchiaPadrino').val(valori[0]);
				$('#parrocchia_padrino').val(valori[1]);
				setTimeout("$('#suggestions_parrocchie_padrino').hide();", 200);
      } 
      
      $('#parrocchia_padrino').focus();
}
//***************************************************************************************	 
// autocomplete in ajax per suggerimento parrocchie
function lookup_parrocchie(inputString,campo) {
      if(inputString.length == 0) {
        // Hide the suggestion box.
				$('#suggestions_parrocchie').hide();
        $('#suggestions_parrocchie_padrino').hide();
			} else {
            if (campo!="parrocchia_padrino") {
                $.post("rpparrocchie.php", {queryString: ""+inputString+""}, function(data){
                if(data.length >0 && data.length != 24) {
                    $('#suggestions_parrocchie').show();
						        $('#autoSuggestionsListParrocchie').html(data);
					     } else {
                    $('#suggestions_parrocchie').hide();
              }
				      });
            } else {
                $.post("rpparrocchia_padrino.php", {queryString: ""+inputString+""}, function(data){
                if(data.length >0 && data.length != 24) {
                    $('#suggestions_parrocchie_padrino').show();
						        $('#autoSuggestionsListParrocchiePadrino').html(data);
					     } else {
                    $('#suggestions_parrocchie_padrino').hide();
              }
				      });
            }
			}
} // lookup

//***************************************************************************************
//MODIFICA GRUPPI
function ElaboraGruppi(azione) {
    var gruppo=document.getElementById('lista_gruppi');
    var stringa=new String();
    var modulo=document.getElementById('CercaIscritti');
    
    switch (azione) {
        case 'Aggiungi':
            // controlla che siano stati compilati i campi obbligatori
            if (document.getElementById("data_gruppo").value==null
                      || document.getElementById("data_gruppo").value==""
                      || document.getElementById("ora_gruppo").value==null
                      || document.getElementById("ora_gruppo").value==""
              )
              {
                  alert("Attenzione! Errore di inserimento. I campi 'data' e 'ora' devono essere compilati!");
                  document.getElementById("data_gruppo").focus();
                  return;
              } else {
                    document.getElementById('azione').value="aggiungi_gruppo";
              }
        break;
        
        case 'Modifica':
            // controlla che sia stato selezionato un gruppo
            if (gruppo.selectedIndex < 0) {
              return;
            }
            // recupera i dati del gruppo
            stringa=gruppo.options[gruppo.selectedIndex].textContent;
            // ottiene la data
            document.getElementById('data_gruppo').value=stringa.substr(0,10);
            // ottiene l'ora
            document.getElementById('ora_gruppo').value=stringa.substr(12,5);
            
            // disabilita gli oggetti che non servono
            document.getElementById('agruppo').disabled=true;
            document.getElementById('rgruppo').disabled=true;
            gruppo.disabled=true;
            
            //prepara il bottone per il salvataggio
            document.getElementById('mgruppo').value="Salva";
            
            //setta il fuoco sul campo data
            document.getElementById('data_gruppo').focus();
            return;
        break;
        
        case 'Rimuovi':
            // controlla che sia stato selezionato un gruppo
            if (gruppo.selectedIndex < 0) {
                return;
            }
            // avvisa dei pericoli dell'operazione
            if (!confirm("Attenzione! Sei sicuro di voler rimuovere questo gruppo? \n Premi OK o annulla per continuare.")) {
                return;
            }
            document.getElementById('azione').value="rimuovi_gruppo";
        break;
        
        case 'Salva':
            document.getElementById('azione').value="aggiorna_gruppo";
            gruppo.disabled=false;
        break;
    }
    
    //invia i dati della pagina
    modulo.submit();
        
    return;
}