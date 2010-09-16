<?php
// Funzioni di utilità
function postClear($nomeCampo) {
	// se trovo [] alla fine del nome lo tolgo
	if (substr($nomeCampo, -2, 2) == "[]") {
		$nomeCampo = substr($nomeCampo, 0, strlen($nomeCampo) - 2);
	}
	// è impostato il post?
	if (isset($_POST[$nomeCampo])) {
		//  è un array ?
		if (is_array($_POST[$nomeCampo])) {
			// è un array
			// &$valContr significa che  viene creato un puntamento all'array, quindi le modifiche 
			// vengono applicate all'array direttamente invece che ad una copia
			foreach ($_POST[$nomeCampo] as &$valContr) {
				$valContr = "";
			}
			unset($valContr); // annulla il riferimento tra la variabile e l'array
		}
		else {
			// è un valore singolo
			$_POST[$nomeCampo] = "";
		}
	}
	return;
}

// Estrae il codice di errore dal messaggio lanciato da una try-catch
function getErrorCode($errMess) {
	return (strtok($errMess, ":"));
}

// rende persistente il valore di un check box da $_POST
// nomecheck:	name del campo input
// valore:		vallue del campo input
// ritorna " checked=checked " se il check è spuntato, altrimenti stringa vuota
function postCheck($nomecheck, $valore) {
	// se trovo [] alla fine del nome lo tolgo
	if (substr($nomecheck, -2, 2) == "[]") {
		$nomecheck = substr($nomecheck, 0, strlen($nomecheck) - 2);
	}
	// è impostato il post?
	if (isset($_POST[$nomecheck])) {
		//  è un array ?
		if (is_array($_POST[$nomecheck])) {
			// è un array
			foreach ($_POST[$nomecheck] as $valContr) {
					if ($valContr == $valore) {
						return " checked=checked ";
					}
			}
		}
		else {
			// è un valore singolo
			if ($_POST[$nomecheck] == $valore) {
				return " checked=checked ";
			}
		}
	}
	return;
}


// ritorna il valore di un checkbox dal $_POST
// 1=checkbox spuntato, 0=checkbox vuoto
function postCheckValue($nomecheck, $valore) {
	if (postCheck($nomecheck, $valore)) {
		return 1;
	} else {
		return 0;
	}
}

// emula la DateAdd di VBScript
function DateAdd($interval, $number, $date) {
    $date_time_array = getdate($date);
    $hours = $date_time_array["hours"];
    $minutes = $date_time_array["minutes"];
    $seconds = $date_time_array["seconds"];
    $month = $date_time_array["mon"];
    $day = $date_time_array["mday"];
    $year = $date_time_array["year"];

    switch ($interval) {   
        case "yyyy":
            $year+=$number;
            break;
        case "q":
            $year+=($number*3);
            break;
        case "m":
            $month+=$number;
            break;
        case "y":
        case "d":
        case "w":
            $day+=$number;
            break;
        case "ww":
            $day+=($number*7);
            break;
        case "h":
            $hours+=$number;
            break;
        case "n":
            $minutes+=$number;
            break;
        case "s":
            $seconds+=$number; 
            break;            
    }
       $timestamp= mktime($hours,$minutes,$seconds,$month,$day,$year);
    return $timestamp;
}

/***
	$from_mask can be:
		s	seconds, with leading zeros
		i	minutes, with leading zeros
		H	24-hour format of an hour with leading zeros
		d	day, with leading zeros
		m	month, with leading zeros
		y	A two digit represenation of a year
		Y	A full numeric represenation of a year, 4 digits
	@author Mariusz Stankiewicz http://prettymad.net
*/
function convert($string, $from_mask, $to_mask='', $return_unix=false)
{
	// define the valid values that we will use to check
	// value => length
	$all = array(
		's' => 'ss',
		'i' => 'ii',
		'H' => 'HH',
		'y' => 'yy',
		'Y' => 'YYYY', 
		'm' => 'mm', 
		'd' => 'dd'
	);

	// this will give us a mask with full length fields
	$from_mask = str_replace(array_keys($all), $all, $from_mask);

	$vals = array();
	foreach($all as $type => $chars)
	{
		// get the position of the current character
		if(($pos = strpos($from_mask, $chars)) === false)
			continue;

		// find the value in the original string
		$val = substr($string, $pos, strlen($chars));

		// store it for later processing
		$vals[$type] = $val;
	}

	foreach($vals as $type => $val)
	{
		switch($type)
		{
			case 's' :
				$seconds = $val;
			break;
			case 'i' :
				$minutes = $val;
			break;
			case 'H':
				$hours = $val;
			break;
			case 'y':
				$year = '20'.$val; // Year 3k bug right here
			break;
			case 'Y':
				$year = $val;
			break;
			case 'm':
				$month = $val;
			break;
			case 'd':
				$day = $val;
			break;
		}
	}

	$unix_time = mktime(
		(int)$hours, (int)$minutes, (int)$seconds, 
		(int)$month, (int)$day, (int)$year);
	
	if($return_unix)
		return $unix_time;

	return date($to_mask, $unix_time);
}

// ritorna se il result set MySql è utilizzabile
function IsResultSet($rst) {
	if (isset($rst)) {
		if (get_resource_type($rst) != "Unknown") {
			return true;
		}
		return false;
	}
	return false;
}

function GetBrowserType() {
	if ( strpos($_SERVER['HTTP_USER_AGENT'], 'Gecko') )
	{
	   if ( strpos($_SERVER['HTTP_USER_AGENT'], 'Netscape') )
	   {
	     $browser = 'NS';
	   }
	   else if ( strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') )
	   {
	     $browser = 'FF';
	   }
	   else if ( strpos($_SERVER['HTTP_USER_AGENT'], 'Iceweasel') )
	   {
	     $browser = 'IW';
	   }
	   else
	   {
	     $browser = 'GK';
	   }
	}
	else if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') )
	{
	   if ( strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') )
	   {
	     $browser = 'OP';
	   }
	   else
	   {
	     $browser = 'IE';
	   }
	}
	else
	{
	   $browser = '';
	}
	return $browser;
}

function SelectCSS($nomeCSS) {
	$bt = GetBrowserType();
	switch ($bt) {
		case "IE":
			switch ($nomeCSS) {
				case "":
					echo "<link href=\"./css/style.css\" rel=\"stylesheet\" type=\"text/css\" />";
					break;
				case "styleanagrafica":
					echo "<link href=\"./css/styleanagrafica.css\" rel=\"stylesheet\" type=\"text/css\" />";
					break;
			}
			break;
	    case "FF":
		case "IW":
			switch ($nomeCSS) {
				case "":
					echo "<link href=\"./css/style_ff.css\" rel=\"stylesheet\" type=\"text/css\" />";
					break;
				case "styleanagrafica":
					echo "<link href=\"./css/styleanagrafica_ff.css\" rel=\"stylesheet\" type=\"text/css\" />";
					break;
			}
			break;
	}
}	

// Converte un resultset in un array
// resultset		resultset MySql
function ResultSet2Array($resultset) {
	if(isset($resultset)) {
		while ($row = mysql_fetch_array($resultset)) {
		       $fields[] = $row;
		}
		return $fields;
	}
}	
?>