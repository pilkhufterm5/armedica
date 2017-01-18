<?php

/* $Revision: 400 $ */
/* $Revision: 400 $ */

$PageSecurity = 11;

include('includes/session.inc');

//echo "<hr>";
//print_r($_SESSION);

$title = _('Location Maintenance');

include('includes/header.inc');

if (isset($_GET['SelectedLocation'])){
	$SelectedLocation = $_GET['SelectedLocation'];
} elseif (isset($_POST['SelectedLocation'])){
	$SelectedLocation = $_POST['SelectedLocation'];
}

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	$_POST['LocCode']=strtoupper($_POST['LocCode']);
	if( trim($_POST['LocCode']) == '' ) {
		$InputError = 1;
		prnMsg( _('The location code may not be empty'), 'error');
	}

        $_POST['LocCodeMaster']=strtoupper($_POST['LocCodeMaster']);
	if( trim($_POST['LocCodeMaster']) == '' ) {
		$InputError = 1;
		prnMsg( _('The location code may not be empty'), 'error');
	}

	if (isset($SelectedLocation) AND $InputError !=1) {

		/* Set the managed field to 1 if it is checked, otherwise 0 */
		if($_POST['Managed'] == 'on'){
			 $_POST['Managed'] = 1;
		} else {
			$_POST['Managed'] = 0;
		}

            /*
             * iJPe
             * realhost
             * 2010-02-12
             *
             * Modificaciones realizadas ya que no se verificaba si el nuevo numero externo de nota de credito
             * ya existia ademas que se realizo la modificacion para que no realizara el update en caso de que el numero externo de factura o nota de
             * credito ya existiera.
             */
            $sql = "SELECT id FROM rh_invoicesreference WHERE extinvoice = '".$_POST['InitExtInv']."' AND loccode = '".$SelectedLocation."'";
            $res = DB_query($sql,$db);

            $sqlNotaCre = "SELECT id FROM rh_crednotesreference WHERE extcn = '".$_POST['InitExtCN']."' AND loccode = '".$SelectedLocation."'";
            $resNotaCre = DB_query($sqlNotaCre,$db);

            $errorSerie = false;
            if ($_SESSION['locationInfo']->InvSerie != $_POST['InvSerie'])
            {
                $sqlSerie = "SELECT transno FROM debtortrans INNER JOIN rh_invoicesreference ON debtortrans.transno = rh_invoicesreference.intinvoice";
                $resSerie = DB_query($sqlSerie, $db);

                if (DB_num_rows($resSerie)>0){
                        prnMsg( _('Ya se han realizado transacciones con la serie: '.$_SESSION['locationInfo']->InvSerie), 'error');
                }

                $errorSerie = true;
            }

            if(DB_num_rows($res)>0 || DB_num_rows($resNotaCre)>0 || $errorSerie){
                    // error ya exista el numero de factura
                    $InputError = 1;

                    if (DB_num_rows($res)>0){
                        prnMsg( _('El numero de factura externo ya existe'), 'error');
                    }
                    if (DB_num_rows($resNotaCre)>0){
                        prnMsg( _('El numero de factura externo ya existe'), 'error');
                    }
                    
            }else{

                    if ($_POST['InitExtInv']!= $_SESSION['locationInfo']->ExtInvoices)
                            $facExt = "extinvoices='".$_POST['InitExtInv']."',";

                    if ($_POST['InitExtCN']!= $_SESSION['locationInfo']->InitExtCN)
                            $ncExt = "rh_extcn = '".($_POST['InitExtCN']-1)."',";

                    $sql = "UPDATE rh_locations_virtual SET $facExt $ncExt
                                    rh_serie='".$_POST['InvSerie']."',
                                    rh_template='".$_POST['InvTemplate']."',
                                    rh_maxitminv = '".$_POST['MaxItmInv']."',
                                    rh_maxitmrem = '".$_POST['MaxItmRem']."',
                                    rh_maxitmpos = '".$_POST['MaxItmPos']."',
                                    rh_maxitmntc = '".$_POST['MaxItmNtc']."',

                                    rh_descaccount='".$_POST['DESCAccount']."',
                                    posaccount='".$_POST['POSAccount']."',
                                    loccode='" . $_POST['LocCode'] . "',
                                 rh_master_loccode='" . $_POST['LocCodeMaster'] . "',
                                    locationname='" . $_POST['LocationName'] . "',
                                    deladd1='" . $_POST['DelAdd1'] . "',
                                    deladd2='" . $_POST['DelAdd2'] . "',
                                    deladd3='" . $_POST['DelAdd3'] . "',
                                    deladd4='" . $_POST['DelAdd4'] . "',
                                    deladd5='" . $_POST['DelAdd5'] . "',
                                    deladd6='" . $_POST['DelAdd6'] . "',
                                    tel='" . $_POST['Tel'] . "',
                                    fax='" . $_POST['Fax'] . "',
                                    email='" . $_POST['Email'] . "',
                                    contact='" . $_POST['Contact'] . "',
                                    taxprovinceid = " . $_POST['TaxProvince'] . ",
                                    managed = " . $_POST['Managed'] . "
                            WHERE loccode = '$SelectedLocation'";

                    $ErrMsg = _('An error occurred updating the') . ' ' . $SelectedLocation . ' ' . _('location record because');
                    $DbgMsg = _('The SQL used to update the location record was');

                    if($InputError==0){
                            $result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
                    }

                    prnMsg( _('The location record has been updated'),'success');
                    unset($_POST['LocCode']);
                    unset($_POST['LocCodeMaster']);
                    unset($_POST['LocationName']);
                    unset($_POST['DelAdd1']);
                    unset($_POST['DelAdd2']);
                    unset($_POST['DelAdd3']);
                    unset($_POST['DelAdd4']);
                    unset($_POST['DelAdd5']);
                    unset($_POST['DelAdd6']);
                    unset($_POST['Tel']);
                    unset($_POST['Fax']);
                    unset($_POST['Email']);
                    unset($_POST['TaxProvince']);
                    unset($_POST['Managed']);
                    unset($SelectedLocation);
                    unset($_POST['Contact']);
                    unset($_POST['InitExtInv']);
                    unset($_POST['POSAccount']);
                    unset($_POST['DESCAccount']);
                    unset($_POST['InvSerie']);
                    unset($_POST['InvTemplate']);
                    unset($_POST['MaxItmInv']);
                    unset($_POST['MaxItmRem']);
                    unset($_POST['MaxItmPos']);
                    unset($_POST['MaxItmNtc']);
                    unset($_POST['InitExtCN']);

                    //iJPe
                    unset($_SESSION['locationInfo']);

            }
	} elseif ($InputError !=1) {

		/* Set the managed field to 1 if it is checked, otherwise 0 */
		if($_POST['Managed'] == 'on') {
			$_POST['Managed'] = 1;
		} else {
			$_POST['Managed'] = 0;
		}

		/*SelectedLocation is null cos no item selected on first time round so must be adding a	record must be submitting new entries in the new Location form */

		$sql = "INSERT INTO rh_locations_virtual (
					extinvoices,
					rh_extcn,
					
					rh_serie,
					rh_template,
					rh_maxitminv,
					rh_maxitmrem,
					rh_maxitmpos,
					rh_maxitmntc,
					
					posaccount,
					rh_descaccount,
					loccode,
                                       rh_master_loccode,
					locationname,
					deladd1,
					deladd2,
					deladd3,
					deladd4,
					deladd5,
					deladd6,
					tel,
					fax,
					email,
					contact,
					taxprovinceid,
					managed
					)
			VALUES (
				'".$_POST['InitExtInv']."',
				'".$_POST['InitExtCN']."',
				
				'".$_POST['InvSerie']."',
				'".$_POST['InvTemplate']."',
				'".$_POST['MaxItmInv']."',
				'".$_POST['MaxItmRem']."',
				'".$_POST['MaxItmPos']."',
				'".$_POST['MaxItmNtc']."',
				
				'" . $_POST['POSAccount'] . "',
				'" . $_POST['DESCAccount'] . "',
				'" . $_POST['LocCode'] . "',
                               '" . $_POST['LocCodeMaster'] . "',
				'" . $_POST['LocationName'] . "',
				'" . $_POST['DelAdd1'] ."',
				'" . $_POST['DelAdd2'] ."',
				'" . $_POST['DelAdd3'] . "',
				'" . $_POST['DelAdd4'] . "',
				'" . $_POST['DelAdd5'] . "',
				'" . $_POST['DelAdd6'] . "',
				'" . $_POST['Tel'] . "',
				'" . $_POST['Fax'] . "',
				'" . $_POST['Email'] . "',
				'" . $_POST['Contact'] . "',
				" . $_POST['TaxProvince'] . ",
				" . $_POST['Managed'] . "
			)";

		$ErrMsg =  _('An error occurred inserting the new location record because');
		$Dbgmsg =  _('The SQL used to insert the location record was');
		$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);

		prnMsg( _('The new location record has been added'),'success');

	/* Also need to add LocStock records for all existing stock items */

                //iJPe Evaluacion
