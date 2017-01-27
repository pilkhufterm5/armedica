<?php
ob_start();

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-09-19 10:50:29 -0500 (Fri, 19 Sep 2008) $
 * $Rev: 402 $
 */


$PageSecurity = 1;

include('includes/session.inc');
//echo "<pre>";print_r($_SESSION['DatabaseName']);exit();
$title=_('Main Menu');
$afil=unserialize(GetConfig('Afiliaciones'));
//if(!isset($afil['Prefijo'])) UpdateConfig('Afiliaciones',serialize(array('Prefijo'=>'T-')));

/*The module link codes are hard coded in a switch statement below to determine the options to show for each tab */
$ModuleLink = array(
		'orders', 
		'AR', 
		'FE', 
		'AF', 
		'CRM',
		'AP',
		'PO', 
		'stock', 
		'manuf', 
		'GL', 
		'system');

/*The headings showing on the tabs accross the main index used also in WWW_Users for defining what should be visible to the user */
$ModuleList = array(_
		('Sales'), 
		_('Receivables'), 
		_('Factura Electronica'), 
		_('Afiliaciones'),
		_('CRM'),
		_('Payables'), 
		_('Purchases'), 
		_('Inventory'), 
		_('Manufacturing'), 
		_('General Ledger'), 
		_('Setup'));

if (isset($_GET['Application'])){ /*This is sent by this page (to itself) when the user clicks on a tab */
	$_SESSION['Module'] = $_GET['Application'];
}

include('includes/header.inc');

// bowikaxu realhost - marzo 16 2007 - verificar que exista al menos un periodo
$FirstPeriodResult = DB_query('SELECT MIN(periodno) FROM periods',$db);
$FirstPeriodRow = DB_fetch_row($FirstPeriodResult);

