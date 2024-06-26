<?php
/***************************************************************************
 *                                 db.php
 *                            -------------------
 *   begin                : Saturday, Feb 13, 2001
 *   copyright            : (C) 2001 The phpBB Group
 *   email                : support@phpbb.com
 *
 *   $Id: db.php 5283 2005-10-30 15:17:14Z acydburn $
 *
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

if ( !defined('IN_PHPBB') )
{
	die("Hacking attempt");
}

switch($dbms)
{
	case 'mysql':
		// mariadb has same $dbms string.
		// but requires php-mysql-shim on PHP 8
		include($phpbb_root_path . 'db/mysql.'.$phpEx);
		break;

	case 'mariadb':
		// requires php-mysql-shim on PHP 8
		// mariadb has same $dbms string.
		include($phpbb_root_path . 'db/mariadb.'.$phpEx);
		break;

	case 'mysql4':
		// requires php-mysql-shim on PHP 8
		include($phpbb_root_path . 'db/mysql4.'.$phpEx);
		break;

	case 'postgres':
		include($phpbb_root_path . 'db/postgres7.'.$phpEx);
		break;

	case 'mssql':
		include($phpbb_root_path . 'db/mssql.'.$phpEx);
		break;

	case 'oracle':
		include($phpbb_root_path . 'db/oracle.'.$phpEx);
		break;

	case 'msaccess':
		include($phpbb_root_path . 'db/msaccess.'.$phpEx);
		break;

	case 'mssql-odbc':
		include($phpbb_root_path . 'db/mssql-odbc.'.$phpEx);
		break;

	default:
		die("Unknown database type \"$dbms\"");
		break;
}

// Make the database connection.
$db = new sql_db($dbhost, $dbuser, $dbpasswd, $dbname, false);
if(!$db->db_connect_id)
{
	message_die(CRITICAL_ERROR, "Could not connect to the database");
}

?>
