<?php

/* $Revision: 1.26 $ */

/*
date validation and parsing functions

These functions refer to the config variable defining the date format
The date format is defined in SystemParameters called DefaultDateFormat
this can be a string either 'd/m/Y' for UK/Australia/New Zealand dates or
'm/d/Y' for US/Canada format dates

or Y/m/d  for Sweden ;) Anders Eriksson anders@weberp.se.

*/

function Is_Date($DateEntry) {
	$DateEntry2=date('Y/m/d',strtotime($DateEntry));
	if($DateEntry2==str_replace(array('\\','-'),'/',$DateEntry)){
		return 1;
	}
	$DateEntry =Trim($DateEntry);

	//echo '<BR>The date entered is ' . $DateEntry;
		
	if (strpos($DateEntry,'/')) {
		$Date_Array = explode('/',$DateEntry);
	} elseif (strpos ($DateEntry,'-')) {
		$Date_Array = explode('-',$DateEntry);
	} elseif (strlen($DateEntry)==6) {
		$Date_Array[0]= substr($DateEntry,0,2);
		$Date_Array[1]= substr($DateEntry,2,2);
		$Date_Array[2]= substr($DateEntry,4,2);
	} elseif (strlen($DateEntry)==8) {
		$Date_Array[0]= substr($DateEntry,0,2);
		$Date_Array[1]= substr($DateEntry,2,2);
		$Date_Array[2]= substr($DateEntry,4,4);
	}


	If ((int)$Date_Array[2] >9999) {
		Return 0;
	}


	if (is_long((int)$Date_Array[0]) AND is_long((int)$Date_Array[1]) AND is_long((int)$Date_Array[2])) {
		if ($_SESSION['DefaultDateFormat']=='d/m/Y'){

			if (checkdate((int)$Date_Array[1],(int)$Date_Array[0],(int)$Date_Array[2])){
				Return 1;
			} else {
				Return 0;
			}
		} elseif ($_SESSION['DefaultDateFormat']=='m/d/Y'){

			if (checkdate((int)$Date_Array[0],(int)$Date_Array[1],(int)$Date_Array[2])){
				Return 1;
			} else {
				Return 0;
			}
		} elseif ($_SESSION['DefaultDateFormat']=='Y/m/d'){
			
			if (checkdate((int)$Date_Array[1],(int)$Date_Array[2],(int)$Date_Array[0])){
				Return 1;
			} else {
				Return 0;
			}
		} else { /*Can't be in an appropriate DefaultDateFormat */
			Return 0;
		}
	}else { // end if all numeric inputs
		Return 0;
	}

} //end of Is_Date function



//_______________________________________________________________

function MonthAndYearFromSQLDate($DateEntry) {

	setlocale(LC_ALL,$_SESSION['Language']);
	if (strpos($DateEntry,'/')) {
		$Date_Array = explode('/',$DateEntry);
	} elseif (strpos ($DateEntry,'-')) {
		$Date_Array = explode('-',$DateEntry);
	}

	if (strlen($Date_Array[2])>4) {
		$Date_Array[2]= substr($Date_Array[2],0,2);
	}

	Return ucfirst(strftime('%B %Y', mktime(0,0,0, (int)$Date_Array[1],(int)$Date_Array[2],(int)$Date_Array[0])));

}

function DayOfWeekFromSQLDate($DateEntry) {


	if (strpos($DateEntry,'/')) {
		$Date_Array = explode('/',$DateEntry);
	} elseif (strpos ($DateEntry,'-')) {
		$Date_Array = explode('-',$DateEntry);
	}

	if (strlen($Date_Array[2])>4) {
		$Date_Array[2]= substr($Date_Array[2],0,2);
	}

	Return date( 'w', mktime(0,0,0, (int)$Date_Array[1],(int)$Date_Array[2],(int)$Date_Array[0]));

}


// Returns the timestamp for the financial year end. To find other year ends,
// use $YearIncrement to move back and fgorward. in -1 gives last year end, 1 
// gives next year end.

function YearEndDate($MonthNo, $YearIncrement) {
	if (Date("m") > $MonthNo) {
		$Year = Date("Y")+1+$YearIncrement;
	} else {
		$Year = Date("Y")+$YearIncrement;
	}
	Return mktime(0,0,0,$MonthNo+1,0,$Year);	
}

function ConvertSQLDate($DateEntry) {

//for MySQL dates are in the format YYYY-mm-dd

	
	if (strpos($DateEntry,'/')) {
		$Date_Array = explode('/',$DateEntry);
	} elseif (strpos ($DateEntry,'-')) {
		$Date_Array = explode('-',$DateEntry);
	}

	if (strlen($Date_Array[2])>4) {  /*chop off the time stuff */
		$Date_Array[2]= substr($Date_Array[2],0,2);
	}


	if ($_SESSION['DefaultDateFormat']=='d/m/Y'){
		Return $Date_Array[2].'/'.$Date_Array[1].'/'.$Date_Array[0];
	} elseif ($_SESSION['DefaultDateFormat']=='m/d/Y'){
		Return $Date_Array[1].'/'.$Date_Array[2].'/'.$Date_Array[0];
	} elseif ($_SESSION['DefaultDateFormat']=='Y/m/d'){
		Return $Date_Array[0].'/'.$Date_Array[1].'/'.$Date_Array[2];
	}

} // end function ConvertSQLDate

