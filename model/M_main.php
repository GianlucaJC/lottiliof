<?php
include("conn.php");
$mysqli =new mysqli($servJ,$utJ,$passJ,$databaseJ);

function reparti_steril() {
	global $mysqli;
	$sql="SELECT * FROM autoclave order by codice";
	$result=$mysqli->query($sql);
	$arr=array();
	while($results = $result->fetch_array()){
		$arr[]=$results;
	}
	return $arr;
}
function elenco_materiali() {
	global $mysqli;
	$sql="SELECT * FROM materiali_steril order by codice";
	$result=$mysqli->query($sql);
	$arr=array();
	while($results = $result->fetch_array()){
		$arr[]=$results;
	}
	return $arr;
	
}
function new_AC() {
	global $mysqli;
	$stab_AC=$_POST['stab_AC'];
	$codice_AC=addslashes($_POST['codice_AC']);
	$reparto_AC=addslashes($_POST['reparto_AC']);
	$descr_reparto_AC=$_POST['descr_reparto_AC'];
	$descr_reparto_AC=addslashes($descr_reparto_AC);
	$sql="INSERT INTO autoclave (`stabilimento`, `codice`, `reparto`, `descrizione_reparto`) VALUES ('$stab_AC', '$codice_AC', '$reparto_AC', '$descr_reparto_AC')";
	$result = $mysqli->query($sql);
}

function new_M() {
	global $mysqli;
	$codice_M=addslashes($_POST['codice_M']);
	$descr_M=$_POST['descr_M'];
	$sql="INSERT INTO materiali_steril (`codice`, `descrizione`) VALUES ('$codice_M', '$descr_M')";
	$result = $mysqli->query($sql);
}
?>