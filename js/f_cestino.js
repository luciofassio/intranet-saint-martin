/* JavaScript Document
 * Oggetto:   Funzioni per pagina web Cestino
 * Creazione: Settembre 2010
 * Autore:    Marco Fogliadini 
*/

// riconosce il browser per caricare i relativi fogli di stile    
var lsBrowser= navigator.appName;
switch (lsBrowser) {
  case "Microsoft Internet Explorer":
    document.write("<link href=\"./css/struttura_pagina.css\" rel=\"stylesheet\" type=\"text/css\" />");
  break;
          
  case "Netscape":
    document.write("<link href=\"./css/struttura_pagina_ff.css\" rel=\"stylesheet\" type=\"text/css\" />");
  break;
}

// recupera l'iscritto cestinato
function fncRecupera() {
    var rigaselezionata = document.getElementsByName("SelIscritto");
    var idcella = document.getElementsByName("cella_id");
    var indice=0;
    var righeselezionate=0;
    var id_iscritti="";

    //controlla le righe che sono state selezionate e prepara la stringa da inviare a php
    for (indice;indice<rigaselezionata.length;indice++) {
        if (rigaselezionata[indice].checked){    
            righeselezionate++;
            id_iscritti+=idcella[indice].textContent+"|";
        }
    }
    
    if (righeselezionate==0) {
        alert("Attenzione! Non hai selezionato nessun iscritto!");
        return;
    }
    
    document.getElementById("azione").value="recupera";
    document.getElementById("id_iscritti").value=id_iscritti;
    document.getElementById("recupera_cestinati").submit();
}

//Cambia la pagina
function ChangePage(nrpagina,nrpagine) {
  /* controlla che l'operatore non vada fuori dall'intervallo
   * di pagine che sono risultate dalla ricerca dei cancellati */
  if ((nrpagina < 1) || (nrpagina > nrpagine)) { 
      return;
  }
  
  // assegna il nuovo nr di pagina corrente
  document.getElementById("nrpagina").value = nrpagina;

  // invia i dati a PHP
  document.getElementById("recupera_cestinati").submit();

  return;
}