function SQLDateToEDI($DateEntry) {

//for MySQL dates are in the format YYYY-mm-dd
//EDI format 102 dates are in the format CCYYMMDD - just need to lose the seperator

	if (strpos($DateEntry,'/')) {
		$Date_Array = explode('/',$DateEntry);
	} elseif (strpos ($DateEntry,'-')) {
		$Date_Array = explode('-',$DateEntry);
	}

	if (strlen($Date_Array[2])>4) {  /*chop off the time stuff */
		$Date_Array[2]= substr($Date_Array[2],0,2);
	}

	Return $Date_Array[0].$Date_Array[1].$Date_Array[2];

} // end function SQLDateToEDI

function ConvertToEDIDate($DateEntry) {

/* takes a date in a the format specified in $_SESSION['DefaultDateFormat']
and converts to a yyyymmdd - EANCOM format 102*/

	
	$DateEntry = trim($DateEntry);

	if (strpos($DateEntry,'/')) {
		$Date_Array = explode('/',$DateEntry);
	} elseif (strpos ($DateEntry,'-')) {
		$Date_Array = explode('-',$DateEntry);
	} elseif (strlen($DateEntry)==6) {
		$Date_Array[0]= substr($DateEntry,0,2);
		$Date_Array[1]= substr($DateEntry,2,2);
		$Date_Array[2]= substr($DateEntry,4,2);
	} elseif (strlen($DateEntry)==8) {
		$Date_Array[0]= substr($DateEntry,0,2);
		$Date_Array[1]= substr($DateEntry,2,2);
		$Date_Array[2]= substr($DateEntry,4,4);
	}


//to modify assumption in 2030

	If ((int)$Date_Array[2] <60) {
		$Date_Array[2] = '20'.$Date_Array[2];
	} elseif ((int)$Date_Array[2] >59 AND (int)$Date_Array[2] <100) {
		$Date_Array[2] = '19'.$Date_Array[2];
	} elseif ((int)$Date_Array[2] >9999) {
		Return 0;
	}

	if ($_SESSION['DefaultDateFormat']=='d/m/Y'){
		Return $Date_Array[2].$Date_Array[1].$Date_Array[0];

	} elseif ($_SESSION['DefaultDateFormat']=='m/d/Y') {
		Return $Date_Array[2].$Date_Array[0].$Date_Array[1];

	} elseif ($_SESSION['DefaultDateFormat']=='Y/m/d') {
		Return $Date_Array[1].$Date_Array[2].$Date_Array[0];

	}

} // end function to convert DefaultDateFormat Date to EDI format 102

function ConvertEDIDate($DateEntry, $EDIFormatCode) {

	/*EDI Format codes:
		102  -  CCYYMMDD
		203  -  CCYYMMDDHHMM
		616  -  CCYYWW  - cant handle the week number
		718  -  CCYYMMDD-CCYYMMDD  can't handle this either a date range
	*/

	
	switch ($EDIFormatCode) {
	case 102:
		if ($_SESSION['DefaultDateFormat']=='d/m/Y'){
			Return substr($DateEntry,6,2).'/'.substr($DateEntry,4,2).'/'.substr($DateEntry,0,4);

		} elseif ($_SESSION['DefaultDateFormat']=='m/d/Y') {
						Return substr($DateEntry,4,2).'/'.substr($DateEntry,6,2).'/'.substr($DateEntry,0,4);

		} elseif ($_SESSION['DefaultDateFormat']=='Y/m/d') {
						Return substr($DateEntry,0,4).'/'.substr($DateEntry,4,2).'/'.substr($DateEntry,6,2);

		}
		break;
	case 203:
		if ($_SESSION['DefaultDateFormat']=='d/m/Y'){
			Return substr($DateEntry,6,2).'/'.substr($DateEntry,4,2).'/'.substr($DateEntry,0,4).' ' . substr($DateEntry,6,2).':' . substr($DateEntry,8,2);

		} elseif ($_SESSION['DefaultDateFormat']=='m/d/Y') {
						Return substr($DateEntry,4,2).'/'.substr($DateEntry,6,2).'/'.substr($DateEntry,0,4).' ' . substr($DateEntry,6,2).':' . substr($DateEntry,8,2);

		} elseif ($_SESSION['DefaultDateFormat']=='Y/m/d') {
						Return substr($DateEntry,0,4).'/'.substr($DateEntry,4,2).'/'.substr($DateEntry,6,2).' ' . substr($DateEntry,6,2).':' . substr($DateEntry,8,2);

		}
		break;
	case 616:
		/*multiply the week number by 7 and add to the 1/1/CCYY */
		Return date($_SESSION['DefaultDateFormat'], mktime(0,0,0, 1,1+(7*(int)substr($DateEntry,4,2)),substr($DateEntry,0,4)));
		break;
	case 718:
		if ($_SESSION['DefaultDateFormat']=='d/m/Y'){
			Return substr($DateEntry,6,2).'/'.substr($DateEntry,4,2).'/'.substr($DateEntry,0,4) . ' - '. substr($DateEntry,15,2).'/'.substr($DateEntry,13,2).'/'.substr($DateEntry,9,4);

		} elseif ($_SESSION['DefaultDateFormat']=='m/d/Y') {
						Return substr($DateEntry,4,2).'/'.substr($DateEntry,6,2).'/'.substr($DateEntry,0,4).' - '. substr($DateEntry,13,2).'/'.substr($DateEntry,15,2).'/'.substr($DateEntry,9,4);
		} elseif ($_SESSION['DefaultDateFormat']=='Y/m/d') {
						Return substr($DateEntry,0,4).'/'.substr($DateEntry,4,2).'/'.substr($DateEntry,6,2).' - '. substr($DateEntry,13,2).'/'.substr($DateEntry,15,2).'/'.substr($DateEntry,9,4);

		}

		break;
	}


}

