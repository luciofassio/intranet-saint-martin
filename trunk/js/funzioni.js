	// flag di cancellazione iscrizione
	var richiestaCancellazioneIscrizione = false;
	// id del bottone che ha fatto submit
	var submitKey = "";
	
	// calcolo della data corrente
    var giorni=new Array();
		  giorni[1]="luned"+"&igrave;";
		  giorni[2]="marted"+"&igrave;";
		  giorni[3]="mercoled"+"&igrave;";
		  giorni[4]="gioved"+"&igrave;";
		  giorni[5]="venerd"+"&igrave;";
		  giorni[6]="sabato";
		  giorni[0]="domenica";
		
		var mesi=new Array(11)
		  mesi[1]="gennaio";
		  mesi[2]="febbraio";
		  mesi[3]="marzo";
		  mesi[4]="aprile";
		  mesi[5]="maggio";
		  mesi[6]="giugno";
		  mesi[7]="luglio";
		  mesi[8]="agosto";
		  mesi[9]="settembre";
		  mesi[10]="ottobre";
		  mesi[11]="novembre";
		  mesi[12]="dicembre";
		
		var time=new Date();
		var mese = mesi[time.getMonth()+1];
		var giorno = giorni[time.getDay()];
    var data=time.getDate();
    var anno =time.getFullYear();  
		strdata = giorno + " " + data + " " + mese+ " " + anno;
		
		// array per la creazione della tabella iscrizioni: intestazioni prima colonna
		var intestazione =new Array();
        intestazione[0]="Attivit&agrave;/GG";
        intestazione[1]="&nbsp";
        intestazione[2]="Mattino";
        intestazione[3]="Pomeriggio";
        intestazione[4]="Sera";
        intestazione[5]="Pranzo";
        intestazione[6]="Cena"; 
        
    // array per la creazione della tabella iscrizioni: identificatori checkbox    
    var chkNomeAttivita=new Array();
        chkNomeAttivita[0]=""; //non utilizzato
        chkNomeAttivita[1]=""; //non utilizzato
        chkNomeAttivita[2]="AttivitaMattino[]";
        chkNomeAttivita[3]="AttivitaPomeriggio[]";
        chkNomeAttivita[4]="AttivitaSera[]";
        chkNomeAttivita[5]="Pranzo[]";
        chkNomeAttivita[6]="Cena[]";
        
		
		// definizione dei costi dell'Estate Ragazzi in base al ruolo        
		var CostoIscrizione=15;

		/*var CostoPranzo = new Array(7);
		  CostoPranzo[0]=0; // nessun ruolo selezionato
		  CostoPranzo[1]=5; // animato
		  CostoPranzo[2]=3; // assistente
		  CostoPranzo[3]=3; // aiutante
		  CostoPranzo[4]=3; // animatore
		  CostoPranzo[5]=0; // staff 
		  CostoPranzo[6]=5; // ospiti
		  
		var CostoCena = new Array(7);
		  CostoCena[0]=0; // nessun ruolo selezionato
		  CostoCena[1]=5; // animato
		  CostoCena[2]=3; // assistente
		  CostoCena[3]=3; // aiutante
		  CostoCena[4]=3; // animatore
		  CostoCena[5]=0; // staff 
		  CostoCena[6]=5; // ospiti

		var CostoCenaFinale=10; */
		
		// definizione variabili per indici attivita (tabella prenotazioni)
		var indiceam=new Array(); //indice per attivita del mattino
		var indiceap=new Array(); //indice per attivita del pomeriggio
		var indiceas=new Array(); //indice per attivita della sera
		
		
		// funzione per settare il fuoco su nome utente del form di convalida dati login
		function CaricamentoLogin() {
			setTimeout("document.getElementById(\"txtNomeUtente\").focus();", 1);			// la setTimeout serve a farlo funzionare con FF
			return;
		}
		
		// funzione per settare il fuoco su cerca iscritti della pagina Iscrzioni e Prenotazioni
		function CaricamentoIscrizioni() {
			// posiziono il cursore sul campo di ricerca Cognome
			setTimeout("document.getElementById(\"txtCognome\").focus();", 1);				// la setTimeout serve a farlo funzionare con FF
			// carico la drop down dei luoli se è vuota
			var RuoloIscritto = document.getElementById("RuoloIscritto");
			if (RuoloIscritto.length == 0) {
				$.post("rpc_ruolo.php", {queryString: ""}, function(data){
					if (data.length > 0) {
						$('#RuoloIscritto').html(data);
						RuoloIscritto.selectedIndex = 0;
					}
				});
			}
			// imposto la dropdown degli eventi
			ddlEventi = document.getElementById("Evento");
			ddlEventi.style.backgroundColor=ddlEventi.options[ddlEventi.selectedIndex].style.backgroundColor;
			return;
  		}
		
		// funzione per abilitare/disabilitare il pulsante squadra della pagina Iscrizioni e Prenotazioni
	function chkIscrizioneClick() {
      if (document.SezioneIscrizioni.chkIscrizione.checked==true) {
        	document.SezioneIscrizioni.squadra.disabled=false; // abilita il pulsante squadra
			// imposto l'indicatore per chiedere conferma all'operatore
			richiestaCancellazioneIscrizione = false;
      } else {
        	document.SezioneIscrizioni.squadra.disabled=true; // disabilita il pulsante squadra
			// imposto l'indicatore per chiedere conferma all'operatore
			richiestaCancellazioneIscrizione = true;
      }
		document.getElementById("CostoTotaleEuroCalcolato").value = CalcolaCostoTotale().toFixed(2).replace(".", ",");
		return;
    }
	
	// funzione di convalida dati di login
	function ConvalidaLogin() {
		// definisce la variabile booleana blnControllo e la setta a false
		var blnControllo = false;
            
		// controllo del nome utente inserito 
        if (document.getElementById("txtNomeUtente").value.length ==0) {
			//alert ("Non hai inserito il nome utente!");
			setTimeout("document.getElementById(\"txtNomeUtente\").focus();", 1);			// la setTimeout serve a farlo funzionare con FF
			return;
        } 
		else {
			blnControllo = true;
        }
        
		// controllo della password inserita
        if (document.getElementById("txtPassword").value.length ==0) {
			//alert ("Non hai inserito la password!");
			setTimeout("document.getElementById(\"txtPassword\").focus();", 1);			// la setTimeout serve a farlo funzionare con FF
			return;
        } 
		else {
            blnControllo = true;
        }
      
        return;
    } // chiusura della function ConvalidaLogin	
    
    
    
          
          // ********* funzione per controllare i pagamenti dei pranzi e delle cene ***************
          function ControlliPagamento(contr) {
            var PranzoAbbonamento = document.getElementById("AbbonamentoPranzo");
           	var CenaAbbonamento = document.getElementById("AbbonamentoCena");
			if (IsStringNumeric(document.getElementById("prezzoCenaFinale").innerHTML)) {
				CostoCenaFinale = document.getElementById("prezzoCenaFinale").innerHTML;
			} else {
				CostoCenaFinale = "10,00";
			}
			if (IsStringNumeric(document.getElementById("prezzoCena").innerHTML)) {
				CostoCena = document.getElementById("prezzoCena").innerHTML;
			} else {
				CostoCena = "3,00";
			}
			if (IsStringNumeric(document.getElementById("prezzoPranzo").innerHTML)) {
				CostoPranzo = document.getElementById("prezzoPranzo").innerHTML;
			} else {
				CostoPranzo = "3,00";
			}
			//var Pranzo = null;
			//var PrezzoPranzo = null;
			//var GratisPranzo = null;
			var cella = null;
			
			switch (contr.name) {
				case "Pranzo[]":
					// individuo i tre controlli nella cella
					cella = CercaControlliPranzo(contr)
					// controlla se l'utente ha selezionato il check gratis 
					if (!PranzoAbbonamento.checked) {
						if (cella.Pranzo.checked) {
							//if (cella.CostoPranzo.value=="" || cella.CostoPranzo.value=="0") {
							cella.CostoPranzo.value = CostoPranzo;
							cella.CostoPranzo.disabled = false;
							campoLocale = cella.CostoPranzo;				// per fare funzionare la focus su FF
							setTimeout("campoLocale.focus();", 1);			// per fare funzionare la focus su FF
							cella.CostoPranzo.select();
						}
						else {
						  cella.CostoPranzo.disabled=true;
						} 
					}
					break;
					
				case "Cena[]":
					// individuo i tre controlli nella cella
					cella = CercaControlliCena(contr)
					// controlla se l'utente ha selezionato il check gratis 
					if (!CenaAbbonamento.checked) {
						if (cella.Cena.checked) {
							//if (cella.CostoCena.value=="" || cella.CostoCena.value=="0") {
							cella.CostoCena.value=CostoCena;
							cella.CostoCena.disabled=false;
							campoLocale = cella.CostoCena;						// per fare funzionare la focus su FF
							setTimeout("campoLocale.focus();", 1);			// per fare funzionare la focus su FF
							cella.CostoCena.select();
						}
						else {
						  cella.CostoCena.disabled=true;
						} 
					}
					break;
					
				case "GratisPranzo[]":
					// individuo i tre controlli nella cella
					cella = CercaControlliPranzo(contr)
					// controlla se l'utente ha selezionato il check gratis 
					if (!PranzoAbbonamento.checked) {
						if (cella.GratisPranzo.checked) {
							cella.Pranzo.checked = true;
							cella.Pranzo.disabled = true;
							cella.CostoPranzo.value = "0,00";
						}
						else {
							cella.Pranzo.checked = false;
							cella.Pranzo.disabled = false;
							cella.CostoPranzo.value="";
						} 
					}
					break;
					
				case "GratisCena[]":
					// individuo i tre controlli nella cella
					cella = CercaControlliCena(contr)
					// controlla se l'utente ha selezionato il check gratis 
					if (!CenaAbbonamento.checked) {
						if (cella.GratisCena.checked) {
							cella.Cena.checked = true;
							cella.Cena.disabled = true;
							cella.CostoCena.value = "0,00";
						}
						else {
							cella.Cena.checked = false;
							cella.Cena.disabled = false;
							cella.CostoCena.value="";
						} 
					}
					break;
			}
			document.getElementById("CostoTotaleEuroCalcolato").value = CalcolaCostoTotale().toFixed(2).replace(".", ",");
			return;
		}

		// copio il valore calcolato nella casella del costo effettivo
		function CopiaInCostoTotaleEuro(costoCalcolato) {
			$('#CostoTotaleEuro').val(costoCalcolato.value);
		}
		
        // calcolo il totale dei costi
		function CalcolaCostoTotale() {
            var totale = 0.0;
			var CostoPranzoAbbonamento = NumberEU(document.getElementById("prezzoAbbPranzo").innerHTML);
            var CostoCenaAbbonamento = NumberEU(document.getElementById("prezzoAbbCena").innerHTML);
            var CostoPranzoCenaAbbonamento = NumberEU(document.getElementById("prezzoAbbPranzoCena").innerHTML);
            var CostoEventoSpecialeER = NumberEU(document.getElementById("prezzoEventoSpecialeER").innerHTML);
			
            var Iscrizione = document.getElementById("Iscrizione");            
            var PranzoAbbonamento = document.getElementById("AbbonamentoPranzo");
           	var CenaAbbonamento = document.getElementById("AbbonamentoCena");
           	var EventoSpecialeER = document.getElementById("EventoSpecialeER");

			var ruoloIscr = document.getElementById("RuoloIscritto");
			
			// calcolo l'iscrizione			
			if (IsStringNumeric(document.getElementById("prezzoIscrizione").innerHTML)) {
				CostoIscrizione = document.getElementById("prezzoIscrizione").innerHTML;
			} 
			if (Iscrizione.checked){
				totale += NumberEU(CostoIscrizione);
			}			
			
			// calcolo i pranzi e le cene settimanali
			if (PranzoAbbonamento.checked && CenaAbbonamento.checked) {
				// abbonamento completo
				totale += CostoPranzoCenaAbbonamento;
			} else {			
				if (!PranzoAbbonamento.checked) {
					// pranzi singoli
					var Pranzi = document.getElementsByName("Pranzo[]");
		            var CostoPranzi = document.getElementsByName("CostoPranzo[]");
		            var PranziGratis = document.getElementsByName("GratisPranzo[]");
		            for (i=0; i < PranziGratis.length; i++) {
						if (!PranziGratis[i].checked) {
							if (Pranzi[i].checked) {
								if (IsStringNumeric(CostoPranzi[i].value)) {
									totale += NumberEU(CostoPranzi[i].value);
								}
							}
						}
					}
				} else {
					//abbonamento pranzo
					totale += CostoPranzoAbbonamento;
				}
				
				if (!CenaAbbonamento.checked) {
					// cene singole
					var Cene = document.getElementsByName("Cena[]");
		            var CostoCene = document.getElementsByName("CostoCena[]");
		            var CeneGratis = document.getElementsByName("GratisCena[]");
		            for (i=0; i < CeneGratis.length; i++) {
						if (!CeneGratis[i].checked) {
							if (Cene[i].checked) {
								if (IsStringNumeric(CostoCene[i].value)) {
									totale += NumberEU(CostoCene[i].value);
								}
							}
						}
					}
				} else {
					// abbonamento cena
					totale += CostoCenaAbbonamento;
				}
			}
			
			// calcolo la cena finale e gli ospiti
			if (document.getElementById("CenaFinale").checked) {
				var CostoCenaFinale = 0;
				var NrOspiti = document.getElementById("NrOspiti");
	            var CostoTotale = document.getElementById("CostoTotaleEuroCalcolato"); 
				if (IsStringNumeric(document.getElementById("prezzoCenaFinale").innerHTML)) {
					CostoCenaFinale = NumberEU(document.getElementById("prezzoCenaFinale").innerHTML);
				}
				if (IsStringNumeric(NrOspiti.value)) {
					// se hanno l'abbonamento la cena finale e' gratis					
					if (PranzoAbbonamento.checked && CenaAbbonamento.checked) {					
						totale += ((NrOspiti.value) * CostoCenaFinale);
					} else {
						totale += ((NrOspiti.value) * CostoCenaFinale) + CostoCenaFinale;
					}
				}
			}
			
			// calcolo l'evento speciale
			if (EventoSpecialeER.checked) {
				totale += CostoEventoSpecialeER;
			}
			return totale;
		}
		
		// ********* funzione per calcolare quanto dovuto dagli ospiti della cena finale ER ***************
        function CalcolaCenaFinale() { 
			var NrOspiti = document.getElementById("NrOspiti");
			if (IsStringNumeric(NrOspiti.value)) {
				NrOspiti.className = "";
				// ricalcolo il totale
				document.getElementById("CostoTotaleEuroCalcolato").value = CalcolaCostoTotale().toFixed(2).replace(".", ",");			
			} else {
				alert("Il numero degli ospiti deve essere numerico");
				NrOspiti.className = "campoerrato";
				campoLocale = NrOspiti;
				setTimeout("campoLocale.focus();", 1);			// la setTimeout serve a farlo funzionare con FF
			}
            return;              
          }

		// ********* funzione per controllare la prenotazione e il pagamento della cena finale ***************
		function ControlloCenaFinale() { 
			var Prenotazione = document.getElementById("CenaFinale");
			var NrOspiti = document.getElementById("NrOspiti");
			var Iscrizione = document.getElementById("Iscrizione");
			if (!Iscrizione.checked && Prenotazione.checked) {
				alert("Non è possibile registrare la prenotazione della cena finale senza l'iscrizione, almeno come ospite");
				return false;
			}

			if (Prenotazione.checked) {
				NrOspiti.disabled = false;
			} else {
				NrOspiti.disabled = true;
			}
		return;
		}

		// ********* funzione per controllare la prenotazione e il pagamento dell'evento speciale ***************
		function ControlloEventoSpecialeER() { 
			var EventoSpecialeER = document.getElementById("EventoSpecialeER");
			var Iscrizione = document.getElementById("Iscrizione");
			if (!Iscrizione.checked && EventoSpecialeER.checked) {
				alert("Non è possibile registrare la prenotazione dell'evento speciale senza l'iscrizione, almeno come ospite");
				return false;
			}
			document.getElementById("CostoTotaleEuroCalcolato").value = CalcolaCostoTotale().toFixed(2).replace(".", ",");
		return;
		}

		// ************** funzione per il controllo della selezione dell'abbonamento pranzi *****************
        function ControlloAbbonamentoPranzo() {
            var i;
			var AbbonamentoPranzo = document.getElementById("AbbonamentoPranzo");
			var Iscrizione = document.getElementById("Iscrizione");
			if (!Iscrizione.checked && AbbonamentoPranzo.checked) {
				alert("Non è possibile registrare l'abbonamento ai pranzi senza l'iscrizione, almeno come ospite");
				return false;
			}
			// chiedo conferma per la cancellazione dell'abbonamento
			if (!AbbonamentoPranzo.checked) {
				//if (!confirm("Attenzione! Sei sicuro di voler togliere all'iscritto l'abbonamento pranzi?")) {
					//AbbonamentoPranzo.checked = true;
				//}
			} else {
				//if (confirm("Attenzione! Impostando l'abbonamento pranzi verranno annullati tutti i pranzi singoli\nVuoi proseguire?")) {
					// annullo i pranzi singoli se è impostato l'abbonamento
					var Pranzi = document.getElementsByName("Pranzo[]");
		            var PranziGratis = document.getElementsByName("GratisPranzo[]");
					for (i=0; i < Pranzi.length; i++) {
						Pranzi[i].checked = false;
						PranziGratis[i].checked = false;
					}
				//} else {
				//	AbbonamentoPranzo.checked = false;
				//}
			}
			// ricalcolo il totale
			document.getElementById("CostoTotaleEuroCalcolato").value = CalcolaCostoTotale().toFixed(2).replace(".", ",");
			return; 
        } // fine funzione controllo dell'abbonamento pranzo
        
        // ************** funzione per il controllo della selezione dell'abbonamento cena *****************
        function ControlloAbbonamentoCena() {
            var i;
			var AbbonamentoCena = document.getElementById("AbbonamentoCena");
			var Iscrizione = document.getElementById("Iscrizione");
			if (!Iscrizione.checked && AbbonamentoCena.checked) {
				alert("Non è possibile registrare l'abbonamento alle cene senza l'iscrizione, almeno come ospite");
				return false;
			}
            // chiedo conferma per la cancellazione dell'abbonamento
            if (!AbbonamentoCena.checked) {
				//if (!confirm("Attenzione! Sei sicuro di voler togliere all'iscritto l'abbonamento cene?")) {
					//AbbonamentoCena.checked = true;
				//}
			} else {
				//if (confirm("Attenzione! Impostando l'abbonamento cene verranno annullati tutti le cene singole\nVuoi proseguire?")) {
					// annullo le cene singole se è impostato l'abbonamento
					var Cene = document.getElementsByName("Cena[]");
		            var CeneGratis = document.getElementsByName("GratisCena[]");
					for (i=0; i < Cene.length; i++) {
						Cene[i].checked = false;
						CeneGratis[i].checked = false;
					}
				//} else {
				//	AbbonamentoCena.checked = false;
				//}
			}
			document.getElementById("CostoTotaleEuroCalcolato").value = CalcolaCostoTotale().toFixed(2).replace(".", ",");
			return; 
        } // fine funzione controllo abbonamento cena
        
		function CercaControlliPranzo(contr) {
			var Pranzo = null;
			var CostoPranzo = null;
			var GratisPranzo = null;
			switch (contr.name) {
				case "Pranzo[]":
					// sono su Pranzo cerco a destra del checkbox
					Pranzo = contr;
					scan = contr;
					while(scan) {
						//alert("Next: "+GratisPranzo.nextSibling);
						if (scan.nextSibling) {
							switch (scan.nextSibling.name) {
								case "CostoPranzo[]":
									//alert(GratisPranzo.nextSibling.name);
									CostoPranzo = scan.nextSibling;
									break;
								case "GratisPranzo[]":
									//alert(GratisPranzo.nextSibling.name);
									GratisPranzo = scan.nextSibling;
									break;
							}
						}
						scan = scan.nextSibling;
					}
					break;
					
				case "GratisPranzo[]":
					// sono su GratisPranzo cerco a sinistra del checkbox
					GratisPranzo = contr;
					scan = contr;
					while(scan) {
						//alert("Next: "+Pranzo.nextSibling);
						if (scan.previousSibling) {
							switch (scan.previousSibling.name) {
								case "CostoPranzo[]":
									//alert(Pranzo.nextSibling.name);
									CostoPranzo = scan.previousSibling;
									break;
								case "Pranzo[]":
									//alert(Pranzo.nextSibling.name);
									Pranzo = scan.previousSibling;
									break;
							}
						}
						scan = scan.previousSibling;
					}
					break;
			}
			return {Pranzo:Pranzo,CostoPranzo:CostoPranzo,GratisPranzo:GratisPranzo};
		}

		function CercaControlliCena(contr) {
			var Cena = null;
			var CostoCena = null;
			var GratisCena = null;
			switch (contr.name) {
				case "Cena[]":
					// sono su Cena cerco a destra del checkbox
					Cena = contr;
					scan = contr;
					while(scan) {
						//alert("Next: "+GratisCena.nextSibling);
						if (scan.nextSibling) {
							switch (scan.nextSibling.name) {
								case "CostoCena[]":
									//alert(GratisCena.nextSibling.name);
									CostoCena = scan.nextSibling;
									break;
								case "GratisCena[]":
									//alert(GratisCena.nextSibling.name);
									GratisCena = scan.nextSibling;
									break;
							}
						}
						scan = scan.nextSibling;
					}
					break;
					
				case "GratisCena[]":
					// sono su GratisCena cerco a sinistra del checkbox
					GratisCena = contr;
					scan = contr;
					while(scan) {
						//alert("Next: "+Pranzo.nextSibling);
						if (scan.previousSibling) {
							switch (scan.previousSibling.name) {
								case "CostoCena[]":
									//alert(Pranzo.nextSibling.name);
									CostoCena = scan.previousSibling;
									break;
								case "Cena[]":
									//alert(Pranzo.nextSibling.name);
									Cena = scan.previousSibling;
									break;
							}
						}
						scan = scan.previousSibling;
					}
					break;
			}
			return {Cena:Cena,CostoCena:CostoCena,GratisCena:GratisCena};
		}
		
		  // *********** funzione per il controllo delle prenotazioni attività **************
       function ControlloAttivita() {
       
          var AttivitaAm = document.getElementsByName("AttivitaMattino");
          var AttivitaAp = document.getElementsByName("AttivitaPomeriggio");
          var AttivitaAs = document.getElementsByName("AttivitaSera");
          
          //inizializza a false l'array indiceam)
          for (indice=0; indice < AttivitaAm.length; indice++) {
              if (indiceam[indice]==undefined) {
                  indiceam[indice]=false;
              }
          }
          
          //inizializza a false l'array indiceap
          for (indice=0; indice < AttivitaAp.length; indice++) {
              if (indiceap[indice]==undefined) {
                  indiceap[indice]=false;
              }
          }
          
          //inizializza a false l'array indiceas
          for (indice=0; indice < AttivitaAs.length; indice++) {
              if (indiceas[indice]==undefined) {
                  indiceas[indice]=false;
              }
          }
          
          // cerca i checkbox dell'attività del mattino selezionati e visualizza l'informazione
          for (indice=0; indice < AttivitaAm.length; indice++) {
              if (AttivitaAm[indice].checked && !indiceam[indice]){
                  if (confirm("Attenzione! Hai selezionato un'attivita' mattutina di un giorno gia' trascorso... Premi OK per tenere la selezione, ANNULLA per cancellarla")) {
                      indiceam[indice]=true; 
                  } else {
                      AttivitaAm[indice].checked=false;
                    }
                  break;          
              }  
              
               if (!AttivitaAm[indice].checked && (indiceam[indice]!=false)){
                  indiceam[indice]=false;
                }
          }
          
          // cerca i checkbox dell'attività del pomeriggio selezionati e visualizza l'informazione
          for (indice=0; indice <AttivitaAp.length; indice++) {
              if (AttivitaAp[indice].checked && !indiceap[indice]){
                  if (confirm("Attenzione! Hai selezionato un'attivita' pomeridiana di un giorno gia' trascorso... Premi OK per tenere la selezione, ANNULLA per cancellarla")) {
                      indiceap[indice]=true; 
                  } else {
                      AttivitaAp[indice].checked=false;
                    }
                  break;          
              }  
              
               if (!AttivitaAp[indice].checked && (indiceap[indice]!=false)){
                  indiceap[indice]=false;
                }
          }
          
          // cerca i checkbox dell'attività della sera selezionati e visualizza l'informazione
          for (indice=0; indice <AttivitaAs.length; indice++) {
              if (AttivitaAs[indice].checked && !indiceas[indice]){
                  if (confirm("Attenzione! Hai selezionato un'attivita' serale di un giorno gia' trascorso... Premi OK per tenere la selezione, ANNULLA per cancellarla")) {
                      indiceas[indice]=true; 
                  } else {
                      AttivitaAs[indice].checked=false;
                    }
                  break;          
              }  
              
               if (!AttivitaAs[indice].checked && (indiceas[indice]!=false)){
                  indiceas[indice]=false;
                }
          }
       return;
       } // chiude funzione Controllo delle prenotazioni attività
		      		      
		  // funzione per creare la finestra squadra
		  function FinestraSquadra() {
            var miaFinestra;
            var CentraFinestraLeft=(1024-520)/2;
            var CentraFinestraTop = (768-500)/2;
			idpersona = document.getElementById('hdnID').value;
			idEventoCorrente = document.getElementById('Evento').value;
            miaFinestra=window.open("squadra.php?id="+idpersona+"&idevcorr="+idEventoCorrente,"FinestraSquadra",'width=520,height=500,top='+CentraFinestraTop+',left='+CentraFinestraLeft);
          return;
          }
		      
