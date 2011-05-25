/* JavaScript Document
* Oggetto:   Funzioni per pagina Contabilità (xcontabilita.php)
* Creazione: Marzo 2011
* Autore:    Marco Fogliadini 
*/


// riconosce il browser per caricare i relativi fogli di stile    
var lsBrowser= navigator.appName;
switch (lsBrowser) {
  case "Microsoft Internet Explorer":
    document.write("<link href=\"./css/styleanagrafica.css\" rel=\"stylesheet\" type=\"text/css\" />");
  break;
          
  case "Netscape":
    document.write("<link href=\"./css/styleanagrafica_ff.css\" rel=\"stylesheet\" type=\"text/css\" />");
  break;
}

//***************************************************************************************
//Visualizza/nasconde il div del Bilancio
function DivBilancio(azione) {
    var divComandi=document.getElementById("comando_dati");
    var divEC=document.getElementById("EstrattoConto");
    var divBilancio=document.getElementById("div_bilancio");
    var frmBilancio = document.getElementById("frmBilancio");
    
    switch (azione){
      case "apri":
          divComandi.style.visibility="hidden";
          divEC.style.visibility="hidden";
          divBilancio.style.visibility="visible";
          
          var larghezza=290;
          var altezza=380;
          
          // centra il messaggio d'errore sullo schermo
          divBilancio.style.width=larghezza+"px";
          divBilancio.style.height=altezza+"px";
          divBilancio.style.left=(980-larghezza)/2+"px";
          divBilancio.style.top=((520-altezza)/2)+"px";
      break;
      
      case "chiudi":
          divComandi.style.visibility="visible";
          divEC.style.visibility="visible";
          divBilancio.style.visibility="hidden";
      break;
      
      case "elabora":
          frmBilancio.submit();
      break;
    }
    return;
}

//***************************************************************************************
//Cambia la pagina
function ChangePage(nrpagina,nrpagine) {
  /* controlla che l'operatore non vada fuori dall'intervallo
   * di pagine che sono risultate */
  if ((nrpagina < 1) || (nrpagina > nrpagine)) { 
      return;
  }
  
  // assegna il nuovo nr di pagina corrente
  document.getElementById("nrpagina").value = nrpagina;

  // invia i dati a PHP
  document.getElementById("frmFiltraVoci").submit();

  return;
}

//***************************************************************************************
function FiltraContabilita() {
  if (document.getElementById("btnOperazione").value!="Salva") {
    document.getElementById("frmFiltraVoci").submit();
  }
  return;
}