function FormatDefaultDate($DateEntry) {
	return Format_Date($DateEntry);
	return date($_SESSION['DefaultDateFormat'],strtotime(ConvertSQLDate($DateEntry)));
}

function Format_Date($DateEntry) {

	$DateEntry =trim($DateEntry);
	
	$DateEntry2=date('Y/m/d',strtotime($DateEntry));
	if($DateEntry2==str_replace(array('\\','-'),'/',$DateEntry)){
		return date($_SESSION['DefaultDateFormat'],strtotime($DateEntry));
	}
	$DateEntry2=date($_SESSION['DefaultDateFormat'],strtotime($DateEntry));
	if($DateEntry2==$DateEntry){
		return date($_SESSION['DefaultDateFormat'],strtotime($DateEntry));
	}
	
	if (strpos($DateEntry,'/')) {
		$Date_Array = explode('/',$DateEntry);
	} elseif (strpos ($DateEntry,'-')) {
		$Date_Array = explode('-',$DateEntry);
	} elseif (strlen($DateEntry)==6) {
		$Date_Array[0]= substr($DateEntry,0,2);
		$Date_Array[1]= substr($DateEntry,2,2);
		$Date_Array[2]= substr($DateEntry,4,2);
	} elseif (strlen($DateEntry)==8) {
		$Date_Array[0]= substr($DateEntry,0,2);
		$Date_Array[1]= substr($DateEntry,2,2);
		$Date_Array[2]= substr($DateEntry,4,4);
	}
	$Y=0;
	$M=0;
	$D=0;
	if($Date_Array[0]>31){
		$Y=$Date_Array[0];
		if($Date_Array[1]<13&&$Date_Array[2]>12){
			$M=$Date_Array[1];
			$D=$Date_Array[2];
		}
		if($D==0)
		if($Date_Array[1]>12&&$Date_Array[2]<13){
			$M=$Date_Array[2];
			$D=$Date_Array[1];
		}
	}
	if($Date_Array[2]>31){
		$Y=$Date_Array[2];
		if($Date_Array[0]<13&&$Date_Array[1]>12){
			$M=$Date_Array[0];
			$D=$Date_Array[1];
		}
		if($D==0)
		if($Date_Array[0]>12&&$Date_Array[1]<13){
			$M=$Date_Array[1];
			$D=$Date_Array[0];
		}
	}
	if($Y!=0&&$M!=0&&$D!=0){
		return date($_SESSION['DefaultDateFormat'],strtotime("{$Y}-{$M}-{$D}"));
	}
//to modify assumption in 2030

	If ((int)$Date_Array[2] <60) {
		$Date_Array[2] = '20'.$Date_Array[2];
	} elseif ((int)$Date_Array[2] >59 AND (int)$Date_Array[2] <100)						{
		$Date_Array[2] = '19'.$Date_Array[2];
	} elseif ((int)$Date_Array[2] >9999) {
		Return 0;
	}

	If (is_long((int)$Date_Array[0]) AND is_long((int)$Date_Array[1]) AND is_long((int)$Date_Array[2])) {
		if ($_SESSION['DefaultDateFormat']=='d/m/Y'){
			if (checkdate((int)$Date_Array[1],(int)$Date_Array[0],(int)$Date_Array[2])){
				Return $Date_Array[0].'/'.$Date_Array[1].'/'.$Date_Array[2];
			}
		} elseif ($_SESSION['DefaultDateFormat']='m/d/Y'){
			if (checkdate((int)$Date_Array[0],(int)$Date_Array[1],(int)$Date_Array[2]))							{
				Return $Date_Array[0].'/'.$Date_Array[1].'/'.$Date_Array[2];
			}
		} elseif ($_SESSION['DefaultDateFormat']='Y/m/d'){
			if (checkdate((int)$Date_Array[2],(int)$Date_Array[0],(int)$Date_Array[1]))							{
				Return $Date_Array[0].'/'.$Date_Array[1].'/'.$Date_Array[2];
			}
		} // end if check date
	} else { // end if all numeric inputs
		Return 0;
	}
}// end of function