// funzione per creare finestra listino estate ragazzi
function aprilistino() {
	var myListino;
	var CentraFinestraLeft=(1024-990)/2;
	var CentraFinestraTop = (768-550)/2;
	idEventoCorrente = document.getElementById('Evento').value;
	myListino=window.open("listino.php?idevcorr="+idEventoCorrente,"Listino","width=990,height=550,top="+CentraFinestraTop+",left="+CentraFinestraLeft);
	return;
}

// aggiorna il listino in ajax
function AggiornaListino(ruolo) {
	var idRuolo = ruolo.options[ruolo.selectedIndex].value;
	Evento = document.getElementById("Evento");
	var idEvento = Evento.options[Evento.selectedIndex].value;
	$.post("rpc_listino.php", {IDRuolo: ""+idRuolo+"", IDEvento: ""+idEvento+""}, function(data){
		if (data.length > 0) {
			$('#listino_prezzi').html(data);
			// attivo/disattivo automaticamente l'iscrizione
			if(idRuolo != 0 && idRuolo != 6) {		
				document.getElementById("Iscrizione").checked = true;		
			} else {		
				document.getElementById("Iscrizione").checked = false;
			}
			chkIscrizioneClick();
		}
	});
}

// controllo di numericità
function ControlloNumerico(campo) {
	if (isNaN(campo.value.replace(",", "."))) {
		alert ("Il valore inserito non \xe8 numerico");
		campo.className = "campoerrato";
		campoLocale = campo;							// altrimenti la setTimeout non riconosce campo !!
		setTimeout("campoLocale.focus();", 1);			// la setTimeout serve a farlo funzionare con FF
	}
	else {
		if (campo.value == "") {						// se il campo è vuoto metto zero
			campo.value = "0";
		}
		if (Number(campo.value) < 0) {
			alert ("Il valore inserito deve essere positivo");
			campo.className = "campoerrato";
			campoLocale = campo;							// altrimenti la setTimeout non riconosce campo !!
			setTimeout("campoLocale.focus();", 1);			// la setTimeout serve a farlo funzionare con FF
		}
		else {
			campo.value = campo.value.replace(".", ",");	// sostituisco l'eventuale punto decimale con la virgola
			campo.className = "";
		}
	}
}		      

