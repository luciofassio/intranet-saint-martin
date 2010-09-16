// JavaScript Document
// Creato da Marco Fogliadini
// per Oratorio Saint-Martin
// Aosta
// data inizio: 09/05/08    
// versione routine: 1.2

//crea l'array per contenere tutti i tagli dell'euro
var Tagli = new Array();
	// * banconote ***
	Tagli[1]=500; // euro
	Tagli[3]=200; // euro
	Tagli[5]=100; // euro
	Tagli[7]=50; // euro
	Tagli[9]=20; // euro
	Tagli[11]=10; // euro
	Tagli[13]=5; // euro
	Tagli[15]="";
	
	// * monete ***
	Tagli[2]=2; // euro
	Tagli[4]=1; // euro
	Tagli[6]=50; // centesimi
	Tagli[8]=20; // centesimi
	Tagli[10]=10; // centesimi
	Tagli[12]=5; // centesimi
	Tagli[14]=2; // centesimi
	Tagli[16]=1; // centesimi

var contarighe;
var indicesx=1; // indice banconote
var indicedx=2; // indice monete
var messaggio=""; // serve per costruire il messaggio finale della somma. Esempio: hai a disposizione 200 euro e 5 centesimi
	
//costruisce la tabella
document.write("<table id=\"idtabella\">");
document.write("<tr>");
document.write("<th colspan=\"3\">BANCONOTE</TH>");
document.write("<th colspan=\"3\">MONETE</TH>");
document.write("</tr>");

document.write("<tr>");
for (indice=0;indice<2;indice++) {
  document.write("<td class=\"intestazione\">taglio</td>");
  document.write("<td class=\"intestazione\">unit&agrave;</td>");
  document.write("<td class=\"intestazione\">qt&agrave;</td>");
}
document.write("</tr>");

for (contarighe=1;contarighe<9;contarighe++) {
  document.write("<tr>");
  if (contarighe!=8) {
    document.write("<td class=\"banconote\">"+Tagli[indicesx]+"</td>"); // tagli euro
    document.write("<td class=\"banconote\">&euro;</td>"); // colonna che contenente il simbolo dell'euro
    document.write("<td class=\"banconote\"><input type=\"text\" name=\"valore\" size=\"3\" class=\"campobanconote\" /></td>"); // colonna contenente il textbox
    indicesx+=2;
  } else {
      document.write("<td class=\"banconote\" colspan=\"3\"><input type=\"hidden\" name=\"valore\" size=\"3\" /></td>"); // essendo i tagli dell'euro nelle banconote < delle monete stampa una riga vuota
    }
  
  document.write("<td class=\"monete\">"+Tagli[indicedx]+"</td>"); // tagli euro monete 
  if (indicedx > 4) {
    document.write("<td class=\"monete\">cent</td>");
  } else {
      document.write("<td class=\"monete\">&euro;</td>");
    }
  
  document.write("<td class=\"monete\"><input type=\"text\" name=\"valore\" size=\"3\" class=\"campomonete\" /></td>"); // textbox monete
  document.write("</tr>");
  indicedx+=2;
} // chiude ciclo for

document.write("<tr>");
document.write("<td class=\"altrimporti\" colspan=\"4\"><nobr>");
document.write("&nbsp;Altri importi&nbsp;"+"<input type=\"text\" name=\"valore\" size=\"3\" class=\"campoaltrimporti\" />&nbsp;&euro;&nbsp;");
document.write("<input type=\"text\" name=\"valore\" size=\"3\" class=\"campoaltrimporti\" />&nbsp;cent</nobr></td>");
document.write("<td><input type=\"button\" value=\"calcola\" onclick=\"Calcola()\" /></td>");
document.write("<td><input type=\"button\" value=\"pulisci\" onclick=\"Pulisci()\" /></td>");
document.write("</tr>");
document.write("</table>");

var valori=document.getElementsByName("valore");
valori[0].focus();


// funzione per calcolare la somma dei soldi
function Calcola() {
	var sommabanconote=0;
	var sommamonete=0;
  var messaggio;
  
	// controlla se nei campo sono stati digitati dei caratteri non  numerici
  for (indice=0; indice < valori.length; indice++) {
      	if (isNaN(valori[indice].value)) {
      	    if (((indice+1) % 2)==0 && indice > 6) {
               messaggio = "Attenzione! Nel campo da "+Tagli[indice+1] + " cent e' stato digitato un valore non numerico";
            }  else {
                  messaggio = "Attenzione! Nel campo da "+Tagli[indice+1] + " euro e' stato digitato un valore non numerico";
               }

            alert(messaggio);    	    
            valori[indice].focus();
            valori[indice].value="";
      	    return;
      	}  
  }
  
  // conta i soldi in base ai valori che sono stati digitati dall'utente
  for (indice=0; indice < (valori.length-2); indice++) {
	  if (valori[indice].value!="") {
	  	if ((indice % 2) && (indice > 3)) {  
	    	sommamonete+=(valori[indice].value*(Tagli[indice+1]));
	  	} else {
	  	  	sommabanconote+=(valori[indice].value*Tagli[indice+1]);
	  	  }
	  } 
	} // chiude il ciclo for

	if (valori[16].value!=""){
		sommabanconote+=parseInt(valori[16].value);
	}
  
  if (valori[17].value!=""){
		sommamonete+=parseInt(valori[17].value);
	}
	
  if (sommamonete==0 && sommabanconote==0) {
  	alert("Nessuna somma e' disponibile");
  	valori[0].focus();
  	return;
  } else {
  		messaggio ="Hai a disposizione la somma di ";
    }
  	
  if (sommamonete >= 100) {                                
  	if ((sommamonete-(parseInt(sommamonete/100)*100))==0) {			// se sommamonete >= 100 centesimi si entra nell'unità di misura 
		sommabanconote+=parseInt(sommamonete/100);					       // dell'euro. Questi if convertono i centesimi in euro interi.
		sommamonete=0;												                    // La rimanenza rimane in centesimi.                  
	} else {
		sommabanconote+=parseInt(sommamonete/100);
		sommamonete=(sommamonete-(parseInt(sommamonete/100)*100));
  	  }
  }
	
  if (sommabanconote >=1 && sommamonete==0) {
        messaggio +=parseInt(sommabanconote)+" euro!";
  } else {
  		if (sommabanconote!=0) {
  			messaggio +=parseInt(sommabanconote)+" euro e ";
    	}
    }
  
  if (sommamonete > 0 && sommamonete < 100) {
		messaggio+=parseInt(sommamonete)+" centesimi!";
  }  
  
  // un piccolo tocco di eleganza... mette nei valori nulli uno zero
  for (indice=0; indice < valori.length; indice++) {
		if (valori[indice].value=="") {
	     valori[indice].value=0;
    }
  }
  
// stampa il messaggio con il valore dei soldi contati  
  alert(messaggio);
  
	return;
}

// funzione per pulire tutte le cellette della tabella
function Pulisci() {
	for (indice=0; indice < valori.length; indice++) {
		valori[indice].value="";
	}
	valori[0].focus();
	return;
}

 // funzione per aprire la pagina dell'homepage
    function ApriHomePage () {
        window.location.replace("homepage.html");
        return;
    }

