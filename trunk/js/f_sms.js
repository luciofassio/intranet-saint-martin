// * JavaScript Document
// * FUNZIONI PER PAGINA GESTIONE SMS
// * INIZIO SVILUPPO: 16 GENNAIO 2012

function Abilita(campo) {
  var ruoli=document.getElementsByName("chkRuolo");
  var classi=document.getElementsByName("chkClasse");
  var gruppo=document.getElementsByName("chkGruppo");
  
  var sel =0;
  var sigla_ruolo;
  var disabilita_classi;
  
  for (i=0;i<ruoli.length;i++) {
      if (ruoli[i].checked) {
          sigla_ruolo=ruoli[i].value;
          if ((sigla_ruolo==6) || (sigla_ruolo==7) || (sigla_ruolo==9)) {
              disabilita_classi=true;
          }  else {
              //disabilita_classi=false;
              sel++;
          }   
      }
  }
  /* se sono stati scelti i campi addetto segreteria, securitas o direttivo (o tutti e tre)
     non permette di selezionare le classi e il gruppo. Questi ruoli non sono associati ad alcuna
     classe o gruppo. 
  */

  if (disabilita_classi) {
    for (i=0;i<classi.length;i++){
        classi[i].checked=false;
        classi[i].disabled=true;
        
    }
  
    for (i=0;i<gruppo.length;i++){
        gruppo[i].checked=false;
        gruppo[i].disabled=true;
    }
    document.getElementById("chkTutteLeClassi").disabled=true;
    document.getElementById("chkTutteLeClassi").checked=false;
  } else {
    for (i=0;i<classi.length;i++){
        classi[i].disabled=false;
    }
  
    for (i=0;i<gruppo.length;i++){
        gruppo[i].disabled=false;
    }
    
     document.getElementById("chkTutteLeClassi").disabled=false;
  }

// se nessun ruolo è stato selezionato viene disabilitato il campo chkDellaClasse
  if (sel==0){ 
      document.getElementById(campo).disabled=true;
      document.getElementById(campo).checked=false;
      
  } else {
      document.getElementById(campo).disabled=false;
  }
  
  return;
}
//*****************************************************
// SELEZIONA CON CLICK TUTTE LE CLASSI
function selTutteLeClassi () {
var AllClass =document.getElementsByName('chkClasse');
var ClassFlag=document.getElementById('chkTutteLeClassi');
var elementi=AllClass.length;

for (i=0;i<elementi;i++) {
    if (ClassFlag.checked) {
        AllClass[i].checked=true;
        AllClass[i].disabled=true
    } else {
        AllClass[i].checked=false;
        AllClass[i].disabled=false;
    }
}
return;
}  
//*************************************************************
// ELABORA LE SCELTE FATTE DALL'UTENTE PER MANDARE I DATI A PHP
function Elabora() {
var frmCriteri=document.getElementById("frmCriteri");
var classi=document.getElementsByName("chkClasse");
var gruppi=document.getElementsByName("chkGruppo");
var ruoli= document.getElementsByName("chkRuolo");
var tesseramento=document.getElementsByName("chkTesseramento");
var partecipazione=document.getElementsByName("chkPartecipazione");
var altro=document.getElementsByName("chkAltro");

// campi nascosti per passaggio dati a php
var hdnClassi=document.getElementById("hdnClassi");
var hdnGruppi=document.getElementById("hdnGruppi");
var hdnRuoli=document.getElementById("hdnRuoli");
var hdnTesseramenti=document.getElementById("hdnTesseramenti");
var hdnPartecipazione=document.getElementById("hdnPartecipazione");
var hdnAltro=document.getElementById("hdnAltro");
var azione=document.getElementById("azione");

var tutteleclassi=document.getElementById("chkTutteLeClassi");
var dellaclasse=document.getElementById("chkDellaClasse")

var elementi=0; // variabile di servizio: contiene il numero di elementi dei vari array
var stringa=new String(); // variabile di servizio
var checked=0;

//*****************************************
// controlla quali CLASSI sono state scelte
if (!tutteleclassi.checked) { // se non sono state scelte tutte le classi
    elementi=classi.length;
    for (i=0;i<elementi;i++){
        if (classi[i].checked){
            stringa+=classi[i].value+"|";
        }
    }
}

if (stringa=="" || stringa==null) {
    hdnClassi.value="";
} else {
    hdnClassi.value=stringa.substr(0,stringa.length-1);
}
//*****************************************
// controlla quali GRUPPI sono stati scelti
elementi=gruppi.length;
stringa="";

for (i=0;i<elementi;i++){
    if (gruppi[i].checked){
        stringa+=gruppi[i].value+"|";
    }
}

if (stringa=="" || stringa==null) {
   hdnGruppi.value="";
} else {
    hdnGruppi.value=stringa.substr(0,stringa.length-1);
}

//****************************************
// controlla quali RUOLI sono stati scelti
elementi=ruoli.length;
stringa="";
checked=0;
    
for (i=0;i<elementi;i++){
    if (ruoli[i].checked){
        checked++;
        stringa+=ruoli[i].value+"|";
    }
}

if (checked==0) {
    hdnRuoli.value="";
} else {
    hdnRuoli.value=stringa.substr(0,stringa.length-1);
}
    
//***********************************************
// controlla quali TESSERAMENTI sono stati scelti
elementi=tesseramento.length;
stringa="";
checked=0;

for (i=0;i<elementi;i++){
    if (tesseramento[i].checked){
        stringa+=tesseramento[i].value+"|";
        checked++;
    } 
}

if (checked==0) {
   hdnTesseramenti.value="";
} else {
    hdnTesseramenti.value=stringa.substr(0,stringa.length-1);
}

//***********************************************
// controlla quali PARTECIPAZIONE è stata scelta
elementi=partecipazione.length;
stringa="";
checked=0;

for (i=0;i<elementi;i++){
    if (partecipazione[i].checked){
        stringa+=partecipazione[i].value+"|";
        checked++;
    }
}

if (checked==0) {
   hdnPartecipazione.value="";
} else {
    hdnPartecipazione.value=stringa.substr(0,stringa.length-1);
}

//**************************************************
// controlla se è stato selezionato il flag del coro
if (altro[0].checked) {
    hdnAltro.value=true; // 
}

azione.value="elabora";
frmCriteri.action="xsms.php?s=3";


return;
}