////		$sql = "INSERT INTO locstock (
////					loccode,
////					stockid,
////					quantity,
////					reorderlevel)
////			SELECT '" . $_POST['LocCode'] . "',
////				stockmaster.stockid,
////				0,
////				0
////			FROM stockmaster";
////
////		$ErrMsg =  _('An error occurred inserting the new location stock records for all pre-existing parts because');
////		$DbgMsg =  _('The SQL used to insert the new stock location records was');
////		$result = DB_query($sql,$db,$ErrMsg, $DbgMsg);

		echo '<BR>........ ' . _('and new stock rh_locations_virtual inserted for all existing stock items for the new location');
		unset($_POST['LocCode']);
                unset($_POST['LocCodeMaster']);
		unset($_POST['LocationName']);
		unset($_POST['DelAdd1']);
		unset($_POST['DelAdd2']);
		unset($_POST['DelAdd3']);
		unset($_POST['DelAdd4']);
		unset($_POST['DelAdd5']);
		unset($_POST['DelAdd6']);
		unset($_POST['Tel']);
		unset($_POST['Fax']);
		unset($_POST['Email']);
		unset($_POST['TaxProvince']);
		unset($_POST['Managed']);
		unset($SelectedLocation);
		unset($_POST['Contact']);
		unset($_POST['InitExtInv']);
		unset($_POST['POSAccount']);
		unset($_POST['DESCAccount']);
		unset($_POST['InitExtCN']);

                //iJPe
                unset($_SESSION['locationInfo']);

	}


	/* Go through the tax authorities for all rh_locations_virtual deleting or adding TaxAuthRates records as necessary */

	$result = DB_query('SELECT COUNT(taxid) FROM taxauthorities',$db);
	$NoTaxAuths =DB_fetch_row($result);

	$DispTaxProvincesResult = DB_query('SELECT taxprovinceid FROM rh_locations_virtual',$db);
	$TaxCatsResult = DB_query('SELECT taxcatid FROM taxcategories',$db);
	if (DB_num_rows($TaxCatsResult) > 0 ) { // This will only work if there are levels else we get an error on seek.

		while ($myrow=DB_fetch_row($DispTaxProvincesResult)){
			/*Check to see there are TaxAuthRates records set up for this TaxProvince */
			$NoTaxRates = DB_query('SELECT taxauthority FROM taxauthrates WHERE dispatchtaxprovince=' . $myrow[0], $db);

			if (DB_num_rows($NoTaxRates) < $NoTaxAuths[0]){

				/*First off delete any tax authoritylevels already existing */
				$DelTaxAuths = DB_query('DELETE FROM taxauthrates WHERE dispatchtaxprovince=' . $myrow[0],$db);

				/*Now add the new TaxAuthRates required */
				while ($CatRow = DB_fetch_row($TaxCatsResult)){
					$sql = 'INSERT INTO taxauthrates (taxauthority,
										dispatchtaxprovince,
										taxcatid)
							SELECT taxid,
								' . $myrow[0] . ',
								' . $CatRow[0] . '
							FROM taxauthorities';

					$InsTaxAuthRates = DB_query($sql,$db);
				}
				DB_data_seek($TaxCatsResult,0);
			}
		}
	}


} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button

	$CancelDelete = 0;

