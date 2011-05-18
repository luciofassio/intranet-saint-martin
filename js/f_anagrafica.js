/* JavaScript Document
 * Oggetto:   Funzioni per pagina web Anagrafica 
 * Creazione: Marzo 2008
 * Modifiche: Novembre 2009;
 *            Agosto-settembre 2010;
 *            Marzo 2011;
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
// funzione per settare il fuoco su cerca iscritti e visualizzare i giusti divs
function CaricamentoTab() {
      
      // se nessun id dell'iscritto è presente visualizza la sezione ricerca
      if (document.getElementById("hdnID").value=="" || document.getElementById("hdnID").value==null) {
          document.getElementById("campi_ricerca").style.visibility="visible";
          document.CercaIscritti.txtCognome.focus();
      } else { 
          document.getElementById("campi_ricerca").style.visibility="hidden";
          document.getElementById("tabella_funzioni").style.visibility="visible"; // è la tabella per le funzioni dell'anagrafica (dati anagrafici, rubrica, ecc.)
          document.getElementById("div_dati_funzioni").style.visibility="visible"; // sono le info dell'iscritto a dx dello schermo
          var tabattivo=document.getElementsByName("tab_attivo"); //legge il valore del tab che è stato attivato
          CambiaTab(tabattivo[1].value);
      }
      
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
// funzione per pulsante nuovo iscritto
function btnNuovoIscritto() {
    document.getElementById("campi_ricerca").style.visibility="hidden";
    document.getElementById("tabella_funzioni").style.visibility="visible";
    document.getElementById("myDatiAnagrafici").style.visibility="visible";
    document.getElementById("div_dati_funzioni").style.visibility="visible";
    document.getElementById("pulsantiera").style.visibility="visible";
    document.getElementById("myprivacy").disabled="disabled";
          
    document.getElementById("hdnID").value=null;
    document.getElementById("hdnIDParente").value=null;
    document.getElementById("dati_anagrafici").value=0;
    document.getElementById("cognome").focus();

    return;     
}

 //***************************************************************************************    
// funzione che serve per modificare il colore delle celle
// della tabella funzioni in base alla selezione dell'utente
// e rende visibile i "div" con i campi relativi
function CambiaTab (cellanumero)
    {
        var tabname = new Array()
            tabname[0]="myDatiAnagrafici";
            tabname[1]="rubrica_telefono";
            tabname[2]="gestione_parentela";
            tabname[3]="myTesseramenti";
            tabname[4]="classecatechismo";
            tabname[5]="ruolo";
            
        // legge il numero di celle della tabella presente in pagina
        var nrcelle = document.getElementsByTagName("td"); 
        
        //setta il tab attivo
        document.getElementById("dati_anagrafici").value=cellanumero;
        document.getElementById("rubrica_telefonica").value=cellanumero;
        document.getElementById("mygestioneparentela").value=cellanumero;
        document.getElementById("mygestioneruoli").value=cellanumero;
          
        // in base al numero di celle e della scelta dell'utente visualizza/nasconde i div
        for (i=0; i < nrcelle.length; i++) { 
            if (i==cellanumero) {
                nrcelle[i].style.background= "green";
                document.getElementById(tabname[i]).style.visibility="visible";
              
                switch (i) { // serve per aprire la giusta pulsantiera all'interno dei vari tab
                    case 1: //pulsantiera rubrica telefonica
                        document.getElementById("pulsantiera1").style.visibility="visible";
                        //document.getElementById("prefisso_naz").focus();
                    break;
                  
                    case 2: //pulsantiera x gestione parentela
                        document.getElementById("pulsantiera2").style.visibility="visible";
                        document.getElementById("CognomeParente").focus();
                    break;
                  
                    case 5: //pulsantiera x gestione ruoli
                        document.getElementById("pulsantiera3").style.visibility="visible";
                        document.getElementById("myruolo").focus();
                    break;
                    
                    default: //pulsantiera x dati anagrafici, tesseramento, classi&catechismo e ruolo
                        document.getElementById("pulsantiera").style.visibility="visible";
                        switch (i) { // setta il fuoco dei campi dei vari tab
                            case 0:
                                document.getElementById("cognome").focus();
                            break;
                          
                            case 3:
                                document.getElementById("mytesseramento").focus();
                            break;
                        }
                      
                    break;
                }
            } else {          
                  nrcelle[i].style.background= "#FF8C00";
                  
                  document.getElementById(tabname[i]).style.visibility="hidden";
                
                  switch (i) { // serve per chiudere la giusta pulsantiera all'interno dei vari tab
                      case 0: //pulsantiera x dati anagrafici, tesseramento, classi&catechismo e ruolo
                          document.getElementById("pulsantiera").style.visibility="hidden";
                      break;
                  
                      case 1: //pulsantiera rubrica telefonica
                          document.getElementById("pulsantiera1").style.visibility="hidden";
                      break;
                    
                      case 2: //pulsantiera gestione parentela
                          document.getElementById("pulsantiera2").style.visibility="hidden";
                      break;
                      
                      case 5: //pulsantiera gestione ruoli
                          document.getElementById("pulsantiera3").style.visibility="hidden";
                      break;
                  }
            }
        }
        // esce dalla funzione
        return;
    }
    
//***************************************************************************************
// funzione per modificare colore e mettere in maiuscolo il testo digitato dall'utente nel campo
// 'cognome' della sezione cerca iscritti  
function ControlloInputCognome() {
        var cognome = document.getElementById("txtCognome");
        // controlla il cognome        
        if (cognome.value!="") {
            cognome.value=cognome.value.toUpperCase();
            cognome.style.color ="#FAF176";
            cognome.style.background ="green";
            cognome.style.border="1px dotted grey";
        } else {
            cognome.style.color ="black";
            cognome.style.background = "white"
            cognome.style.border="1px dotted grey";
        }
        return;
}  
     
//***************************************************************************************
// funzione per modificare colore e mettere in maiuscolo il testo digitato dall'utente nel campo
// 'cognome' della sezione cerca iscritti  
function ControlloInputNome() { 
        var nome = document.getElementById ("txtNome");
        // controlla il nome
         if (nome.value!="") {
            nome.value=nome.value.toUpperCase();
            nome.style.color ="#FAF176";
            nome.style.background = "green"
            nome.style.border="1px dotted grey";
        } else {
            nome.style.color ="black";
            nome.style.background = "white"
            nome.style.border="1px dotted grey";
        }
        return;
}
    
//***************************************************************************************
// funzione per modificare colore e mettere in maiuscolo il testo digitato dall'utente nel campo
// 'codice a barre' della sezione cerca iscritti  
function ControlloInputCodiceBarre() {
    var strBarCode = document.getElementById("txtBarCode");
    // controlla che il campo non sia vuoto        
        if (strBarCode.value!="") {
            strBarCode.value=strBarCode.value.toUpperCase();
            strBarCode.style.color ="#FAF176";
            strBarCode.style.background ="green";
            strBarCode.style.border="1px dotted grey";
        } else {
            strBarCode.style.color ="black";
            strBarCode.style.background = "white"
            strBarCode.style.border="1px dotted grey";
        }
    return;
}  

//***************************************************************************************     
// funzione per modificare colore e mettere in minuscolo il nome digitato dall'utente
function ResetCampoCognome() {
    var cognome = document.getElementById("txtCognome");
    cognome.value=cognome.value.toLowerCase();
    cognome.style.color ="black";
    cognome.style.background ="#FAF176";
    cognome.style.border="1px dotted grey";
            
    return;
}    
         
//***************************************************************************************
// funzione per modificare colore e mettere in maiuscolo il nome digitato dall'utente
function ResetCampoNome() {
    var nome = document.getElementById ("txtNome");
    nome.value=nome.value.toLowerCase();
    nome.style.color ="black";
    nome.style.background ="#FAF176";
    nome.style.border="1px dotted grey";
    return;
}

//***************************************************************************************
// funzione per modificare colore e mettere in maiuscolo il nome digitato dall'utente
function ResetCampoBarCode() {
    var nome = document.getElementById ("txtBarCode");
    nome.value=nome.value.toLowerCase();
    nome.style.color ="black";
    nome.style.background ="#FAF176";
    nome.style.border="1px dotted grey";
    return;
}

//***************************************************************************************
//funzione fuoco campo cognome (cambia aspetto quando il campo ha il fuoco)
function FuocoCampoCognome() {
    var campo=document.getElementById("cognome");
    campo.style.color ="black";
    campo.style.background ="#FAF176";
    campo.style.border="1px dotted grey";
    return;
}

//***************************************************************************************
//funzione fuoco campo nome (cambia aspetto quando il campo ha il fuoco)
function FuocoCampoNome() {
    var campo=document.getElementById("nome");
    campo.style.color ="black";
    campo.style.background ="#FAF176";
    campo.style.border="1px dotted grey";
    return;
}
  
//***************************************************************************************
//funzione fuoco campo data di nascita (cambia aspetto quando il campo ha il fuoco)
function FuocoCampoDataNascita() {
    var campo=document.getElementById("dataN");
    campo.style.align="left";
    campo.style.color ="black";
    campo.style.background ="#FAF176";
    campo.style.border="1px dotted grey";
    return;
}
  
//***************************************************************************************  
//funzione fuoco campo Nato A (cambia aspetto quando il campo ha il fuoco)
function FuocoCampoNatoA() {
    var campo=document.getElementById("natoa");
    campo.style.color ="black";
    campo.style.background ="#FAF176";
    campo.style.border="1px dotted grey";
    return;
}
  
//***************************************************************************************
//funzione fuoco campo via (cambia aspetto quando il campo ha il fuoco)
function FuocoCampoVia() {
    var campo=document.getElementById("miostradario");
    campo.style.color ="black";
    campo.style.background ="#FAF176";
    campo.style.border="1px dotted grey";
    return;
}
  
//***************************************************************************************
//funzione fuoco campo indirizzo (cambia aspetto quando il campo ha il fuoco)
function FuocoCampoIndirizzo() {
    var campo=document.getElementById("indirizzo");
    campo.style.color ="black";
    campo.style.background ="#FAF176";
    campo.style.border="1px dotted grey";
    return;
}
  
//***************************************************************************************  
//funzione fuoco campo numero civico (cambia aspetto quando il campo ha il fuoco)
function FuocoCampoNr() {
    var campo=document.getElementById("numero");
    campo.style.color ="black";
    campo.style.background ="#FAF176";
    campo.style.border="1px dotted grey";
    return;
}
  
//***************************************************************************************
//funzione fuoco campo comune (cambia aspetto quando il campo ha il fuoco)
function FuocoCampoComune() {
    var campo=document.getElementById("miocomune");
    
    campo.style.color ="black";
    campo.style.background ="#FAF176";
    campo.style.border="1px dotted grey";
    return;
}
  
//***************************************************************************************
//funzione fuoco campo CAP (cambia aspetto quando il campo ha il fuoco)
  function FuocoCampoCap() {
    var campo=document.getElementById("cap");
    campo.style.color ="black";
    campo.style.background ="#FAF176";
    campo.style.border="1px dotted grey";
    return;
}
  
//***************************************************************************************
//funzione fuoco campo provincia (cambia aspetto quando il campo ha il fuoco)
function FuocoCampoProv() {
    var campo=document.getElementById("prov");
    campo.style.color ="black";
    campo.style.background ="#FAF176";
    campo.style.border="1px dotted grey";
    return;
}

//***************************************************************************************  
//funzione fuoco campo parrocchia (cambia aspetto quando il campo ha il fuoco)
function FuocoCampoParrocchia() {
    var campo=document.getElementById("miaparrocchia");
    campo.style.color ="black";
    campo.style.background ="#FAF176";
    campo.style.border="1px dotted grey";
    return;
}

//***************************************************************************************
//funzione fuoco campo email (cambia aspetto quando il campo ha il fuoco)
function FuocoCampoEmail() {
    var campo=document.getElementById("myemail");
    campo.style.color ="black";
    campo.style.background ="#FAF176";
    campo.style.border="1px dotted grey";
    return;
}
  
//***************************************************************************************
//funzione fuoco campo quota (cambia aspetto quando il campo ha il fuoco)
function FuocoCampoQuota() {
    var campo=document.getElementById("myaltraquota");
    campo.style.color ="black";
    campo.style.background ="#FAF176";
    campo.style.border="1px dotted grey";
    return;
}

//***************************************************************************************  
//funzione fuoco campo data di tesseramento (cambia aspetto quando il campo ha il fuoco)
function FuocoCampoDataTesseramento() {
    var campo=document.getElementById("mytesseramento");
    campo.style.color ="black";
    campo.style.background ="#FAF176";
    campo.style.border="1px dotted grey";
    return;
}

//***************************************************************************************  
//funzione fuoco campo data scadena tesseramento (cambia aspetto quando il campo ha il fuoco)
function FuocoCampoDataScadenzaTesseramento() {
    var campo=document.getElementById("mydataST");
    campo.style.color ="black";
    campo.style.background ="#FAF176";
    campo.style.border="1px dotted grey";
    return;
}
//***************************************************************************************
//funzione fuoco campo classe catechismo (cambia aspetto quando il campo ha il fuoco)
function FuocoClasseCatechismo() {
    var campo=document.getElementById("myclassi");
    campo.style.color ="black";
    campo.style.background ="#FAF176";
    campo.style.border="1px dotted grey";
    return;
}
  
//***************************************************************************************
//funzione fuoco campo sezione catechismo(cambia aspetto quando il campo ha il fuoco)
function FuocoSezioneCatechismo() {
    var campo=document.getElementById("mysezione");
    campo.style.color ="black";
    campo.style.background ="#FAF176";
    campo.style.border="1px dotted grey";
    return;
}

//***************************************************************************************
//funzione fuoco campo sezione ruoli(cambia aspetto quando il campo ha il fuoco)
function FuocoSezioneRuoli() {
    var campo=document.getElementById("myruolo");
    campo.style.color ="black";
    campo.style.background ="#FAF176";
    campo.style.border="1px dotted grey";
    return;
}

//***************************************************************************************
//funzione fuoco campo sezione ruoli(cambia aspetto quando il campo ha il fuoco)
function FuocoSezioneRuoliClasse() {
    var campo=document.getElementById("myruoloclasse");
    campo.style.color ="black";
    campo.style.background ="#FAF176";
    campo.style.border="1px dotted grey";
    return;
}

//***************************************************************************************
//funzione fuoco campo sezione ruoli(cambia aspetto quando il campo ha il fuoco)
function FuocoSezioneRuoliGruppo() {
    var campo=document.getElementById("myruologruppo");
    campo.style.color ="black";
    campo.style.background ="#FAF176";
    campo.style.border="1px dotted grey";
    return;
}

//***************************************************************************************
// funzione per il controllo del cognome inserito (non sezione cerca iscriti)
function ControlloCognome() {
    var campo=document.getElementById("cognome");
    if (campo.value !="") {
        campo.value=FiltroStringa(campo.value);
    }
    
    campo.style.color =" #000088";
    campo.style.background="white";
    campo.style.border="1px dotted grey";
    return;
}
  
//***************************************************************************************
// funzione per il controllo del Nome inserito (non sezione cerca iscriti)
function ControlloNome() {
    var campo=document.getElementById("nome");
    if (campo.value !="") {
        campo.value=FiltroStringa(campo.value);
    }
    
    campo.style.color ="#000088";
    campo.style.background="white";
    campo.style.border="1px dotted grey";
    return;
}

//***************************************************************************************  
// funzione per il controllo della data di nascita inserita (non sezione cerca iscriti)
function ControlloDataNascita() {
    var dt="dn";
    var campo=document.getElementById("dataN");
    if (campo.value !="") {
        campo.value=ControlloDataInserita(campo,dt);
    }
    
   campo.style.color ="#000088";
   campo.style.background="white";
   campo.style.border="1px dotted grey";

   //SEZIONE PER PROPORRE LA CLASSE DELL'ISCRITTO IN BASE ALLA DATA DI NASCITA
  // setta l'oggetto classe
    var classe=document.getElementById("myclassi");
    if (classe.value !=0){
        return;
    } else {
        // legge la data di nascita dell'iscritto        
        var campodatanascita=document.getElementById("dataN");
        var datanascita=campodatanascita.value.split('/');
    
        // legge la data di oggi già stampata in pagina
        var campodataoggi=document.getElementById("dataserver");
        var dataoggi=campodataoggi.value.split('/');
    
        // trova la differenza fra le due date
        var differenzadate=(Number(dataoggi[2])-Number(datanascita[2]));
        classe.value=ClasseProposta(differenzadate);
    }
    return;
  }
  
//***************************************************************************************
// funzione per il controllo del NatoA inserito (non sezione cerca iscriti)
function ControlloNato() {
    var campo=document.getElementById("natoa");
    if (campo.value !="") {
        campo.value=FiltroStringa(campo.value);
    }
    
    campo.style.color ="#000088";
    campo.style.background="white";
    campo.style.border ="1px dotted grey";
    return;
}
  
//***************************************************************************************  
// funzione per il controllo della Via inserita (non sezione cerca iscriti)
function ControlloVia() {
    var campo=document.getElementById("miostradario");
    if (campo.value !="") {
        campo.value=FiltroStringa(campo.value);
    }
    
    campo.style.color ="#000088";
    campo.style.background="white";
    campo.style.border="1px dotted grey";
    return;
}
  
//***************************************************************************************
// funzione per il controllo dell'indirizzo inserito (non sezione cerca iscriti)
function ControlloIndirizzo() {
    var campo=document.getElementById("indirizzo");
    if (campo.value !="") {
        campo.value=FiltroStringa(campo.value);
    }
    
    campo.style.color ="#000088";
    campo.style.background="white";
    campo.style.border="1px dotted grey";
    return;
}

//***************************************************************************************  
// funzione per il controllo dell'indirizzo inserito (non sezione cerca iscriti)
function ControlloNumero() {
    var campo=document.getElementById("numero");
    if (campo.value !="") {
          campo.value=FiltroStringa(campo.value);
          campo.value=campo.value.toUpperCase();
    }
    
    campo.style.color ="#000088";
    campo.style.background="white";
    campo.style.border="1px dotted grey";
    return;
}
  
//***************************************************************************************
// funzione per il controllo dell'indirizzo inserito (non sezione cerca iscriti)
function ControlloCap() {
    var campo=document.getElementById("cap");
    if (campo.value !="") {
        if (isNaN(campo.value)) {
          alert("Attenzione! Il Cap che hai inserito è errato. Sono ammessi soltanto valori numerici!");
          campo.focus();
          campo.value="";
        }
    }
    
    campo.style.color ="#000088";
    campo.style.background="white";
    campo.style.border="1px dotted grey";
    return;
}
 
//***************************************************************************************  
// funzione per il controllo dell'indirizzo inserito (non sezione cerca iscriti)
function ControlloProv() {
    var campo=document.getElementById("prov");
    if (campo.value !="") {
        campo.value=campo.value.toUpperCase();
    }
    campo.style.color ="#000088";
    campo.style.background="white";
    campo.style.border="1px dotted grey";
    return;
}

//***************************************************************************************  
// funzione per il controllo dell'indirizzo inserito (non sezione cerca iscriti)
function ControlloCitta() {
    var campo=document.getElementById("miocomune");
    
    campo.value=FiltroStringa(campo.value);
    campo.style.color ="#000088";
    campo.style.background="white";
    campo.style.border="1px dotted grey";
    return;
}

//***************************************************************************************  
// funzione per il controllo dell'indirizzo inserito (non sezione cerca iscriti)
function ControlloParrocchia() {
    var campo=document.getElementById("miaparrocchia");
    
    campo.value=FiltroStringa(campo.value);
    campo.style.color ="#000088";
    campo.style.background="white";
    campo.style.border="1px dotted grey";
    return;
}
  
//***************************************************************************************  
// funzione per il controllo dell'indirizzo email inserito 
function ControlloEmail() {
    var campo=document.getElementById("myemail");
    var emailPattern =  /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (campo.value !="") {
        if (!emailPattern.test(campo.value)) {
            alert("Attenzione! L'indirizzo email che hai inserito non e' corretto");
            campo.focus();
            return;
        }
    }
    
    campo.style.color ="#000088";
    campo.style.background="white";
    campo.style.border="1px dotted grey";
    return;
  }
 
 //*************************************************************************************** 
// funzione per il controllo della quota inserita (non sezione cerca iscriti)
function ControlloQuota() {
    var campo=document.getElementById("myaltraquota");

    if (campo.value !="") {
        // tramite l'utilizzo delle espressioni regolari si controlla che l'utente abbia inserito
        // numeri e la virgola per il valore del campo quota
        var QuotaPattern= /^([0-9])+$/;
        if (!QuotaPattern.test(campo.value)) {
            var QuotaPattern= /^([0-9])+([\,\.])+([0-9]{2,2})+$/;
            if (!QuotaPattern.test(campo.value)) {
                alert("Attenzione! La quota che hai inserito è errata. Sono ammessi soltanto valori numerici e nel formato 'nn' oppure 'nn,nn'");
                campo.focus();
                return;
            }
        } 

        // controlla se l'utente ha inserito un formato decimale con il punto. In caso affermativo
        // lo sostituisce con la virgola
        if (campo.value.search(".")>-1) {
            campo.value=campo.value.replace(".",",");
        }
        
        // controlla se l'utente ha inserito un formato di quota senza i decimali. In caso affermativo
       // li aggiunge
        if (campo.value.search(",")==-1) {
            campo.value+=",00";
        } 
    }

    campo.style.color ="#000088";
    campo.style.background="white";
    campo.style.border ="1px dotted grey";
    return;
}

//***************************************************************************************  
// funzione per il controllo della data di tesseramento inserita (non sezione cerca iscriti)
function ControlloDataTesseramento() {
    var dt ="dt";
    var campo=document.getElementById("mytesseramento");
    if (campo.value !="") {
        campo.value=ControlloDataInserita(campo,dt);
    } else {
        document.getElementById("mydataST").value="";
    }
    
    campo.style.color ="#000088";
    campo.style.background="white";
    campo.style.border="1px dotted grey";
    return;
}

//***************************************************************************************  
// funzione per il controllo della data di scadenza del tesseramento inserita (non sezione cerca iscriti)
function ControlloDataScadenzaTesseramento() {
    var dt ="dst";
    var campo=document.getElementById("mydataST");
    if (campo.value !="") {
        campo.value=ControlloDataInserita(campo,dt);
    }
    
    campo.style.color ="#000088";
    campo.style.background="white";
    campo.style.border="1px dotted grey";
    return;
}

//***************************************************************************************  
// funzione per stampare e calcalore la data di tesseramento e di scadenza tessera (non sezione cerca iscriti)
function CalcolaDateTesseramento() {
    //stampa nei campi i valori impostati 
    // data tesseramento
    document.getElementById("mytesseramento").value="Oggi";
    
    //data di scadenza tessera
    document.getElementById("mydataST").disabled="disabled";
    document.getElementById("mydataST").value="";
        
    //setta il fuoco sul campo della quota versata e resetta il campo
    //document.getElementById("myquota").focus();
    //document.getElementById("myquota").value="";
    
    return;
}

//***************************************************************************************  
// funzione per il controllo della classe del catechismo (non sezione cerca iscriti)
function ControlloClasseCatechismo() {
    var campo=document.getElementById("myclassi");
    campo.style.color ="#000088";
    campo.style.background="white";
    campo.style.border="1px dotted grey";
    return;
}
  
 //*************************************************************************************** 
// funzione per controllare che la classe scelta sia nell'intervallo giusto dell'età dell'iscritto
function ControlloClasseFrequentata() {
    var msgerrore=false;
    
    // legge il valore scelto dall'utente
    var classe=document.getElementById("myclassi");
    
    // legge la data di nascita dell'iscritto        
    var campodatanascita=document.getElementById("dataN");
    var datanascita=campodatanascita.value.split('/');
    
    // legge la data di oggi già stampata in pagina
    var campodataoggi=document.getElementById("dataserver");
    var dataoggi=campodataoggi.value.split('/');

    
    // trova la differenza fra le due date
    var differenzadate=(Number(dataoggi[2])-Number(datanascita[2]));
   
    //classe.value=ClasseProposta(differenzadate);
    
    // controlla la scelta dell'utente
    if (differenzadate < 6 && classe.value > 1) { // asilo
        msgerrore=true;
    }
   
    if ((differenzadate > 5 && differenzadate < 11)) { // elementari
        if (!(classe.value >1 && classe.value <=6)) {
           msgerrore=true;
        }
    }
  
    if ((differenzadate > 10 && differenzadate < 14)) { // medie
        if (!(classe.value >6 && classe.value <=9)) {
           msgerrore=true;
        }
    }
   
    if ((differenzadate > 13 && differenzadate <= 19)) { // superiori
        if (!(classe.value >9 && classe.value <=14)) {
           msgerrore=true;
        }
    } 
  
    if ((differenzadate > 19 && differenzadate < 30)) { // universitari, giovani
      if (classe.value !=15) {
           msgerrore=true;
        }
    }
    
    if (differenzadate > 29) { // adulti
      if (classe.value !=16) {
           msgerrore=true;
        }
    }

    if (msgerrore) {
        if (!confirm ("Attenzione! La classe che hai scelto non è corretta per l'età dell'iscritto... Scegliendo OK mantieni la tua scelta. Cliccando su ANNULLA, ti propongo la classe più corretta per la sua età.")) {
          classe.value =ClasseProposta(differenzadate);
        }
    }
  return;
}
  
//***************************************************************************************
//funzione per proporre automaticamente la classe frequentata dall'iscritto in base all'età
function ClasseProposta(differenzadate) {
      var ValoreClasse=0;
      
      if (differenzadate < 3) {
          ValoreClasse=0;
      }
      if (differenzadate < 6 && differenzadate > 2) {
          ValoreClasse=1;
      }
      
      if (differenzadate >= 6 && differenzadate <=18) {
          ValoreClasse=(differenzadate-4);
      }
      
      if (differenzadate >=19 && differenzadate <=29) {
          ValoreClasse=15;
      }
      
      if (differenzadate >=30) {
          ValoreClasse=16;
      }
      
  return ValoreClasse;
}
  
//***************************************************************************************  
// funzione per il controllo della sezione del catechismo (non sezione cerca iscriti)
function ControlloSezioneCatechismo() {
    var campo=document.getElementById("mysezione");
    campo.style.color ="#000088";
    campo.style.background="white";
    campo.style.border="1px dotted grey";
    return;
}

//***************************************************************************************  
// funzione per il controllo della sezione del catechismo (non sezione cerca iscriti)
function ControlloSezioneRuoli() {
    var campo=document.getElementById("myruolo");
    campo.style.color ="#000088";
    campo.style.background="white";
    campo.style.border="1px dotted grey";
    return;
}

//***************************************************************************************  
// funzione per il controllo della sezione del catechismo (non sezione cerca iscriti)
function ControlloSezioneRuoliClasse() {
    var campo=document.getElementById("myruoloclasse");
    campo.style.color ="#000088";
    campo.style.background="white";
    campo.style.border="1px dotted grey";
    return;
}

//***************************************************************************************  
// funzione per il controllo della sezione del catechismo (non sezione cerca iscriti)
function ControlloSezioneRuoliGruppo() {
    var campo=document.getElementById("myruologruppo");
    campo.style.color ="#000088";
    campo.style.background="white";
    campo.style.border="1px dotted grey";
    return;
}
//***************************************************************************************
// funzione per filtrare i dati inseriti dall'utente (cognome, nome, indirizzo, ecc.)
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

if (datadafiltrare.value=="undefined" || datadafiltrare.value=="") { //se il campo è vuoto esce dalla funzione
  datadafiltrare.style.color="#000088";
  datadafiltrare.style.background="white";
  return;
}

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
        alert("Attenzione! La data che hai inserito e' errata. Sono validi i seguenti formati: 'ggmmaa','gg/mm/aa','ggmmaaaa' e 'gg/mm/aaaa'");
        datadafiltrare.focus();
        datadafiltrare.value="";
        return datadafiltrare.value;
}

//controlla che i dati di riferimento al giorno, al mese e all'anno siano dati numerici 
if (isNaN(gg) || isNaN(mm) || isNaN(yy)) {
	alert("Attenzione! La data che hai inserito e' errata.");
        datadafiltrare.focus();
        datadafiltrare.value="";
        return datadafiltrare.value;
}

//controlla che siano stati inseriti giusti tutti gli elementi che compongono la data
if (Number(gg) <= 0 || Number(mm) <= 0 || Number(mm) > 12 || Number(yy) < 0) {                     
        alert("Attenzione! La data che hai inserito e' errata.");
        datadafiltrare.focus();
        datadafiltrare.value="";
        return datadafiltrare.value;
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
        datadafiltrare.focus();
        datadafiltrare.value="";
        return datadafiltrare.value;
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
	alert("Attenzione! La data che hai inserito e' errata.");
    datadafiltrare.focus();
    datadafiltrare.value="";
    return datadafiltrare.value;
}
// ricompone e stampa la data filtrata
datadafiltrare.value=gg+"/"+mm+"/"+yy;
datadafiltrare.style.color="#000088";
datadafiltrare.style.background="white";
return datadafiltrare.value;
}

//***************************************************************************************
// funzione per abilitare e disabilitare il campo Altro (tesseramenti)
function AbilitaAltraQuota() {
    //recupera le options delle quote
    var tipoquota=document.getElementsByName("myquota");
    
    //trova l'opzione selezionata per abilitare e disabilitare il campo altro
    for (i=0;i<tipoquota.length;i++) {
        if (tipoquota[i].checked==true) {
            if (tipoquota[i].value==4) {
                document.getElementById("myaltraquota").disabled=false;
                document.getElementById("myaltraquota").value="";
                document.getElementById("myaltraquota").focus();
            } else {
                  document.getElementById("myaltraquota").disabled=true;
                  document.getElementById("myaltraquota").value="0.00";
                  document.getElementById("myaltraquota").style.color="grey";
            }
        } 
    }
return;
}

//***************************************************************************************
// serve per rilevare se l'utente ha premuto il tasto tab per spostarsi di campo e nasconde i suggest
function RilevaTab(obj) {
      if (obj.keyCode==9) {
          $('#suggestions').hide();
          $('#suggestions_comuni').hide();
          $('#suggestions_parrocchie').hide();
      }
      
     if (document.getElementById("CognomeParente").value=="" || document.getElementById("CognomeParente").value==null) {
        $('#tabella_parentela_nucleo_A').hide();
     }
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
} // lookup

//***************************************************************************************
// autocomplete in ajax-suggest per i cognomi degli iscritti-parenti
function lookup_parentela(inputString) {
      if(inputString.length == 0) {
        // Hide the suggestion box.
				$('#hdnIDParente').val("");
        $('#suggestions_parentela').hide();
        $('#tabella_parentela_nucleo_A').hide();
			} else {
        $.post("rpc_parentela.php", {queryString: ""+inputString+""}, function(data){
          if(data.length >0) {
            $('#suggestions_parentela').show();
						$('#autoSuggestionsListParentela').html(data);
					} else {
            $('#suggestions_parentela').hide();
            $('#tabella_parentela_nucleo_A').hide();
          }
				});
			}
} // lookup

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
        modulo.submit();
      } 
      
      ControlloInputCognome();
      ControlloInputNome();
      ControlloInputCodiceBarre();
}

//***************************************************************************************		
function fill_names(thisValue) { // per i nomi della sezione ricerca
      if (thisValue != null) {
        modulo = document.getElementById("CercaIscritti");
        valori = thisValue.split('|');	
				$('#hdnID').val(valori[0]);
				$('#txtNome').val(valori[1]);
        $('#txtCognome').val(valori[2]);
				
				// costruisce il codice a barre a 13 cifre e lo stampa
				var barcode="0000000000000";
				barcode=barcode.slice(0,-valori[0].length)+valori[0];
        $('#txtBarCode').val(barcode);
        // $('#txtBarCode').val(valori[3]); dismesso perché utilizza il codice a barre del vecchio programma Access

				setTimeout("$('#suggestions').hide();", 200);
        modulo.submit();
      } 
      
      ControlloInputCognome();
      ControlloInputNome();
      ControlloInputCodiceBarre();
}

//***************************************************************************************
function fill_parentela(thisValue) { // per i cognomi della sezione parentela
      if (thisValue != null) {
        var valori = thisValue.split('|');
        document.getElementById("hdnIDParente").value=valori[0];
				document.getElementById("CognomeParente").value=valori[1]+" "+valori[2];
				setTimeout("$('#suggestions_parentela').hide();", 200);
				
				// manda a php i valori per cercare i famigliari del nucleo A 
        document.getElementById("id_famiglia_nucleo_A").value=valori[3];
        document.getElementById("GestioneParentela").submit();
      } 
      return;
}

//***************************************************************************************
// autocomplete in ajax per suggerimento comuni
function lookup_comuni(inputString) {
      if(inputString.length == 0) {
          // Hide the suggestion box.
          $('#cap').val("");
          $('#prov').val("");
          $('#suggestions_comuni').hide();
			} else {
          $.post("rpcomuni.php", {queryString: ""+inputString+""}, function(data){
          if(data.length >0 && data.length != 24) {
            $('#suggestions_comuni').show();
						$('#autoSuggestionsListComuni').html(data);
					} else {
            $('#suggestions_comuni').hide();
          }
				});
			}
} // lookup
		
//***************************************************************************************
//sugget ajax per i comuni
function fill_comuni(thisValue) {
        var valore=new String();
        var mio = new String();
        
        // Visualizza i valori trovati in base ai caratteri inseriti nel campo città/comune dall'utente
        // thisValue ha il valore del click sulla lista visualizzata
        if (thisValue != null) {
            var valori = thisValue.split('|');
            $('#hdnIdcomune').val(valori[0]);
            $('#miocomune').val(valori[1]);
				    $('#cap').val(valori[2]);
				    $('#prov').val(valori[3]);
				    setTimeout("$('#suggestions_comuni').hide();", 200);
				    
				    $('#myemail').focus(); // setta il fuoco sul campo email

        } else {
           
           /*
           // questa parte serve per interrogare il database in caso non venga cliccato da parte dell'utente il suggerimento
           // dato dal programma
           if (document.getElementById("miocomune") != null) { // controlla che non il campo città/comune non sia nullo
              inputString=document.getElementById("miocomune").value; // formula la richiesta da mandare al server/database
              $.post("rpcomuni.php", {queryString: ""+inputString+""}, function(data) // manda la richiesta al server/database
                  {
                      if(data.length >0 && data.length != 24) { // se il server/database restituisce dei valori...
                         // questa parte serve per recuperare l'id del comune scelto
                          var pattern=new RegExp("[0-9]");
                          var valori =data.split('|');
                          for (i=0;i<valori[0].length;i++) {
                              if (pattern.test(valori[0].charAt(i))) {
                                  valore+= valori[0].charAt(i);
                              } 
                          }
                          $('#hdnIdcomune').val(valore);
                          
                          // assegna i valori trovati
                            $('#miocomune').val(valori[1]);
                          
                          $('#cap').val(valori[2]);
				                  $('#prov').val(valori[3].slice(0,2));
				                  
				                  $('#myemail').focus();
					            } else { */
                          $('#hdnIdcomune').val("");
				                  $('#cap').val("");
				                  $('#prov').val("");
                      /*}
				          }); 
            }*/
        }
      
        ControlloCitta();
        ControlloCap();
        ControlloProv();
}