//***************************************************************************************
function InserisciOperazione(azione) {
    
    switch (azione) {
        case "salva":
            // ottiene i vari valori da salvare
            //var selContabilita=document.getElementById("tipo_contabilita");
            var optOperazione=document.getElementsByName("optOperazione");
            var operazione=new String();
            var voce=document.getElementById("voci");
            var importo=document.getElementById("txtImporto");
            var dataOperazione=document.getElementById("data_operazione");
            
           
            
            // CONTROLLA CHE SIANO STATI COMPILATI CORRETTAMENTE I CAMPI
            if (optOperazione[0].checked) { // è stata selezionata un'operazione di entrata
                operazione=optOperazione[0].value;
            }
            
            if (optOperazione[1].checked) { // è stata selezionata un'operazione di uscita
                operazione=optOperazione[1].value;
            }
            
            if (operazione==null || operazione=="") { // nessuna operazione è stata scelta
                StampaMessaggioErrore("<ul><li>Attenzione!<br />Non hai scelto nessuna operazione!<br /><br /></li></ul>",true,1);
                return;
            }
            
            if (voce.selectedIndex < 1){ // nessuna voce è stata selezionata
                StampaMessaggioErrore("<ul><li>Attenzione!<br />Non hai scelto nessuna voce!<br /><br /></li></ul>",true,1);
                return;
            }
            
            if (importo.value==null || importo.value =="") { // nessun importo è stato compilato
                StampaMessaggioErrore("<ul><li>Attenzione!<br />Non hai compilato il campo importo!<br /><br /></li></ul>",true,1);
                return;
            }
            
            if (dataOperazione.value==null || dataOperazione.value==""){ // nessuna data è stata compilata
                StampaMessaggioErrore("<ul><li>Attenzione!<br />Non hai messo nessuna data!<br /><br /></li></ul>",true,1);
                return;
            }
            
            
            if (document.getElementById("btnOperazione").value=="Salva") {
                document.getElementById("myazione").value="AggiornaOperazioneEC";
            } else {
                document.getElementById("myazione").value="SalvaOperazioneEC";
            }
            
        break;
    
    case "modifica":
        // ottiene l'array con le righe da modificare
        righe_selezionate=document.getElementsByName("seleziona_operazione");
        
        // inizializza variabili di servizio
        var riga=0;
        var indice_selezione = new Array();
        
        // controlla quante righe sono state selezionate
        for (index=0;index<righe_selezionate.length;index++) {
            if (righe_selezionate[index].checked) {
                riga++;
                indice_selezione[riga]=index;
            }
        }
        
        // se più di una riga è stata selezionata dà errore
        if (riga>1) {
            StampaMessaggioErrore("<ul><li>Attenzione! Si può modificare<br />soltanto una movimentazione per volta!</li></ul><br />",true,1);
            return;
        }
        
        // controlla che almeno una riga sia stata selezionata
        if (riga==0) {
            StampaMessaggioErrore("<ul><li>Attenzione! Non &egrave; stata selezionata<br />nessuna movimentazione!</li></ul><br />",true,1);
            return;
        }
        
        document.getElementById("mymodificaoperazione").value=righe_selezionate[indice_selezione[riga]].value;
        document.getElementById("myazione").value="ModificaOperazioneEC";
    break;

        case "elimina":
            if (!confirm("Sei sicuro di voler cancellare le movimentazioni selezionate?")) {
                return;
            }
            
            // ottiene l'array con le righe da modificare
            righe_selezionate=document.getElementsByName("seleziona_operazione");
        
            // inizializza variabili di servizio
            var riga=0;
            var indice_selezione = new Array();
            var parametri_toPHP = new String();
            
            // controlla quante righe sono state selezionate
            for (index=0;index<righe_selezionate.length;index++) {
                if (righe_selezionate[index].checked) {
                    riga++;
                    parametri_toPHP+=righe_selezionate[index].value+"|";
                }
            }
            
            // controlla che almeno una riga sia stata selezionata
            if (riga==0) {
                StampaMessaggioErrore("<ul><li>Attenzione! Non &egrave; stata selezionata<br />nessuna movimentazione!</li></ul><br />",true,1);
                return;
            }
            
            document.getElementById("mymodificaoperazione").value=parametri_toPHP
            document.getElementById("myazione").value="EliminaOperazioneEC";
        break;
    }
    
    document.getElementById("frmFiltraVoci").submit();
    return;
}

//***************************************************************************************
// funzione per filtrare le voci e i capitoli
function FiltraVociEC() {
    var selCapitoli = document.getElementById("filtra_capitolo");
    
    if (selCapitoli.selectedIndex >-1) {
        //inizializza il filtro da utilizzare
        document.getElementById("xfiltro").value="FEC";
        
        if (document.getElementById("btnOperazione").value=="Salva"){
            document.getElementById("myazione").value="ModificaOperazioneECEU";
        }
        
    } else {
        document.getElementById("xfiltro").value=null;
        //StampaMessaggioErrore("<ul><li>Attenzione!<br />Non hai selezionato nessun capitolo!</li></ul><br />",true,1);
    }
    
    // deseleziona eventuali voci selezionate
    //document.getElementById("addVoci").selectedIndex=-1;
    
    // invia i dati a PHP
    document.getElementById("frmFiltraVoci").submit();
    return;
}

