<?php  
/*  
=============  
This file is part of a Microsoft SQL Server Shared Source Application.  
Copyright (C) Microsoft Corporation.  All rights reserved.  
  
THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY  
KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE  
IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A  
PARTICULAR PURPOSE.  
=============  
*/  
//$serverName = "(local)\LIOSDB01";
$serverName = "192.168.129.41";
$uid = "lf";  
$pwd = "LfTest2021.";    
  
    
/* Connect using Windows Authentication. */  
try  
{  
	$conn = new PDO( "sqlsrv:Server=$serverName ; Database=DBLF", $uid, $pwd);  
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );  
}  
catch(Exception $e)  
{   

	die( print_r( $e->getMessage() ) );   
}  
  
/* Get the product picture for a given product ID. */  
try  
{  
	$tsql = "SELECT LargePhoto   
	 FROM Production.ProductPhoto AS p  
	 JOIN Production.ProductProductPhoto AS q  
	 ON p.ProductPhotoID = q.ProductPhotoID  
	 WHERE ProductID = ?";  
	$stmt = $conn->prepare($tsql);  
	$stmt->execute(array(&$_GET['productId']));  
	$stmt->bindColumn(1, $image, PDO::PARAM_LOB, 0, PDO::SQLSRV_ENCODING_BINARY);  
	$stmt->fetch(PDO::FETCH_BOUND);  
	echo $image;  
}  
catch(Exception $e)  
{   
	die( print_r( $e->getMessage() ) );   
}  
?>