//***************************************************************************************	 
// autocomplete in ajax per suggerimento parrocchie
function lookup_parrocchie(inputString) {
      if(inputString.length == 0) {
        // Hide the suggestion box.
				$('#suggestions_parrocchie').hide();
			} else {
        $.post("rpparrocchie.php", {queryString: ""+inputString+""}, function(data){
          if(data.length >0 && data.length != 24) {
            $('#suggestions_parrocchie').show();
						$('#autoSuggestionsListParrocchie').html(data);
					} else {
            $('#suggestions_parrocchie').hide();
          }
				});
			}
} // lookup
		
//***************************************************************************************		
// suggest ajax per le parrocchie
function fill_parrocchie(thisValue) {
    if (thisValue != null) {
        var valori = thisValue.split('|');
        $('#hdnIdParrocchia').val(valori[0]);
				$('#miaparrocchia').val(valori[1]);
				setTimeout("$('#suggestions_parrocchie').hide();", 200);
      } 
      ControlloParrocchia();
}
		
//***************************************************************************************
// funzione per chiudere la scheda dell'iscritto (pulisce i campi)
function ChiudiIscritto(annulla) {

 // il valore 0 assegnato a chiudi iscritto (quando la scheda di un iscritto è già aperta)
 // il valore 1 assegnato a annulla iscrizione (quando la scheda è di un nuovo iscritto o vuota)
  if (annulla==0) {
        document.getElementById("save_scheda").value="chiudi_iscritto";
        fncSalvaScheda();
  } else {
        // assegna informazioni al form contenente i dati dell'iscritto da chiudere    
        document.getElementById("txtCognome").value=null;
        document.getElementById("hdnID").value=null;
        document.getElementById("hdnIDParente").value=null;
        document.getElementById("SalvaSchedaIscritto").submit(); 
  }
  return;
}