// valuta la numericità di una stringa
// true= numerico, false=non numerico
function IsStringNumeric(campo) {
	if (isNaN(campo.replace(",", "."))) {
		return false;
	}
	else {
		if (campo == "") {
			return false;
		}
		else {
			return true;
		}
	}
}

// converte un numero con la virgola (stringa) in numero
function NumberEU(num) {
	num = num.replace(",", ".");
	return Number(num);
}

function ValidaPrenotazioni() {
	// se è stato inserito il bar code evito i controlli
	if (submitKey == "caricaPersona") {
		return true;
	}
	if (richiestaCancellazioneIscrizione) {
		var r = confirm("Attenzione: insieme all'iscrizione saranno cancellate tutte le prenotazioni inserite. Per confermare fai click su Ok, per annullare su Annulla")
		if (r) {
			return true;
		} else {
			return false;
		}
	} else {
		// controllo che sia stata selezionata una persona
		var ID = document.getElementById("hdnID").value;
		if (ID == "") {
			alert ("Devi scegliere una persona per potere effettueare le prenotazioni.");
			return false;
		}
		var ruoloIscr = document.getElementById("RuoloIscritto");
		if (ruoloIscr.selectedIndex == 0) {
			alert("Devi selezionare un ruolo per l'iscritto prima di registrare l'iscrizione");
			return false;
		}
		var tesserato = document.getElementById("tesserato").innerHTML;
		// controllo sul ruolo di ospite
		if(tesserato.toLowerCase() == "no" && document.getElementById("Iscrizione").checked && ruoloIscr.options[ruoloIscr.selectedIndex].innerHTML.toLowerCase() != "ospite") {
			alert("Se la persona non è tesserata può essere iscritta all'ER solo come ospite.\nIl tesseramento oltre agli altri vantaggi dà la copertura assicurativa");
			return false;
		}
		// controllo sull'iscrizione all'ER
		if(tesserato.toLowerCase() == "no" && !document.getElementById("Iscrizione").checked) {
			alert("Se la persona non è tesserata e non è iscritta all'ER non può prenotare gli eventi dell'ER.\nIl tesseramento oltre agli altri vantaggi dà la copertura assicurativa");
			return false;
		}
		return true;
	}
}		
      
