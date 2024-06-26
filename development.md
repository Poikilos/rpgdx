# rpgdx Development
"rpgdx" is a fork of phpBB.
This document describes issues with installation or debugging, including fork-specific or phpBB 2.x specific info that therefore may be difficult.

### Debug setup
This debug setup process uses the insecure default username and password for *local debugging only* not on a live server!

If using this as a troubleshooting guide, replace username, password, and database name with secure values...
- and do not push the values (config.php) to the repo.

Using old versions of MySQL: Note `ENGINE=MyISAM` was formerly `TYPE=MyISAM` in "sql/database.sql". Only old versions of mysql use the `TYPE` keyword for this.

```bash
sudo su -
mysql
```

```sql
CREATE DATABASE indierp_main;
CREATE USER 'username'@'localhost' IDENTIFIED BY 'password';
GRANT ALL ON indierp_main.* TO 'username'@'localhost';
quit;
```
- Adding `WITH GRANT OPTION` to the GRANT ALL ON line would allow the user to grant privs to other users.

```bash
mysql -u username -p indierp_main < sql/database.sql
```
- then type password

```sql
use indierp_main;
@sql/database.sql;
```

If you are on Fedora or a similar distro and get AVC denials, you may have to do:
```
/sbin/restorecon -v /opt/git/php-ereg-shim-procedural
/sbin/restorecon -v /opt/git/php8-shims.php
/sbin/restorecon -v /opt/git/mysql-shim/mysql-shim.php
/sbin/restorecon -v /opt/git/php-ereg-shim-procedural/php-ereg-shim-procedural.php
/sbin/restorecon -v /opt/git/polyfill-each/polyfill-each.php
/sbin/restorecon -v /opt/git/polyfill-each
```
- result: "Relabeled /opt/git/mysql-shim/mysql-shim.php from unconfined_u:object_r:default_t:s0 to unconfined_u:object_r:usr_t:s0"

```
setsebool -P httpd_can_network_connect 1
setsebool -P httpd_read_user_content 1
semanage port -a -t http_port_t -p tcp 9003
```

Place a copy of the repo in /var/www/html or change your Apache or NGINX configuration.

If you still cannot view localhost://forums/install/install.php, try adding the following just after the `<?php` line of the file:
```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

Scroll past all of the "Warning: Undefined variable $table_prefix" error lines to get to the install form.

Required:
- For the Database type, choose mariadb (requires *forked* install.php)
- Prefix for tables: `phpbb_`  (This is must be *different* from the rpgdx_ tables. Also, the custom php code of rpgdx assumes `phpbb_` for internal phpbb_ data.)

After the first step you may see:

"The PHP configuration on your server doesn't support the database type that you chose"

(for related code, see uses of `$lang['Install_No_Ext']`)

If the forum doesn't appear after install, try:
```
tail /var/log/httpd/error_log
```
And diagnose and fix errors manually.

If no errors appear, try adding the error reporting lines mentioned earlier to each of your index.php files.


### Troubleshooting database structure
The database structure can be dumped without data as follows:
```
mysqldump --no-data -u username -p -h localhost indierp_main
```
- except change username, localhost (may be something else on shared hosting), and indierp_main.
- ensure the --no-data option is used, or it will output all data!
  - to backup both structure and data, leave out `--no-data` but redirect to a file: `> backup-2024.sql`

### Troubleshooting mysqldump
If you don't get the structure, but only get something like:
```
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
```
Ensure the table name such as `indierp_main` in the example is correct in the dump commmand.