//***************************************************************************************
// funzione per il controllo dei componenti il numero di telefono (non sezione cerca iscriti)
  function InputNrTel(stringa,indice) {
    switch (stringa) {
        case "pref_int":
            var campo=document.getElementsByName("prefisso_intnaz");
        break;
    
        case "pref_naz":
            var campo=document.getElementsByName("prefisso_naz");
            
        break;
        
        case "nr_phone":
            var campo=document.getElementsByName("numero_phone");
        break;
    }
    
    campo[indice-1].style.background="#FAF176";
    campo[indice-1].style.color="black";
    campo[indice-1].style.border="1px dotted grey";
    
  return;  
  }

//***************************************************************************************
// funzione per il controllo dei componenti il numero di telefono
  function ControlloNrTel(stringa,indice) {
    switch (stringa) {
        case "pref_int":
            var campo=document.getElementsByName("prefisso_intnaz");
            var Pattern= /^([+0-9]{3,4})+$/;
            var messaggio="Attenzione! Il numero di prefisso internazionale che hai inserito e' errato."; 
        break;
    
        case "pref_naz":
            var campo=document.getElementsByName("prefisso_naz");
            var Pattern= /^([0-9]{3,4})+$/; 
            var messaggio="Attenzione! Il numero di prefisso che hai inserito e' errato.";
        break;
        
        case "nr_phone":
            var campo=document.getElementsByName("numero_phone");
            var Pattern= /^([0-9]{4,12})+$/;
            var messaggio="Attenzione! Il numero di telefono che hai inserito e' errato.";
        break;
    }

    if (campo[indice-1].value !="") {
        if (!Pattern.test(campo[indice-1].value)) {
            alert(messaggio);
            campo[indice-1].value="";
            campo[indice-1].focus();
        } 
    }
    
    campo[indice-1].style.color="red";
    campo[indice-1].style.background="white";
    campo[indice-1].style.border="1px dotted grey";
    
    return;  
  }
  
  //*************************************************************************************** 
  // funziione per il controllo dei check SMS rubrica telefono
  function ControllaChkSmS(indice) {
      var checks=document.getElementsByName("SelSms");
     
     // spegne tutti i flags attivati
      for (i=0;i<=(checks.length-1);i++) {
          if (i!=(indice-1)) { 
              checks[i].checked=false;
          }
      }

    return;
}