// PREVENT DELETES IF DEPENDENT RECORDS
	$sql= "SELECT COUNT(*) FROM salesorders WHERE fromstkloc_virtual='$SelectedLocation'";
	$result = DB_query($sql,$db);
	$myrow = DB_fetch_row($result);
	if ($myrow[0]>0) {
		$CancelDelete = 1;
		prnMsg( _('Cannot delete this location because sales orders have been created delivering from this location'),'warn');
		echo  _('There are') . ' ' . $myrow[0] . ' ' . _('sales orders with this Location code');
	} else {
		$sql= "SELECT COUNT(*) FROM stockmoves WHERE stockmoves.loccode='$SelectedLocation'";
		$result = DB_query($sql,$db);
		$myrow = DB_fetch_row($result);
		if ($myrow[0]>0) {
			$CancelDelete = 1;
			prnMsg( _('Cannot delete this location because stock movements have been created using this location'),'warn');
			echo '<BR>' . _('There are') . ' ' . $myrow[0] . ' ' . _('stock movements with this Location code');

		} else {
			$sql= "SELECT COUNT(*) FROM locstock WHERE locstock.loccode='$SelectedLocation' AND locstock.quantity !=0";
			$result = DB_query($sql,$db);
			$myrow = DB_fetch_row($result);
			if ($myrow[0]>0) {
				$CancelDelete = 1;
				prnMsg(_('Cannot delete this location because location stock records exist that use this location and have a quantity on hand not equal to 0'),'warn');
				echo '<BR> ' . _('There are') . ' ' . $myrow[0] . ' ' . _('stock items with stock on hand at this location code');
			} else {
				$sql= "SELECT COUNT(*) FROM www_users WHERE www_users.defaultlocation='$SelectedLocation'";
				$result = DB_query($sql,$db);
				$myrow = DB_fetch_row($result);
				if ($myrow[0]>0) {
					$CancelDelete = 1;
					prnMsg(_('Cannot delete this location because it is the default location for a user') . '. ' . _('The user record must be modified first'),'warn');
					echo '<BR> ' . _('There are') . ' ' . $myrow[0] . ' ' . _('users using this location as their default location');
				} else {
					$sql= "SELECT COUNT(*) FROM bom WHERE bom.loccode='$SelectedLocation'";
					$result = DB_query($sql,$db);
					$myrow = DB_fetch_row($result);
					if ($myrow[0]>0) {
						$CancelDelete = 1;
						prnMsg(_('Cannot delete this location because it is the default location for a bill of material') . '. ' . _('The bill of materials must be modified first'),'warn');
						echo '<BR> ' . _('There are') . ' ' . $myrow[0] . ' ' . _('bom components using this location');
					} else {
						$sql= "SELECT COUNT(*) FROM workcentres WHERE workcentres.location='$SelectedLocation'";
						$result = DB_query($sql,$db);
						$myrow = DB_fetch_row($result);
						if ($myrow[0]>0) {
							$CancelDelete = 1;
							prnMsg( _('Cannot delete this location because it is used by some work centre records'),'warn');
							echo '<BR>' . _('There are') . ' ' . $myrow[0] . ' ' . _('works centres using this location');
						} else {
							$sql= "SELECT COUNT(*) FROM custbranch WHERE custbranch.defaultlocation='$SelectedLocation'";
							$result = DB_query($sql,$db);
							$myrow = DB_fetch_row($result);
							if ($myrow[0]>0) {
								$CancelDelete = 1;
								prnMsg( _('Cannot delete this location because it is used by some work order records'),'warn');
								echo '<BR>' . _('There are') . ' ' . $myrow[0] . ' ' . _('work orders using this location');
							}else {
								$sql= "SELECT COUNT(*) FROM custbranch WHERE custbranch.defaultlocation='$SelectedLocation'";
								$result = DB_query($sql,$db);
								$myrow = DB_fetch_row($result);
								if ($myrow[0]>0) {
									$CancelDelete = 1;
									prnMsg(_('Cannot delete this location because it is used by some branch records as the default location to deliver from'),'warn');
									echo '<BR> ' . _('There are') . ' ' . $myrow[0] . ' ' . _('branches set up to use this location by default');
								}
							}
						}
					}
				}
			}
		}
	}
	if (! $CancelDelete) {

		/* need to figure out if this location is the only one in the same tax province */
		$result = DB_query("SELECT taxprovinceid FROM rh_locations_virtual WHERE loccode='" . $SelectedLocation . "'",$db);
		$TaxProvinceRow = DB_fetch_row($result);
		$result = DB_query("SELECT COUNT(taxprovinceid) FROM rh_locations_virtual WHERE taxprovinceid=" .$TaxProvinceRow[0],$db);
		$TaxProvinceCount = DB_fetch_row($result);
		if ($TaxProvinceCount[0]==1){
		/* if its the only location in this tax authority then delete the appropriate records in TaxAuthLevels */
			$result = DB_query('DELETE FROM taxauthrates WHERE dispatchtaxprovince=' . $TaxProvinceRow[0],$db);
		}

		$result= DB_query("DELETE FROM locstock WHERE loccode ='" . $SelectedLocation . "'",$db);
		$result = DB_query("DELETE FROM rh_locations_virtual WHERE loccode='" . $SelectedLocation . "'",$db);

		prnMsg( _('Location') . ' ' . $SelectedLocation . ' ' . _('has been deleted') . '!', 'success');
		unset ($SelectedLocation);
	} //end if Delete Location
	unset($SelectedLocation);
	unset($_GET['delete']);
}