if (is_null($FirstPeriodRow[0])){
	//There are no periods defined
	$InsertFirstPeriodResult = DB_query("INSERT INTO periods VALUES (1,'" . Date('Y-m-d',mktime(0,0,0,Date('m')+1,0,Date('Y'))) . "')",$db,_('Imposible Insertar el primer periodo'));
}
if (count($_SESSION['AllowedPageSecurityTokens'])==0){

/* if there is only one security access and its 1 (it has to be 1 for this page came up at all)- it must be a customer log on need to limit the menu to show only the customer accessible stuff this is what the page looks like for customers logging in */
?>

		<tr>
		<td class="menu_group_items">  <!-- Orders transaction options -->
		<table class="table_index">
			<tr>
			<td class="menu_group_item">
				<?php echo "<A HREF='" . $rootpath . '/CustomerInquiry.php?' .SID . '&CustomerID=' . $_SESSION['CustomerID'] . "'><LI>" . _('Account Status') . '</LI></A>'; ?>
			</td>
			</tr>
			<tr>
			<td class="menu_group_item">
				<?php echo "<A HREF='" . $rootpath . '/SelectOrderItems.php?' .SID . "&NewOrder=Yes'><LI>" . _('Place An Order') . '</LI></A>'; ?>
			</td>
			</tr>
			<tr>
			<td class="menu_group_item">
				<?php echo "<LI><A HREF='" . $rootpath . '/SelectCompletedOrder.php?' .SID . "&SelectedCustomer=" . $_SESSION['CustomerID'] . "'>" . _('Order Status') . '</A></LI>'; ?>
			</td>
			</tr>
		</table>
	</td>
<?php
	include('includes/footer.inc');
	exit;
} else {  /* Security settings DO allow seeing the main menu */

?>
		<table class="main_menu" width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
			<td class="main_menu">
				<table class="main_menu">
					<tr>

	<?php


	$i=0;

	while ($i < count($ModuleLink)){

		// This determines if the user has display access to the module see config.php and header.inc
		// for the authorisation and security code
		if ($_SESSION['ModulesEnabled'][$i]==1)	{

			// If this is the first time the application is loaded then it is possible that
			// SESSION['Module'] is not set if so set it to the first module that is enabled for the user
			if (!isset($_SESSION['Module'])OR $_SESSION['Module']==''){
				$_SESSION['Module']=$ModuleLink[$i];
			}

			if ($ModuleLink[$i] == $_SESSION['Module']){
				if($ModuleLink[$i] == 'CRM'){
					echo "<td class='main_menu_selected'><A HREF='" . $rootpath . '/modulos/index.php?r=crm/leads' . SID . "'>". $ModuleList[$i] .'</A></td>';
				}else{
					echo "<td class='main_menu_selected'><A HREF='". $_SERVER['PHP_SELF'] .'?'. SID . '&Application='. $ModuleLink[$i] ."'>". $ModuleList[$i] .'</A></td>';
				}
			} else {
				if($ModuleLink[$i] == 'CRM'){
					echo "<td class='main_menu_unselected'><A HREF='" . $rootpath . '/modulos/index.php?r=crm/leads' . SID . "'>". $ModuleList[$i] .'</A></td>';
				}else{
					echo "<td class='main_menu_unselected'><A HREF='". $_SERVER['PHP_SELF'] .'?'. SID . '&Application='. $ModuleLink[$i] ."'>". $ModuleList[$i] .'</A></td>';
				}

			}
		}
		$i++;
	}
    if ($_SESSION['ExtraModule']==1){
        echo "<td class='main_menu_unselected'><A HREF='". $rootpath ."/rh_yiierp?Application=extra' >". _('Extras') .'</A></td>';
    }
	?>
					</tr>
				</table>
			</td>
			</tr>
		</table>
	<?php


	switch ($_SESSION['Module']) {

	case 'orders': //Sales Orders
	?>

		<table width="100%">
			<tr>
			<td class="menu_group_area">
				<table width="100%" >

					<?php
  					// displays the main area headings
					  OptionHeadings();
					?>

					<tr>
					<td class="menu_group_items">  <!-- Orders transaction options -->
						<table width="100%" class="table_index">
							<?php // bowikaxu - punto de venta ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/SelectOrderItems.php?' .SID . "&NewOrder=Yes'><LI>" . _('Enter An Order') . '</LI></A>'; ?>
							</td>
							</tr>

                            <!--	<tr>
							<td class="menu_group_item">
  								<?php echo "<A HREF='" . $rootpath . '/rh_venta_perdida.php?' .SID . "'><LI>" . _('Ventas Perdidas') . '</LI></A>'; ?>
							</td>
							</tr>   -->
						   	<!--<tr>
								<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/POSEntry.php?' .SID . "&NewOrder=1'><LI>" . _('Punto de Venta') . '</LI></A>'; ?>
								</td>
							</tr>-->

     						<?php // bowikaxu - fin punto de venta ?>
							<tr>
								<td class="menu_group_item">
									<?php echo "<A HREF='" . $rootpath . '/SelectSalesOrder.php?' . SID . "'><LI>" . _('Outstanding Sales Orders') . '</LI></A>'; ?>
								</td>
							</tr>
							<?php // bowikaxu - punto de venta ?>
                             <!---
						    <tr>
								<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_pos_pre_principal.php?' .SID . "&NewOrder=1'><LI>" . _('Punto de Venta') . '</LI></A>'; ?>
								</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/SpecialOrder.php?' .SID . "&NewSpecial=Yes'><LI>" . _('Special Order') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/SelectRecurringSalesOrder.php?' .SID . "'><LI>" . _('Recurring Order Template') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/RecurringSalesOrdersProcess.php?' .SID . "'><LI>" . _('Process Recurring Orders') . '</LI></A>'; ?>
							</td>
							</tr>  -->
						</table>
					</td>
					<td class="menu_group_items"> <!-- Orders Inquiry options -->
						<table width="100%" class="table_index">
							<?php // iJPe - Reporte Vendedor ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/SelectCompletedOrder.php?' . SID . "'><LI>" . _('Order Inquiry') . '</LI></A>'; ?>
							</td>
							</tr>
							<!---<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_reporteVendedor.php?' . SID . "'><LI>" . _('Reporte por Vendedor') . '</LI></A>'; ?>
							</td>
							</tr>

                                                        <tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_reportProduct-Client.php?' . SID . "'><LI>" . _('Reporte Ventas Producto-Cliente') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // iJPe - Reporte Ventas Mensuales ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_reporteVentasM.php?' . SID . "'><LI>" . _('Reporte Ventas Mensuales') . '</LI></A>'; ?>
							</td>
							</tr>-->

							<?php // bowikaxu - Reporte Punto de Venta ?>
							<!---<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_PosInquiry.php?' . SID . "'><LI>" . _('POS Inquiry') . '</LI></A>'; ?>
							</td>
							</tr> -->

							<?php // bowikaxu - Reporte Pedidos Cancelados ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_PDFOrdersCancelled.php?' . SID . "'><LI>" . _('Cancelled Orders') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/PDFPriceList.php?' . SID . "'><LI>" . _('Print Price Lists') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // andres amaya - realhost 30 april 2007 - view prices?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_Prices.php?' . SID . "'><LI>" . _('View Prices') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // andres amaya - realhost sept 2007 - reporte de autorizacion de precios?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_PriceAuth_Inquiry.php?' . SID . "'><LI>" . _('Reporte Autorizacion de Precios') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/PDFOrderStatus.php?' . SID . "'><LI>" . _('Order Status Reports (Print)') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/PDFOrdersInvoiced.php?' . SID . "'><LI>" . _('Orders Invoiced Reports') . '</LI></A>'; ?>
							</td>
							</tr>
                            <!---
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/PDFDeliveryDifferences.php?' . SID . "'><LI>" . _('Order Delivery Differences Report') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/PDFDIFOT.php?' . SID . "'><LI>" . _('Delivery In Full On Time (DIFOT) Report') . '</LI></A>'; ?>
							</td>
							</tr> -->
							<tr>
							<td class="menu_group_item">
								<?php echo GetRptLinks('ord'); ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_items"> <!-- Orders Maintenance options -->
						<table width="100%">
							<?php // bowikaxu realhsot - may 2008 - OSCommerce Integration
							/*
							?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_oscomm/Orders_sync.php?' . SID . "'><LI>" . _('OSCommerce Sincronizar').' '._('Orders') . '</LI></A>'; ?>
							</td>
							</tr>
						<?php */ ?>
						</table>
					</td>
					</tr>
				</table>
			</td>
			</tr>
		</table>
	<?php
		break;
	/* ****************** END OF ORDERS MENU ITEMS **************************** */


	case 'AR': //Debtors Module

	unset($ReceiptBatch);
	unset($AllocTrans);

	?>
		<table width="100%">
			<tr>
			<td valign="top" class="menu_group_area">
				<table width="100%">

					<?php OptionHeadings(); ?>

					<tr>
					<td class="menu_group_items">
						<table width="100%"class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/SelectSalesOrder.php?' . SID . "'><LI>" . _('Select Order to Invoice') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu - menu de orden a remision ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_SelectSalesOrder_NC.php?' . SID . "'><LI>" . _('Crear Nota de Cargo') . '</LI></A>'; ?>
							</td>
							</tr>
  							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_ViewTickets.php?' . SID . "'><LI>" . _('Asignaci&oacute;n de ticket a Cliente') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu - menu de orden a remision ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_SelectSalesOrder_Shipment.php?' . SID . "'><LI>" . _('Select Order to Remision') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu - menu de remision a factura ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_ViewRemisiones3.php?' . SID . "'><LI>" . _('Select Remision to Invoice') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu - relacionar facturas internas y externas ?>
						   	<tr>
							<td class="menu_group_item">
								<?php //echo "<A HREF='" . $rootpath . '/ExtInvoiceInquiry.php?' . SID . "'><LI>" . _('Relacion de Facturas') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu - relacionar notas de credito internas y externas ?>
						   	<!---<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_ExtCN_Inquiry.php?' . SID . "'><LI>" . _('Relacion de Notas de Cr&eacute;dito') . '</LI></A>'; ?>
							</td>
							</tr>  -->


							<?php // bowikaxu - manejar facturas internas y externas ?>
							<!--<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_ExtInvoice.php?' . SID . "'><LI>" . _('Dissallow Invoices').'</LI></A>'; ?>
							</td>
							</tr> -->

                            <?php // bowikaxu - manejar facturas internas y externas ?>
							<!--<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_ExtNC.php?' . SID . "'><LI>" . _('Control de Notas de Cr&eacute;dito').'</LI></A>'; ?>
							</td>
							</tr>  -->

							<?php // bowikaxu - copiar detalles de una factura y generar una orden nueva ?>
							<tr>
								<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_CpyInvoice.php?' .SID . "&NewOrder=YES'><LI>" . _('Orden a Partir de Factura') . '</LI></A>'; ?>
								</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/SelectCreditItems.php?' .SID . "&NewCredit=Yes'><LI>" . _('Create A Credit Note') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php //bowikaxu realhost - nota de descuento ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_DiscountCredit.php?' .SID . "&NewCredit=Yes'><LI>" . _('Crear Nota de Descuento') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/CustomerReceipt.php?' . SID . "'><LI>" . _('Enter Receipts') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
								<td class="menu_group_item">

									<?php echo "<A HREF='" . $rootpath . '/CustomerAllocations.php?' . SID . "'><LI>" . _('Allocate Receipts or Credit Notes') . '</LI></A>'; ?>
								</td>
							</tr>

							<tr>
                                <td class="menu_group_item">
                                    <?php echo "<A HREF='" . $rootpath . "/modulos/index.php?r=facturacion/pagosadelantados/'><LI>" . _('Pagos Adelantados') . '</LI></A>'; ?>
                                </td>
                            </tr>

						</table>
					</td>
					<td class="menu_group_items">
						<table width="100%" class="table_index">
							<?php // bowikaxu - Reporte de Clientes ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/SelectCustomer.php?' . SID . "'><LI>" . _('Customer Transaction Inquiries') . '</LI></A>'; ?>
							</td>
							</tr>
							<!--Se agrego liga para mostrar facturas con el domicilio anterior en Torreón Angeles Perez 06/04/2016 -->
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/SelectCustomerTres.php?' . SID . "'><LI>" . _('Domicilio Fiscal Anterior') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_ClientInquiry_dates.php?' . SID . "'><LI>" . _('Balance Clientes') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_Rem_Inquiry.php?' . SID . "'><LI>" . _('Reporte de Remisiones') . '</LI></A>'; ?>
							</td>
							</tr>
							<?php //bowikaxu realhost - july 19 '07 - reporte synpos ?>
							<tr>
							<td class="menu_group_item">
								<?php //echo "<A HREF='" . $rootpath . '/rh_synPOS_Inquiry.php?' . SID . "'><LI>" . _('Reporte synPOS') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu - Impresion de Remisiones Grandes ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_PDFRemGde.php?' . SID . "'><LI>" . _('Impresion Remisiones') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu - Reporte de Clientes/Productos ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_CustItem_Inquiry.php?' . SID . "'><LI>" . _('Customer Item Inquiry') . '</LI></A>'; ?>
							</td>
							</tr>

                                                        <?php //iJPe - Reporte solicitado para la impresion de facturas con desglose de IVA 2010-03-18 ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_InvoiceReport.php?' . SID . "'><LI>" . _('Reporte de Ventas - Facturas con Desglose') . '</LI></A>'; ?>
							</td>
							</tr>


							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/CustWhereAlloc.php?' . SID . "'><LI>" . _('Where Allocated Inquiry') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php
									if ($_SESSION['InvoicePortraitFormat']==0){
										echo "<A HREF='" . $rootpath . '/rh_PrintCustTrans.php?' . SID . "'><LI>" . _('Imprimir Facturas, Notas de Credito o Notas de Cargo') . '</LI></A>';
									} else {
										echo "<A HREF='". $rootpath . "/PrintCustTransPortrait.php?" . SID . "'><LI>" . _('Print Invoices or Credit Notes') . '</LI></A>';
									}
								?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/modulos/index.php?r=reportes/estadoscuenta' . SID . "'><LI>" . _('Imprimir Estados de Cuenta').' </LI></A>'; ?>
								<!-- <?php echo "<A HREF='" . $rootpath . "/PrintCustStatements.php?" . SID . "'><LI>" . _('Print Statements') . '</LI></A>'; ?>-->
							</td>
							</tr>
							<?php // bowikaxu realhost - Dunning Letters ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . "/rh_DunningLetter.php?" . SID . "'><LI>" . _('Cartas de adeudo') . '</LI></A>'; ?>
							</td>
							</tr>
							<?php
						if ($_SESSION['DatabaseName']!= "artorr_erp_001") {
							?>
								<td class="menu_group_item" style="display: none;">
								<?php echo "<A HREF='" . $rootpath . "/rh_DunningLetter_Torreon.php?" . SID . "'><LI>" . _('Cartas de adeudo solo Pago con Tarjeta') . '</LI></A>'; ?>
								</td>

							<?php
							}else{
								?>
								<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . "/rh_DunningLetter_Torreon.php?" . SID . "'><LI>" . _('Cartas de adeudo solo Pago con Tarjeta') . '</LI></A>'; ?>
							</td>

								<?php

							}
							?>
							<tr>
							
							</tr>

							<!--<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/SalesAnalRepts.php?' . SID . "'><LI>" . _('Sales Analysis Reports') . '</LI></A>'; ?>
							</td>
							</tr>  -->

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_SalesDate_Inquiry.php?' . SID . "'><LI>" . _('Cliente sin ventas/fecha') . '</LI></A>'; ?>
							</td>
							</tr>


							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/CustomerTransInquiry.php?' . SID . "'><LI>" . _('Transaction Inquiries') . '</LI></A>'; ?>
							</td>
							</tr>

                            <tr>
                                <td class="menu_group_item">
                                    <?php echo "<A HREF='" . $rootpath . '/CustomerInquiry2.php?' . SID . "'><LI>" . _('Transacciones Globales') . '</LI></A>'; ?>
                                </td>
                            </tr>

                                                        <?php //iJPe 2010-04/08 Se añadio este archivo para mostrar los depositos para facturas de credito y de contado ?>
                            <!--</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_DepositosCredCont.php?' . SID . "'><LI>" . _('Consulta Depositos para Facturas de Credito/Contado') . '</LI></A>'; ?>
							</td>
							</tr>  --->

							<tr>
							<td class="menu_group_item">
							<?php echo "<A HREF='" . $rootpath . '/PDFBankingSummary.php?' . SID . "'><LI>" . _('Re-Print A Deposit Listing') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/DebtorsAtPeriodEnd.php?' . SID . "'><LI>" . _('Debtor Balances At A Prior Month End') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
							<?php echo "<A HREF='" . $rootpath . '/PDFCustomerList.php?' . SID . "'><LI>" . _('Customer Listing By Area/Salesperson') . '</LI></A>'; ?>
							</td>
							</tr>
							<!--<tr>
							<td class="menu_group_item">
							<?php echo "<A HREF='" . $rootpath . '/rh_SalesGraph.php?' . SID . "'><LI>" . _('Sales Graphs') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
							<?php echo "<A HREF='" . $rootpath . '/rh_reporte_venta.php?' . SID . "'><LI>" . _('Reporte de ventas') . '</LI></A>'; ?>
							</td>
							</tr>  -->

							<?php // bowikaxu realhost oct 2007 - reportes visuales ?>
							<tr>
							<td class="menu_group_item">
							<?php echo "<A HREF='" . $rootpath . '/rh_charts1.php?' . SID . "'><LI>" . _('DashBoard') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
							<?php echo "<A HREF='" . $rootpath . '/rh_Vtas_Qty.php?' . SID . "'><LI>" . _('Ventas').' '._('Item').'/'._('Quantity') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/AgedDebtorshtml2.php?' . SID . "'><LI>" . _('Aged Customer Balances/Overdues Report') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
							<?php echo "<A HREF='" . $rootpath . '/modulos/index.php?r=facturacion/presupuestocobranza' . SID . "'><LI>" . _('Presupuesto de Cobranza').' </LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
							<?php echo "<A HREF='" . $rootpath . '/modulos/index.php?r=facturacion/revisionfactura' . SID . "'><LI>" . _('Facturas a Revisi&oacute;n').' </LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
							<?php echo "<A HREF='" . $rootpath . '/modulos/index.php?r=facturacion/cobrarfactura' . SID . "'><LI>" . _('Facturas a Cobrar').' </LI></A>'; ?>
							</td>
							</tr>

							<tr>
								<td class="menu_group_item">
									<?php echo "<A HREF='" . $rootpath . '/modulos/index.php?r=facturacion/relacionfacturas' . SID . "'><LI>" . _('Relacion de Facturas').' </LI></A>'; ?>
								</td>
							</tr>

							<tr>
								<td class="menu_group_item">
									<?php echo "<A HREF='" . $rootpath . '/rh_FormasEspeciales.php' . SID . "'><LI>" . _('Impresi&oacute;n de Formas Especiales Maestros').' </LI></A>'; ?>
								</td>
							</tr>
							<tr>
								<td class="menu_group_item">
									<?php echo "<A HREF='" . $rootpath . '/rh_RelacionVigencias.php' . SID . "'><LI>" . _('Relaci&oacute;n de Vigencia de Maestros').' </LI></A>'; ?>
								</td>
							</tr>
							<tr>
								<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/modulos/index.php?r=reportes/comisionasesor' . SID . "'><LI>" . _('Comision de Asesores').' </LI></A>'; ?>
								</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo GetRptLinks('ar'); ?>
							</td>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . "/modulos/index.php?r=afiliaciones/HistorialPersonas" . SID . "'><LI>" . _('Permanencia de socios en la empresa') . '</LI></A>'; ?>
							</td>
							</tr>
                                                        <tr>
                                                        <td class="menu_group_item">
                                                                <?php echo "<A HREF='" . $rootpath . "/modulos/index.php?r=facturacion/ConciliacionFactura" . SID . "'><LI>" . _('Conciliación de Faturas') . '</LI></A>'; ?>
                                                        </td>
                                                        </tr>
						</table>
					</td>
					<td class="menu_group_items">
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/Customers.php?' . SID . "'><LI>" . _('Add Customer') . '</LI></A>'; ?>
							</td>
                            </tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/SelectCustomer.php?' . SID . "'><LI>" . _('Customers') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php
								include_once 'php-ofc-library/open_flash_chart_object.php';
								open_flash_chart_object( 300, 300, 'rh_charts8.php',false);
								?>
							</td>
							</tr>

							<?php // bowikaxu realhsot - may 2008 - OSCommerce Integration
							/*
							?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_oscomm/oscomm2erp_cust.php?' . SID . "'><LI>" . _('OSCommerce Sincronizar').' '._('Customers') . '</LI></A>'; ?>
							</td>
							</tr>
							<?php */ ?>
						</table>
					</td>
					</tr>
				</table>
			</td>
			</tr>
		</table>
	<?php

	/* ********************* 	END OF AR OPTIONS **************************** */
	break;

    case 'AF': //Links de Afiliaciones
    ?>
        <table width="100%">
            <tr>
                <td valign="top" class="menu_group_area">
                    <table width="100%">
                        <?php OptionHeadings(); ?>
                        <tr>
                            <td class="menu_group_items">
                                <table width="100%"class="table_index">
                                    <tr>
                                        <td class="menu_group_item">
                                            <?php echo "<A HREF='" . $rootpath . "/modulos/index.php?r=afiliaciones/asignarfolio/'><LI>" . _('Asignacion de Folios') . '</LI></A>'; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="menu_group_item">
                                            <?php echo "<A HREF='" . $rootpath . '/modulos/index.php?r=afiliaciones/encuesta' . SID . "'><LI>" . _('Encuesta') . '</LI></A>'; ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="menu_group_item">
                                            <?php echo "<A HREF='" . $rootpath . '/modulos/index.php?r=afiliaciones/buscarfolio' . SID . "'><LI>" . _('Busqueda de Folios por Nombre') . '</LI></A>'; ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="menu_group_item">
                                            <?php echo "<A HREF='" . $rootpath . '/modulos/index.php?r=afiliaciones/afiliacion' . SID . "'><LI>" . _('Afiliación') . '</LI></A>'; ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="menu_group_item">
                                            <?php echo "<A HREF='" . $rootpath . '/modulos/index.php?r=facturacion/buscarfacturas' . SID . "'><LI>" . _('Consulta de Facturas') . '</LI></A>'; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="menu_group_item">
                                            <?php echo "<A HREF='" . $rootpath . '/modulos/index.php?r=facturacion/emision' . SID . "'><LI>" . _('Emision de Facturas') . '</LI></A>'; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="menu_group_item">
                                            <?php echo "<A HREF='" . $rootpath . '/modulos/index.php?r=facturacion/pendientesfacturar' . SID . "'><LI>" . _('Bitacora Facturacion') . '</LI></A>'; ?>
                                        </td>
                                    </tr>
                                    <!-- AGREGADO POR DANIEL VILLARREAL EL 30 DE DICIEMBRE DEL 2015 -->
                                    <tr>
                                       <td class="menu_group_item">
                                          <?php echo "<A HREF='" . $rootpath . '/modulos/index.php?r=facturacion/emisionmasiva' . SID . "'><LI>" . _('Emision de Facturas por Empresa') . '</LI></A>'; ?>
                                       </td>
                                    </tr>
												<tr>
                                       <td class="menu_group_item">
                                            <?php echo "<A HREF='" . $rootpath . '/modulos/index.php?r=facturacion/emisionmasiva1concepto' . SID . "'><LI>" . _('Emision de Facturas por Empresa 1 concepto') . '</LI></A>'; ?>
                                        </td>
                                    </tr>
												<!-- TERMINA AGREGADO POR DANIEL VILLARREAL EL 30 DE DICIEMBRE DEL 2015 -->

                                </table>
                            </td>
                            <td class="menu_group_items">
                                <table width="100%" class="table_index">

                                    <tr>
                                    <?php
                                    	if ($_SESSION['DatabaseName'] != "sainar_erp_001") {
                                    		?>
                                    		<td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=reportes/relacionvmaestros" style="display: none;"><li>Relacion de Vigencias Maestros SECCION 50</li></a></label>
                                        </td>
                                    		<?php
                                    	}else{
                                    		?>
                                    		<td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=reportes/relacionvmaestros"><li>Relacion de Vigencias Maestros SECCION 50</li></a></label>
                                        </td>

                                    		<?php
                                    	}
                                    ?>
                                        
                                    </tr>
                                    <tr>
                                    <?php
                                    	if ($_SESSION['DatabaseName'] != "sainar_erp_001") {
                                    		?>
                                    		<td class="menu_group_item" style="display: none;">
                                            <label><a href="modulos/index.php?r=reportes/relacionvmaestros21"><li>Relacion de Vigencias Maestros SECCION 21</li></a></label>
                                        </td>
                                        </td>
                                    		<?php
                                    	}else{
                                    		?>
                                    		<td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=reportes/relacionvmaestros21"><li>Relacion de Vigencias Maestros SECCION 21</li></a></label>
                                        </td>

                                    		<?php
                                    	}
                                    ?>
                                        
                                    </tr>
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=reportes/candidatossuspension"><li>Reporte Candidatos a Suspensión</li></a></label>
                                        </td>
                                    </tr>
                                    <!--Se agrego para agregar el Reporte Incidencias del Servicio Angeles Perez 2016-09-09 -->
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=incidencias/index"><li>Reporte Incidencias del Servicio</li></a></label>
                                        </td>
                                    </tr>
                                    <!--Termina-->
                                    <!--Se agrego para agregar el Reporte Encuesta Post-Venta Angeles Perez 2016-08-31-->
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=reportes/encuestapostventa"><li>Reporte de Encuestas de POST-VENTA</li></a></label>
                                        </td>
                                    </tr>
                                    <!--Termina-->
                                    <!-- COTIZADOR MAESTRO-->
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=cotizador/index"><li>Cotizador Maestro</li></a></label>
                                        </td>
                                    </tr>
                                    <!-- termina -->

                                    <!-- SIMULACION AUMENTOS DE PRECIO -->
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=Simulaciones/index"><li>Simulación Aumentos De Precio</li></a></label>
                                        </td>
                                    </tr>
                                    <!-- termina -->
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=reportes/tarjetas"><li>Reporte Tarjetas de Debito</li></a></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=reportes/tarjetascredito"><li>Reporte Tarjetas de Credito</li></a></label>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td class="menu_group_items">
                                <table width="100%" class="table_index">
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=precios/index"><li>Matriz de Precios(variaprecio)</li></a></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=precios/preciocomis"><li>Matriz de Precios(preciocomis)</li></a></label>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=cobradores/index"><li>Cobradores</li></a></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=comisionista/index"><li>Comisionistas</li></a></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=convenios/index"><li>Convenios</li></a></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=coordinadores/index"><li>Coordinadores</li></a></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=empresas/index"><li>Empresas</li></a></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=estados/index"><li>Estados</li></a></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=especialidades/index"><li>Especialidades</li></a></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=frecuenciapago/index"><li>Frecuencias de Pago</li></a></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=paymentmethod/index"><li>Formas de Pago</li></a></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=hospitales/index"><li>Hospitales</li></a></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=identificaciones/index"><li>Identificaciones</li></a></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=municipios/index"><li>Municipios</li></a></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=motivoscancelacion/index"><li>Motivos de Cancelación</li></a></label>
                                        </td>
                                    </tr>
                                    <!--Se agrego para agregar el catalogo de motivos de incidencias Angeles Perez 2016-09-09-->
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=motivosincidencias/index"><li>Motivos Incidencias del Servicio</li></a></label>
                                        </td>
                                    </tr>
                                     <!--Termina-->
                                     <!--Se agrego para agregar el catalogo de asignacion de personal a incidencias Angeles Perez 2016-09-09-->
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=asignacionpersonal/index"><li>Asignación de Personal a Incidencias</li></a></label>
                                        </td>
                                    </tr>
                                    <!--Termina-->
                                    <!--Se agrego para agregar el catalogo de Clasificaciones de la empresa Angeles Perez 2016-06-28-->
                                      <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=clasificacion/index"><li>Clasificacion Empresas</li></a></label>
                                        </td>
                                    </tr>
                                    <!--Termina-->
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=parentesco/index"><li>Parentescos</li></a></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=tipostarjeta/index"><li>Tipos de Tarjetas</li></a></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=stockmoves/products"><li>Productos</li></a></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=tipofacturas/index"><li>Tipo de Facturas</li></a></label>
                                        </td>
                                    </tr>
                                    <!--
													AGREGADO POR DANIEL VILLARREAL 
                                    -->
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="or_motivosnotascredito.php"><li>Motivos notas credito</li></a></label>
                                        </td>
                                    </tr>
                                    <!-- 
                                    	TERMINA
                                   	-->
                                   	 <!-- AGREGADO POR DANIEL VILLARREAL EL 30 DE DICIEMBRE DEL 2015 -->
                                    <tr>
                                       <td class="menu_group_item">
                                          <?php echo "<A HREF='" . $rootpath . '/modulos/index.php?r=relacionempresas/empresaspadre' . SID . "'><LI>" . _('Empresas Padre') . '</LI></A>'; ?>
                                       </td>
                                    </tr>
												<!-- TERMINA AGREGADO POR DANIEL VILLARREAL EL 30 DE DICIEMBRE DEL 2015 -->
												<!-- AGREGADO POR DANIEL VILLARREAL EL 30 DE DICIEMBRE DEL 2015 -->
                                    <tr>
                                       <td class="menu_group_item">
                                          <?php echo "<A HREF='" . $rootpath . '/modulos/index.php?r=reportes/emisionmultiempresas' . SID . "'><LI>" . _('Reporte de Emision multi empresas') . '</LI></A>'; ?>
                                       </td>
                                    </tr>
												<!-- TERMINA AGREGADO POR DANIEL VILLARREAL EL 30 DE DICIEMBRE DEL 2015 -->
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    <?php
    /* *********************    Termina Modulo Afiliaciones **************************** */
    break;

    case 'EXP': //Links de Expediente Clinico

        /*Para Accessa alas Ligas de Expediente Clinico se debe Tener seleccionado un Cliente, en caso de no tener redirecciona a SelectCustomer*/
        if(empty($_SESSION['CustomerID'])){
            $_SESSION['ReturnEXP'] = true;
            echo '<BR>' . _('This page is expected to be called after a supplier has been selected');
            echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=" . $rootpath . '/SelectCustomer.php?' . SID . "'>";
            exit;
        }

    ?>
        <table width="100%">
            <tr>
                <td valign="top" class="menu_group_area">
                    <table width="100%">
                        <?php OptionHeadings(); ?>
                        <tr>

                            <td class="menu_group_items">
                                <table width="100%"class="table_index">

                                    <tr>
                                        <td class="menu_group_item">
                                            <?php echo "<A HREF='" . $rootpath . "/rh_alergias_pacientes.php?DebtorNo=" . $_SESSION['CustomerID'] . "'><LI>" . _('Alergias') . '</LI></A>'; ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="menu_group_item">
                                            <?php echo "<A HREF='" . $rootpath . "/rh_datos_seguro_paciente_paciente.php?DebtorNo=" . $_SESSION['CustomerID'] . "'><LI>" . _('Aseguradoras del Paciente') . '</LI></A>'; ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="menu_group_item">
                                            <?php echo "<A HREF='" . $rootpath . "/rh_cirugias_pacientes.php?DebtorNo=" . $_SESSION['CustomerID'] . "'><LI>" . _('Cirug&iacute;as') . '</LI></A>'; ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="menu_group_item">
                                            <?php echo "<A HREF='" . $rootpath . "/rh_enfermedades_paciente_pacientes.php?DebtorNo=" . $_SESSION['CustomerID'] . "'><LI>" . _('Enfermedades del Paciente') . '</LI></A>'; ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="menu_group_item">
                                            <?php echo "<A HREF='" . $rootpath . "/rh_estivo_vida_pacientes.php?DebtorNo=" . $_SESSION['CustomerID'] . "'><LI>" . _('Estilo de Vida') . '</LI></A>'; ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="menu_group_item">
                                            <?php echo "<A HREF='" . $rootpath . "/rh_medicamentos_pacientes.php?DebtorNo=" . $_SESSION['CustomerID'] . "'><LI>" . _('Medicamentos Pacientes') . '</LI></A>'; ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="menu_group_item">
                                            <?php echo "<A HREF='" . $rootpath . "/rh_catalogo_notas.php?DebtorNo=" . $_SESSION['CustomerID'] . "'><LI>" . _('Notas') . '</LI></A>'; ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="menu_group_item">
                                            <?php echo "<A HREF='" . $rootpath . "/rh_padecimientos_familiares_pacientes.php?DebtorNo=" . $_SESSION['CustomerID'] . "'><LI>" . _('Padecimientos Familiares') . '</LI></A>'; ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="menu_group_item">
                                            <?php echo "<A HREF='" . $rootpath . "/rh_datos_recomendaciones_medicas.php?DebtorNo=" . $_SESSION['CustomerID'] . "'><LI>" . _('Recomendaciones M&eacute;dicas') . '</LI></A>'; ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="menu_group_item">
                                            <?php echo "<A HREF='" . $rootpath . "/rh_suplementos_vitaminas_paciente.php?DebtorNo=" . $_SESSION['CustomerID'] . "'><LI>" . _('Suplementos y Vitaminas') . '</LI></A>'; ?>
                                        </td>
                                    </tr>

                                </table>
                            </td>
                            <td class="menu_group_items">
                                <table width="100%" class="table_index">
                                    <tr>
                                        <td class="menu_group_item">
                                            <?php //echo "<A HREF='" . $rootpath . '/test.php' . SID . "'><LI>" . _('TEST') . '</LI></A>'; ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td class="menu_group_items">
                                <table width="100%" class="table_index">
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="#"><li></li></a></label>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    <?php
    /* *********************    Termina Modulo Expedientes **************************** */


    break;

    case 'AF': //Links de CRM
    ?>
        <table width="100%">
            <tr>
                <td valign="top" class="menu_group_area">
                    <table width="100%">
                        <?php OptionHeadings(); ?>
                        <tr>
                            <td class="menu_group_items">
                                <table width="100%"class="table_index">
                                    <tr>
                                        <td class="menu_group_item">
                                            <?php echo "<A HREF='" . $rootpath . "/modulos/index.php?r=afiliaciones/asignarfolio/'><LI>" . _('Asignacion de Folios') . '</LI></A>'; ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td class="menu_group_items">
                                <table width="100%" class="table_index">
                                    <tr>
                                        <td class="menu_group_item">
                                            <?php //echo "<A HREF='" . $rootpath . '/test.php' . SID . "'><LI>" . _('TEST') . '</LI></A>'; ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td class="menu_group_items">
                                <table width="100%" class="table_index">
                                    <tr>
                                        <td class="menu_group_item">
                                            <label><a href="modulos/index.php?r=precios/index"><li>Matriz de Precios(variaprecio)</li></a></label>
                                        </td>
                                    </tr>
                                    </tr>

                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    <?php
    /* *********************    Termina Modulo CRM **************************** */
    break;




/*rleal
 * Jul 19 2010, Se agrega el link de Factura Electr�nica
 */

	case 'FE': //Debtors Module

	unset($ReceiptBatch);
	unset($AllocTrans);

	?>
		<table width="100%">
			<tr>
			<td valign="top" class="menu_group_area">
				<table width="100%">

					<?php OptionHeadings(); ?>

					<tr>
					<td class="menu_group_items">
						<table width="100%"class="table_index">
							<tr>
							<td class="menu_group_item">
                                                            <li>
                                                                <label style="color: blue">CFD</label>
                                                            <?php
                                                                echo '<select onchange="window.location.href=this.value"><option></option>';
								echo '<option value="' . ($rootpath . '/SelectOrderItems.php?' .SID . "&NewOrder=Yes") . '">Factura</option>"';
                                                                echo '<option value="' . ($rootpath . '/SelectCreditItems.php?' .SID . "&NewCredit=Yes") . '">Nota de Credito</option>"';
                                                                echo '</select>';
                                                            ?>
                                                            </li>
                                                        </td>
							</tr>
                                                        <tr>
							<td class="menu_group_item">
								<?php // echo "<A HREF='" . $rootpath . "/rh_cartaPorte__abcCatalogos.php?'><LI>" . _('Catalogos Carta Porte') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/SelectSalesOrder.php?' . SID . "'><LI>" . _('Select Order to Invoice') . '</LI></A>'; ?>
							</td>
							</tr>
                            							<tr>
							<td class="menu_group_item">
								<?php //echo "<A HREF='" . $rootpath . '/rh_cartaPorte__SelectSalesOrder.php?' . SID . "'><LI>" . _('Facturar Carta Porte') . '</LI></A>'; ?>
							</td>
							</tr>
 							<tr>
							<td class="menu_group_item">
								<?php //echo "<A HREF='" . $rootpath . '/SelectCreditItems.php?' .SID . "&NewCredit=Yes'><LI>" . _('Create A Credit Note') . '</LI></A>'; ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_items">
						<table width="100%" class="table_index">
							<?php if($_SESSION['CFDIVersion']==22){?>
 							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_cfd_reportemensual.php' . SID . "'><LI>" . _('Reporte Mensual y CFDs emitidos') . '</LI></A>'; ?>
							</td>
							</tr>
                            <?php }else{ ?>

                            <?php }?>

						</table>
					</td>
					<td class="menu_group_items">
						<table width="100%" class="table_index">
                        <?php if($_SESSION['CFDIVersion']==22){?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_cfd_certificate.php' . SID . "'><LI>" . _('Administracion de Sellos') . '</LI></A>'; ?>
							</td>
                            </tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_cfd_folios.php' . SID . "'><LI>" . _('Administracion de Folios') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_cfd_series.php' . SID . "'><LI>" . _('Administracion de Series') . '</LI></A>'; ?>
							</td>
							</tr>
                        <?php }else if($_SESSION['CFDIVersion']==32){ ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_CFDI_csd.php' . SID . "'><LI>" . _('Administracion de Sellos') . '</LI></A>'; ?>
							</td>
                                                        </tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_CFDI_folio.php' . SID . "'><LI>" . _('Administracion de Folios') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_CFDI_serie.php' . SID . "'><LI>" . _('Administracion de Series') . '</LI></A>'; ?>
							</td>
							</tr>
                        <?php } ?>
                                                        <tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/CompanyPreferences.php?' . SID . "'><LI>" . _('Company Preferences') . '</LI></A>'; ?>
							</td>
							</tr>
                                                        <tr>
							<!--<td class="menu_group_item">-->
								<?php //echo "<A HREF='" . $rootpath . '/Shippers.php?' . SID . "'><LI>" . _('Shippers') . '</LI></A>'; ?>
							<!--</td>
							</tr>-->
                                                        <tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/Locations.php?' . SID . "'><LI>" . _('Inventory Locations Maintenance') . '</LI></A>'; ?>
							</td>
							</tr>
						</table>
					</td>
					</tr>
				</table>
			</td>
			</tr>
		</table>
	<?php

	/* ********************* 	END OF AR OPTIONS **************************** */
	break;
/*
 * Fin de Factura Electr�nica
 */
	case 'AP':

	?>
		<table width="100%">
			<tr>
			<td valign="top" class="menu_group_area">
				<table width="100%">

					<?php OptionHeadings(); ?>

					<tr>
					<td class="menu_group_items"> <!-- AP transaction options -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/SelectSupplier.php?' . SID . "'><LI>" . _('Select Supplier') . '</LI></A>'; ?>
							</td>
							</tr><tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . "/SupplierAllocations.php?" . SID . "'><LI>" . _('Supplier Allocations') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu - supplier payment notifications - 26 june 2008
							?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_PayNotifications_Inquiry.php?' . SID . "'><LI>" . _('Report').' '._('Notificacion') . '</LI></A>'; ?>
							</td>
							</tr>

						</table>
					</td>
					<td class="menu_group_items">  <!-- AP Inquiries -->
						<table width="100%" class="table_index">
							<?php // bowikaxu - suppliers transaction inquiries
							?>
                                                        <tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_PDFChequePoliza.php?' . SID . "'><LI>" . _('Imprimir Cheques') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_SupplierTransInquiry.php?' . SID . "'><LI>" . _('Transaction Inquiries') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/AgedSuppliers.php?' . SID . "'><LI>" . _('Aged Supplier Report') . '</LI></A>'; ?>
							</td>
							</tr>
							<!--
							    rleal se esconde la liga
							    Ene 19, 2014
							<tr>
							<td class="menu_group_item">
								<?//php echo "<A HREF='" . $rootpath . '/SuppPaymentRun.php?' . SID . "'><LI>" . _('Payment Run Report') . '</LI></A>'; ?>
							</td>
							</tr>
							-->
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/OutstandingGRNs.php?' . SID . "'><LI>" . _('Outstanding GRNs Report') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php  // bowikaxu realhost - April 2008
							?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_invoice_discounts.php?' . SID . "'><LI>" . _('Report').' '._('Discount').' '._('Supplier Invoice') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/SupplierBalsAtPeriodEnd.php?' . SID . "'><LI>" . _('Creditor Balances At A Prior Month End') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php //echo "<A HREF='" . $rootpath . '/rh_agedSuppliers.php?' . SID . "'><LI>" . _('Reporte de cuentas por pagar') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo GetRptLinks('ap'); ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_items">   <!-- AP Maintenance Options -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/Suppliers.php?' . SID . "'><LI>" . _('Add Supplier') . '</LI></A>'; ?>
							</td>
							</tr>
						</table>
					</td>
					</tr>
				</table>
			</td>
			</tr>
		</table>
	<?php
		break;

	Case 'PO': /* Purchase Ordering */

	?>
		<table width="100%">
			<tr>
			<td valign="top" class="menu_group_area">
				<table width="100%">

					<?php OptionHeadings(); ?>

					<tr>
					<td class="menu_group_items">  <!-- PO Transactions -->
						<table width="100%" class="table_index">
							<?php // bowikaxu - March 2007 - corregir los links ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/PO_Header.php?' . SID . "&NewOrder=Yes'><LI>" . _('Ingresar Pedido de Compra') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/PO_SelectOSPurchOrder.php?' . SID . "'><LI>" . _('Consulta de Pedidos Sobresalientes') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/PO_AuthoriseMyOrders.php?' . SID . "'><LI>" . _('Ordenes por autorizar') . '</LI></A>'; ?>
							</td>
							</tr>
                                                        <tr>
							<td class="menu_group_item">
								<?php /*iJPe*/ echo "<A HREF='" . $rootpath . '/rh_Search_PO_InvNum.php?' . SID . "'><LI>" . _('Ver Facturas Asignadas a Pedidos Recibidos') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu - se corrigio el link de shipments march 2007?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/Shipments.php?' . SID . "&NewShipment=Yes'><LI>" . _('Shipment Entry') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/Shipt_Select.php?' . SID . "'><LI>" . _('Select A Shipment') . "</LI></A>"; ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_items">  <!-- PO Inquiries -->
						<table width="100%" class="table_index">

							<tr>
                            <td class="menu_group_item">
                                <?php echo "<A HREF='" . $rootpath . '/modulos/index.php?r=purchorders' . SID . "'><LI>" . _('Pendientes por Facturar') . '</LI></A>'; ?>
                            </td>
                            </tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/PO_SelectPurchOrder.php?' . SID . "'><LI>" . _('Purchase Order Inquiry') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu - suppliers report by name and date ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_SuppInquiry_dates.php?' . SID . "'><LI>" . _('Suppliers by Name/Date') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu - reporte de pedidos de compra ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_purchorders_inquiry.php?' . SID . "'><LI>" . _('Report').' '._('Purchase Orders') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // rleal - reporte de pedidos de compra por proveedor ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_purchorders_inquiry_supp.php?' . SID . "'><LI>" . _('Report').' '._('Purchase Orders') . '/Proveedor</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo GetRptLinks('prch'); ?>
							</td>
							</tr>
					</table>
					</td>
					<td class="menu_group_items">   <!-- PO Maintenance -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/PurchData.php?' . SID . "'><LI>" . _('Maintain Purchasing Data') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php
								//iJPe 2010-01-22
								//Se quito mangueras no lo utiliza
								//include_once 'php-ofc-library/open_flash_chart_object.php';
								//open_flash_chart_object( 500, 300, 'rh_charts9.php',false);
								?>
							</td>
							</tr>
  							<tr>
    							<td class="menu_group_item">
    								<?php echo "<A HREF='" . $rootpath . '/rh_pedimentos.php?' . SID . "'><LI>" . _('Pedimentos de Importaci&oacute;n') . '</LI></A>'; ?>
    							</td>
							</tr>
							<tr>
                                <td class="menu_group_item">
                                    <?php echo "<A HREF='" . $rootpath . '/modulos/index.php?r=deliveryaddress' . SID . "'><LI>" . _('Direcciones de Envio') . '</LI></A>'; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="menu_group_item">
                                    <?php echo "<A HREF='" . $rootpath . '/rh_Cerrar_PO.php' . SID . "'><LI>" . _('Cerrar Ordenes de Compra') . '</LI></A>'; ?>
                                </td>
                            </tr>
						</table>
					</td>
					</tr>
				</table>
			</td>
			</tr>
		</table>
	<?php
		break;

	/* ****************************** END OF PURCHASING OPTIONS ******************************** */


	Case 'stock': //Inventory Module

	?>
		<table width="100%">
			<tr>
			<td valign="top" class="menu_group_area">
				<table width="100%">

					<?php OptionHeadings(); ?>

					<tr>
					<td class="menu_group_items">
						<table width="100%" class="table_index">


							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/PO_SelectOSPurchOrder.php?' . SID . "'><LI>" . _('Receive Purchase Orders') . '</LI></A>'; ?>
							</td>
							</tr>
							<?php if($_SESSION['DatabaseName'] != 'consulta_externa_erp_001'): ?>
							<tr>
								<td class="menu_group_item">
									<?php echo "<A HREF='" . $rootpath . '/rh_transferencias.php' . SID . "'><LI>" . _('Transferencias ISSSTELEON') . '</LI></A>'; ?>
								</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/StockLocTransfer.php' . SID . "'><LI>" . _('Bulk Inventory Transfer') . ' - ' . _('Dispatch') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/StockLocTransferReceive.php?' . SID . "'><LI>" . _('Bulk Inventory Transfer') . ' - ' . _('Receive') . '</LI></A>'; ?>
							</td>
							</tr>
							<?php endif; ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/StockTransfers.php?' . SID . "'><LI>" . _('Inventory Location Transfers') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/StockAdjustments.php?' . SID . "'><LI>" . _('Inventory Adjustments') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
                            <td class="menu_group_item">
                                <?php echo "<A HREF='" . $rootpath . '/modulos/index.php?r=stockmoves/bajaporconsumo' . SID . "'><LI>" . _('Baja por Consumo') . '</LI></A>'; ?>
                            </td>
                            </tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/ReverseGRN.php?' . SID . "'><LI>" . _('Reverse Goods Received') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
								<td class="menu_group_item">
									<?php echo "<A HREF='" . $rootpath . '/StockCounts.php?' . SID . "'><LI>" . _('Enter Stock Counts') . '</LI></A>'; ?>
								</td>
							</tr>
							<tr>
								<td class="menu_group_item">
									<?php echo "<A HREF='" . $rootpath . '/rh_stock_report.php?' . SID . "'><LI>" . _('Descargar Listado de Articulos') . '</LI></A>'; ?>
								</td>
							</tr>
							<?php if($_SESSION['DatabaseName'] != 'consulta_externa_erp_001'): ?>
							<tr>
								<td class="menu_group_item">
									<?php echo "<A HREF='" . $rootpath . '/rhStocktransferLote.php?' . SID . "'><LI>" . _('Transferencia entre Almacenes por Lote (Env&iacute;o)') . '</LI></A>'; ?>
								</td>
							</tr>
							<tr>
								<td class="menu_group_item">
									<?php echo "<A HREF='" . $rootpath . '/rh_listadoTransferLote.php?' . SID . "'><LI>" . _('Transferencia entre Almacenes por Lote (Recepci&oacute;n)') . '</LI></A>'; ?>
								</td>
							</tr>
 							<tr>
 								<td class="menu_group_item"> 
									<?php echo "<A HREF='" . $rootpath . '/modulos/index.php?r=stockmoves/bajaporconsumows' . SID . "'><LI>" . _('ISSTELEON WS') . '</LI></A>'; ?>
 								</td> 
 							</tr> 
							<?php endif; ?>
						</table>
					</td>
					<td class="menu_group_items">
						<table width="100%" class="table_index">


							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . "/StockSerialItemResearch.php?" . SID . "'><LI>" . _('Serial Item Research Tool') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . "/StockMovements.php?" . SID . "'><LI>" . _('Inventory Item Movements') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
                                <td class="menu_group_item">
                                    <?php echo "<A HREF='" . $rootpath . '/modulos/index.php?r=stockmoves/reportebajas' . SID . "'><LI>" . _('Reporte Baja por Consumo') . '</LI></A>'; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="menu_group_item">
                                    <?php echo "<A HREF='" . $rootpath . '/modulos/index.php?r=stockmoves/reporteinventario' . SID . "'><LI>" . _('Reporte de Inventario') . '</LI></A>'; ?>
                                </td>
                            </tr>
							<?php // bowikaxu realhost - price history report ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . "/rh_PriceHistory_Inquiry.php?" . SID . "'><LI>" . _('Price History') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu realhost - cost history report ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . "/rh_CostHistory_Inquiry.php?" . SID . "'><LI>" . _('Cost History') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu realhost - price history report ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . "/rh_InventoryBarcode.php?" . SID . "'><LI>" . _('Cat&aacute;logo de C&oacute;digo de Barras') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/StockStatus.php?' . SID . "'><LI>" . _('Inventory Item Status') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/StockUsage.php?' . SID . "'><LI>" . _('Inventory Item Usage') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/InventoryValuation.php?' . SID . "'><LI>" . _('Inventory Valuation Report') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/InventoryPlanning.php?' . SID . "'><LI>" . _('Inventory Planning Report') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/StockCheck.php?' . SID . "'><LI>" . _('Inventory Stock Check Sheets') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
								<td class="menu_group_item">
									<?php echo "<A HREF='" . $rootpath . '/StockQties_csv.php?' . SID . "'><LI>" . _('Make Inventory Quantities CSV') . '</LI></A>'; ?>
								</td>
							</tr>


							<tr>
							</tr>

							<?php // bowikaxu realhost - 15 07 2008 - inventario con cantidad y costo ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/StockCost_csv.php?' . SID . "'><LI>" . _('Make Inventory Quantities CSV').' / ' . _('Cost') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>

							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/PDFStockCheckComparison.php?' . SID . "'><LI>" . _('Compare Counts Vs Stock Check Data') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/StockLocMovements.php?' . SID . "'><LI>" . _('All Inventory Movements By Location/Date') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/StockLocStatus.php?' . SID . "'><LI>" . _('List Inventory Status By Location/Category') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/StockQuantityByDate.php?' . SID . "'><LI>" . _('Historical Stock Quantity By Location/Category') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu realhost - costo cero ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_costo_cero.php?' . SID . "'><LI>" . _('Costo Cero') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu realhost - precio cero ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_precio_cero.php?' . SID . "'><LI>" . _('Precio Cero') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/PDFStockNegatives.php?' . SID . "'><LI>" . _('List Negative Stocks') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_Item_Prices.php?' . SID . "'><LI>" . _('Items').' / '. _('Prices') . '</LI></A>'; ?>
							</td>
							</tr>

                                                        <tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/ImportPrices.php?' . SID . "'><LI>" . _('Mantenimiento Lista de precios') . '</LI></A>'; ?>
							</td>
							</tr>

                            <tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_existencias.php?' . SID . "'><LI>" . _('Saldos Articulos') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_InventoryPlanning.php?' . SID . "'><LI>" . _('Inventory Planning Report')." HTML" . '</LI></A>'; ?>
							</td>
							</tr>
							<?php  if($_SESSION['DatabaseName'] != 'consulta_externa_erp_001'): ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_reporteRecetasISSSTELEON.php?' . SID . "'><LI>" . _('Reporte Recetas ISSSTELEON global') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_reporteRecetasISSSTELEON_det.php?' . SID . "'><LI>" . _('Reporte Recetas ISSSTELEON detallado') . '</LI></A>'; ?>
							</td>
							</tr>
							<?php endif; ?>
							<tr>
								<td class="menu_group_item">
									<?php echo "<A HREF='" . $rootpath . '/rh_expirationdate.php?' . SID . "'><LI>" . _('Inventario con Lotes') . '</LI></A>'; ?>
								</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_existencias_id_agrupador.php?' . SID . "'><LI>" . _('Inventario por ID Agrupador') . '</LI></A>'; ?>
							</td>
							</tr>


							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_reporte_movimientos.php?' . SID . "'><LI>" . _('Movimientos de inventario') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_kardex.php?' . SID . "'><LI>" . _('Kardex') . '</LI></A>'; ?>
							</td>
							</tr>
							<?php  if($_SESSION['DatabaseName'] != 'consulta_externa_erp_001'): ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_isssteleonWS.php?' . SID . "'><LI>" . _('Sincronizaci&oacute;n ISSSTELEON') . '</LI></A>'; ?>
							</td>
							</tr>
							<?php endif; ?>

							<tr>
							<td class="menu_group_item">
								<?php echo GetRptLinks('inv'); ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_items">
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/Stocks.php?' . SID . "'><LI>" . _('Add A New Item') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/SelectProduct.php?' . SID . "'><LI>" . _('Select An Item') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/SalesCategories.php?' . SID . "'><LI>" . _('Sales Category Maintenance') . '</LI></A>'; ?>
							</td>
							</tr>
						   <!-- <tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_sales_factors.php?' . SID . "'><LI>" . _('Factores de venta') . '</LI></A>'; ?>
							</td>
							</tr>
 						     -->

							<?php // bowikaxu realhsot - may 2008 - OSCommerce Integration
							/*
							?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_oscomm/oscomm2erp_prod.php?' . SID . "'><LI>" . _('OSCommerce Sincronizar').' '._('Items') . '</LI></A>'; ?>
							</td>
							</tr>
							<?php */
							if(isset($_SESSION['StorageBins'])&&$_SESSION['StorageBins']==1){
								?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_storagebins.php?' . SID . "'><LI>" . _('Storage bins') . '</LI></A>'; ?>
							</td>
							</tr>
							<?php }?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_Ajuste_Masivo.php?' . SID . "'><LI>" . _('Ajuste Masivo') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
								<td class="menu_group_item">
									<?php echo "<A HREF='" . $rootpath . '/rh_imprimirCodigosBarras.php?' . SID . "'><LI>" . _('Impresion Etiquetas') . '</LI></A>'; ?>
								</td>
							</tr>

							<tr>
								<td class="menu_group_item">
									<?php echo "<A HREF='" . $rootpath . '/rh_ReimprimirCodigosBarras.php?' . SID . "'><LI>" . _('Reimpresion Etiquetas') . '</LI></A>'; ?>
								</td>
							</tr>
							<tr>
								<td class="menu_group_item">
									<?php echo "<A HREF='" . $rootpath . '/rh_imprimir_hoja_codigo.php?' . SID . "'><LI>" . _('Reimprimir Relación de Etiquetas') . '</LI></A>'; ?>
								</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_stock_grupos.php?' . SID . "'><LI>" . _('Mantenimiento ID Agrupador') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php
								include_once 'php-ofc-library/open_flash_chart_object.php';
								open_flash_chart_object( 300, 300, 'rh_charts7.php',false);
								?>
							</td>
							</tr>
						</table>
					</td>
					</tr>
				</table>
			</td>
			</tr>
		</table>
	<?php
		break;

	/* ****************************** END OF INVENTORY OPTIONS *********************************** */

	Case 'manuf': //Manufacturing Module

	?>
		<table width="100%">
			<tr>
			<td valign="top" class="menu_group_area">
				<table width="100%">

					<?php OptionHeadings(); ?>

					<tr>
					<td class="menu_group_items">
						<table width="100%" class="table_index">
							<tr>
							  <td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/WorkOrderEntry.php?' . SID . "'><LI>" . _('Work Order Entry') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/SelectWorkOrder.php?' . SID . "'><LI>" . _('Select A Work Order') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu realhost - facturar ordenes de produccion ?>
							<!--<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_WO_Invoice.php?' . SID . "'><LI>" . _('Invoice').' '._('Work Order') . '</LI></A>'; ?>
							</td>
							</tr> -->

						</table>
					</td>
					<td class="menu_group_items">
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/SelectWorkOrder.php?' . SID . "'><LI>" . _('Select A Work Order') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/BOMInquiry.php?' . SID . "'><LI>" . _('Costed Bill Of Material Inquiry') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/WhereUsedInquiry.php?' . SID . "'><LI>" . _('Where Used Inquiry') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/BOMListing.php?' . SID . "'><LI>" . _('Bills Of Material Listing') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo GetRptLinks('man'); ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_items">
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/WorkCentres.php?' . SID . "'><LI>" . _('Work Centre') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu - menu impresion de ordenes de trabajo  ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_PrintCustWO.php?' . SID . "'><LI>" . _('Print Work Orders') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/BOMs.php?' . SID . "'><LI>" . _('Bills Of Material') . '</LI></A>'; ?>
							</td>
							</tr>
						</table>
					</td>

					</tr>
				</table>
			</td>
			</tr>
		</table>
	<?php
		break;


	Case 'system': //System setup

	?>
		<table width='100%'>
			<tr>
			<td valign="top" class="menu_group_area">
				<table width="100%" >
					<tr>
					<td class="menu_group_headers">
						<table>
							<tr>
							<td>
								<?php echo '<img src="'.$rootpath.'/css/'.$theme.'/images/company.png" TITLE="' . _('General Setup Options') . '" ALT="">'; ?>
							</td>
							<td class="menu_group_headers_text">
								<?php echo _('General'); ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_headers">
						<table>
							<tr>
							<td>
								<?php echo '<img src="'.$rootpath.'/css/'.$theme.'/images/ar.png" TITLE="' . _('Receivables/Payables Setup') . '" ALT="">'; ?>
							</td>
							<td class="menu_group_headers_text">
								<?php echo _('Receivables/Payables'); ?>

							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_headers">
						<table>
							<tr>
							<td>
								<?php echo '<img src="'.$rootpath.'/css/'.$theme.'/images/inventory.png" TITLE="' . _('Inventory Setup') . '" ALT="">'; ?>
							</td>
							<td class="menu_group_headers_text">
								<?php echo _('Inventory Setup'); ?>
							</td>
							</tr>
						</table>
					</td>


					</tr>
					<tr>

					<td class="menu_group_items">	<!-- Gereral set up options -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/CompanyPreferences.php?' . SID . "'><LI>" . _('Company Preferences') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/SystemParameters.php?' . SID . "'><LI>" . _('Configuration Settings') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/WWW_Users.php?' . SID . "'><LI>" . _('User Accounts') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_Users.php?' . SID . "'><LI>" . _('Habilitar/Deshabilitar Usuarios') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/WWW_Access.php?' . SID . "'><LI>" . _('Role Permissions') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/BankAccounts.php?' . SID . "'><LI>" . _('Bank Accounts') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/Currencies.php?' . SID . "'><LI>" . _('Currency Maintenance') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/TaxAuthorities.php?' . SID . "'><LI>" . _('Tax Authorities and Rates Maintenance') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/TaxGroups.php?' . SID . "'><LI>" . _('Tax Group Maintenance') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/TaxProvinces.php?' . SID . "'><LI>" . _('Dispatch Tax Province Maintenance') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/TaxCategories.php?' . SID . "'><LI>" . _('Tax Category Maintenance') . '</LI></A>'; ?>
							</td>
							</tr>

							<!--<?php  /* bowikaxu realhost - may 2008 - especialistas */ ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_especialistas.php?' . SID . "'><LI>" . _('Specialits'); ?>
							</td>
							</tr>

							<?php  /* bowikaxu realhost - may 2008 - maquinas */ ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_maquinas.php?' . SID . "'><LI>" . _('Machines'); ?>
							</td>
							</tr>

							--><tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/PeriodsInquiry.php?' . SID . "'><LI>" . _('List Periods Defined') . ' <FONT SIZE=1>(' . _('Periods are automatically maintained') . ')</FONT></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<A HREF="' . $rootpath . '/reportwriter/admin/ReportCreator.php"><LI>' . _('Report Builder Tool') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu realhost March 2008 - reporte de archivos relacionados con transacciones ?>
							<tr>
							<td class="menu_group_item">
								<?php echo '<A HREF="' . $rootpath . '/rh_Files_Inquiry.php"><LI>' . _('Report').' '._('File Upload') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu realhost March 2008 - diff from weberp original 3.08 ?>
							<tr>
							<td class="menu_group_item">
								<?php echo '<A HREF="' . $rootpath . '/AuditTrail.php"><LI>' . _('View Audit Trail') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu realhost March 2008 - diff from weberp original 3.08 ?>
							<tr>
    							<td class="menu_group_item">
    								<?php echo '<A HREF="' . $rootpath . '/rh_pos_terminal.php"><LI>' . _('Terminales Punto de Venta') . '</LI></A>'; ?>
    							</td>
							</tr>

							<tr>
    							<td class="menu_group_item">
    								<?php echo '<A HREF="' . $rootpath . '/rh_BlockPageUser.php"><LI>' . _('BlockPageUser') . '</LI></A>'; ?>
    							</td>
							</tr>

							<tr>
                                <td class="menu_group_item">
                                    <?php echo '<A HREF="' . $rootpath . '/modulos/cobradores"><LI>' . _('Cobradores') . '</LI></A>'; ?>
                                </td>
                            </tr>

						</table>
					</td>

					<td class="menu_group_items">	<!-- AR/AP set-up options -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/SalesTypes.php?' . SID . "'><LI>" . _('Sales Types') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/CreditStatus.php?' . SID . "'><LI>" . _('Credit Status') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/PaymentTerms.php?' . SID . "'><LI>" . _('Payment Terms') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/PO_AuthorisationLevels.php?' . SID . '"><LI>' . _('Niveles de autorizaci&oacute;n de Ord. de compra') . '</a></LI>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/modulos/paymentmethod?' . SID . "'><LI>" . _('Payment Methods') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/SalesPeople.php?' . SID . "'><LI>" . _('Sales People') . '</LI></A>'; ?>
							</td>
							</tr>
							<?php // bowikaxu - realhost - salespeople report of comissions ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_SalesMan_Inquiry.php?' . SID . "'><LI>" . _('Report') .' '. _('Sales People') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/Areas.php?' . SID . "'><LI>" . _('Sales Areas') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/Shippers.php?' . SID . "'><LI>" . _('Shippers') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/SalesGLPostings.php?' . SID . "'><LI>" . _('Sales GL Interface Postings') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/COGSGLPostings.php?' . SID . "'><LI>" . _('COGS GL Interface Postings') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/FreightCosts.php?' . SID . "'><LI>" . _('Freight Costs Maintenance') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/DiscountMatrix.php?' . SID . "'><LI>" . _('Discount Matrix') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu realhost - April 2008 - Suppliers Discounts ?>
							<!--<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_Supplier_DiscMatrix.php?' . SID . "'><LI>" . _('Discount Matrix') . ' ' . _('Suppliers') . '</LI></A>'; ?>
							</td>
							</tr> -->

						</table>
					</td>

					<td class="menu_group_items">	<!-- Inventory set-up options -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/StockCategories.php?' . SID . "'><LI>" . _('Inventory Categories Maintenance') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/Locations.php?' . SID . "'><LI>" . _('Inventory Locations Maintenance') . '</LI></A>'; ?>
							</td>
							</tr>
                                                        <tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_Locations.php?' . SID . "'><LI>" . _('Administrar Dep&oacute;sitos y Almacenes Virtuales') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/DiscountCategories.php?' . SID . "'><LI>" . _('Discount Category Maintenance') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/UnitsOfMeasure.php?' . SID . "'><LI>" . _('Units of Measure') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_marca.php?' . SID . "'><LI>" . _('Catalogo de Marca') . '</LI></A>'; ?>
							</td>
							</tr>
                            <? if($_SESSION['Gamma']==1){ ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_gamma.php?' . SID . "'><LI>" . _('Catalogo de '.$_SESSION['GammaName']) . '</LI></A>'; ?>
							</td>
							</tr>
                            <? }  ?>

                            <tr>
                            <td class="menu_group_item">
                                <?php echo "<A HREF='" . $rootpath . '/rh_sustanciaactiva.php?' . SID . "'><LI>" . _('Catalogo de Sustancia Activa') . '</LI></A>'; ?>
                            </td>
                            </tr>

                            <?if($_SESSION['Especie']==1){ ?>
 							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_especie.php?' . SID . "'><LI>" . _('Catalogo de '.$_SESSION['EspecieName']) . '</LI></A>'; ?>
							</td>
							</tr>
                            <?} ?>

                            <? if($_SESSION['Rutas']==1){ ?>
 							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_rutas.php?' . SID . "'><LI>" . _('Catalogo de rutas') . '</LI></A>'; ?>
							</td>
							</tr>
                            <?} ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_blockuser.php?' . SID . "'><LI>" . _('Acceso a Usuarios sin IP') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_blockip.php?' . SID . "'><LI>" . _('Acceso de IP') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_reglaprecios.php?' . SID . "'><LI>" . _('Regla de Descuentos') . '</LI></A>'; ?>
							</td>
							</tr>
							<?
/****************************************************************************************************************************
* Jorge Garcia
* 28/Ene/2009 Administrar cuentas de ref espeacial
****************************************************************************************************************************/
							?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_refaccounts.php?' . SID . "'><LI>" . _('Maintenance Account').'s '._('Special Ref').'</LI></A>'; ?>
							</td>
							</tr>
							<?
/****************************************************************************************************************************
* Jorge Garcia Fin Modificacion
****************************************************************************************************************************/
							?>
							<tr>
							<td class="menu_group_item">
								<?php
								/*
								$username = 'admin';
								$load_layers = true;
								$allow_user_override = true;
								echo "<LI><B>CITAS</B><BR>";
								echo "<iframe border=0 width=400 height=300 src='webcalendar/upcoming.php?user=admin'>";
								//include ('webcalendar/upcoming.php?user=admin');
								echo "</iframe>";
								//echo '<iframe height="200" width="180" scrolling="yes" src="webcalendar/minical.php"></iframe>';
								//echo '<iframe src="webcalendar/upcoming.php" width="400" height="400" name="califrame"></iframe>';
								*/
								?>
							</td>
							</tr>
 							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/ImportPrices.php?' . SID . "'><LI>" . _('Mantenimiento Lista de precios') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_maximos_minimos.php?' . SID . "'><LI>" . _('Mantenimiento Maximos Minimos') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_familia.php?' . SID . "'><LI>" . _('Mantenimiento Familias') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_idmaxmin_upload.php?' . SID . "'><LI>" . _('Actualizar Maximos Minimos') . '</LI></A>'; ?>
							</td>
							</tr>


						</table>
					</tr>
					</tr>
				</table>
			</td>
			</tr>
		</table>
	<?php
		break;

	Case 'GL': //General Ledger

	?>
		<table width="100%">
			<tr>
			<td valign="top" class="menu_group_area">
				<table width="100%">		<!-- Gereral Ledger Option Headings-->

    					<?php OptionHeadings(); ?>

					<tr>
					<td class="menu_group_items"> <!-- General transactions options -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/Payments.php?' .SID . "&NewPayment=Yes'><LI>" . _('Bank Account Payments') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/CustomerReceipt.php?' . SID . "'><LI>" . _('Bank Account Receipts') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/GLJournal.php?' .SID . "&NewJournal=Yes'><LI>" . _('Journal Entry') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/BankMatching.php?' .SID . "&Type=Receipts'><LI>" . _('Bank Deposits Matching') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/BankMatching.php?' .SID . "&Type=Payments'><LI>" . _('Bank Payments Matching') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/layoutBancos.php?' .SID . "'><LI>" . _('Bank Layout') . '</LI></A>'; ?>
							</td>
							</tr>


						</table>
					</td>
					<td class="menu_group_items">  <!-- Gereral inquiry options -->
						<table width="100%" class="table_index">

							<?php // bowikaxu realhost Feb 2008 - Balanze General Presupuestado ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_BalComp.php?' . SID . "'><LI>" . _('Trial Balance') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu realhost Feb 2008 - Balanza de comprobacion detallada ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_GLTrialBalance.php?' . SID . "'><LI>" . _('Detailed Trial Balance') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_GtosInquiry.php?' . SID . "'><LI>" . _('Expenses Inquiry') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/SelectGLAccount.php?' . SID . "'><LI>" . _('Account Inquiry') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>

							<?php // bowikaxu realhost Feb 2008 - Estado de Resultados ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_GLProfit_Loss.php?' . SID . "'><LI>" . _('Profit and Loss Statement') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu realhost July 2008 - Estado de Resultados Original ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/GLProfit_Loss.php?' . SID . "'><LI>" . _('Profit and Loss Statement') .' '._('Legacy'). '</LI></A>'; ?>
							</td>
							</tr>

							<?php /*
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/GLBalanceSheet.php?' . SID . "'><LI>" . _('Balance Sheet') . '</LI></A>'; ?>
							</td>
							</tr>
							*/?>

							<?php // bowikaxu realhost Feb 2008 - Estado de Poscicion Financiera ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_EdoFinan.php?' . SID . "'><LI>" . _('Financial Status') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu realhost July 2008 - Estado de Poscicion Financiera Original ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/GLBalanceSheet.php?' . SID . "'><LI>" . _('Balance Sheet'). ' '. _('Legacy') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_GLTrans_Inquiry.php?' . SID . "'><LI>" . _('GL Trans Inquiry') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_GL_Trans_Inquiry.php?' . SID . "'><LI>" . _('GL Trans Inquiry'). ' ' . _('Detailed') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_AccountInquiry.php?' . SID . "'><LI>" . _('Bank Inquiry') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu realhost - poliza de cheques ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_PDFChequePoliza.php?' . SID . "'><LI>" . _('P&oacute;liza de Cheques') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu realhost July 2008 - Balanze General Presupuestado Original ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/GLTrialBalance.php?' . SID . "'><LI>" . _('Trial Balance').' '._('Budget') .' ' . _('Legacy') . '</LI></A>'; ?>
							</td>
							</tr>

							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/BankReconciliation.php?' . SID . "'><LI>" . _('Bank Reconciliation Statement') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/PDFChequeListing.php?' . SID . "'><LI>" . _('Cheque Payments Listing') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php /// bowikaxu realhost jan 2008 - policy reports ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_polizas_menu.php?' . SID . "'><LI>" . _('Policy') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/Tax.php?' . SID . "'><LI>" . _('GST Reports') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu - reporte de cuentas especiales ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_Accounts.php?' . SID . "'><LI>" . _('Account').'s '._('Special Ref') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu - reporte de impuestos ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_TaxInquiry.php?' . SID . "'><LI>" . _('Tax Reporting') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu - reporte de impuestos 2 ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_supplier_tax.php?' . SID . "'><LI>" . _('Tax').' '._('Pagado') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu - reporte de cheques ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_ChequeInquiry.php?' . SID . "'><LI>" . _('Cheque').' '._('Reports') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu - reporte de asignacion de cheques ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_ChequeWhereAlloc.php?' . SID . "'><LI>" . _('Allocation').' '._('Cheque') . '</LI></A>'; ?>
							</td>
							</tr>

							<?php // bowikaxu - link al reporte de GL by year ?>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_GLPL_year.php?' . SID . "'><LI>" . _('GLPL Year') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/Tax.php?' . SID . "'><LI>" . _('Tax Reports') . '</LI></A>'; ?>
							</td>
							</tr>
							
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/rh_DIOT.php?' . SID . "'><LI>" . _('DIOT') . '</LI></A>'; ?>
							</td>
							</tr>

							<tr>
							<td class="menu_group_item">
								<?php echo GetRptLinks('gl'); ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_items">  <!-- Gereral Ledger Maintenance options -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/GLAccounts.php?' . SID . "'><LI>" . _('GL Account') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/GLBudgets.php?' . SID . "'><LI>" . _('GL Budgets') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/AccountGroups.php?' . SID . "'><LI>" . _('Account Group') . '</LI></A>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo "<A HREF='" . $rootpath . '/AccountSections.php?' . SID . "'><LI>" . _('Account Sections') . '</LI></A>'; ?>
							</td>
							</tr>
						</table>
					</td>
					</tr>
				</table>
			</td>
			</tr>
		</table>
	<?php
		break;
	} //end of module switch
} /* end of if security allows to see the full menu */