//***************************************************************************************
// funzione per salvare la rubrica telefonica nel db
function fncSalvaRubrica() {
    //variabili di servizio
    var DatiToPhp="";
    //var ErroreValidazione=false;
    
    // recupera i valori inseriti nella rubrica dall'utente
    var ValoriRubrica=document.getElementsByName("SelNumero"); // check di selezione numeri
    var PrefissoInt=document.getElementsByName("prefisso_intnaz"); // input prefissi internazionali
    var PrefissoNaz=document.getElementsByName("prefisso_naz"); // input prefissi nazionali e cellulari
    var NumeroTelefono=document.getElementsByName("numero_phone"); // i numeri di telefono
    var SelezioneSms=document.getElementsByName("SelSms"); // selezione invio sms
    
    // costruisce la stringa che invia a PHP per l'elaborazione e il salvataggio
    for (i=0;i<=(ValoriRubrica.length-1);i++) {
        // controlla che il campo del prefisso internazionale non sia vuoto quando è associato a un numero di telefono
        //Nel caso sia nullo inserisce di default l'indicativo internazionale dell'Italia
        if (PrefissoInt[i].value=="" && PrefissoNaz[i].value!="" && NumeroTelefono[i].value!="") {
            PrefissoInt[i].value="+39";
        }
       
        /* controlla che il prefisso nazionale e il numero di telefono siano associati
        if ((PrefissoNaz[i].value=="" && NumeroTelefono[i].value!="") || (PrefissoNaz[i].value!="" && NumeroTelefono[i].value=="")) {
            ErroreValidazione=true;
        }*/
       
        // se il prefisso nazionale e il numero di telefono sono associati compone la stringa da inviare a PHP 
        if (PrefissoNaz[i].value!="" && NumeroTelefono[i].value!=""){
            DatiToPhp+=ValoriRubrica[i].value+";"+PrefissoInt[i].value+";"+PrefissoNaz[i].value+";"+NumeroTelefono[i].value+";"+SelezioneSms[i].checked+"|";
        }
    }            

    //controlla che la stringa da inviare a PHP non sia vuota
    if (DatiToPhp!="") {
        document.getElementById("rubrica").value="salva_rubrica"; // azione da far compiere a PHP
        document.getElementById("DatiRubrica").value=DatiToPhp; //assegna i dati da inviare a PHP
        document.getElementById("SalvaNumeroTel").submit(); // invia i dati a PHP
    }
  
    return;
}

