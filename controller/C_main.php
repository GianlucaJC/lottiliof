<?php
$date_next=$_POST['date_next'];
if (strlen($date_next)==0) $date_next=date("Y-m-d");

$sub_AC=$_POST['sub_AC'];
if ($sub_AC=="1") {
	$new_AC=new_AC();
}

$sub_M=$_POST['sub_M'];
if ($sub_M=="1") {
	$new_M=new_M();
}

?>