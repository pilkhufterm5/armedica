<?php

/*
	This file is part of the Legal Case Management System (LCM).
	(C) 2004-2005 Free Software Foundation, Inc.

	This program is free software; you can redistribute it and/or modify it
	under the terms of the GNU General Public License as published by the
	Free Software Foundation; either version 2 of the License, or (at your
	option) any later version.

	This program is distributed in the hope that it will be useful, but
	WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
	or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
	for more details.

	You should have received a copy of the GNU General Public License along
	with this program; if not, write to the Free Software Foundation, Inc.,
	59 Temple Place, Suite 330, Boston, MA  02111-1307, USA

	$Id: inc_db_mysql.php,v 1.23.2.8 2006/05/23 07:39:29 mlutfy Exp $
*/

if (defined('_INC_DB_MYSQL')) return;
define('_INC_DB_MYSQL', '1');

if (! function_exists("mysql_query"))
	die("ERROR: MySQL is not correctly installed. Verify that the php-mysql
	module is installed and that the php.ini has something similar to
	'extension=mysql.so'. Refer to the user's manual FAQ for more information.");

//
// SQL query functions
//

function lcm_sql_server_info() {
	return "MySQL " . @mysql_get_server_info();
}

function lcm_mysql_set_utf8() {
	mysql_query('SET NAMES utf8');
	mysql_query("SET CHARACTER SET UTF8");
	mysql_query("SET SESSION CHARACTER_SET_SERVER = UTF8");

	// And yet more overkill, because I am having problems with MySQL 4.1.9
	mysql_query("SET CHARACTER_SET_RESULTS = UTF8");
	mysql_query("SET CHARACTER_SET_CONNECTION = UTF8");
	mysql_query("SET SESSION CHARACTER_SET_DATABASE = UTF8");
	mysql_query("SET SESSION collation_connection = utf8_general_ci");
	mysql_query("SET SESSION collation_database = utf8_general_ci");
	mysql_query("SET SESSION collation_server = utf8_general_ci");
}

function lcm_query_db($query, $accept_fail = false) {
	global $lcm_mysql_link;
	global $debug;
	static $tt = 0;

	$my_debug   = $GLOBALS['sql_debug'];
	$my_profile = $GLOBALS['sql_profile'];

	/* [ML] I have no idea whether this is overkill, but without it,
	   we get strange problems with Cyrillic and other non-latin charsets.
	   We need to check whether tables were installed correctly, or else
	   it will not show non-latin utf8 characters correctly. (i.e. for
	   people who upgraded LCM, but didn't import/export their data to 
	   fix the tables.)
	*/
	if (read_meta('db_utf8') == 'yes') {
		lcm_mysql_set_utf8();
	} elseif ((! read_meta('db_utf8') == 'no') && (! read_meta('lcm_db_version'))) {
		// We are not yet installed, so check MySQL version on every request
		// Note: checking is is_file('inc/data/inc_meta_cache.php') is not
		// enough, because the keywords cache may have been generated, but not
		// the meta.
		if (! preg_match("/^(4\.0|3\.)/", mysql_get_server_info())) {
			lcm_mysql_set_utf8();
		}
	}

	$query = process_query($query);

	if ($my_profile)
		$m1 = microtime();

	if ($GLOBALS['mysql_recall_link'] AND $lcm_mysql_link)
		$result = mysql_query($query, $lcm_mysql_link);
	else 
		$result = mysql_query($query);

	if ($my_debug AND $my_profile) {
		$m2 = microtime();
		list($usec, $sec) = explode(" ", $m1);
		list($usec2, $sec2) = explode(" ", $m2);
		$dt = $sec2 + $usec2 - $sec - $usec;
		$tt += $dt;
		echo "<small>".htmlentities($query);
		echo " -> <font color='blue'>".sprintf("%3f", $dt)."</font> ($tt)</small><p>\n";
	}

	if ($my_debug)
		lcm_log("QUERY: $query\n", "mysql");

	if (lcm_sql_errno() && (!$accept_fail)) {
		$s = lcm_sql_error();
		$error = _T('warning_sql_query_failed') . "<br />\n" . htmlentities($query) . "<br />\n";
		$error .= "&laquo; " . htmlentities($s) . " &raquo;<br />";
		lcm_panic($error);
	}

	return $result;
}

function lcm_query_create_table($query, $restore = false) {
	$ver = @mysql_get_server_info();

	if (preg_match("/^CREATE TABLE/", $query)) {
		if ($restore) {
			// [ML] Switching LCM to InnoDB by default not for today.. needs testing.

			// Remove "FULLTEXT KEY" stuff which InnoDB doesn't like 
			// (and whose utility is not very clear..)
			// $query = preg_replace("/FULLTEXT KEY `?[a-zA-Z]+`? \(`?[a-zA-Z]+`?\),?/", "", $query);
			// $query = preg_replace("/,\s*\\)/", ")", $query);
			
			// Remove possible ENGINE=MyISAM, CHARSET=latin1, etc. at end of query
			$query = preg_replace("/\) (ENGINE=|TYPE=)[^\)]*/", ")", $query);

			// InnoDB needs tweaking with MySQL 3.23, so avoid for now since I cannot test
			// if (! preg_match("/^3\.2/", $ver))
			//	$query .= " TYPE=InnoDB ";
		}

		// Activate UTF-8 only if using MySQL >= 4.1
		// (regexp excludes MySQL <= 4.0, easier for forward compatibility)
		if (! preg_match("/^(4\.0|3\.)/", $ver)) {
			$query .= " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ";

			// [ML] SHOULD BE DONE IN inc_db_upgrade.php since lcm_meta might not exist yet!
			// For those wondering why.. LCM <= 0.6.3 didn't correctly create
			// tables using "character set utf8". We still need to be somehow
			// backwards compatible, so we use the lcm_meta hack to activate
			// whether lcm_query() should "set session character_set_server = utf8"
			// if ($restore) {
			//	write_meta('db_utf8', 'yes');
			//	write_metas();
			// }
		}
	}

	return lcm_query($query);
}

function spip_query_db($query) {
	lcm_log("use of deprecated function: spip_query_db, use lcm_query_db instead");
	return lcm_query_db($query);
}

function lcm_create_table($table, $query) {
	lcm_log("use of deprecated function: lcm_create_table, use lcm_query instead");
	return lcm_query_db('CREATE TABLE '.$GLOBALS['table_prefix'].'_'.$table.'('.$query.')');
}


//
// Process a standard query
// This includes the "prefix" name for the database tables
//
function process_query($query) {
	$db = '';
	$suite = '';

	if ($GLOBALS['mysql_recall_link'] AND $db = $GLOBALS['lcm_mysql_db'])
		$db = '`'.$db.'`.';

	// change the names of the tables ($table_prefix)
	// for example, lcm_case may become foo_case
	if ($GLOBALS['flag_pcre']) {
		if (preg_match('/\s(VALUES|WHERE)\s/i', $query, $regs)) {
			$suite = strstr($query, $regs[0]);
			$query = substr($query, 0, -strlen($suite));
		}
		$query = preg_replace('/([,\s])lcm_/', '\1'.$db.$GLOBALS['table_prefix'].'_', $query) . $suite;
	}
	else {
		if (eregi('[[:space:]](VALUES|WHERE)[[:space:]]', $query, $regs)) {
			$suite = strstr($query, $regs[0]);
			$query = substr($query, 0, -strlen($suite));
		}
		$query = ereg_replace('([[:space:],])lcm_', '\1'.$db.$GLOBALS['table_prefix'].'_', $query) . $suite;
	}

	return $query;
}


//
// Connection to the database
//

function lcm_connect_db($host, $port = 0, $login, $pass, $db = 0, $link = 0) {
	global $lcm_mysql_link, $lcm_mysql_db;	// for multiple connections

	lcm_debug("lcm_connect_db: host = $host, login = $login, pass =~ " . strlen($pass) . " chars", "lcm");

	if (! $login)
		lcm_panic("missing login?");

	if ($link && $db)
		return mysql_select_db($db);

	if ($port > 0) $host = "$host:$port";
	$lcm_mysql_link = @mysql_connect($host, $login, $pass);

	// LCM <= 0.6.x is not MySQL 5.0 compatible!
	if (preg_match("/^5\./", mysql_get_server_info()))
		mysql_query("SET SESSION sql_mode='MYSQL40'");

	if ($lcm_mysql_link && $db) {
		$lcm_mysql_db = $db;
		return @mysql_select_db($db);
	} else {
		return $lcm_mysql_link;
	}
}

function lcm_connect_db_test($host, $login, $pass, $port = 0) {
	unset($link);

	// Non-silent connect, should be shown in <!-- --> anyway
	if ($port > 0) $host = "$host:$port";
	$link = mysql_connect($host, $login, $pass, $port);

	if ($link) {
		mysql_close($link);
		return true;
	} else {
		return false;
	}
}

function lcm_list_databases($host, $login, $pass, $port = 0) {
	$databases = array();

	if ($port > 0) $host = "$host:$port";
	$link = @mysql_connect($host, $login, $pass, $port);

	if ($link) {
		$result = @mysql_list_dbs();

		if ($result AND (($num = mysql_num_rows($result)) > 0)) {
			for ($i = 0; $i < $num; $i++) {
				$name = mysql_dbname($result, $i);
				if ($name != 'test' && $name != 'information_schema')
					array_push($databases, $name);
			}
		}

		return $databases;
	} else {
		echo "<!-- NO LINK -->\n";
		return NULL;
	}
}


//
// Fetch the results
//

function lcm_fetch_array($r) {
	if ($r)
		return mysql_fetch_array($r);
}

function lcm_fetch_assoc($r) {
	if ($r)
		return mysql_fetch_assoc($r);
}

function spip_fetch_array($r) {
	lcm_log("use of deprecated function: spip_fetch_array, use lcm_fetch_array instead");
	return lcm_fetch_array($r);
}

function lcm_fetch_object($r) {
	if ($r)
		return mysql_fetch_object($r);
}

function spip_fetch_object($r) {
	lcm_log("use of deprecated function: spip_fetch_object, use lcm_fetch_object instead");
	return lcm_fetch_object($r);
}

function lcm_fetch_row($r) {
	if ($r)
		return mysql_fetch_row($r);
}

function spip_fetch_row($r) {
	lcm_log("use of deprecated function: spip_fetch_row, use lcm_fetch_row instead");
	return lcm_fetch_row($r);
}

function lcm_sql_error() {
	return mysql_error();
}

function lcm_sql_errno() {
	return mysql_errno();
}

function lcm_num_rows($r) {
	if ($r)
		return mysql_num_rows($r);
}

function spip_num_rows($r) {
	lcm_log("use of deprecated function: spip_num_rows, use lcm_num_rows instead");
	return lcm_num_rows($r);
}

function lcm_data_seek($r,$n) {
	if ($r)
		return mysql_data_seek($r,$n);
}

function lcm_free_result($r) {
	if ($r)
		return mysql_free_result($r);
}

function spip_free_result($r) {
	lcm_log("use of deprecated function: spip_free_result, use lcm_free_result instead");
	return lcm_free_result($r);
}

function lcm_insert_id() {
	return mysql_insert_id();
}

function spip_insert_id() {
	lcm_log("use of deprecated function: spip_insert_id, use lcm_insert_id instead");
	return lcm_insert_id();
}

// Put a local lock on a given LCM installation
// [ML] we can probably ignore this
function spip_get_lock($nom, $timeout = 0) {
	global $lcm_mysql_db, $table_prefix;
	if ($table_prefix) $nom = "$table_prefix:$nom";
	if ($lcm_mysql_db) $nom = "$lcm_mysql_db:$nom";

	$nom = addslashes($nom);
	list($lock_ok) = spip_fetch_array(spip_query("SELECT GET_LOCK('$nom', $timeout)"));
	return $lock_ok;
}

function spip_release_lock($nom) {
	global $lcm_mysql_db, $table_prefix;
	if ($table_prefix) $nom = "$table_prefix:$nom";
	if ($lcm_mysql_db) $nom = "$lcm_mysql_db:$nom";

	$nom = addslashes($nom);
	spip_query("SELECT RELEASE_LOCK('$nom')");
}

?>