function FormatDateForSQL($DateEntry) {

/* takes a date in a the format specified in $_SESSION['DefaultDateFormat']
and converts to a yyyy/mm/dd format */

	$DateEntry=str_replace('T',' ',trim($DateEntry));

	list($DateEntry,$time)=explode(' ',$DateEntry);
	$time=trim($time,"0:\\/-\r\n\t\0\x0B ");
	
	if($time!='')$time=' '.$time;
	if (strpos($DateEntry,'/')) {
		$Date_Array = explode('/',$DateEntry);
	} elseif (strpos ($DateEntry,'-')) {
		$Date_Array = explode('-',$DateEntry);
	} elseif (strlen($DateEntry)==6) {
		$Date_Array[0]= substr($DateEntry,0,2);
		$Date_Array[1]= substr($DateEntry,2,2);
		$Date_Array[2]= substr($DateEntry,4,2);
	} elseif (strlen($DateEntry)==8) {
		$Date_Array[0]= substr($DateEntry,0,4);
		$Date_Array[1]= substr($DateEntry,4,2);
		$Date_Array[2]= substr($DateEntry,6,2);
	}
	
	
	
	if ($_SESSION['DefaultDateFormat']=='Y/m/d') {
		Return $Date_Array[0].'/'.$Date_Array[1].'/'.$Date_Array[2].$time;

	}
	
//to modify assumption in 2030
	if(strlen($Date_Array[0])<3&&strlen($Date_Array[1])<3){
		If ((int)$Date_Array[2] <60) {
			$Date_Array[2] = '20'.$Date_Array[2];
		} elseif ((int)$Date_Array[2] >59 AND (int)$Date_Array[2] <100) {
			$Date_Array[2] = '19'.$Date_Array[2];
		} elseif ((int)$Date_Array[2] >9999) {
			Return 0;
		}
	}
	$formato=explode('/',$_SESSION['DefaultDateFormat']);
	
	if(strlen($Date_Array[0])>3&&$formato[0]!='Y'){
		Return $Date_Array[0].'/'.$Date_Array[1].'/'.$Date_Array[2].$time;
	}
	
	if($formato[0]=='Y'&&$formato[1]=='d'){
		Return $Date_Array[0].'/'.$Date_Array[2].'/'.$Date_Array[1].$time;
	}
	if ($_SESSION['DefaultDateFormat']=='d/m/Y'){
		/* echo '<BR>The date returned is ' . $Date_Array[2].'/'.$Date_Array[1].'/'.$Date_Array[0]; */
		Return $Date_Array[2].'/'.$Date_Array[1].'/'.$Date_Array[0].$time;

	} elseif ($_SESSION['DefaultDateFormat']=='m/d/Y') {
		Return $Date_Array[2].'/'.$Date_Array[0].'/'.$Date_Array[1].$time;
	} elseif ($_SESSION['DefaultDateFormat']=='Y/m/d') {
		Return $Date_Array[0].'/'.$Date_Array[1].'/'.$Date_Array[2].$time;

	}
	return date('Y/m/d H:i:s',strtotime($DateEntry.$time));

}// end of function

