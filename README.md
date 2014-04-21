Service Monitor
---------------

Dependencies:

* PHP5
* MySQL5
* php-mysql
* php-curl
* php-pear
* PHP pear: net\_ping
* Local email utility, e.g. postfix

Install
-------

Create a PHP file called ```db.php``` and connect to MySQL database in it. All other files will include db.php for database connection.

Create a crontab to run ```croncheck.php``` every minute.

Put the PHP website online.

* Browse ```index.html``` to add new URLs.
* Modify or delete existing URLs in the database (we have not come up with a web page yet).
* View the current status: ```hostlist.php```
* View the history: ```record.php```
* View history of a specific URL: ```record.php?host=$id```, where ```$id``` can be found in ```hostlist.php``` or database.
* View chart: ```chart.php```
* View chart: ```chart.php?id=$id_list&time=$hours```, ```$id_list``` are whitespace-separated id(s) (default show all), ```$hours``` is the number of hours to show (default 24).

Report bugs to <bojieli@gmail.com>
