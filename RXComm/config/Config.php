<?php
/**
 * @author LAPTOP
 * @version 1.0
 * @created 23-Sep-2010 03:59:16 p.m.
 */
class Config{
	const GENERAL_HOST='10.183.7.232';
    const GENERAL_USER='usr_sme';
    const GENERAL_PASS='YHy65tgFRtggfds';
	//const GENERAL_PASS='';
    //const GENERAL_USER='usr_bp';
	//const GENERAL_PASS='FFVtrfgbyr';
	const GENERAL_DB='sme_erp_001';

	public static function noCACHE(){
		header("Expires: Tue, 04 Mar 1986 16:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	}
}
?>