function Date1GreaterThanDate2 ($Date1, $Date2) {

/* returns 1 true if Date1 is greater than Date 2 */

	$Date1 = trim($Date1);
	$Date2 = trim($Date2);
	$Date1_array = explode('/', $Date1);
	$Date2_array = explode('/', $Date2);

	/*Try to make the year of each date comparable - if one date is specified as just 
	2 characters and the other >2 then take the last 2 characters of the other date only */
	if (strlen($Date1_array[2])>2 AND strlen($Date2_array[2])==2){
		$Date1_array[2] = substr($Date1_array[2], strlen($Date1_array[2])-2);
	}
	if (strlen($Date2_array[2])>2 AND strlen($Date1_array[2])==2){
		$Date2_array[2] = substr($Date2_array[2], strlen($Date2_array[2])-2);
	}
	
	/*The 2 element of the array will be the year in either d/m/Y or m/d/Y formats */

	If (($Date1_array[2] - $Date2_array[2]) >0){
		Return 1;
	} Elseif (($Date1_array[2] - $Date2_array[2]) ==0){

	/*The 0 and 1 elements of the array are switched depending on the format used */

		if ($_SESSION['DefaultDateFormat']=='d/m/Y'){
			If ( ($Date1_array[1] -  $Date2_array[1]) >0){
				Return 1;
			} elseif (($Date1_array[1] - $Date2_array[1])==0){
				If (($Date1_array[0] -  $Date2_array[0])>0){
					Return 1;
				}
			}

		} elseif ($_SESSION['DefaultDateFormat'] =='m/d/Y'){
			If (($Date1_array[0] - $Date2_array[0])>0){
				Return 1;
			} elseif (($Date1_array[0] - $Date2_array[0])==0){
				If (($Date1_array[1] - $Date2_array[1])>0){
					Return 1;
				}
			}
		} elseif ($_SESSION['DefaultDateFormat'] =='Y/m/d'){
			If (($Date1_array[2] - $Date2_array[2])>0){
				Return 1;
			} elseif (($Date1_array[2] - $Date2_array[2])==0){
				If (($Date1_array[1] - $Date2_array[1])>0){
					Return 1;
				}
			}
		}
	}
	
	Return 0;
}

function CalcDueDate($TranDate, $DayInFollowingMonth, $DaysBeforeDue){

	$TranDate = trim($TranDate);

	$Date_array = explode('/', $TranDate);

	if ($_SESSION['DefaultDateFormat']=='d/m/Y'){
		if ($DayInFollowingMonth==0){ /*then it must be set up for DaysBeforeDue type */

			$DayDue = $Date_array[0]+$DaysBeforeDue;
			$MonthDue = $Date_array[1];
			$YearDue = $Date_array[2];

		} elseif($DayInFollowingMonth>=29) { //take the last day of month

			$DayDue = 0;
			$MonthDue = $Date_array[1]+2;
			$YearDue = $Date_array[2];
		} else {
			$DayDue = $DayInFollowingMonth;
			$MonthDue = $Date_array[1]+1;
			$YearDue = $Date_array[2];

		}
	} elseif ($_SESSION['DefaultDateFormat']=='m/d/Y'){
		if ($DayInFollowingMonth==0){ /*then it must be set up for DaysBeforeDue type */
			$DayDue = $Date_array[1]+$DaysBeforeDue;
			$MonthDue = $Date_array[0];
			$YearDue = $Date_array[2];

		} elseif($DayInFollowingMonth>=29) { //take the last day of month

			$DayDue = 0;
			$MonthDue = $Date_array[0]+2;
			$YearDue = $Date_array[2];
		} else {
			$DayDue = $DayInFollowingMonth;
			$MonthDue = $Date_array[0]+1;
			$YearDue = $Date_array[2];
		}
	} elseif ($_SESSION['DefaultDateFormat']=='Y/m/d'){
		if ($DayInFollowingMonth==0){ /*then it must be set up for DaysBeforeDue type */
			$DayDue = $Date_array[2]+$DaysBeforeDue;
			$MonthDue = $Date_array[1];
			$YearDue = $Date_array[0];

		} elseif($DayInFollowingMonth>=29) { //take the last day of month

			$DayDue = 0;
			$MonthDue = $Date_array[1]+2;
			$YearDue = $Date_array[0];
		} else {
			$DayDue = $DayInFollowingMonth;
			$MonthDue = $Date_array[1]+1;
			$YearDue = $Date_array[0];
		}
	}
	Return Date($_SESSION['DefaultDateFormat'], mktime(0,0,0, $MonthDue, $DayDue,$YearDue));

}