//***************************************************************************************
// funzione per visualizzare il giusto Div
function DivAperto(stringa) {
    switch (stringa) {
        case "VC":
            VisualizzaDivVoci("apri");
        break;
        
        default:
            VisualizzaDivVoci("chiudi");
            document.getElementById("EstrattoConto").style.visibility="visible";
        break;
    }
    
    return;
}

//***************************************************************************************
// funzione per filtrare le voci e i capitoli
function FiltraVoci() {
    var selCapitoli = document.getElementById("addCapitoli");
    
    if (selCapitoli.selectedIndex >-1) {
        //inizializza il filtro da utilizzare
        document.getElementById("filtrovoce").value="F";
    } else {
        document.getElementById("filtrovoce").value=null;
        //StampaMessaggioErrore("<ul><li>Attenzione!<br />Non hai selezionato nessun capitolo!</li></ul><br />",true,1);
    }
    
    // deseleziona eventuali voci selezionate
    document.getElementById("addVoci").selectedIndex=-1;
    
    // invia i dati a PHP
    document.getElementById("frmVociCapitoli").submit();
    return;
}

//***************************************************************************************
// funzione per modificare le voci e i capitoli
function RimuoviVoceCapitolo(stringa) {
    
    // ottiene il nome del form da trattare
    var frmVociCapitoli=document.getElementById("frmVociCapitoli");
    
    // ottiene la selezione del capitolo e della voce
    var selCapitoli = document.getElementById("addCapitoli");
    var selVoce = document.getElementById("addVoci");
    
    // inizializza l'azione da far svolgere a PHP
    var azione=document.getElementById("azione");

    // controlla se l'utente ha selezionato un capitolo o una voce 
        if (selCapitoli.selectedIndex >-1) {
            if (!confirm("Attenzione! L'eliminazione di un capitolo cancellerà tutte le voci collegate! Vuoi proseguire?")) {
                return;
            } else {
                      azione.value="CancellaCapitoloVoce";
                      frmVociCapitoli.submit();
            }
        } else {
            if (selVoce.selectedIndex >-1) {
                if (!confirm("Attenzione! Sei sicuro di voler cancellare la voce selezionata?")) {
                    return;
                } else {
                      azione.value="CancellaCapitoloVoce";
                      frmVociCapitoli.submit();
                }
            } else {
                StampaMessaggioErrore("<ul><li>Attenzione!<br />Non hai selezionato nessun elemento!<br /><br /></li></ul>",true,1);
            }
        }
    
    return;
}
//***************************************************************************************
// funzione per modificare le voci e i capitoli
function ModificaVociCapitoli(stringa) {
  switch (stringa) {
      case "voce":
          var selCapitoli = document.getElementById("addCapitoli");
          selCapitoli.selectedIndex=-1;
      break;
      
      case "capitolo":
          document.getElementById("filtrovoce").value=null;
      break;
  }
  document.getElementById("divAperto").value="VC"
  document.getElementById("frmVociCapitoli").submit();
  return;
}
//***************************************************************************************
// funzione per controllare l'inserimento corretto della data
function ControlloData(stringa){
    var datadafiltrare=document.getElementById("data_operazione");
    
    switch (stringa) {
      case "focus":
          datadafiltrare.style.color="red";
          datadafiltrare.style.textAlign="left";

          if (datadafiltrare.value=="" || datadafiltrare.value==null) {
              return;
          }
      break;
      
      case "blur":
          if (datadafiltrare.value=="" || datadafiltrare.value==null) {
              return;
          }
          
          var controllo=ControlloDataInserita(datadafiltrare,"");
          
          if (!controllo) {
              datadafiltrare.style.color="red";
              datadafiltrare.style.textAlign="left";
          } else {
              datadafiltrare.style.textAlign="right";
              datadafiltrare.style.color="green";
          }
      break;
  }


    return;
}