// all tables started are ended within this index script which means 2 outstanding from footer.

include('includes/footer.inc');

function OptionHeadings() {

global $rootpath, $theme;

?>

	<tr>
	<td class="menu_group_headers"> <!-- Orders option Headings -->
		<table>
			<tr>
			<td>
				<?php echo '<img src="'.$rootpath.'/css/'.$theme.'/images/transactions.png" TITLE="' . _('Transactions') . '" ALT="">'; ?>
			</td>
			<td class="menu_group_headers_text">
				<?php echo _('Transactions'); ?>
			</td>
			</tr>
		</table>
	</td>
	<td class="menu_group_headers">
		<table>
			<tr>
			<td>
				<?php echo '<img src="'.$rootpath.'/css/'.$theme.'/images/reports.png" TITLE="' . _('Inquiries and Reports') . '" ALT="">'; ?>
			</td>
			<td class="menu_group_headers_text">
				<?php echo _('Inquiries and Reports'); ?>
			</td>
			</tr>
		</table>
	</td>
	<td class="menu_group_headers">
		<table>
			<tr>
			<td>
				<?php echo '<img src="'.$rootpath.'/css/'.$theme.'/images/maintenance.png" TITLE="' . _('Maintenance') . '" ALT="">'; ?>
			</td>
			<td class="menu_group_headers_text">
				<?php echo _('Maintenance'); ?>
			</td>
			</tr>
		</table>
	</td>
	</tr>

<?php

}