//***************************************************************************************
// funzione per cancellare i numeri di telefono dalla rubrica
function fncCancellaNumeroTelefono() {
     //variabili di servizio
    var DatiToPhp="";
    
    // recupera i valori inseriti nella rubrica dall'utente
    var ValoriRubrica=document.getElementsByName("SelNumero"); // check di selezione numeri
    
    // costruisce la stringa da inviare a PHP in base alle selezioni dell'utente
    for (i=0;i<=(ValoriRubrica.length-1);i++) {
        if (ValoriRubrica[i].checked) {
            DatiToPhp+=ValoriRubrica[i].value+"|";    
        }
    }
    
    //controlla che sia stato selezionato almeno un numero di telefono
    if (DatiToPhp=="" || DatiToPhp==null) {
        alert("Non hai selezionato nessun numero di telefono!")
        return;
    } else {
        // si assicura che l'utente voglia proprio cancellare i numeri di telefono selezionati
        if (confirm("Sei proprio sicuro di voler cancellare i numeri di telefono che hai selezionato dalla rubrica?")) {
            document.getElementById("rubrica").value="delete_rubrica"; // azione da far compiere a PHP
            document.getElementById("DatiRubrica").value=DatiToPhp; //assegna i dati da inviare a PHP
            document.getElementById("SalvaNumeroTel").submit(); // invia i dati a PHP
        }
    }
    return;    
}