//***************************************************************************************
// funzione per controllare l'inserimento corretto della data
function ControlloDataInserita(datadafiltrare,dt) {

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

switch (datadafiltrare.value.length) { //analizza il formato della data e prepara le variabili gg, mm, yy
    case is = 6: //formato data breve
        gg = datadafiltrare.value.slice(0,2);
        mm = datadafiltrare.value.slice(2,4);
        yy = datadafiltrare.value.slice(4,6);
    break;    
 
    case is = 8: //formato data a 8 caratteri
        if (datadafiltrare.value.indexOf("/") >= 0 || datadafiltrare.value.indexOf("-")>=0) {  //con separatori
           gg = datadafiltrare.value.slice(0,2);
           mm = datadafiltrare.value.slice(3,5);
           yy = datadafiltrare.value.slice(6,8);
        } else { //data a 8 cifre senza separatori
           gg = datadafiltrare.value.slice(0,2);
           mm = datadafiltrare.value.slice(2,4);
           yy = datadafiltrare.value.slice(4,8);
        }
    break;
        
    case is = 10: //formato data completa
        gg = datadafiltrare.value.slice(0,2);
        mm = datadafiltrare.value.slice(3,5);
        yy = datadafiltrare.value.slice(6,10);
    break;

    default: //negli altri formati inseriti la routine genera un errore
        StampaMessaggioErrore("<ul><li>Attenzione! La data che hai inserito e' errata.<br />Sono validi i seguenti formati: <br /><ul><li>ggmmaa</li><li>gg/mm/aa</li><li>ggmmaaaa</li><li>gg/mm/aaaa</li></ul></li></ul>",true,2);
        datadafiltrare.style.color="red";
        datadafiltrare.style.textAlign="left";
        return false;
    break;
}

//controlla che i dati di riferimento al giorno, al mese e all'anno siano dati numerici 
if (isNaN(gg) || isNaN(mm) || isNaN(yy)) {
        StampaMessaggioErrore("<ul><li>Attenzione! La data che hai inserito e' errata.</li></ul><br />",true,1);
        datadafiltrare.style.color="red";
        datadafiltrare.style.textAlign="left";
        return false;
}

//controlla che siano stati inseriti giusti tutti gli elementi che compongono la data
if (Number(gg) <= 0 || Number(mm) <= 0 || Number(mm) > 12 || Number(yy) < 0) {                     
        StampaMessaggioErrore("<ul><li>Attenzione! La data che hai inserito e' errata.</li></ul><br />",true,1);
        datadafiltrare.style.color="red";
        datadafiltrare.style.textAlign="left";
        return false;
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
        StampaMessaggioErrore("<ul><li>Attenzione! L'anno che hai inserito e' errato!</li></ul><br />",true,1);
        datadafiltrare.style.color="red";
        datadafiltrare.style.textAlign="left";
        return false;
    }
}
// controlla che l'anno inserito non sia bisestile e assegna il valore alla variabile indice
if ((Number(yy) % 4) == 0) {
	indice=0;
} else {
	indice=Number(mm);
}

// controlla che l'utente non abbia inserito un numero giorni del mese errato
if (Number(gg) > nrgiorni[indice]) {	
    StampaMessaggioErrore("<ul><li>Attenzione! La data che hai inserito e' errata.</li></ul><br />",true,1);
    datadafiltrare.style.color="red";
    datadafiltrare.style.textAlign="left";
    return false;
}

// ricompone e stampa la data filtrata
datadafiltrare.value=gg+"/"+mm+"/"+yy;
datadafiltrare.style.color="green";
//datadafiltrare.style.color="#000088";
//datadafiltrare.style.background="white";
return datadafiltrare.value;
}