function DateAdd ($DateToAddTo,$PeriodString,$NumberPeriods){

	$Date_array = explode('/', trim($DateToAddTo));
	if ($_SESSION['DefaultDateFormat']=='d/m/Y'){
		
		switch ($PeriodString) {
		case 'd': //Days
			return Date($_SESSION['DefaultDateFormat'],mktime(0,0,0, (int)$Date_array[1],(int)$Date_array[0]+$NumberPeriods ,(int)$Date_array[2]));
			break;
		case 'w': //weeks
			return Date($_SESSION['DefaultDateFormat'],mktime(0,0,0, (int)$Date_array[1],(int)$Date_array[0]+($NumberPeriods*7),(int)$Date_array[2]));
			break;
		case 'm': //months
			return Date($_SESSION['DefaultDateFormat'],mktime(0,0,0, (int)$Date_array[1]+$NumberPeriods,(int)$Date_array[0],(int)$Date_array[2]));
			break;
		case 'y': //years
			return Date($_SESSION['DefaultDateFormat'],mktime(0,0,0, (int)$Date_array[1],(int)$Date_array[0],(int)$Date_array[2]+$NumberPeriods));
			break;
		default:
			Return 0;
		}
	} elseif ($_SESSION['DefaultDateFormat']=='m/d/Y'){
		
		switch ($PeriodString) {
		case 'd':
			return Date($_SESSION['DefaultDateFormat'],mktime(0,0,0, (int)$Date_array[0],(int)$Date_array[1]+$NumberPeriods,(int)$Date_array[2]));
			break;
		case 'w':
			return Date($_SESSION['DefaultDateFormat'],mktime(0,0,0, (int)$Date_array[0],(int)$Date_array[1]+($NumberPeriods*7),(int)$Date_array[2]));
			break;
		case 'm':
			return Date($_SESSION['DefaultDateFormat'],mktime(0,0,0, (int)$Date_array[0]+$NumberPeriods,(int)$Date_array[1],(int)$Date_array[2]));
			break;
		case 'y':
			return Date($_SESSION['DefaultDateFormat'],mktime(0,0,0, (int)$Date_array[0],(int)$Date_array[1],(int)$Date_array[2]+$NumberPeriods));
			break;
		default:
			Return 0;
		}
	} elseif ($_SESSION['DefaultDateFormat']=='Y/m/d'){
		
		switch ($PeriodString) {
		case 'd':
			return Date($_SESSION['DefaultDateFormat'],mktime(0,0,0, (int)$Date_array[2],(int)$Date_array[1]+$NumberPeriods,(int)$Date_array[0]));
			break;
		case 'w':
			return Date($_SESSION['DefaultDateFormat'],mktime(0,0,0, (int)$Date_array[2],(int)$Date_array[1]+($NumberPeriods*7),(int)$Date_array[0]));
			break;
		case 'm':
			return Date($_SESSION['DefaultDateFormat'],mktime(0,0,0, (int)$Date_array[2]+$NumberPeriods,(int)$Date_array[1],(int)$Date_array[0]));
			break;
		case 'y':
			return Date($_SESSION['DefaultDateFormat'],mktime(0,0,0, (int)$Date_array[2],(int)$Date_array[1],(int)$Date_array[0]+$NumberPeriods));
			break;
		default:
			Return 0;
		}
	}
}

function DateDiff ($Date1, $Date2, $Period) {

/* expects dates in the format specified in $_SESSION['DefaultDateFormat'] - period can be one of 'd','w','y','m'
months are assumed to be 30 days and years 365.25 days This only works
provided that both dates are after 1970. Also only works for dates up to the year 2035 ish */

	$Date1 = trim($Date1);
	$Date2 = trim($Date2);
	$Date1_array = explode('/', $Date1);
	$Date2_array = explode('/', $Date2);

	if ($_SESSION['DefaultDateFormat']=='d/m/Y'){
		$Date1_Stamp = mktime(0,0,0, (int)$Date1_array[1],(int)$Date1_array[0],(int)$Date1_array[2]);
		$Date2_Stamp = mktime(0,0,0, (int)$Date2_array[1],(int)$Date2_array[0],(int)$Date2_array[2]);
	} elseif ($_SESSION['DefaultDateFormat']=='m/d/Y'){
		$Date1_Stamp = mktime(0,0,0, (int)$Date1_array[0],(int)$Date1_array[1],(int)$Date1_array[2]);
		$Date2_Stamp = mktime(0,0,0, (int)$Date2_array[0],(int)$Date2_array[1],(int)$Date2_array[2]);
	} elseif ($_SESSION['DefaultDateFormat']=='Y/m/d'){
		$Date1_Stamp = mktime(0,0,0, (int)$Date1_array[1],(int)$Date1_array[2],(int)$Date1_array[0]);//Changeorder of entries to match Y/M/D format
		$Date2_Stamp = mktime(0,0,0, (int)$Date2_array[1],(int)$Date2_array[2],(int)$Date2_array[0]); //Changeorder of entries to match Y/M/D format
	}
	$Difference = $Date1_Stamp - $Date2_Stamp;

/* Difference is the number of seconds between each date negative if Date 2 > Date 1 */

	switch ($Period) {
	case 'd':
		Return (int) ($Difference/(24*60*60));
		break;
	case 'w':
		Return (int) ($Difference/(24*60*60*7));
		break;
	case 'm':
		Return (int) ($Difference/(24*60*60*30));
		break;
	case 's':
		Return $Difference;
		break;
	case 'y':
		Return (int) ($Difference/(24*60*60*365.25));
		break;
	default:
		Return 0;
	}

}