//***************************************************************************************
// funzione per validare i dati dell'iscritto da salvare nel database
  function fncSalvaScheda (){
      // recupera informazioni dal form contenente i dati dell'iscritto da salvare      
      var salvascheda = document.getElementById("SalvaSchedaIscritto"); // nome del form contenente i dati dell'iscritto da salvare
      var OkSalva=document.getElementById("save_scheda"); // flag per dare il via libera a php di salvare i dati della scheda
      var id_iscritto=document.getElementById("hdnIdx");  // identificativo dell'iscritto nel database
      
      var cognome=document.getElementById("cognome");
      var nome=document.getElementById("nome");
      var sesso=document.getElementById("sesso");   // per i maschietti
      var sessof=document.getElementById("sessof"); //  per le femminucce
      
      var data_di_nascita=document.getElementById("dataN");
      var luogo_di_nascita=document.getElementById("natoa");
      var stradario=document.getElementById("miostradario");
     
      var indirizzo=document.getElementById("indirizzo");
      var numero_civico=document.getElementById("numero");
      var comune=document.getElementById("miocomune");
      
      var cap=document.getElementById("cap");
      var provincia=document.getElementById("prov");
      var email=document.getElementById ("myemail");
      
      var parrocchia=document.getElementById("miaparrocchia");
      var idparrocchia=document.getElementById("hdnIdParrocchia");
      var spedizione=document.getElementById("spedizione");
      var datascadenzatessera=document.getElementById("mydataST");
      
      var quota=document.getElementById("myaltraquota");
      var data_tesseramento=document.getElementById("mytesseramento");
      var miaclasse=document.getElementById("myclassi");
      
      var sezione=document.getElementById("mysezione");
      var partecipazione=document.getElementById("optPartecipa");
      var coro=document.getElementById("chkCoro");
     
      
      if (OkSalva.value!="chiudi_iscritto") {
          OkSalva.value='anagrafica';
      }  
      
      //controllo dei campi obbligatori
      if (cognome.value=="undefined" || cognome.value=="" || cognome.value==null) {
          OkSalva.value='false';
      }
      
      if (nome.value=="undefined" || nome.value=="" || cognome.value==null) {
          OkSalva.value='false';
      }      
     
      if (!sesso.checked && !sessof.checked) {
          OkSalva.value='false';
      } 
      
      if (OkSalva.value=='false') {
          alert("Attenzione! Impossibile proseguire nel salvataggio: errore di validazione dei dati. Uno dei campi obbligatori (Cognome, Nome, Sesso) non e' stato compilato in maniera adeguata.")
          return;      
      }
      
      // controllo campi non obbligatori
      if (parrocchia.value=="" || parrocchia.value==null) {
          idparrocchia.value=0;
      }
      
      if (sezione.value=="" || sezione.value ==null) {
        sezione.value=1;
      }
      
      salvascheda.submit();
      return;  
  }
  