// autocomplete in ajax
// inputstring:	stringa da ricercare nel db
// field:Id		campo sul quale fare la ricerca, serve a posizionare l'elenco
function lookup(inputString, fieldId) {
	if(inputString.length == 0) {
		// Hide the suggestion box.
		$('#suggestions').hide();
	} else {
		switch (fieldId) {
			case "cognome":
				$('#suggestions').css("left","100px");
				web_service = "rpc.php";
				break;
			case "nome":
				$('#suggestions').css("left","336px");
				web_service = "rpc_nome.php";
				break;
		}
		$.post(web_service, {queryString: ""+inputString+""}, function(data){
			if (data.length > 0) {
				$('#suggestions').show();
				$('#autoSuggestionsList').html(data);
			}
		});
	}
} // lookup

function fill(thisValue) {
	if (thisValue != null) {
		var modulo = document.getElementById("SezioneIscrizioni");
		var valori = thisValue.split('|');		
		var idEventoCorrente = $("#Evento").val();		// salvo l'evento corrente
		$(":input").val("");							// azzero tutti i campi input
		$("#Evento").val(idEventoCorrente);				// ripristino l'evento corrente
		$("#postback").val("1");						// ripristino il postback
		$('#hdnID').val(valori[0]);						// carico i valori della persona selezionata dall'elenco a discesa
		$('#txtCognome').val(valori[1]);
		$('#txtNome').val(valori[2]);
		$('#txtBarCode').val(valori[3]);
		$('#data_loaded').val('0');						// azzero il flag che forza la lettura dei dati dal db
		setTimeout("$('#suggestions').hide();", 200);
		modulo.submit();
	}
}