//***************************************************************************************
// funzione controllare textbox importo
function ControlloImporto(stringa) {
  var importo = document.getElementById("txtImporto");
  var pattern =/^([0-9\,])+$/;
  var importofiltrato=new String();

  switch (stringa) {
      case "focus":
          importo.style.color="red";
          importo.style.textAlign="left";
         
          if (importo.value.match(/[.]/)) {
              for (i=0;i<importo.value.length;i++) {
                  if (importo.value.charAt(i)!=".") {
                      importofiltrato+=importo.value.charAt(i);
                  }
              }
              importo.value=importofiltrato;
          }
      break;
      
      case "blur":
          if (importo.value=="" || importo.value==null) {
              return;
          }
          
          if (!pattern.test(importo.value)) {
              if (importo.value.match(/[.]/)) {
                  StampaMessaggioErrore("<ul><li>Attenzione!<br />Per indicare i centesimi di euro<br />non utilizzare il punto, ma la virgola.</li></ul>",true,1)
              } else {              
                  StampaMessaggioErrore("<ul><li>Attenzione!<br />Il valore che hai inserito non &egrave; corretto.<br />Questo campo accetta soltanto numeri.</li></ul>",true,1)
              }
          } else {
              if (importo.value.indexOf(".")<0) {
                  var controllo=FormattaValuta(importo.value);
                  if (!controllo) {
                      importo.style.color="red";
                      importo.style.textAlign="left";
                  } else {
                      importo.value=controllo;
                      importo.style.color="green";
                      importo.style.textAlign="right";
                  }
              } else {
                  if (importo.value.indexOf(".")!=(importo.value.length-(importo.value.length-importo.value.indexOf(".")))) {
                      for (i=0;i<importo.value.length;i++) {
                          if (importo.value.charAt(i)!=".") {
                              importofiltrato+=importo.value.charAt(i);
                          }
                      }
                  } else {
                      importofiltrato=importo.value
                  }
                  var controllo=FormattaValuta(importo.value);
                  if (!controllo) {
                      importo.style.color="red";
                      importo.style.textAlign="left";
                  } else {
                      importo.value=controllo;
                      importo.style.color="green";
                      importo.style.textAlign="right";
                  }
              }
          }
      break;
  }
  
  return;
}

//***************************************************************************************
// funzione per mandare a php il criterio per filtrare le voci di contabilità
function xFiltraVoci(index) {
  frmFiltraVoce=document.getElementById("frmFiltraVoci");
  filtro=document.getElementById("xfiltro");
  
  optOperazione=document.getElementsByName("optOperazione");
  
  document.getElementById("mydivAperto").value=null;
  document.getElementById("filtra_capitolo").selectedIndex=0;
  
  if (document.getElementById("btnOperazione").value=="Salva"){
      document.getElementById("myazione").value="ModificaOperazioneECEU";
  }
  
  filtro.value=optOperazione[index].value;
  
  frmFiltraVoce.submit();

  return;
}