if (!isset($SelectedLocation)) {

/* It could still be the second time the page has been run and a record has been selected for modification - SelectedLocation will exist because it was sent with the new call. If its the first time the page has been displayed with no parameters
then none of the above are true and the list of rh_locations_virtual will be displayed with
links to delete or edit each. These will call the same page again and allow update/input
or deletion of the records*/

	$sql = "SELECT loccode,
                      rh_master_loccode,
			locationname,
			taxprovinces.taxprovincename as description,
			managed
		FROM rh_locations_virtual INNER JOIN taxprovinces ON rh_locations_virtual.taxprovinceid=taxprovinces.taxprovinceid";
	$result = DB_query($sql,$db);

	if (DB_num_rows($result)==0){
		prnMsg (_('There are no rh_locations_virtual that match up with a tax province record to display. Check that tax provinces are set up for all dispatch rh_locations_virtual'),'error');
	}

	echo '<CENTER><table border=1>';
	echo '<TR><TD class="tableheader">' . _('Location Code') . '</TD>
            <TD class="tableheader">' . _('Localidad Maestra') . '</TD>
			<TD class="tableheader">' . _('Location Name') . '</TD>
			<TD class="tableheader">' . _('Tax Province') . '</TD>
			<TD class="tableheader">' . _('Managed') . '</TD>
		</TR>';

$k=0; //row colour counter
while ($myrow = DB_fetch_array($result)) {
	if ($k==1){
		echo "<TR bgcolor='#CCCCCC'>";
		$k=0;
	} else {
		echo "<TR bgcolor='#EEEEEE'>";
		$k=1;
	}

	if($myrow['managed'] == 1) {
		$myrow['managed'] = _('Yes');
	}  else {
		$myrow['managed'] = _('No');
	}

	printf("<TD>%s</TD>
            <TD>%s</TD>
                <TD>%s</TD>
		<TD>%s</TD>
		<TD>%s</TD>
		<TD><a href='%sSelectedLocation=%s'>" . _('Edit') . "</TD>
		<TD><a href='%sSelectedLocation=%s&delete=1'>" . _('Delete') . '</TD>
		</TR>',
		$myrow['loccode'],
            $myrow['rh_master_loccode'],
		$myrow['locationname'],
		$myrow['description'],
		$myrow['managed'],
		$_SERVER['PHP_SELF'] . '?' . SID . '&',
		$myrow['loccode'],
		$_SERVER['PHP_SELF'] . '?' . SID . '&',
		$myrow['loccode']);

	}
	//END WHILE LIST LOOP
	echo '</CENTER></table>';
}