function CaricaIscrizione() {
	var modulo = document.getElementById("SezioneIscrizioni");
	var idEventoCorrente = $("#Evento").val();		// salvo l'evento corrente
	var idPersona = $("#hdnID").val();				// salvo l'id della persona
	$(":input").val("");							// azzero tutti i campi input
	$("#Evento").val(idEventoCorrente);				// ripristino l'evento corrente
	$("#postback").val("1");						// ripristino il postback
	$('#hdnID').val(idPersona);						// ripristino id persona
	$('#data_loaded').val('0');						// azzero il flag che forza la lettura dei dati dal db
	modulo.submit();
}

var EventoBackColor = "";
var ClickOnce = false;
function EventoChange(ev) {
	ev.style.backgroundColor = ev.options[ev.selectedIndex].style.backgroundColor;
	EventoBackColor = ev.style.backgroundColor;
	//v = $("#note").val() + "-change";
	//$("#note").val(v);
}

function EventoClick(ev) {
	//v = $("#note").val() + "-" + ClickOnce + EventoBackColor;
	//$("#note").val(v);
	if (ClickOnce) {
		ev.style.backgroundColor = EventoBackColor;
	}
	ClickOnce = !ClickOnce;
	//v = $("#note").val() + "-click";
	//$("#note").val(v);
}

