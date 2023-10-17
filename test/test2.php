<?php  
/* Specify the server and connection string attributes. */  
ini_set('display_errors', 1); 
$serverName = "LIOSDB01";  
  
/* Get UID and PWD from application-specific files.  */  
$uid = "lf";  
$pwd = "LfTest2021.";  

// Server in the this format: <computer>\<instance name> or 
// <server>,<port> when using a non default port number

$db="DBLF";




// Connect to MSSQL
$link = mssql_connect($serverName, $uid, $pwd);

if (!$link) {
    die('Something went wrong while connecting to MSSQL');
}

$db_selected = mssql_select_db($db, $link);
if (!$db_selected) {
  die ('Can\'t use db : ' . mssql_get_last_message());
} else{

    // Success
}

$query ="SELECT * FROM WLF_PROTOCOLLO  ";
$result =mssql_query($query);
while ( $record = mssql_fetch_array($result) )
{
	print_r($record);
	echo "<hr>";
}

?>