//***************************************************************************************
// funzione per gestire la visualizzazione del div gestione voci e capitoli di contabilità
function VisualizzaDivVoci(stringa){
  
  switch (stringa) {
    case 'apri':
        document.getElementById("voci_capitoli").style.visibility="visible";
        
        // abilita l'apertura del div voci capitoli al caricamento di una nuova pagina
        document.getElementById("divAperto").value="VC";
        
        // disabilita i comandi della barra comandi
        var contabilita=document.getElementById("tipo_contabilita").disabled=true;
        var operazione=document.getElementsByName("optOperazione");
        operazione[0].disabled=true;
        operazione[1].disabled=true;
        var filtro_capitolo=document.getElementById("filtra_capitolo").disabled=true;
        var voce=document.getElementById("voci").disabled=true;
        var importo=document.getElementById("txtImporto").disabled=true;
        document.getElementById("txtImporto").style.color="grey";
        var data_operazione=document.getElementById("data_operazione").disabled=true;
        document.getElementById("data_operazione").style.color="grey";
        
        var btnOperazione=document.getElementById("btnOperazione").disabled=true;
        var btnModifica=document.getElementById("btnModifica").disabled=true;
        var btnCancella=document.getElementById("btnCancella").disabled=true;
        
        var btnVoci=document.getElementById("btnVoci").disabled=true;
        
        // setta il fuoco sulla txtbox capitolo
        document.getElementById("txtCapitolo").focus();
  
    break;
    
    case 'chiudi':
        document.getElementById("voci_capitoli").style.visibility="hidden";
        
        // abilita i comandi della barra comandi
        var contabilita=document.getElementById("tipo_contabilita").disabled=false;
        var operazione=document.getElementsByName("optOperazione");
        operazione[0].disabled=false;
        operazione[1].disabled=false;
        var filtro_capitolo=document.getElementById("filtra_capitolo").disabled=false;
        var voce=document.getElementById("voci").disabled=false;
        var importo=document.getElementById("txtImporto").disabled=false;
        document.getElementById("txtImporto").style.color="green";
        var data_operazione=document.getElementById("data_operazione").disabled=false;
        document.getElementById("data_operazione").style.color="green";
        var btnOperazione=document.getElementById("btnOperazione").disabled=false;
        var btnModifica=document.getElementById("btnModifica").disabled=false;
        var btnCancella=document.getElementById("btnCancella").disabled=false;
        var btnVoci=document.getElementById("btnVoci").disabled=false;
        
        // disabilita l'apertura del div voci capitoli al caricamento di una nuova pagina
        document.getElementById("divAperto").value=null;
    break;
  }
  
  
  return;
}

//***************************************************************************************
// funzione per controllare textbox capitolo
function ControlloCapitolo(stringa) {
    var campo=document.getElementById("txtCapitolo");
    
    switch (stringa) {
        case 'focus':
            campo.style.color="red";
            campo.style.textAlign="left";
        break;
    
        case 'blur':
            if (campo.value !="") {
                campo.value=FiltroStringa(campo.value);
                campo.style.color="blue";
                campo.style.textAlign="right";
            }
        break;
    }
    return;
}

//***************************************************************************************
// funzione per controllare textbox sigla
function ControlloSigla(stringa) {
    var campo=document.getElementById("txtSigla");
    
    switch (stringa) {
        case 'focus':
            campo.style.color="red";
            campo.style.textAlign="left";
        break;
    
        case 'blur':
            if (campo.value !="") {
                campo.value=campo.value.toUpperCase();
                campo.style.color="blue";
                campo.style.textAlign="right";
            }
        break;
    }

    return;
}

//***************************************************************************************
// funzione per controllare textbox voce
function ControlloVoce(stringa) {
    var campo=document.getElementById("txtVoce");
    
    switch (stringa) {
        case 'focus':
            campo.style.color="red";
            campo.style.textAlign="left";
        break;
    
         case 'blur':
            if (campo.value !="") {
                campo.value=FiltroStringa(campo.value);
                campo.style.color="blue";
                campo.style.textAlign="right";
            }
        break;
    }

    return;
}

//***************************************************************************************
// funzione per filtrare i dati inseriti dall'utente (capitolo,voce)
function FiltroStringa(stringa) {
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
                              || vecchiocarattere.charCodeAt(0)==45)  // trattino -
            {
                nuovastringa += carattere.toUpperCase();
            } else {
                nuovastringa += carattere.toLowerCase();
            }
            vecchiocarattere = carattere;
        }
    }
    stringa=nuovastringa;
    return stringa;
}

