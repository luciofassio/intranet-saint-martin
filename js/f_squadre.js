// JavaScript Document

var iscrittisquadra = new Array();
    iscrittisquadra[0]="Fogliadini Marco";
    iscrittisquadra[1]="Fassio Lucio";
    iscrittisquadra[2]="Chierzi Fabrizio";
    iscrittisquadra[3]="Sabbatini Riccardo";
    iscrittisquadra[4]="Perna Marilena";
    iscrittisquadra[5]="Cerisey Antonella";
    iscrittisquadra[6]="Dupont Michel";
    iscrittisquadra[7]="Paciello Simona";
    iscrittisquadra[8]="Pagliarulo Gianrico";
    iscrittisquadra[9]="Frasson Marco";
    iscrittisquadra[10]="Brogna Adriano";
    iscrittisquadra[11]="Bionaz Gian Maria";
    iscrittisquadra[12]="Dalle Elisa";
    iscrittisquadra[13]="Gagnor Elena";
    iscrittisquadra[14]="Olivotto Hilary";
    iscrittisquadra[15]="Petey Tiziana";
    iscrittisquadra[16]="Sette Cristina";
    iscrittisquadra[17]="Cerisey Valentina";
    iscrittisquadra[18]="Lago Federica";
    iscrittisquadra[19]="Cerutti Giulia";
    
    
    function fncListaIscritti() {
      
      iscrittisquadra.sort() // mette in ordine alfabetico gli iscritti nell'array
      
      document.write("<fieldset>");
      document.write("<legend>Iscritti alla squadra selezionata</legend>");
      document.write("<select name=\"squadreiscritti\" size=\"12\">");
      
      for (indice=0;indice < iscrittisquadra.length; indice++) {
          document.write("<option value=\""+indice+"\" class=\"listaiscritti\">"+iscrittisquadra[indice]+"</option>");
      }
      document.write("</select>");
      document.write("</fieldset>");    
      return;
    } // fine funzione mostra iscritti nelle squadre
    
    
    function fncMostraIscritti (){
      var squadraselezionata = document.getElementsByName("nomesquadre");

      if (parseInt(squadraselezionata[0].value) >= 0) {
          var mydiv = document.getElementById("posizionelista");
          mydiv.style.visibility="visible";
      } else {
          alert("Attenzione! Non hai selezionato nessuna squadra!");
        }  
      return;    
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    