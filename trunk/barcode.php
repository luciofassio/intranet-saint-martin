<?php												
// PHP: Barcode user interface
// Do we know what we should output?
if (!isset($_GET['bar']))	{ 
  echo '<body onLoad="document.f.bar.focus()"><form name="f" action="barcode.php">
        Bar Code:<input type=text name=bar><input type=submit>';
} else {        									// 
  require ('bar128.php');							// Our Library of Barcode Functions
  echo bar128(stripslashes($_GET['bar']));		// Ask for a barcode     
}
?>