function GetRptLinks($GroupID) {
/*
This function retrieves the reports given a certain group id as defined in /reports/admin/defaults.php
in the acssociative array $ReportGroups[]. It will fetch the reports belonging solely to the group
specified to create a list of links for insertion into a table to choose a report. Two table sections will
be generated, one for standard reports and the other for custom reports.
*/
	global $db, $rootpath;
	require_once('reportwriter/languages/en_US/reports.php');
	require_once('reportwriter/admin/defaults.php');

	$Title= array(_('Custom Reports'), _('Standard Reports and Forms'));

	$sql= "SELECT id, reporttype, defaultreport, groupname, reportname
		FROM reports ORDER BY groupname, reportname";
	$Result=DB_query($sql,$db,'','',false,true);
	$ReportList = '';
	while ($Temp = DB_fetch_array($Result)) $ReportList[] = $Temp;

	$RptLinks = '';
	for ($Def=1; $Def>=0; $Def--) {
		$RptLinks .= '<tr><td class="menu_group_headers"><div align="center">'.$Title[$Def].'</div></td></tr>';
		$NoEntries = true;
		if ($ReportList) { // then there are reports to show, show by grouping
			foreach ($ReportList as $Report) {
				if ($Report['groupname']==$GroupID AND $Report['defaultreport']==$Def) {
					$RptLinks .= '<tr><td class="menu_group_item">';
					$RptLinks .= '<A HREF="'.$rootpath.'/reportwriter/ReportMaker.php?action=go&reportid='.$Report['id'].'"><LI>'._($Report['reportname']).'</LI></A>';
					$RptLinks .= '</td></tr>';
					$NoEntries = false;
				}
			}
			// now fetch the form groups that are a part of this group (List after reports)
			$NoForms = true;
			foreach ($ReportList as $Report) {
				$Group=explode(':',$Report['groupname']); // break into main group and form group array
				if ($NoForms AND $Group[0]==$GroupID AND $Report['reporttype']=='frm' AND $Report['defaultreport']==$Def) {
					$RptLinks .= '<tr><td class="menu_group_item">';
					$RptLinks .= '<img src="'.$rootpath.'/css/'.$_SESSION['Theme'].'/images/folders.gif" width="16" height="13">&nbsp;';
					$RptLinks .= '<A HREF="'.$rootpath.'/reportwriter/FormMaker.php?id='.$Report['groupname'].'">';
					$RptLinks .= $FormGroups[$Report['groupname']].'</A>';
					$RptLinks .= '</td></tr>';
					$NoForms = false;
					$NoEntries = false;
				}
			}
		}
		if ($NoEntries) $RptLinks .= '<tr><td class="menu_group_item">'._('There are no reports to show!').'</td></tr>';
	}
	return $RptLinks;
}
//ObEndLinks();