//***************************************************************************************
// funzione per filtrare i dati inseriti dall'utente (capitolo,voce)
function SalvaVociCapitoli(stringa) {
    // ottiene il nome del form da trattare
    var frmVociCapitoli=document.getElementById("frmVociCapitoli");

    // inizializza l'azione da far svolgere a PHP
    var azione=document.getElementById("azione");  

    //ottiene i valori inseriti dall'utente
    var capitolo = document.getElementById("txtCapitolo");
    var sigla = document.getElementById("txtSigla");
    var voce = document.getElementById("txtVoce");
    var operazione = document.getElementsByName("selEntrataUscita");
    
    var selCapitoli = document.getElementById("addCapitoli");
    var optCapitoli = document.getElementsByName("optCapitoli");
    
    // questa variabile serve per riassumere in un'unica stringa la tipologia di voce
    // (se è solo entrata, solo uscita o entrambe)
    // è un campo hidden nella pagina html
    var chkOperazione =document.getElementById("chkOperazione");
    
    // resetta a null chkOperazione
    chkOperazione.value=null;
    
    // inizializza l'ok all'invio dei dati a PHP
    var DatiOk = false;
    
    // inizializza la variabile per i messaggi d'errore
    var messaggio="<ul>";
    
    // inizializza la variabile nr d'errori commessi
    var nr_errori =0; 
    
    // controlla se deve aggiungere un capitolo
    if (capitolo.value!="") {
        DatiOk = true;
        myAction="ModificaCapitolo";
        
        //controlla se l'utente ha compilato il campo sigla
        if (sigla.value=="") {
            DatiOk = false;
            messaggio+="<li>Non hai compilato il campo sigla...</li><br /><br />"
            nr_errori++;
        } else {
            if (stringa!="modifica") {
               for (index=0;index<selCapitoli.length;index++) {
                   if (optCapitoli[index].textContent.slice(0,3)==sigla.value) {
                      messaggio+="<li>Attenzione! La sigla che hai inserito<br />esiste gi&agrave; nell'archivio capitoli...</li><br />";
                      nr_errori++;
                      DatiOk=false
                      break;
                  } 
                }
            }
        }
        
    } else { // altrimenti controlla se deve inserire una voce
        // controlla se l'utente ha compilato il campo voce
        if (voce.value!="") {
            DatiOk = true;
            myAction="ModificaVoce";
           
            //controlla se l'utente ha associato la voce a un capitolo
            if (selCapitoli.selectedIndex < 0) {
                DatiOk = false;
                messaggio+="<li>Non hai associato nessun capitolo<br />alla voce che vuoi inserire...</li><br />"
                nr_errori++;
            } 
                
            // controlla se l'utente ha scelto almeno una tipologia di voce (entrata,uscita,entrata-uscita)
            var operazione_selezionata=0;
            
            for (i=0;i<operazione.length;i++) {
                if (operazione[i].checked==true){
                    chkOperazione.value+=operazione[i].value
                    operazione_selezionata++;
                }
            }
            
            if (operazione_selezionata==0){
                DatiOk = false;
                messaggio+="<li>Non hai specificato se la voce che vuoi inserire<br />appartiene alle entrate o alle uscite...</li><br />";
                nr_errori++;
            } else {
                
            }
        } else {
              DatiOk=false;
              //controlla se l'utente ha compilato solo il campo sigla
              if (sigla.value!="") {
                  messaggio+="<li>Attenzione! La sigla che hai inserito <br />non &egrave; associata a nessun capitolo!</li><br />"
                  nr_errori++;
              } else {
                  messaggio+="<li>Non &egrave; stato compilato nessun campo!!!</li><br /><br />";
                  nr_errori++;
              }    
        }
    }
    
    messaggio+="</ul>";
    
    if (!DatiOk) { 
        StampaMessaggioErrore(messaggio,true,nr_errori);
    } else {
        switch (stringa) {
            case "aggiungi":
                azione.value="addVoceCapitolo";
            break;
            
            case "modifica":
                azione.value=myAction;
            break;
        
        }
        
        frmVociCapitoli.submit();
    }
    
    return;
}