function CalcEarliestDispatchDate (){

/*This function will need to be modified depending on the business - many businesses run 24x7
The default assumes no delivery on Sat and Sun*/

	$EarliestDispatch = Mktime();
	If (Date('w',$EarliestDispatch)==0 ){

/*if today is a sunday the dispatch date must be tomorrow (Monday) or after */

		//$EarliestDispatch = Mktime(0,0,0,Date('m',$EarliestDispatch),Date('d',$EarliestDispatch)+1,Date('y',$EarliestDispatch));
		$EarliestDispatch = Mktime(0,0,0,Date('m',$EarliestDispatch),Date('d',$EarliestDispatch),Date('y',$EarliestDispatch));

	} elseIf (Date('w',$EarliestDispatch)==6){

/*if today is a saturday the dispatch date must be Monday or after */

		//$EarliestDispatch = Mktime(0,0,0,Date('m',$EarliestDispatch),Date('d',$EarliestDispatch)+2,Date('y',$EarliestDispatch));
		$EarliestDispatch = Mktime(0,0,0,Date('m',$EarliestDispatch),Date('d',$EarliestDispatch),Date('y',$EarliestDispatch));

	}elseif (Date('H')>$_SESSION['DispatchCutOffTime']) {

/* If the hour is after Dispatch Cut Off Time default dispatch date to tomorrow */

		//$EarliestDispatch = Mktime(0,0,0,Date('m'),Date('d')+1,Date('y'));
		$EarliestDispatch = Mktime(0,0,0,Date('m'),Date('d'),Date('y'));
	}
	Return $EarliestDispatch;
}