//***************************************************************************************
function btnCercaIscritti() {
    // funzione per risolvere il problema delle omonimie nella ricerca degli iscritti
    var idiscritto=document.getElementById("hdnID");
    var modulo=document.getElementById("CercaIscritti");
    
    // annulla il valore di un eventuale id dell'iscritto (se non lo si cerca con il suggest ajax)
    idiscritto.value=null;
 
    // lancia la richiesta al server
    modulo.submit();
    return;  
}

//***************************************************************************************
function stampa_privacy (id) {
// lancia la stampa della privacy se gli viene passato un id
    if (id!='') {
        location.href="stampa_privacy.php?id="+id;
        return true;
    }
}  

//***************************************************************************************
// funzione per salvare parenti in archivio
function fncAggiungiParente(){
    var idfamiglia=document.getElementsByName("selparente");
    var DatiNonValidati=0;
    var MexErrore="Attenzione! Errore di validazione dei dati. Controlla i campi obbligatori 'Cognome e Nome parente' e 'Grado Parentela'. Per il campo 'Cognome e Nome parente' usa il suggerimento che ti viene dato.";
    var indice_selezione = false;
    
    if (document.getElementById("hdnIDParente").value < 1 || document.getElementById("hdnIDParente").value == document.getElementById("hdnID").value) {
        DatiNonValidati++;
    }
   
     if (document.getElementById("GradoParentela").options[document.getElementById("GradoParentela").selectedIndex].value <1) {
        DatiNonValidati++;
    } 
    
    if (DatiNonValidati>0) {
        alert(MexErrore);
    } else {
        document.getElementById("save_family").value="parentela";
        
        // se nella tabella c'è un famigliare, assegna l'id della famiglia da inviare a PHP
        if (idfamiglia.length!=0) {
            document.getElementById("id_famiglia").value=idfamiglia[0].value;
        } else {
            document.getElementById("id_famiglia").value=-1; // non c'è nessun famigliare associato
        }
        
        document.getElementById("GestioneParentela").submit();
    }
    
    return;
}

