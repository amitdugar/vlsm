<?php

require_once('config.production.php');

$HOST = '127.0.0.1';
$USER = 'root';
$PASSWORD = 'zaq12345';
$DBNAME = 'vl_lab_request';
$PORT = 3306;

// Uniform Server
//$MYSQLDUMP = __DIR__.'\..\..\core\mysql\bin\mysqldump.exe';

// Linux
$MYSQLDUMP = '/usr/bin/mysqldump'; 