function GetPeriod ($TransDate, &$db) {

/*Gets the period for a transaction from the date entered from the period table -
if the date entered is out of range monthly periods are inserted as necessary and
the correct, newly inserted period returned

Dates are parsed using the $_SESSION['DefaultDateFormat'] string */

	if (strpos($TransDate,'/')){
		$Date_array = explode('/', $TransDate);
	} elseif (strpos($TransDate,'-')){
		$Date_array = explode('-', $TransDate);
	} else {
		prnMsg(_('Dates must be entered in the format') . ' ' . $_SESSION['DefaultDateFormat'],'error');
		exit;
	}

//	If ((int)$Date_array[2] <60) {
//		$Date_array[2] = '20'. $Date_array[2];
//	} elseif ((int)$Date_array[2] >59 AND (int)$Date_array[2] <100) {
//		$Date_array[2] = '19'. $Date_array[2];
//	}

	if ($_SESSION['DefaultDateFormat']=='d/m/Y'){
		$TransDate = mktime(0,0,0,$Date_array[1],$Date_array[0],$Date_array[2]);
	} elseif ($_SESSION['DefaultDateFormat']=='m/d/Y'){
		$TransDate = mktime(0,0,0,$Date_array[0],$Date_array[1],$Date_array[2]);
	} elseif ($_SESSION['DefaultDateFormat']=='Y/m/d'){
		$TransDate = mktime(0,0,0,$Date_array[1],$Date_array[2],$Date_array[0]);
	}
	
	/*if (!Is_Date($_SESSION['ProhibitPostingsBefore'])){
		//set a suitable default - 1 year ago
		$_SESSION['ProhibitPostingsBefore'] = Date('Y-m-d',mktime(0,0,0,Date('m'),0,Date('Y')-2));
		$result = DB_query("UPDATE config SET confvalue='" . $_SESSION['ProhibitPostingsBefore'] . "' WHERE confname='ProhibitPostingsBefore'",$db);
	}*/
	if (strpos($_SESSION['ProhibitPostingsBefore'],'/')) {
		$ProhibitPostingsBeforeDate_Array = explode('/',$_SESSION['ProhibitPostingsBefore']);
	} elseif (strpos ($_SESSION['ProhibitPostingsBefore'],'-')) {
		$ProhibitPostingsBeforeDate_Array = explode('-',$_SESSION['ProhibitPostingsBefore']);
	}

	if (strlen($ProhibitPostingsBeforeDate_Array[2])>4) {  /*chop off the time stuff */
		$ProhibitPostingsBeforeDate_Array[2]= substr($ProhibitPostingsBeforeDate_Array[2],0,2);
	}

	$NoPostingBeforeDate = mktime(0,
					0,
					0,
					$ProhibitPostingsBeforeDate_Array[1],
					$ProhibitPostingsBeforeDate_Array[2],
					$ProhibitPostingsBeforeDate_Array[0]);


	if ($NoPostingBeforeDate >= $TransDate){
		prnMsg(_('No postings are allowed before') . ' ' . Date($_SESSION['DefaultDateFormat'],$NoPostingBeforeDate) . ' ' . _('This transaction will be posted in the period following as all previous periods are closed'),'warn');
		$TransDate = mktime(0,0,0,Date('m',$NoPostingBeforeDate),Date('d',$NoPostingBeforeDate)+1,Date('Y',$NoPostingBeforeDate));
	}

	$MonthAfterTransDate = Mktime(0,0,0,Date('m',$TransDate)+1,Date('d',$TransDate),Date('Y',$TransDate));

	$GetPrdSQL = "SELECT periodno FROM periods WHERE lastdate_in_period < '" . Date('Y/m/d', $MonthAfterTransDate) . "' AND lastdate_in_period >= '" . Date('Y/m/d', $TransDate) . "'";

	$ErrMsg = _('An error occurred in retrieving the period number');
	$GetPrdResult = DB_query($GetPrdSQL,$db,$ErrMsg);

	if (DB_num_rows($GetPrdResult)==0) {

/*The date entered does not fall between currently defined period ranges.
Need to insert some new periods */

		DB_free_result($GetPrdResult);
		$GetPrdSQL = 'SELECT MAX(lastdate_in_period), MAX(periodno) FROM periods';
		$GetPrdResult = DB_query($GetPrdSQL,$db);
			$myrow = DB_fetch_row($GetPrdResult);

		if ($myrow[1] != null) {
			$Date_array = explode('-', $myrow[0]);

			$LastPeriodEnd = mktime(0,0,0,$Date_array[1]+1,0,(int)$Date_array[0]);
			$LastPeriodNo = $myrow[1];
		} else {
			$LastPeriodEnd = mktime(0,0,0,$Date_array[1],0,(int)$Date_array[0]);
			$LastPeriodNo = 0;
		}

		if (DateDiff(Date($_SESSION['DefaultDateFormat'],$TransDate), Date($_SESSION['DefaultDateFormat'], $LastPeriodEnd),'d')>0){

		/*Then the date entered is after the currently defined period */

			While (DateDiff(Date($_SESSION['DefaultDateFormat'], $TransDate), Date($_SESSION['DefaultDateFormat'], $LastPeriodEnd),'d')>0){

/* The date of the last period added is less than the transaction date */

				$MonthOfLastPeriod = Date('m', $LastPeriodEnd);
				If ($MonthOfLastPeriod ==12) {
					$LastPeriodEnd =  Mktime(0,0,0,2,0,Date('Y',$LastPeriodEnd)+1);
				} else {
					$LastPeriodEnd = Mktime(0,0,0,($MonthOfLastPeriod +2),0,Date('Y',$LastPeriodEnd));
				}

				$LastPeriodNo = $LastPeriodNo + 1;

				$GetPrdSQL = 'INSERT INTO periods (periodno, lastdate_in_period) VALUES (' . $LastPeriodNo . ", '" . Date('Y/m/d', $LastPeriodEnd) . "')";
				$ErrMsg = _('An error occurred in adding a new period number');
				$GetPrdResult = DB_query($GetPrdSQL, $db, $ErrMsg);

				$sql = 'INSERT INTO chartdetails (accountcode, period)
						SELECT chartmaster.accountcode, periods.periodno
							FROM chartmaster
								CROSS  JOIN periods
						WHERE ( chartmaster.accountcode, periods.periodno ) NOT 
							IN ( SELECT chartdetails.accountcode, chartdetails.period FROM chartdetails )';
							
				$InsNewChartDetails = DB_query($sql,$db,'','','',false); /*dont trap errors - chart details records created only as required - duplicate messages ignored */
;
			}
			return $LastPeriodNo;

		} else {
		/* then the transactions date must be before periods have been created for need to insert periods before currently defined periods */

			$GetPrdSQL = 'SELECT MIN(lastdate_in_period), MIN(periodno) FROM periods';
			$ErrMsg = _('An error occurred in getting the first period number in the database');
			$GetPrdResult = DB_query($GetPrdSQL,$db);

			$myrow = DB_fetch_row($GetPrdResult);

			$Date_array = explode('-', $myrow[0]);

			$FirstPeriodEnd = mktime(0,0,0,$Date_array[1]+1,0,(int)$Date_array[0]);
			$FirstPeriodNo = $myrow[1];

			While (DateDiff(Date($_SESSION['DefaultDateFormat'], $TransDate), Date($_SESSION['DefaultDateFormat'], $FirstPeriodEnd), 'd')<0){

			/* The date of the first period is after the transaction date */

				$FirstPeriodEnd = Mktime(0,0,0, Date('m',$FirstPeriodEnd), 0, Date('Y',$FirstPeriodEnd));

				$FirstPeriodNo = $FirstPeriodNo - 1;
				$GetPrdSQL = 'INSERT INTO periods (periodno, lastdate_in_period) VALUES (' . $FirstPeriodNo . ", '" . Date('Y/m/d', $FirstPeriodEnd) . "')";
				$ErrMsg = _('An error occurred in inserting periods before the first period to accomodate back dated transactions');
				$GetPrdResult = DB_query($GetPrdSQL,$db, $ErrMsg);
				$sql = 'INSERT INTO chartdetails (accountcode, period)
						SELECT accountcode,' . $FirstPeriodEnd . ' FROM chartmaster';
				$InsNewChartDetails = DB_query($sql,$db,'','','',false); 
				/*dont trap errors - chart details records */
			}
			return $FirstPeriodNo + 1 ;
		} /*end of logic for dates before currently defined periods */
	} else {

	/*the date is in a range currently defined by period numbers */
		$myrow = DB_fetch_row ($GetPrdResult);
		return $myrow[0];

	}


} /*end of get period function */
?>