// ****************** > FUNZIONE STAMPA AVVISI E ERRORI < *************************
function StampaMessaggioErrore(messaggio,visibile,nr_errori) {
    var div_errori=document.getElementById("stampamessaggio");
    var cella_messaggio=document.getElementById("messaggio_errore");
    
    switch (visibile) {
        case false:
            div_errori.style.visibility="hidden";
        break;
    
        case true:
            var larghezza_div_errori=400;
            
            // modifica l'altezza in base al numero di errori
            switch (nr_errori) {
                case 1:
                    var altezza_div_errori=170;
                    messaggio+="<br />";
                break;
                
                case 2:
                    var altezza_div_errori=190;
                break;
            }
    
            // centra il bottone ok nel div
             var chiudi_messaggio=document.getElementById("ChiudiMessaggio");
             chiudi_messaggio.style.left=(larghezza_div_errori-80)/2+"px";
    
            // centra il messaggio d'errore sullo schermo
            div_errori.style.width=larghezza_div_errori+"px";
            div_errori.style.height=altezza_div_errori+"px";
            div_errori.style.left=(980-larghezza_div_errori)/2+"px";
            div_errori.style.top=((520-altezza_div_errori)/2-30)+"px";
    
            cella_messaggio.innerHTML=messaggio;
    
            div_errori.style.visibility="visible";
    
        break;
    }
    
    return;
}

/*******************************************************************************************************************/
// per una migliore lettura del numero formatta il valore della valuta con il punto delle migliaia
function FormattaValuta(valore) {
    // variabili di servizio
    var somma_formattata=new String(); // inizializza la variabile come stringa
    
    // sostituisce un'eventuale virgola con il punto
    if (valore.match(/[,]/)) {
        valore=valore.replace(/[,]/,".");
        valore=Number(valore.replace(/[,]/,"."));
        if (isNaN(valore)) {
            StampaMessaggioErrore("<ul><li>Attenzione!<br />Il numero inserito non &egrave; corretto!</ul></li><br />",true,1);
            return false;
        }
    } else {
        valore=Number(valore);
        if (isNaN(valore)) {
            StampaMessaggioErrore("<ul><li>Attenzione!<br />Il numero inserito non &egrave; corretto!</ul></li><br />",true,1);
            return false;
        }
    }
   
    // controlla se il valore da formattare è un numero con i decimali
    var mantissa=parseFloat(valore-parseInt(valore));
    if (mantissa > 0) {
        var somma_da_formattare= new String(parseInt(valore)); // crea una nuova stringa con la variabile intera di valore
    } else {
        var somma_da_formattare= new String(valore); // crea una nuova stringa con la variabile valore
    }
    
    // stabilisce quanti punti bisogna inserire nella somma da formattare
    if (somma_da_formattare.length % 3) { // controlla se il risultato della divisione tra la lunghezza della stringa e 3 (tripletta) dà resto 
        var punti=parseInt(somma_da_formattare.length/3); // l'intero della divisione tra la lunghezza della stringa e 3 dà il numero dei punti da inserire
    } else {
        var punti=((parseInt(somma_da_formattare.length/3))-1); // se la divisione non dà resto toglie un punto. Siamo nel caso di multipli di 3
    }
    
    // costruisce la nuova stringa in base ai punti calcolati
    for (indice = 1;indice <= punti;indice++){
        somma_formattata="."+somma_da_formattare.substr(somma_da_formattare.length-(3*indice),3)+somma_formattata;
    }    
    
    // completa la somma formattata con le eventuali cifre rimaste fuori dalle triplette
    somma_formattata=somma_da_formattare.substr(0,(somma_da_formattare.length-(somma_formattata.length-punti)))+somma_formattata;
    
    // aggiunge l'eventuale mantissa tralasciata per formattare correttamente il valore
    if (mantissa > 0) {
        // prende solo due cifre dopo lo zero 
        mantissa*=100; 
        mantissa=mantissa.toFixed(0);
        
        // aggiunge la mantissa alla somma formattata
        if (mantissa < 10) {
            somma_formattata+=",0"+mantissa;
        } else {
            somma_formattata+=","+mantissa;
        }
    } /*else {
        somma_formattata+=",00";
    }*/
    
    return somma_formattata; // ritorna il valore formattato
}