<script type="text/javascript">
function ApriStampa (id){
	newWindow = window.open("","newWindow","width=650","height=250","scrollbars=yes","resizable=yes");
	newWindow.document.write('<html><title>"Anagrafica Websi"</title><body bgcolor="#FFFFFF" leftmargin="10" topmargin="10" marginheight="10" marginwidth="10">'); 
	newWindow.document.write('<div align="center">');
	newWindow.document.write('<img src="/websi/template/images/logoComuneAostaPrint.jpg"><br>'); 	
	newWindow.document.write('CITTA’ DI AOSTA – VILLE D’AOSTE<br>VISURA ANAGRAFICA<br>(esclusivamente per uso interno)<br><br><br>');
	newWindow.document.write('</div>');
	newWindow.document.write('<table border=0 cellspacing=0 cellpadding=0 width=100%>'); 	
	newWindow.document.write(document.getElementById(id).innerHTML);
	newWindow.document.write('</table>');
	newWindow.document.write('</body></html>');
	newWindow.document.close();
	newWindow.focus();
	newWindow.print();	
}
</script>