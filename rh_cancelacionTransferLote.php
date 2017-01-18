<?php
// Este programa se creo para la cancelación de transferencias Angeles Perez 2016-06-07

include('includes/SQL_CommonFunctions.inc');

$PageSecurity=2;

include('includes/session.inc');

include('includes/header.inc');
echo "<a href='rh_listadoTransferLote.php?Search=1'>" . _('Regresar') . '</A><BR>';
$_POST['ShowMenu']=0;


echo "<BR><CENTER><B>"._('Cancelación de Transferencia por Lote')."</B></CENTER><BR>";

		$sql = 'update rh_transfer_lote_preview set recibida=2 where id='.$_GET['TransferID'];
		DB_query($sql,$db);
		
		$Result = DB_query('COMMIT',$db);
		prnMsg(_('Se ha Cancelado correctamente la mercanc&iacute;a.'), 'success');


?>
