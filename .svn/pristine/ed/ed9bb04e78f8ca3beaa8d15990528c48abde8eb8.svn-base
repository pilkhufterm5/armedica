<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2005                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

//include('inc/inc.php');

//alberto Realhost 15/Agosto/2007
//experimentos con la sesion

$PageSecurity = 1;
require_once('rh_calendar/inc/lang/lcm_es.php');
include('includes/session.inc');
$title=_('Main Menu');

/*The module link codes are hard coded in a switch statement below to determine the options to show for each tab */
$ModuleLink = array('orders', 'AR', 'AP', 'PO', 'stock', 'manuf', 'GL', 'system');
/*The headings showing on the tabs accross the main index used also in WWW_Users for defining what should be visible to the user */
$ModuleList = array(_('Sales'), _('Receivables'), _('Payables'), _('Purchases'), _('Inventory'), _('Manufacturing'), _('General Ledger'), _('Setup'));

if (isset($_GET['Application'])){ /*This is sent by this page (to itself) when the user clicks on a tab */
	$_SESSION['Module'] = $_GET['Application'];
}
include('includes/header.inc');

//end alberto

$GLOBALS['lcm_lang']="es";
/*
require_once('rh_calendar/inc/inc_version.php');
require_once('rh_calendar/inc/inc_lang.php');
include_lcm('inc_text');
include_lcm('inc_presentation');*/
include('rh_calendar/inc/inc.php');
include_lcm('inc_calendar');

lcm_page_start(_T('title_calendar_view'));

$afficher_bandeau_calendrier = true;
echo http_calendrier_init('', $_REQUEST['type']);

//lcm_page_end();

?>