function EventoMouseDown(ev) {
	if (!ClickOnce) {
		EventoBackColor = ev.style.backgroundColor;
	}
	ev.style.backgroundColor = 'white';
	//v = $("#note").val() + "-down";
	//$("#note").val(v);
}

function PagamentoCompleto(dapagare, pagato) {
	var da_pagare = document.getElementById(dapagare);
	if (IsStringNumeric(da_pagare.value) && IsStringNumeric(pagato.value)) {
		// imposto il colore rosso se insufficiente
		if (Number(da_pagare.value.replace(",", ".")) <= Number(pagato.value.replace(",", "."))) {
			pagato.className = "nero";
		} else {
			pagato.className = "rosso";
		}
		// formatto con i decimali
		//pagato.value = NumberEU(pagato.value).toFixed(2).replace(".", ",");
	} else {
		// controllo numericità
		ControlloNumerico(pagato);
	}
}	      
		      
function AbilitaCampiPerSubmit() {		      
	document.getElementById("CostoTotaleEuro").disabled = false;
}

// funzione per aprire e chiudere il div release&credits nell'home page
function ReleaseCredits(myaction)
{
  switch (myaction) {
      case "apri":
          document.getElementById("release_credits").style.visibility= "visible";
      break;
      
      case "chiudi":
          document.getElementById("release_credits").style.visibility= "hidden";
      break;
  }
}

			/*********************** SEZIONE PRANZI ********************************/

            //******** trova l'indice del checkbox del pranzo selezionato ********
            /* eliminato

            for (indice=0; indice < Pranzi.length; indice++) { 
                if (Pranzi[indice].checked && !Pranzi[indice].disabled && CostoPranzi[indice].disabled) {
            		    CostoPranzi[indice].disabled=false;
                    
                    // individua il ruolo selezionato dall'utente e assegna il costo alla casella di testo relativa
                    var ruolo=document.SezioneIscrizioni.RuoloIscritto.options[document.SezioneIscrizioni.RuoloIscritto.selectedIndex].value;
                    if (ruolo==0) { // nessun ruolo selezionato
                        alert("Attenzione non e' stato selezionato nessun ruolo. Impossibile conteggiare il costo del pranzo");
                        Pranzi[indice].checked=false;
                        CostoPranzi[indice].disabled=true;
                        PranziGratis[indice].disabled=false;                
                        return;
                    } else {
                          CostoPranzi[indice].value=CostoPranzo[ruolo];
                          PranziGratis[indice].disabled=true;
                          break;
                      }
            	    } // chiude l'if pranzi
            } //chiude il ciclo for
                                  
            // trova l'indice dei checkbox pranzi deselezionati per ristabilire situazione di partenza
				    for (indice=0; indice < Pranzi.length; indice++) { //trova l'indice del checkbox deselezionato
                if (!Pranzi[indice].checked && !CostoPranzi[indice].disabled) {
            		    CostoPranzi[indice].disabled=true;
            		    CostoPranzi[indice].value="";
            		    PranziGratis[indice].disabled=false;
            			  break;
            	  }
            } //chiude ciclo for

            /*********************** SEZIONE CENE ********************************/
             
		        
            //******** trova l'indice del checkbox della cena selezionata ********

			/* eliminato
			
            for (indice=0; indice < Cene.length; indice++) { 
                if (Cene[indice].checked && !Cene[indice].disabled && CostoCene[indice].disabled) {
            		    CostoCene[indice].disabled=false;
                    
                    // individua il ruolo selezionato dall'utente e assegna il costo alla casella di testo relativa
                    var ruolo=document.SezioneIscrizioni.RuoloIscritto.options[document.SezioneIscrizioni.RuoloIscritto.selectedIndex].value;
                    if (ruolo==0) { // nessun ruolo selezionato
                        alert("Attenzione non e' stato selezionato nessun ruolo. Impossibile conteggiare il costo della cena");
                        Cene[indice].checked=false;
                        CostoCene[indice].disabled=true;
                        CeneGratis[indice].disabled=false;                
                        return;
                    } else {
                          CostoCene[indice].value=CostoCena[ruolo];
                          CeneGratis[indice].disabled=true;
                          break;
                      }
            	    } // chiude l'if cene
            } //chiude il ciclo for
                                  
            // trova l'indice dei checkbox cene deselezionate per ristabilire situazione di partenza
				    for (indice=0; indice < Cene.length; indice++) { //trovo l'indice del checkbox deselezionato
                if (!Cene[indice].checked && !CostoCene[indice].disabled) {
            		    CostoCene[indice].disabled=true;
            		    CostoCene[indice].value="";
            		    CeneGratis[indice].disabled=false;
            			  break;
            	  }
            } //chiude ciclo for
            return;
        } // ************** fine funzione Controllo Pagamenti *********************
        */
	
	       /*     var Pranzi=document.getElementsByName("Pranzo");
            var CostoPranzi=document.getElementsByName("CostiPranzo");
            var PranziGratis=document.getElementsByName("PranzoGratis");
            var PranzoAbbonamento=document.getElementsByName("AbbonamentoPranzo");
            var ruolo=document.SezioneIscrizioni.RuoloIscritto.options[document.SezioneIscrizioni.RuoloIscritto.selectedIndex].value;
                             
            //****** controlla se l'utente ha selezionato il check dell'abbonamento pranzo *******
            if (PranzoAbbonamento[0].checked) {
                // disabilita i checkbox "gratis" e i textbox del costo dei pranzi
                for (indice=0; indice < PranziGratis.length; indice++) {
                      PranziGratis[indice].disabled=true;
                      CostoPranzi[indice].disabled=true;
                } //chiude il ciclo for
                return;
            } else {
                  if (confirm("Attenzione! Sei sicuro di voler togliere all'iscritto l'abbonamento pranzi?")) {
                      for (indice=0; indice < PranziGratis.length; indice++) {
                          PranziGratis[indice].disabled=false;
                          if (CostoPranzi[indice].value=="" && Pranzi[indice].checked) {
                               if (ruolo==0) { // nessun ruolo selezionato
                                  alert("Attenzione non e' stato selezionato nessun ruolo. Impossibile conteggiare il costo del pranzo");
                                  PranzoAbbonamento[0].checked=true;
                                  PranziGratis[indice].disabled=true;
                                  return;
                              } else {
                                  CostoPranzi[indice].disabled=false;
                                  CostoPranzi[indice].value=CostoPranzo[ruolo];
                                  PranziGratis[indice].disabled=true;
                                }
                          } else {
                              if (CostoPranzi[indice].value!="" ) {
                                  if (CostoPranzi[indice].value=="0") {
                                      CostoPranzi[indice].disabled=true;
                                      PranziGratis[indice].disabled=false;
                                  } else {
                                      CostoPranzi[indice].disabled=false;
                                      PranziGratis[indice].disabled=true;
                                      Pranzi[indice].disabled=false;
                                    }
                                }
                            }
                      } //chiude il ciclo for
                  } else {
                        PranzoAbbonamento[0].checked=true;
                    }
                  }
	      */
		      
		/*
		            var Cene=document.getElementsByName("Cena");
            var CostoCene=document.getElementsByName("CostiCena");
            var CeneGratis=document.getElementsByName("CenaGratis");
           	var CenaAbbonamento=document.getElementsByName("AbbonamentoCena");
            var ruolo=document.SezioneIscrizioni.RuoloIscritto.options[document.SezioneIscrizioni.RuoloIscritto.selectedIndex].value;
                             
             //****** controlla se l'utente ha selezionato il check dell'abbonamento cena *******
            if (CenaAbbonamento[0].checked) {
                // disabilita i checkbox "gratis" e i textbox del costo delle cene
                for (indice=0; indice < CeneGratis.length; indice++) {
                      CeneGratis[indice].disabled=true;
                      CostoCene[indice].disabled=true;
                } //chiude il ciclo for
                return;
            } else {
                  if (confirm("Attenzione! Sei sicuro di voler togliere all'iscritto l'abbonamento alle cene?")) {
                      for (indice=0; indice < CeneGratis.length; indice++) {
                          CeneGratis[indice].disabled=false;
                          if (CostoCene[indice].value=="" && Cene[indice].checked) {
                               if (ruolo==0) { // nessun ruolo selezionato
                                  alert("Attenzione non e' stato selezionato nessun ruolo. Impossibile conteggiare il costo della cena");
                                  CenaAbbonamento[0].checked=true;
                                  CeneGratis[indice].disabled=true;
                                  return;
                              } else {
                                  CostoCene[indice].disabled=false;
                                  CostoCene[indice].value=CostoPranzo[ruolo];
                                  CeneGratis[indice].disabled=true;
                                }
                          } else {
                             if (CostoCene[indice].value!="") {
                                  if (CostoCene[indice].value=="0") {
                                      CostoCene[indice].disabled=true;
                                      CeneGratis[indice].disabled=false;
                                  } else {
                                      CostoCene[indice].disabled=false;
                                      CeneGratis[indice].disabled=true;
                                      Cene[indice].disabled=false;
                                  }  
                                }
                            }
                      } //chiude il ciclo for
                  } else {
                        CenaAbbonamento[0].checked=true;
                    }
                  }
		*/
		// funzione per il controllo dei costi in base al ruolo all'interno dell'Er
		// eliminata **************************************
		/*function ControlloRuolo() {
	
            // individua il ruolo selezionato dall'utente
              var ruolo=document.SezioneIscrizioni.RuoloIscritto.options[document.SezioneIscrizioni.RuoloIscritto.selectedIndex].value;
            
            // visualizza i costi del pranzo
              var strPranzo = document.getElementById("etichettapranzo");
              if (ruolo==0) {
                strPranzo.innerText = " **** ";
              } else {
              		if (ruolo==5) {
              			strPranzo.innerText = " Gratuito ";
              		} else {
	                    strPranzo.innerText = " "+CostoPranzo[ruolo];
			 		  }                
              	}
              	
             // visualizza i costi della cena
              var strCena = document.getElementById("etichettacena");
              if (ruolo==0) {
              	strCena.innerText = " **** ";
              } else {
              		if (ruolo==5) {
              			strCena.innerText = " Gratuita ";
              		} else {
	                    strCena.innerText =  " " + CostoCena[ruolo];
			 		  }                
              	}
 				
 				// visualizza i costi di iscrizione
 				  var strIscrizioni = document.getElementById("etichettaiscrizione");
 				  if (ruolo!=0) {
 				  	if (ruolo==6) {
              strIscrizioni.innerText = " **** ";
 				  	} else {
                strIscrizioni.innerText = " " + CostoIscrizione;
             }
 				  } else {
 				  		strIscrizioni.innerText = " **** ";  
 				  	}
 				  	
 				// visualizza i costi della cena finale
 				  var strCenaFinale = document.getElementById("etichettacenafinale");
 				  if (ruolo!=0) {
 				  	strCenaFinale.innerText = " " + CostoCenaFinale;
 				  } else {
 				  		strCenaFinale.innerText = " **** ";
 				  	}	
              return;   
           } // chiusura function ControlloRuolo()  */     
		      
		      
		      