//******************************************************
// controlla quanti caratteri ha il messaggio da inviare
function ControllaNrCaratteri(messaggio,campo) {
var controllo_mex =document.getElementById(campo);

// controlla se i caratteri dei campi da controllare sono regolari
switch (campo) {
    case "from":
        var pattern=/^([A-Za-z]{1,11})$/;
        if (pattern.test(messaggio)) {
            var testo_ok=true;
        } else {
              pattern=/^([0-9\+]{13,16})$/;
              if (pattern.test(messaggio)) {
                  var testo_ok=true;
              } else {
                  var testo_ok=false;
              }
        }
    break;
    default:
        if (!messaggio.match(/\S/)){
            var testo_ok=false;
        } else {
            var pattern=/^([a-zA-Z0-9_\x28\x29\xE0\xE8\xE9\xEC\xF2\xF9\\\s\/\=\@\"\'\.\,\:\;\?\!\-\+]{1,160})$/;
            if (pattern.test(messaggio)) {
                var testo_ok=true;
            } else {
                var testo_ok=false;
            }
        }
    break;
}

// cambia colori, calcola caratteri, abilita/disabilita pulsanti
if (testo_ok) {
    switch (campo) {
        case "from":
            var txtFrom=document.getElementById(campo);
            var btnInviaGateway=document.getElementById("btnInviaGateway");
            txtFrom.style.color="green";
            btnInviaGateway.disabled=false;
        break;
        
        case "sms_text2":
            var btnFineModifica=document.getElementById("btnModificaTesto");
            controllo_mex.style.color="green";
            btnFineModifica.disabled=false;
        break;    
        
        default:
            var caratteri_mex=document.getElementById("nr_caratteri_sms");
            var caratteri_mex_rimanenti=document.getElementById("nr_caratteri_rimanenti");
            var btnSubmit=document.getElementById("btnSubmit");

            
            controllo_mex.setAttribute('style','color:green');
            caratteri_mex.setAttribute('style','background:green');
            caratteri_mex_rimanenti.setAttribute('style','background:green');
            controllo_mex.style.border='1px dotted green';
            
            btnSubmit.disabled=false; 
            
            caratteri_mex.value=messaggio.length;
            caratteri_mex_rimanenti.value=(160-messaggio.length);
        break;
    }
} else {
      switch (campo) {
          case "from":
            var txtFrom=document.getElementById(campo);
            var btnInviaGateway=document.getElementById("btnInviaGateway");
            txtFrom.style.color="red";
            btnInviaGateway.disabled=true;
          break;
          
          case "sms_text2":
            var btnFineModifica=document.getElementById("btnModificaTesto");
            controllo_mex.style.color="red";
            btnFineModifica.disabled=true;
          break;    
        
          default:
              var caratteri_mex=document.getElementById("nr_caratteri_sms");
              var caratteri_mex_rimanenti=document.getElementById("nr_caratteri_rimanenti");
              var btnSubmit=document.getElementById("btnSubmit");
              
              
              controllo_mex.setAttribute('style','color:red');
              caratteri_mex.setAttribute('style','background:red');
              caratteri_mex_rimanenti.setAttribute('style','background:red');
              controllo_mex.style.border="1px dotted red";
              
              btnSubmit.disabled=true;  
              
              caratteri_mex.value=messaggio.length;
              caratteri_mex_rimanenti.value=(160-messaggio.length);
        break;
    }
}

return;
}

//******************************************************
// controlla quanti cellulari sono stati selezionati per l'invio
function ContaSelezionati() {
var selezionati=0;
var lista=document.getElementById("lstListaTelefoni");
var status_invio=document.getElementById("status_invio");
var btnDeseleziona=document.getElementById("btnDeseleziona");
var messaggio=new String();
var in_lista_spedizione=document.getElementById("in_lista");
var selezionati =0;

for (i=0;i<lista.length;i++) {
    if (lista.options[i].selected) {
        selezionati++;
    }
}

if (selezionati > 0 || lista.length!=0) {
    if (selezionati<lista.length && selezionati!=0) {
        // indica il nr di cellulari ai quali inviare l'SMS
        in_lista_spedizione.textContent=selezionati;
        status_invio.textContent="Selezione";
        btnDeseleziona.disabled=false;
    } else {
        in_lista_spedizione.textContent=lista.length;
        status_invio.textContent="Invia a tutta la lista";
        if (selezionati==0) btnDeseleziona.disabled=true;
    }
} else {
    in_lista_spedizione.textContent=lista.length;
    status_invio.textContent="Lista vuota";
    btnDeseleziona.disabled=true;
}

return;
}

//******************************************************
// Elabora la lista dei numeri di cellulare trovati
// per l'invio del messaggio
function PreparaListaDestinatari() {
    var lista=document.getElementById("lstListaTelefoni");
    var destinatari=new String();
    var selezionati =0;
    
    // controlla quali numeri sono stati selezionati e prepara la stringa da inviare a PHP
    for (i=0;i<lista.length;i++) {
        if (lista.options[i].selected) {
            destinatari+=lista.options[i].value+"|";
            selezionati++;
        }
    }

    // se nessun numero è stato selezionato si manda a tutta la lista dei numeri trovati
    if (selezionati<1) {
        for (i=0;i<lista.length;i++) {
            destinatari+=lista.options[i].value+"|";
        }
    }
    
    document.getElementById("hdnListaDestinatari").value=destinatari.substr(0,destinatari.length-1);
    return;    
}

//******************************************************
// function per controllare il tipo di spedizione e visualizzare/nascondere i vari divs
function Spedizione(tipo) {
  var frmCriteri=document.getElementById('frmCriteri');
  var azione=document.getElementById("azione");
  
  switch (tipo) {
      case "menu_invia_sms":
          frmCriteri.action="xsms.php?s=1";
          azione.value="parametri";
          document.getElementById("hdnCreditoMessaggi").value=document.getElementById("tdMessaggi").textContent;
      break;
      
      case "riepilogo_sms":
          frmCriteri.action="xsms.php?s=4";
          azione.value="riepilogo";
          PreparaListaDestinatari();
      break;
      
      case "annulla_spedizione":
        if (confirm("Vuoi annullare la spedizione e tornare alla ricerca?")) {
            clearInterval(p);
            frmCriteri.action="xsms.php?s=1";
            azione.value="parametri";
        } else {
            return;
        }
      break;
      
      case "togateway":
          frmCriteri.action="xsms.php?s=5";
          azione.value="togateway";
          document.getElementById('sms_text2').disabled=false;
      break;
      
      default:
          frmCriteri.action="xsms.php?s=0";
          azione.value="menu";
      break;
  }
  
  frmCriteri.submit();

return;
}

//******************************************************
//serve per deselezionare in un colpo unico la lista dei numeri trovati 
//a cui inviare l'SMS
function DeselezionaLista(){
    var lista=document.getElementById("lstListaTelefoni");
    
    if (lista.length==0) return;
    
    for  (i=0;i<lista.length;i++) {
        if (lista.options[i].selected) {
          lista.options[i].selected=false;
        }
    }
    ContaSelezionati();
    return;

}

//******************************************************
//serve per deselezionare in un colpo unico la lista dei numeri trovati 
//a cui inviare l'SMS
function ModificaTesto() {
    var testo=document.getElementById("sms_text2");
    var btnModificaTesto=document.getElementById("btnModificaTesto");
    var btnInviaGateway=document.getElementById("btnInviaGateway");
    
    switch (btnModificaTesto.value) {
        case "Modifica testo":
          // abilita il testo
          testo.disabled=false;
    
          //sposta il puntatore sul testo
          testo.focus();
    
          // cambia il valore del pulsante   
          btnModificaTesto.value="Fine modifica";
          
          // disabilita il pulsante di invia messaggio
          btnInviaGateway.disabled=true;
        break;
    
        case "Fine modifica":
          // abilita il testo
          testo.disabled=true;
    
          // cambia il valore del pulsante   
          btnModificaTesto.value="Modifica testo";
          
          // disabilita il pulsante di invia messaggio
          btnInviaGateway.disabled=false;
          
          btnInviaGateway.focus();
        break;
    }

    return;
}



//******************************************************
//serve per fare il giochetto delle scritte lampeggianti...
function CambiaColore(selezionati) {
  var testo=document.getElementById("status_invio");
        
        if (testo.style.color=="white") {
            testo.style.color="red";  
        } else {
            testo.style.color="white";  
        }
return;
}