//***************************************************************************************
// funzione per cancellare parenti dall'archivio
function fncCancellaParente() {
    var rigaselezionata = document.getElementsByName("selparente");
    var idcella = document.getElementsByName("cella_id");
    var indice=0;
    var righeselezionate=0;
    var id_parenti="";
    
    //controlla le righe che sono state selezionate e prepara la stringa da inviare a php
    for (indice;indice<rigaselezionata.length;indice++) {
        if (rigaselezionata[indice].checked){    
            righeselezionate++;
            id_parenti+=idcella[indice].textContent+"|";
        }
    }
    
    if (righeselezionate==0) {
        alert("Attenzione! Non hai selezionato nessun familiare!");
        return;
    }
    
    if (confirm("Attenzione! Vuoi cancellare i familiari selezionati?")) {
        document.getElementById("save_family").value="delete_parente";
        document.getElementById("id_famiglia").value=id_parenti+rigaselezionata[0].value+"|"+rigaselezionata.length;
       
        document.getElementById("GestioneParentela").submit();
    }

    return;
} 

//***************************************************************************************
function ChiudiMessaggio(){
  document.getElementById("stampamessaggio").style.visibility="hidden";
  document.getElementById("txtCognome").focus();
  return;
}
//***************************************************************************************

//***************************************************************************************
function GestioneErrori(){
        alert ("Per accedere a questa sezione devi prima salvare i dati dell'iscritto");
        return;
}
//***************************************************************************************

//***************************************************************************************
function fncCancellaScheda(){
    var deletescheda = document.getElementById("SalvaSchedaIscritto"); // nome del form contenente i dati dell'iscritto da salvare
    var OkDelete=document.getElementById("save_scheda"); // flag per dare il via libera a php di cancellare i dati della scheda

    if (confirm("Attenzione! Sei proprio sicuro di voler cancellare la scheda dell'iscritto?")) {
        OkDelete.value="delete_scheda"; // azione da compiere per PHP
        deletescheda.submit(); // manda a PHP l'ordine di cancellare i dati dell'iscritto
    }
    return;
}

//***************************************************************************************
// funzione per visualizzare/nascondere la legenda dei ruoli oratorio
function LegendaRuoli(azione){
    
    switch (azione) {
        case 0:
            document.getElementById("legendaruoli").style.visibility="visible";
        break;
        
        case 1:
            document.getElementById("legendaruoli").style.visibility="hidden";
        break;
    }
    
    return;
}

//***************************************************************************************
// funzione per aggiungere/cancellare i ruoli oratorio
function fncAggiungiCancellaRuolo(azione){
    var ModificaRuolo = document.getElementById("ModificaRuolo");
    var Azione_PHP=document.getElementById("modifica_ruolo");
    
    switch (azione) {
        case 0: // aggiungi ruolo
            var ruolo = document.getElementById("myruolo");
            var classe = document.getElementById("myruoloclasse");
            var gruppo = document.getElementById("myruologruppo");
           
            switch (ruolo.value) {
                case "1": // nessun ruolo non va a salvare i dati
                    return;
                break;
                
                case "5": // coordinatore: ammessi soltanto nessuna classe, giovani e adulti
                    if (classe.value < 15) {
                        alert("Attenzione! La classe scelta non è associabile a questo ruolo");
                          return;
                    }
                break;
            
                case "6": // segreteria: non ha classe e gruppo
                    classe.value=17;
                    gruppo.value=1;
                break;
                
                case "7": //securitas: non ha classe, può avere il gruppo
                    classe.value=17;
                break;            
                
                case "9": //direttivo: non ha classe e gruppo
                    classe.value=17;
                    gruppo.value=1;
                break;
                
                default: //catechisti, educatori, animatori, cuochi
                      if (ruolo.value==2 && classe.value >14) {
                          alert("Attenzione! La classe scelta non è associabile a questo ruolo");
                          return;
                      }
                break;
            }
            
            Azione_PHP.value="aggiungi_ruolo";
            
        break;
        
        case 1: //cancella ruolo
            if (!confirm ("Attenzione! Sei proprio sicuro di cancellare i ruoli selezionati?")) {
                return;
            }
            
            //recupera in un array i valori selezionati da cancellare
            var RuoloSelezionato=document.getElementsByName("SelRuolo"); // check ruolo
            
            // definisce la stringa da inviare a PHP
            var DatiToPhp="";
            
            // costruisce la stringa da inviare a PHP per l'elaborazione
            for (i=0;i<=(RuoloSelezionato.length-1);i++) {
                if (RuoloSelezionato[i].checked) {
                    DatiToPhp+=RuoloSelezionato[i].value+"|";
                }
            }
            
            if (DatiToPhp==""){
                alert("Attenzione! Non è stato scelto nessun ruolo.");
                return;
            }
            
            document.getElementById("dati_check_ruoli").value=DatiToPhp;
           
            Azione_PHP.value="cancella_ruolo";
        break;
    }
    
    ModificaRuolo.submit();
    return;
}

//Cambia la pagina alla tabella ruoli
function ChangePageTabellaRuoli(nrpagina,nrpagine) {
  var ModificaRuolo = document.getElementById("ModificaRuolo");
  var Azione_PHP=document.getElementById("modifica_ruolo")
  
  /* controlla che l'operatore non vada fuori dall'intervallo
   * di pagine che sono risultate dalla ricerca dei cancellati */
  if ((nrpagina < 1) || (nrpagina > nrpagine)) { 
      return;
  }
  
  // assegna il nuovo nr di pagina corrente
  document.getElementById("nrpagina_tabella_ruoli").value = nrpagina;

  // invia i dati a PHP
  ModificaRuolo.submit();

  return;
}









