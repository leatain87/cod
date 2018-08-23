<?php

/*
public $host = 'alexanders.ce56dybrnaef.ap-southeast-2.rds.amazonaws.com';
public $user = 'cowd';
public $password = 'D5FaC5QdHB45k2AW';
public $db = 'cowd';
*/

/*
$DBUSER="user";
$DBPASSWD="password";
$DATABASE="user_db";
$DBHOST = "db_host";
*/

/*
$DBUSER="cowd";
$DBPASSWD="D5FaC5QdHB45k2AW";
$DATABASE="cowd";
$DBHOST = "alexanders.ce56dybrnaef.ap-southeast-2.rds.amazonaws.com";

$filename = "backup-" . date("d-m-Y") . ".sql.gz";
$mime = "application/x-gzip";

header( "Content-Type: " . $mime );
header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

$cmd = "mysqldump -u $DBUSER --password=$DBPASSWD --host= $DATABASE | gzip --best";   

passthru( $cmd );

exit(0);

*/

exec("mysqldump -h alexanders.ce56dybrnaef.ap-southeast-2.rds.amazonaws.com -u root -pD5FaC5QdHB45k2AW cowd > dump.sql");
?>