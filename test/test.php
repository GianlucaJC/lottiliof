<?php  
/* Specify the server and connection string attributes. */  
ini_set('display_errors', 1); 
$serverName = "LIOSDB01";  
  
/* Get UID and PWD from application-specific files.  */  
$uid = "lf";  
$pwd = "LfTest2021.";  
$connectionInfo = array( "UID"=>$uid,  
                         "PWD"=>$pwd,  
                         "Database"=>"DBLF");  
  
/* Connect using SQL Server Authentication. */  
$conn = sqlsrv_connect( $serverName, $connectionInfo);  
if( $conn === false )  
{  
     echo "Unable to connect.</br>";  
     die( print_r( sqlsrv_errors(), true));  
}  else echo "OK";
?>