<?php
/* SETUP ACCESSO GATEWAY SMS DI MOBYT PER IL SERVIZIO INVIO MESSAGGINI SUL CELLULARE*/

// Codice cliente Mobyt associato all'oratorio
$account="C04660_HQS";

// Mobyt ha più tipi di qualità per l'invio dei messaggi. L'oratorio ha scelto quella alta (più garanzia di arrivo dell'sms)
$quality="HQS";

// password dispositiva
$pw_gtw="gd7pwqxi";

/* Per la gestione del mittente si sono utilizzati gli array. Si è voluto dare la possibilità di avere più varietà
 * nella scelta del mittente e impedire che utenti diversi possano mandare sms utilizzando un mittente non coerente.
 * L'array utilizzato è multidimensionale. La chiave "primaria" contiene l'associazione che manderà gli sms.
 * Il valore della chiave primaria conterrà i vari mittenti che compariranno sui cellulari e se è elencabile 
 * nella lista mittente del "form".
 *   
 * ATTENZIONE
 *     Il mittente alfanumerico deve essere lungo max 11 caratteri compresi gli spazi. 
 *     Quello numerico max 16 caratteri nel formato internazionel +391231234567 o 00391231234567.
 * 
 *     Non verranno visualizzati nella lista dei mittenti le stringhe più lunghe delle specifiche e quelle che conterranno
 *     caratteri non compresi nel range "a-z", "A-Z" e "+0-9"   
 */       
$mittente=array (
                "oratorio"=>array("OratorioStm"        =>true,
                                   "DNick"             =>true
                                  ),
                                  
                "zaccheo" =>array("AZaccheo"           =>true,
                                  "AZ"                 =>true,
                                  "Zaccheo"            =>true
                                 )
                );


// Metodo utilizzato per l'"accodamento" al gateway Mobyt; l'altro è l'ftp
$metodo_tx="HTTP/POST";

/* Secondo le specifiche MOBYT:
 *   - Con il metodo HTTP/POST si possono inviare contemporaneamente (invio batch) al massimo 5000 sms.
 *   - Con il metodo FTP il numero di invii in contemporanea salgono a 25000.
 * I messaggi che superano questi limiti non vengono presi in considerazione dal gateway di Mobyt 
 */
$max_messaggi_inviabili=5000;

// abilita/disabilita la ricezione delle notifiche di avvenuta spedizione (per ogni singolo numero della lista destinatari)
$abilita_notifiche=false;                            
                               