//end of ifs and buts!

?>

<p>
<?php
if ($SelectedLocation) {  ?>
	<Center><a href="<?php echo $_SERVER['PHP_SELF'];?>"><?php echo _('REVIEW RECORDS'); ?></a></Center>
<?php } ?>

<P>


<?php



if (!isset($_GET['delete'])) {

	echo "<FORM METHOD='post' action='" . $_SERVER['PHP_SELF'] . '?' . SID . "'>";
	
	if ($SelectedLocation) {
		//editing an existing Location

                unset($_SESSION['locationInfo']);

		mysql_select_db("mangueras_erp_001");
		$sql = "SELECT loccode,
                               rh_master_loccode,
				extinvoices,
				rh_extcn,
				rh_serie,
				rh_template,
				rh_maxitminv,
				rh_maxitmrem,
				rh_maxitmpos,
				rh_maxitmntc,
				posaccount,
				rh_descaccount,
				locationname,
				deladd1,
				deladd2,
				deladd3,
				deladd4,
				deladd5,
				deladd6,
				contact,
				fax,
				tel,
				email,
				taxprovinceid,
				managed
			FROM rh_locations_virtual
			WHERE loccode='$SelectedLocation'";			
				
		$result = DB_query($sql, $db);
		$myrow = DB_fetch_array($result);		
		
		// bowikaxu - obtener numero de factura externa
		$_POST['ExtInvoices'] = $myrow['extinvoices'];
                $_SESSION['locationInfo']->ExtInvoices = $myrow['extinvoices'];

		$_POST['LocCode'] = $myrow['loccode'];
                $_POST['LocCodeMaster'] = $myrow['rh_master_loccode'];
		$_POST['LocationName']  = $myrow['locationname'];
		$_POST['DelAdd1'] = $myrow['deladd1'];
		$_POST['DelAdd2'] = $myrow['deladd2'];
		$_POST['DelAdd3'] = $myrow['deladd3'];
		$_POST['DelAdd4'] = $myrow['deladd4'];
		$_POST['DelAdd5'] = $myrow['deladd5'];
		$_POST['DelAdd6'] = $myrow['deladd6'];
		$_POST['Contact'] = $myrow['contact'];
		$_POST['Tel'] = $myrow['tel'];
		$_POST['Fax'] = $myrow['fax'];
		$_POST['Email'] = $myrow['email'];
		$_POST['TaxProvince'] = $myrow['taxprovinceid'];
		$_POST['Managed'] = $myrow['managed'];
		$_POST['POSAccount'] = $myrow['posaccount'];
		$_POST['DESCAccount'] = $myrow['rh_descaccount'];
		$_POST['InvSerie'] = $myrow['rh_serie'];
                $_SESSION['locationInfo']->InvSerie = $myrow['rh_serie'];

		$_POST['InvTemplate'] = $myrow['rh_template'];
		$_POST['MaxItmInv'] = $myrow['rh_maxitminv'];
		$_POST['MaxItmRem'] = $myrow['rh_maxitmrem'];
		$_POST['MaxItmPos'] = $myrow['rh_maxitmpos'];
		$_POST['MaxItmNtc'] = $myrow['rh_maxitmntc'];
		//Se le suma uno al numero de nota de credito, para mostrar el numero de nota de credito que se le asignara
		//a la siguiente nota de credito que se cree
		$_POST['rh_ExtCN'] = $myrow['rh_extcn']+1;
		$_SESSION['locationInfo']->InitExtCN = $myrow['rh_extcn']+1;
		
		echo "<INPUT TYPE=HIDDEN NAME=SelectedLocation VALUE=" . $SelectedLocation . '>';
		echo "<INPUT TYPE=HIDDEN NAME=LocCode VALUE=" . $_POST['LocCode'] . '>';
		echo '<CENTER><TABLE> <TR><TD>' . _('Location Code') . ':</TD><TD>';
		echo $_POST['LocCode'] . '</TD></TR>';
	} else { //end of if $SelectedLocation only do the else when a new record is being entered
		echo '<CENTER><TABLE><TR><TD>' . _('Location Code') . ':</TD><TD><input type="Text" name="LocCode" value="' . $_POST['LocCode'] . '" SIZE=5 MAXLENGTH=5></TD></TR>';
	}

        echo "</TR><TD>"._('Localidad Maestra') .":</TD><TD><SELECT NAME='LocCodeMaster'>";

	$LocationMaster = DB_query('SELECT loccode, locationname FROM locations',$db);
	while ($myrowLM=DB_fetch_array($LocationMaster)){
		if ($_POST['LocCodeMaster']==$myrowLM['loccode']){
			echo '<OPTION SELECTED VALUE=' . $myrowLM['loccode'] . '>' . $myrowLM['locationname'];
		} else {
			echo '<OPTION VALUE=' . $myrowLM['loccode'] . '>' . $myrowLM['locationname'];
		}
	}

        echo "<TD></TR>";

        //echo '<TR><TD>' . _('Clave de Localidad Maestra') . ':</TD><TD><input type="Text" name="LocCodeMaster" value="' . $_POST['LocCodeMaster'] . '" SIZE=5 MAXLENGTH=5></TD></TR>';
	
	// bowikaxu - ya existen facturas hechas o no
	$SQL = "SELECT COUNT(extinvoice) AS totinv FROM rh_invoicesreference WHERE loccode='".$SelectedLocation."'";
		$result = DB_query($SQL, $db);
		$extinv = DB_fetch_array($result);

	// bowikaxu - already set the initial external invoice or set it up?
		if($extinv['totinv']>=1){
			//echo "<TR><TD>"._('Factura Externa Actual').":</TD><TD>";
			//echo $_POST['ExtInvoices']."</TD></TR>";
			//echo "<INPUT TYPE=HIDDEN NAME='InitExtInv' VALUE='".$_POST['ExtInvoices']."'>";
		}
		/*
		else {
			// bowikaxu - mostrar campo para poner factura externa inicial
			echo "<TR><TD>"._('Factura Externa Inicial').":</TD><TD>";
			echo "<INPUT TYPE=text name='InitExtInv' value='".$_POST['ExtInvoices']."' SIZE=5 MAXLENGTH=5></TD></TR>";
		}
		*/
		
		echo "<TR><TD>"._('Factura Externa Actual').":</TD><TD>";
		echo "<INPUT TYPE=text name='InitExtInv' value='".$_POST['ExtInvoices']."' SIZE=5 MAXLENGTH=5></TD></TR>";
		
		// bowikaxu realhost -sept 2007 nota de credito externa
		echo "<TR><TD>"._('Credit Note').' '._('Externa Actual').":</TD><TD>";
		echo "<INPUT TYPE=text name='InitExtCN' value='".$_POST['rh_ExtCN']."' SIZE=5 MAXLENGTH=5></TD></TR>";
		
		// bowikaxy april 2007 - serie de las facturas
			echo "<TR><TD>"._('Serie de Facturas').":</TD><TD>";
			echo "<INPUT TYPE=text name='InvSerie' value='".$_POST['InvSerie']."' SIZE=5 MAXLENGTH=5></TD></TR>";
			
			// bowikaxy june 2007 - template de facturas
			echo "<TR><TD>"._('Invoice Template').":</TD><TD>";
			echo "<INPUT TYPE=text name='InvTemplate' value='".$_POST['InvTemplate']."' SIZE=5 MAXLENGTH=5></TD></TR>";
			
			// bowikaxy june 2007 - numero maximo de articulos en factura
			echo "<TR><TD>"._('Art&iacute;culos Maximos').":</TD><TD>";
			echo _('Invoice').":<INPUT TYPE=text name='MaxItmInv' value='".$_POST['MaxItmInv']."' SIZE=5 MAXLENGTH=5>";
			echo ' '._('Remisi&oacute;n').":<INPUT TYPE=text name='MaxItmRem' value='".$_POST['MaxItmRem']."' SIZE=5 MAXLENGTH=5>";
			echo ' '._('POS').":<INPUT TYPE=text name='MaxItmPos' value='".$_POST['MaxItmPos']."' SIZE=5 MAXLENGTH=5>";
			// bowikaxy june 2007 - numero maximo de articulos en remision
			//echo "<TR><TD>"._('Shipment Max Items').":</TD><TD>";
			echo ' '._('Credit Note').":<INPUT TYPE=text name='MaxItmNtc' value='".$_POST['MaxItmNtc']."' SIZE=5 MAXLENGTH=5></TD></TR>";

	
	?>

	<TR><TD><?php echo _('Location Name') . ':'; ?></TD>
	<TD><input type="Text" name="LocationName" value="<?php echo $_POST['LocationName']; ?>" SIZE=51 MAXLENGTH=50></TD></TR>
	<TR><TD><?php echo _('Contact for deliveries') . ':'; ?></TD>
	<TD><input type="Text" name="Contact" value="<?php echo $_POST['Contact']; ?>" SIZE=31 MAXLENGTH=30></TD></TR>
	<TR><TD><?php echo _('Delivery Address 1') . ':'; ?></TD>
	<TD><input type="Text" name="DelAdd1" value="<?php echo $_POST['DelAdd1']; ?>" SIZE=41 MAXLENGTH=40></TD></TR>
	<TR><TD><?php echo _('Delivery Address 2') . ':'; ?></TD>
	<TD><input type="Text" name="DelAdd2" value="<?php echo $_POST['DelAdd2']; ?>" SIZE=41 MAXLENGTH=40></TD></TR>
	<TR><TD><?php echo _('Delivery Address 3') . ':'; ?></TD>
	<TD><input type="Text" name="DelAdd3" value="<?php echo $_POST['DelAdd3']; ?>" SIZE=41 MAXLENGTH=40></TD></TR>
	<TR><TD><?php echo _('Delivery Address 4') . ':'; ?></TD>
	<TD><input type="Text" name="DelAdd4" value="<?php echo $_POST['DelAdd4']; ?>" SIZE=41 MAXLENGTH=40></TD></TR>
	<TR><TD><?php echo _('Delivery Address 5') . ':'; ?></TD>
	<TD><input type="Text" name="DelAdd5" value="<?php echo $_POST['DelAdd5']; ?>" SIZE=21 MAXLENGTH=20></TD></TR>
	<TR><TD><?php echo _('Delivery Address 6') . ':'; ?></TD>
	<TD><input type="Text" name="DelAdd6" value="<?php echo $_POST['DelAdd6']; ?>" SIZE=16 MAXLENGTH=15></TD></TR>
	<TR><TD><?php echo _('Telephone No') . ':'; ?></TD>
	<TD><input type="Text" name="Tel" value="<?php echo $_POST['Tel']; ?>" SIZE=31 MAXLENGTH=30></TD></TR>
	<TR><TD><?php echo _('Facsimile No') . ':'; ?></TD>
	<TD><input type="Text" name="Fax" value="<?php echo $_POST['Fax']; ?>" SIZE=31 MAXLENGTH=30></TD></TR>
	<TR><TD><?php echo _('Email') . ':'; ?></TD>
	<TD><input type="Text" name="Email" value="<?php echo $_POST['Email']; ?>" SIZE=31 MAXLENGTH=55></TD></TR>

	<TD><?php echo _('Tax Province') . ':'; ?></TD><TD><SELECT NAME='TaxProvince'>

	<?php
	$TaxProvinceResult = DB_query('SELECT taxprovinceid, taxprovincename FROM taxprovinces',$db);
	while ($myrow=DB_fetch_array($TaxProvinceResult)){
		if ($_POST['TaxProvince']==$myrow['taxprovinceid']){
			echo '<OPTION SELECTED VALUE=' . $myrow['taxprovinceid'] . '>' . $myrow['taxprovincename'];
		} else {
			echo '<OPTION VALUE=' . $myrow['taxprovinceid'] . '>' . $myrow['taxprovincename'];
		}
	}

	?>
	</SELECT></TD></TR>
	<?php //bowikaxu - seleccionar cuenta del punto de venta ?>
	<TR><TD><?php echo _('Cuenta Punto de Venta') . ':'; ?></TD><TD><SELECT NAME='POSAccount'>

	<?php
	$Cuentas = DB_query('SELECT accountcode, accountname FROM chartmaster',$db);
	while ($myrow=DB_fetch_array($Cuentas)){
		if ($_POST['POSAccount']==$myrow['accountcode']){
			echo '<OPTION SELECTED VALUE=' . $myrow['accountcode'] . '>' . $myrow['accountcode'].' - '.$myrow['accountname'];
		} else {
			echo '<OPTION VALUE=' . $myrow['accountcode'] . '>' . $myrow['accountcode'].' - '.$myrow['accountname'];
		}
	}

	?>
	</SELECT></TD></TR>
	
	
	<?php //bowikaxu - sept 07 - seleccionar cuenta de descuentos ?>
	<TR><TD><?php echo _('Cuenta de Descuentos') . ':'; ?></TD><TD><SELECT NAME='DESCAccount'>

	<?php
	$Cuentas = DB_query('SELECT accountcode, accountname FROM chartmaster',$db);
	while ($myrow=DB_fetch_array($Cuentas)){
		if ($_POST['DESCAccount']==$myrow['accountcode']){
			echo '<OPTION SELECTED VALUE=' . $myrow['accountcode'] . '>' . $myrow['accountcode'].' - '.$myrow['accountname'];
		} else {
			echo '<OPTION VALUE=' . $myrow['accountcode'] . '>' . $myrow['accountcode'].' - '.$myrow['accountname'];
		}
	}

	?>
	</SELECT></TD></TR>
	
	<TR><TD><?php echo _('Enable Warehouse Management') . ':'; ?></TD>
	<TD><INPUT TYPE='checkbox' name='Managed'<?php if($_POST['Managed'] == 1) echo ' checked';?>></TD></TR>
	</TABLE>

	<CENTER><input type="Submit" name="submit" value="<?php echo _('Enter Information'); ?>">

	</FORM>

<?php } //end if record deleted no point displaying form to add record

include('includes/footer.inc');
?>
