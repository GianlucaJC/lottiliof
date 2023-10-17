<?php
session_start();
include("conn.php");
$operazione=$_POST['operazione'];

if ($operazione=="login") {
	$datx=date("Y-m-d");
	$ora = date('H:i:s', time());
	$mysqli =new mysqli($servJ,$utJ,$passJ,$databaseJ);
	$user=$_POST['user'];
	$pass=$_POST['pass'];
	$sql ="SELECT userid,passkey,operatore,id,admin_lotti FROM utenti where userid='$user' and passkey='$pass';";
	$rows=array();
	$rows['header']['error']="";

	
	if ($result = $mysqli->query($sql)) {
		$row_cnt = $result->num_rows;
		if ($row_cnt==0 || $row_cnt>1) {
			$rows['header']['login']="KO";	
			$rows['header']['error']="Nome utente o password errata";
			print json_encode($rows);
			exit;
		}
		$res = $result->fetch_row();
		$operatore=$res[2];
		$id_user=$res[3];
		$admin_lotti=$res[4];
		
		if ($result) {
			$_SESSION['user'] = $user;
			$_SESSION['pass'] = $pass;
			$_SESSION['operatore'] = $operatore;
			$_SESSION['id_user'] = $id_user;
			$_SESSION['admin_lotti'] = $admin_lotti;
			$rows['header']['login']="OK";
		} else {
			$rows['header']['login']="KO";	
			$rows['header']['error']="Utente o password errata";				
		}
		$t_oper="Accesso archivio Impegno Lotti";
		$sql="INSERT INTO log_fo(ip,data,sezione,operazione,utente,ora) VALUES('$ip','$datx','IMPEGNOLOTTI','$t_oper','$operatore','$ora')";
		$result = $mysqli->query($sql);

		
	} else  {
		$rows['header']['login']="KO";	
		$rows['header']['error']="Nome utente o password errata";	
	}	
	
	print json_encode($rows);
	exit;

}

$operatore="";$id_user="";$admin_lotti="";
if(isset($_SESSION['user']) && isset($_SESSION['pass'])  ) {		
	$operatore=$_SESSION['operatore'];
	$id_user=$_SESSION['id_user'];
	$admin_lotti=$_SESSION['admin_lotti'];
} else exit;






///INSERIRE DA QUI' IN POI IL CODICE, in riferimento alle operazioni Ajax dopo il login 

$upload=$_GET['upload'];
if ($upload=="1") {
	$mysqli =new mysqli($servJ,$utJ,$passJ,$databaseJ);
	// file name
	$lotto=$_GET['lotto'];
	$filename = $_FILES['userfile']['name'];
	// Location
	$location = 'upload/'.$lotto.".pdf";

	// file extension
	$file_extension = pathinfo($location, PATHINFO_EXTENSION);
	$file_extension = strtolower($file_extension);

	// Valid image extensions
	$image_ext = array("pdf","PDF");

	if(in_array($file_extension,$image_ext)){
	  // Upload file
	  if(move_uploaded_file($_FILES['userfile']['tmp_name'],$location)){
		/*
			file_pdf_scontrino=NULL: not set
			file_pdf_scontrino=1: File pdf inviato
			file_pdf_scontrino=2: Scontrino assente
		*/
		
		$sql="SELECT inizio_ora_steril,inizio_temp_steril,fine_ora_steril,fine_temp_steril FROM impegnolotti_steril WHERE lotto='$lotto'";
		$result=$mysqli->query($sql);
		$res = $result->fetch_row();
		$completo=0;
		if ($res[0]!=NULL && $res[1]!=NULL && $res[2]!=NULL && $res[3]!=NULL) $completo=1;
		
		$sql="UPDATE impegnolotti_steril set file_pdf_scontrino=1,completo=$completo WHERE lotto='$lotto'";
		$result = $mysqli->query($sql);
		
		$completamento="No";
		if ($completo==1) $completamento="Sì";		
		$datx=date("Y-m-d");
		$ora = date('H:i:s', time());
		
		$operatore=$_SESSION['operatore'];
		$descr="Data operazione:$datx $ora;Descrizione operazione:Aggiornamento scontrino;Completamento Ciclo:$completamento";
		$qwx = "INSERT INTO log_steril (id_lotto,operazione,descrizione,operatore,tipo,data_op) VALUES('$lotto',1,'$descr','$operatore','2','$datx')";		
		
		$result=$mysqli->query($qwx);

		
		header("HTTP/1.1 200 OK");
	  } else header("HTTP/1.1 501 KO");
	} else header("HTTP/1.1 501 KO");
	exit;
	
}

if ($operazione=="mod29") {
	include ("mod29.php");
	exit;
}

if ($operazione=="save_close") {
	$lotto=$_POST['lotto'];
	$inizio_ora_steril=$_POST['inizio_ora_steril'];
	$inizio_temp_steril=$_POST['inizio_temp_steril'];
	$fine_ora_steril=$_POST['fine_ora_steril'];
	$fine_temp_steril=$_POST['fine_temp_steril'];
	$no_scontrino=$_POST['no_scontrino'];
	$altro="";
	if ($no_scontrino==1) $altro=",file_pdf_scontrino=2";
	$mysqli =new mysqli($servJ,$utJ,$passJ,$databaseJ);

	$sql="SELECT file_pdf_scontrino FROM impegnolotti_steril WHERE lotto='$lotto'";
	$result=$mysqli->query($sql);
	$res = $result->fetch_row();
	
	$compl=0;
	if ($res[0]==1 || $res[0]==2 || $no_scontrino==1) $compl=1;
	
	$completo=0;
	if (strlen($inizio_ora_steril)!=0 && strlen($inizio_temp_steril)!=0 && strlen($fine_ora_steril)!=0 && strlen($fine_temp_steril)!=0 && $compl==1) $completo=1;
	
	
	$sql="UPDATE impegnolotti_steril SET inizio_ora_steril='$inizio_ora_steril', inizio_temp_steril='$inizio_temp_steril', fine_ora_steril='$fine_ora_steril', fine_temp_steril='$fine_temp_steril', completo=$completo $altro WHERE lotto='$lotto'";
	$result=$mysqli->query($sql);
	if ($result) echo "OK"; else echo "KO";
	
		
	$completamento="No";
	if ($completo==1) $completamento="Sì";		
	$datx=date("Y-m-d");
	$ora = date('H:i:s', time());
	
	$operatore=$_SESSION['operatore'];
	$descr="Data operazione:$datx $ora;Descrizione operazione:Aggiornamento ciclo;Inizio ora:$inizio_ora_steril;Inizio Temperatura:$inizio_temp_steril;Fine Ora:$fine_ora_steril;Fine Temperatura:$fine_temp_steril;Completamento Ciclo:$completamento";
	$qwx = "INSERT INTO log_steril (id_lotto,operazione,descrizione,operatore,tipo,data_op) VALUES('$lotto',2,'$descr','$operatore','2','$datx')";		
	
	$result=$mysqli->query($qwx);
	
	
	exit;
}

if ($operazione=="new_lotto_steril") {
	$rows=array();
	$date_next=$_POST['date_next'];
	$ora = date('H:i:s', time());
	$datx=$date_next;
	$giorno=substr($datx,8,2);
	$mese=substr($datx,5,2);
	$annoatt=substr($datx,2,2);
	$operatore=$_SESSION['operatore'];
	$id_user=$_SESSION['id_user'];		

	$autoclave=$_POST['autoclave'];
	
	$arr_info=explode(";",$autoclave);
	$stab_reparto=$arr_info[0].$arr_info[1];
	$tempo=$_POST['tempo'];
	$temperatura=$_POST['temperatura'];
	$dati=$_POST['dati'];
	
		
	
	$mysqli =new mysqli($servJ,$utJ,$passJ,$databaseJ);
	

	$qwx ="SELECT id_lotto from impegnolotti_steril where data='$datx' and stab_reparto='$stab_reparto' order by id_lotto desc;";
	$result=$mysqli->query($qwx);
	$lo = $result->fetch_row();
	$id_lottox=$lo[0]+1;

	if (strlen($id_lottox)==1) $id_lottox="00$id_lottox";
	if (strlen($id_lottox)==2) $id_lottox="0$id_lottox";
	$lotto="$mese$giorno$annoatt$stab_reparto$id_lottox";

	$codice_materiale="";$qta="";$lotto_materiale="";
	$indice=0;
	

	for ($sca=0;$sca<=count($dati)-1;$sca++){
		$codice_materiale=$dati[$sca]['cod_materiale'];
		$qta=$dati[$sca]['qta_materiale'];
		$lotto_materiale=$dati[$sca]['lotto_materiale'];
		$sql="INSERT INTO materiali_impegni (`lotto`,`codice_materiale`,`qta`,`lotto_materiale`) VALUES ('$lotto','$codice_materiale','$qta','$lotto_materiale')";
		$result=$mysqli->query($sql);
		$rows['body'][$lotto]['materiali']['codice_materiale'][$indice]=$codice_materiale;
		$rows['body'][$lotto]['materiali']['qta'][$indice]=$qta;
		$rows['body'][$lotto]['materiali']['lotto_materiale'][$indice]=$lotto_materiale;
		$indice++;
	}

	
	$sql="INSERT INTO impegnolotti_steril (`lotto`,`id_lotto`,`data`,`autoclave`,`stab_reparto`,`tempo`,`temperatura`) VALUES ('$lotto',$id_lottox,'$date_next','$autoclave','$stab_reparto','$tempo','$temperatura')";
	$result=$mysqli->query($sql);
	if ($result) {
		$rows['header']['resp'][0]="OK";
		$rows['body'][$lotto]['id_lotto']=$lotto;
		$rows['body'][$lotto]['lotto']=$lotto;
		$rows['body'][$lotto]['autoclave']=$arr_info[2];
		$rows['body'][$lotto]['date_next']=$date_next;
		$rows['body'][$lotto]['stab_reparto']=$stab_reparto;
		$rows['body'][$lotto]['tempo']=$tempo;
		$rows['body'][$lotto]['temperatura']=$temperatura;
		$rows['body'][$lotto]['completo']=0;
	} else {
		$rows['header']['resp'][0]="KO";
		
	}
	
	$datx=date("Y-m-d");
	$ora = date('H:i:s', time());
	$descr_autoclave=$arr_info[0]." ".$arr_info[1]." ".$arr_info[2];
	$descr="Data operazione:$datx $ora;Descrizione operazione: Nuovo Lotto Sterilizzazione $lotto;Data impegno:$date_next;Autoclave:$descr_autoclave;Stabilimento_Reparto:$stab_reparto;Tempo:$tempo;Temperatura:$temperatura";
	$qwx = "INSERT INTO log_steril (id_lotto,operazione,descrizione,operatore,tipo,data_op) VALUES('$lotto',1,'$descr','$operatore','$tipo','$datx')";		
	
	$result=$mysqli->query($qwx);
	
	print json_encode($rows);	
	exit;
}

if ($operazione=="close_control") {
	$mysqli =new mysqli($servJ,$utJ,$passJ,$databaseJ);
	$lotto=$_POST['lotto'];
	$ids=$_POST['ids'];
	$cond="";
	$arr_id=explode(";",$ids);
	for ($sca=0;$sca<=count($arr_id)-1;$sca++) {
		$check_id=$arr_id[$sca];
		if (strlen($cond)!=0) $cond.=" or ";
		$cond.="id=$check_id ";
	}		
	$cond="($cond) ";
	$sql="SELECT COUNT(id) q
		FROM check_list_codici
		WHERE $cond and conformita=2";
	
	$result=$mysqli->query($sql);
	$res = $result->fetch_row();
	$num=$res[0];
	$conf="S";
	if ($num>0) $conf="X";
	$sql="UPDATE impegnolotti 
			SET save_quality='$conf',close_quality=1 
			WHERE DBlotto='$lotto'";
	$result=$mysqli->query($sql);
	
	$dati['header']="KO";
	if ($result) $dati['header']="OK";
	print json_encode($dati);
	exit;
	
}	

if ($operazione=="save_quality") {
	$mysqli =new mysqli($servJ,$utJ,$passJ,$databaseJ);
	$codice=$_POST['codice'];
	$lotto=$_POST['lotto'];
	$ids=$_POST['ids'];
	$sql="DELETE from check_lotti WHERE lotto='$lotto'";
	$result=$mysqli->query($sql);
	$arr_id=explode(";",$ids);
	$dati['header']="KO";
	for ($sca=0;$sca<=count($arr_id)-1;$sca++) {
		$ck=$arr_id[$sca];
		$arr=explode("|",$ck);
		$check_id=$arr[0];
		$data_check=$arr[1];
		$sql="INSERT INTO check_lotti (`lotto`,`codice`,`check_id`,`data_check`) values ('$lotto','$codice',$check_id,'$data_check')";
	
		$result=$mysqli->query($sql);
		if ($result) $dati['header']="OK";
	}

	$sql="UPDATE impegnolotti 
			SET save_quality='S'
			WHERE DBlotto='$lotto'";
	$result=$mysqli->query($sql);	

	print json_encode($dati);
	exit;
}

if ($operazione=="load_qualita") {
	$mysqli =new mysqli($servJ,$utJ,$passJ,$databaseJ);

	
	$codice=$_POST['codice'];
	$lotto=$_POST['lotto'];
	

	
	$codici_check=codici_check($codice);
	$dati=array();
	if ($codici_check['resp']=="S") {
		$arr_cod=explode(";",$codici_check['codici_check']);
		for ($sca=0;$sca<=count($arr_cod)-1;$sca++) {
			$id_ref=$arr_cod[$sca];


			$sql="SELECT c.name_check,c.lbl_check,c.conformita,c.class_required,c1.check_id,c1.data_check FROM check_list_codici c
			LEFT JOIN check_lotti c1 ON c.id=c1.check_id and c1.lotto='$lotto' 
			WHERE c.id=$id_ref";

			$result=$mysqli->query($sql);
			$res = $result->fetch_row();
			$name_check=$res[0];

			$lbl_check=$res[1];
			$conformita=$res[2];
			$class_required=$res[3];
			$check_id=$res[4];
			$data_check=$res[5];
			
			$dati[$id_ref]['check']=$name_check;
			$dati[$id_ref]['lbl_check']=$lbl_check;
			$dati[$id_ref]['conformita']=$conformita;
			$dati[$id_ref]['class_required']=$class_required;
			$dati[$id_ref]['check_id']=$check_id;
			$dati[$id_ref]['data_check']=$data_check;
		}
	}

	print json_encode($dati);
	exit;	
	
	
}

if ($operazione=="check_qualita") {
	$mysqli =new mysqli($servJ,$utJ,$passJ,$databaseJ);
	$codice=$_POST['codice'];
	$lotto=$_POST['lotto'];
	$sql="SELECT close_quality,DBverifica FROM impegnolotti WHERE DBlotto='$lotto'";
	$result=$mysqli->query($sql);
	$res = $result->fetch_row();	
	$close_quality=$res[0];
	$verifica=$res[1];
	
	$codici_check=codici_check($codice);
	if (strpos($codice,"-")>0) $codici_check="";
	$rows=array();
	$rows['resp']=$codici_check['resp'];
	$rows['codici_check']=$codici_check['codici_check'];
	$rows['close_quality']=$close_quality;
	$rows['verifica']=$verifica;
	print json_encode($rows);
	exit;
}

if ($operazione=="log_eventi") {
	$mysqli =new mysqli($servJ,$utJ,$passJ,$databaseJ);

	$id_lotto=$_POST['id_lotto'];
	$tipo=$_POST['tipo'];
	$db="log_lotti";
	if ($tipo=="steril") $db="log_steril";
	$sql="SELECT * FROM $db WHERE id_lotto='$id_lotto'";
	$result=$mysqli->query($sql);

	$rows=array();$indice=0;
	while($results = $result->fetch_array()){
		$prodotto = array_map('utf8_encode', $results);	
		$rows['body'][$indice]['operazione']=$prodotto['operazione'];
		$rows['body'][$indice]['data']=$prodotto['data'];
		$rows['body'][$indice]['operatore']=$prodotto['operatore'];
		$descrizione=$prodotto['descrizione'];
		$arr_descr=explode(";",$descrizione);
		$descr_ret="";
		for ($sca=0;$sca<=count($arr_descr)-1;$sca++) {
			$descr=$arr_descr[$sca];
			$descr_ret.="<p>$descr</p>";
		}
		$rows['body'][$indice]['descr_ret']=$descr_ret;
		$indice++;
		
	} 
	
	print json_encode($rows);
	exit;
}

if ($operazione=="salva_modifica") {
	$datx=date("Y-m-d");
	$ora = date('H:i:s', time());

	
	$codice=$_POST['E_codice'];
	$quantita=$_POST['E_quantita'];
	$protocollo=$_POST['E_protocollo'];
	$id_lotto=$_POST['E_id_lotto'];
	$lotto_post=$_POST['E_lotto'];
	$tipo=$_POST['E_tipo'];
	$campioni=$_POST['E_campioni'];
	if (strlen($campioni)==0) $campioni=0;
	
	

	$new_tipo="1";
	$char=substr($codice,0,1);
	if ($char=="0" || $char=="1" || $char=="2" || $char=="3" || $char=="4" || $char=="5" || $char=="6" || $char=="7" || $char=="8" || $char=="9") $new_tipo="2";
	
	if ($tipo!=$new_tipo) {
		$tipo_cod_old="Semilavorato";
		if ($tipo=="2") $tipo_cod_old="Prodotto finito";

		$tipo_cod_new="Semilavorato";
		if ($new_tipo=="2") $tipo_cod_new="Prodotto finito";
		
		$risp.="<h5><b>Il nuovo codice è un $tipo_cod_new mentre il codice iniziale era un $tipo_cod_old. Operazione non possibile </b><h5>";
		$rows['header']['resp']="KO";
		$rows['header']['cod_err']="2";
		$rows['header']['error']=$risp;
		print json_encode($rows);
		exit;
	}
	
	$rows=array();
	
	$mysqli =new mysqli($servJ,$utJ,$passJ,$databaseJ);	
	$qwx ="SELECT DBlotto,DBcodice,DBcontrollo,DBdata,DBtipo from $tb_lotti where DBprot='$protocollo' and DBcontrollo<>'!' and id<>$id_lotto;";
	$result=$mysqli->query($qwx);
	$res = $result->fetch_row();
	if (strlen($res[0])!=0) {
		$risp.="<h5><b>Il protocollo $protocollo risulta associato al lotto $res[0] - codice $res[1] del $res[3]</b><h5>";
		$rows['header']['resp']="KO";
		$rows['header']['cod_err']="1";
		$rows['header']['error']=$risp;
		print json_encode($rows);			
		exit;
	}

	$qwx ="SELECT lotto_m from $tb_lotti where id=$id_lotto;";
	$result=$mysqli->query($qwx);
	$res = $result->fetch_row();
	$lotto_m=$res[0];
	
	$mysqli =new mysqli($servT,$utT,$passT,$databaseT);
	$qwx="SELECT A.COD_ART codice,A.DES_ART as descrizione, B.GGSCAD as scadenza, B.GGDISP as disp,B.MARCATURA_CE, B.RANGE_TEMP, B.SIGLA AS SIGLA_CUSTOM, B.DESCRIZIONE AS DESCRIZIONE_CUSTOM from ART_ANA AS A LEFT OUTER JOIN ART_USER AS B on A.COD_ART=B.COD_ART WHERE A.COD_ART = '$codice' LIMIT 0,1";
	$result=$mysqli->query($qwx);
	$info = $result->fetch_array();
	$info = array_map('utf8_encode', $info);	
	$prodotto=$info['descrizione'];
	$exp=$info['scadenza'];
	$d_disp=$info['disp'];
	$marcatura_ce=$info['MARCATURA_CE'];
	$range_temp=$info['RANGE_TEMP'];
	$sigla_custom=$info['SIGLA_CUSTOM'];
	$descrizione_custom=$info['DESCRIZIONE_CUSTOM'];



	$mysqli =new mysqli($servJ,$utJ,$passJ,$databaseJ);
	
	//gtin Videojet
	$info_gtin=gtin($codice);
	$gtin10=$info_gtin['gtin10'];
	$gtin1=$info_gtin['gtin1'];

	//la scadenza e disponibilità devo ricolcolarla a partire dal lotto impegnato
	$giorno=substr($lotto_post,2,2);
	$mese=substr($lotto_post,0,2);
	$annoatt=substr($lotto_post,4,2);
	if ($lotto_m==1) {
		$giorno=date("d");
		$mese=date("m");
		$annoatt=date("Y");
	}
	$scad="";$data_scad1=NULL;
	if ($exp<>0 && strlen($exp)!=0) {
		$data_scad1 = date("Y-m-d",mktime(0,0,0,$mese ,$giorno+$exp ,$annoatt));	
		$scad=",DBscadenza='$data_scad1'";
	}	

	$disp="";
	if ($d_disp<>0 && strlen($d_disp)!=0) {
		$data_disp1 = date("Y-m-d",mktime(0,0,0,$mese ,$giorno+$d_disp ,$annoatt));				
		if (strlen($scad)!=0) $disp=",";
		$disp.="DBdisp='$data_disp1'";
	}


	$sigla_arr=get_string_between($prodotto,"<",">");
	$sigla=$sigla_arr['contenuto'];
	
	
	$info_regole=regole($codice);
	$DBmodelli=$info_regole['DBmodelli'];
	$DBmodelli1=$info_regole['DBmodelli1'];	

	
	$sql="UPDATE $tb_lotti set DBcodice='$codice', DBprodotto='$prodotto', DBquantita='$quantita', DBprot='$protocollo', marcatura_ce='$marcatura_ce',range_temp='$range_temp',sigla_custom='$sigla_custom',descrizione_custom='$descrizione_custom',gtin1='$gtin1',gtin10='$gtin10',sigla_interna='$sigla',DBmodelli='$DBmodelli',DBmodelli1='$DBmodelli1',campioni=$campioni $scad $disp WHERE id=$id_lotto";
	$result = $mysqli->query($sql);

	$descr="Data:$datx $ora;Codice:$codice;Descrizione:$prodotto;Scadenza:$data_scad1;Disp:$data_disp1;ID_user:$id_user;Operatore:$operatore;Qta:$quantita;Prot:$protocollo,Campioni:$campioni";
	$qwx = "INSERT INTO log_lotti (id_lotto,operazione,codice,descrizione,operatore,tipo,data_op) VALUES($id_lotto,2,'$codice','$descr','$operatore','$tipo','$datx')";		
	$result=$mysqli->query($qwx);

	
	if ($result) {
		$rows['header']['resp']="OK";
		$rows['body']['operatore']="";
		$rows['body']['descrizione']=$info['descrizione'];
		$rows['body']['scadenza']=$data_scad1;
		$rows['body']['disp']=$info['disp'];
		$rows['body']['MARCATURA_CE']=$info['MARCATURA_CE'];
		$rows['body']['RANGE_TEMP']=$info['RANGE_TEMP'];
		$rows['body']['SIGLA_CUSTOM']=$info['SIGLA_CUSTOM'];
		$rows['body']['DESCRIZIONE_CUSTOM']=$info['DESCRIZIONE_CUSTOM'];
	} else {
		$rows['header']['resp']="KO";
		
	}
	print json_encode($rows);	
	exit;
}
if ($operazione=="edit_lotto") {
	$mysqli =new mysqli($servT,$utT,$passT,$databaseT);
	$codice=$_POST['E_codice'];

	$qwx="SELECT A.COD_ART codice,A.DES_ART as descrizione, B.GGSCAD as scadenza, B.GGDISP as disp,B.MARCATURA_CE, B.RANGE_TEMP, B.SIGLA AS SIGLA_CUSTOM, B.DESCRIZIONE AS DESCRIZIONE_CUSTOM from ART_ANA AS A LEFT OUTER JOIN ART_USER AS B on A.COD_ART=B.COD_ART WHERE A.COD_ART = '$codice' LIMIT 0,1";

	$result=$mysqli->query($qwx);
	$info = $result->fetch_array();
	$row_cnt = $result->num_rows;
	$rows=array();
	$info = array_map('utf8_encode', $info);	
	$rows['header']['resp']="OK";
	$rows['header']['num_rec']=$row_cnt;
	$rows['body']['descrizione']=$info['descrizione'];
	
	$mysqli =new mysqli($servJ,$utJ,$passJ,$databaseJ);
	//Verifico se il codice deve richiedere i 'campioni'		
	$tipo_p="PF";
	if (substr($codice,0,1)=="$" || substr($codice,0,1)=="*") $tipo_p="SL";

	//Prima faccio la verifica diretta sul codice...
	$sql="SELECT id
		FROM check_codici_assoc_single
		WHERE codice='$codice' and (codici_check like '1;%' or codici_check='1') ";

	$result=$mysqli->query($sql);
	$res = $result->fetch_row();
	$num = $res[0];
	if (strlen($num)!=0) 
		$campioni="S";
	else {
		//...altrimenti tramite range di regole sui codici
		$sql="SELECT * FROM `check_codici_assoc`
			WHERE tipo_codice='$tipo_p' and (codici_check like '1;%' or codici_check='1') ";
		$result=$mysqli->query($sql);

		$codice_ref=$codice;
		if ($tipo_p=="SL") $codice_ref=substr($codice,1);
		$codice_ref=intval($codice_ref);
		$campioni="N";
		while($results = $result->fetch_array()){
			$cod_da=$results['cod_da'];
			$cod_a=$results['cod_a'];
			if ($codice_ref>=$cod_da && $codice_ref<=$cod_a) {
				$campioni="S";
				break;
			}
			
		}
	}
	if (strpos($codice,"-")>0) $campioni="N";
	$rows['body']['campioni']=$campioni;
	///////////fine verifica campioni	
	
	print json_encode($rows);	
	exit;
}

if ($operazione=="convalida") {
	$mysqli =new mysqli($servJ,$utJ,$passJ,$databaseJ);
	$id_lotto=$_POST['id_lotto'];
	
	$sql="SELECT DBcodice FROM $tb_lotti WHERE id=$id_lotto";
	$result=$mysqli->query($sql);
	$res = $result->fetch_row();
	$codice = $res[0];

	$codici_check=codici_check($codice);

	
	$tipo=$_POST['tipo'];
	$ver="N";
	if ($tipo=="1") $ver="S";
	if ($codici_check['resp']=="S") $ver="C";
	
	
	$sql="UPDATE $tb_lotti set DBverifica='$ver' where id=$id_lotto";
	$result = $mysqli->query($sql);
	if ($result) echo "OK"; else echo "KO";
	exit;
}

if ($operazione=="cancella_lotto") {
	$datx=date("Y-m-d");
	$ora = date('H:i:s', time());

	$id_lotto=$_POST['id_lotto'];
	$mysqli =new mysqli($servJ,$utJ,$passJ,$databaseJ);
	$sql="UPDATE $tb_lotti set DBcontrollo='!' WHERE id=$id_lotto";	
	$result = $mysqli->query($sql);
	$descr="Data:$datx $ora;ID_user:$id_user;Operatore:$operatore";
	$qwx = "INSERT INTO log_lotti (id_lotto,operazione,codice,descrizione,operatore,tipo,data_op) VALUES($id_lotto,3,'--','--','$operatore','--','$datx')";		
	$result=$mysqli->query($qwx);
	if ($result) echo "OK"; else echo "KO";
	exit;
}

if ($operazione=="elenco_lotti_steril") {
	$lotto=$_POST['lotto'];
	if (strlen($lotto)==0 || $lotto=="0") $lotto=NULL;
	$elenco_lotti_steril=elenco_lotti_steril($lotto);
	$elenco_lotti_steril['header']="lotti_steril";
	print json_encode($elenco_lotti_steril);
	exit;
}
if ($operazione=="elenco_lotti" || $operazione=="stampa") {
	$tipo=$_POST['tipo'];
	$tipo_view=$_POST['tipo_view'];
	$tipo_cq=$_POST['tipo_cq'];
	$da_data=$_POST['da_data'];
	$a_data=$_POST['a_data'];
	$select_ord=$_POST['select_ord'];
	$rows=array();
	$mysqli =new mysqli($servJ,$utJ,$passJ,$databaseJ);
	
	$escludi=array();
	/*
		calcolo array di esclusione.
		esempio di casistica riferita a tutti i no ricezione campioni:
		se mi limito a fare la query con c.check_id<>'1'
		ottengo tutti i lotti con check<>'1'
		avrò sì un elenco di lotti con 'NO ricezione campioni' (check_id<>1)
		ma otterrò anche un lotto che magari ha 'anche' check_id=1
		es:
		tabella check_lotti
		lotto 			check_id
		032523001 			1
		032523001 			2
		032523001 			4
		se filtro tutti i lotti con check_id<>1 ottengo comunque 
		il lotto 032523001 e poi cliccandoci sopra vedrò ricezione campioni flaggato
		
		Per risolvere mi faccio restituire tutti i lotti con Ricezione campioni e poi farò una query di esclusione dei lotti ottenuti
	*/
	if ($tipo_cq=="2") {
		$escludi=array();
		$sql="SELECT c.lotto FROM $tb_lotti t 
		INNER JOIN check_lotti c ON t.DBlotto=c.lotto
		WHERE c.check_id=1";
	
		$result=$mysqli->query($sql);
		while($results = $result->fetch_array()){
			$escludi[]=$results['lotto'];
		}		
	}

	if ($tipo_cq=="4") {
		$escludi=array();
		$sql="SELECT c.lotto FROM $tb_lotti t 
		INNER JOIN check_lotti c ON t.DBlotto=c.lotto
		WHERE (c.check_id=2 or c.check_id=3)";
	
		$result=$mysqli->query($sql);
		while($results = $result->fetch_array()){
			$escludi[]=$results['lotto'];
		}		
	}

	if ($tipo_cq=="6") {
		$escludi=array();
		$sql="SELECT c.lotto FROM $tb_lotti t 
		INNER JOIN check_lotti c ON t.DBlotto=c.lotto
		WHERE (c.check_id=4 or c.check_id=5)";
	
		$result=$mysqli->query($sql);
		while($results = $result->fetch_array()){
			$escludi[]=$results['lotto'];
		}		
	}

	if ($tipo_cq=="8") {
		$escludi=array();
		$sql="SELECT c.lotto FROM $tb_lotti t 
		INNER JOIN check_lotti c ON t.DBlotto=c.lotto
		WHERE c.check_id=6";
	
		$result=$mysqli->query($sql);
		while($results = $result->fetch_array()){
			$escludi[]=$results['lotto'];
		}		
	}

	$escl=implode(",",$escludi);
	if (strlen($escl)==0) $escl="0";
	
	$cond="1";
	if ($operazione!="stampa") $cond="t.DBcontrollo<>'!'";
	
	if (strlen($tipo)!=0 && $tipo!="0") $cond.=" and t.DBtipo='$tipo'";
	if ($tipo_view=="1") $cond.=" and (t.DBverifica='N' or t.DBverifica is null)";
	if ($tipo_view=="2") $cond.=" and (t.DBverifica='S' or t.DBverifica='C')";
	if ($tipo_view=="3") $cond.=" and t.save_quality='X'";
	
	
	
	if ($tipo_cq=="1") $cond.=" and c.check_id='1'";
	if ($tipo_cq=="2") $cond.=" and t.DBlotto NOT in ($escl) and  t.controlli_cq like '%|1|%'";
	
	if ($tipo_cq=="3") $cond.=" and (c.check_id='2' or c.check_id='3')";
	if ($tipo_cq=="4") $cond.=" and t.DBlotto NOT in ($escl) and (t.controlli_cq like '%|2|%' or t.controlli_cq like '%|3|%' )";

	if ($tipo_cq=="5") $cond.=" and (c.check_id='4' or c.check_id='5')";
	if ($tipo_cq=="6") $cond.=" and t.DBlotto NOT in ($escl) and (t.controlli_cq like '%|4|%' or t.controlli_cq like '%|5|%' )";


	if ($tipo_cq=="7") $cond.=" and c.check_id='6'";
	if ($tipo_cq=="8") $cond.=" and t.DBlotto NOT in ($escl) and t.controlli_cq like '%|6|%'";	


	$cond_data="";
	if (strlen($da_data)==10) $cond_data=" and DBdata>='$da_data'";
	if (strlen($a_data)==10) $cond_data=" and DBdata<='$a_data'";
	if (strlen($da_data)==10 && strlen($a_data)==10) $cond_data=" and (DBdata>='$da_data' and DBdata<='$a_data')";
	$cond.=$cond_data;


	
	$sql="SELECT COUNT(distinct(DBlotto)) as total FROM $tb_lotti t 
	LEFT OUTER JOIN check_lotti c ON t.DBlotto=c.lotto
	WHERE $cond";
	
	$total_rows=0;
	if ($result = $mysqli->query($sql)) {
		$res = $result->fetch_row();
		$total_rows = $res[0];
	}	
	$rows['header']['total_rows']=$total_rows;
	$pageno=$_POST['pageno'];
	if	(strlen($pageno)==0) $pageno=1;
	$no_of_records_per_page = 20;
	$offset = ($pageno-1) * $no_of_records_per_page;
	$total_pages = ceil($total_rows / $no_of_records_per_page);
	$limit="LIMIT $offset, $no_of_records_per_page";
	if (strlen($da_data)!=0 && strlen($a_data)!=0) {

		$datetime1 = date_create($da_data);
		$datetime2 = date_create($a_data);
	   
		$interval = date_diff($datetime1, $datetime2);
		$giorni=intval($interval->format("%R%a"));
		$rows['header']['interval']=$giorni;
		if ($giorni<=5 && $giorni>=0) {
			
			$limit="";
			$total_pages=1;
		}
	}	

	$rows['header']['total_pages']=$total_pages;

	$ord="";
	if ($select_ord=="2") $ord="desc";
	if ($operazione=="stampa") $limit="";
	$sql="SELECT t.id,t.DBtipo,t.DBdata,t.DBprot,t.DBscadenza,t.DBlotto,t.DBcodice,t.DBprodotto,t.DBquantita,t.DBoperatore,t.DBverifica,t.DBcontrollo,t.campioni,t.user_id,t.save_quality,t.close_quality FROM $tb_lotti t 
	LEFT OUTER JOIN check_lotti c ON t.DBlotto=c.lotto
	WHERE $cond 
	GROUP BY t.DBlotto
	ORDER BY id $ord 
	$limit";

	
	
	
	$result=$mysqli->query($sql);
	if ($operazione=="stampa") {
		include("report.php");
		exit;
	}
	while($results = $result->fetch_array()){
		$prodotto = array_map('utf8_encode', $results);	
		$rows['body'][]=$prodotto;
		//here goes the data
	} 

	$view=null;
	
	$view.="<ul class='pagination mt-3'>";
		
		

			$pages  = $total_pages;
			
			$view.="<li class='page-link page'>Pagine </li>";
			
			if($pageno !=1)	
				$view.='<li class="page-item"><a href="javascript:void(0);" class="page-link page" onclick="elenco_lotti(1)" >&#8810;</a></li><li class="page-item"><a href="javascript:void(0);" class="page-link page" onclick="elenco_lotti('.($pageno-1).')">&#60;</a></li>';
			
			
			if ($pageno<4){
				$start 	= 1;
				$end 	= 5;
			}
			else{
				$start 	= ($pageno-2);
				$end 	= ($pageno+2);
			}
			
			$endpage = ($pages-3);
			if ($pageno>$endpage){
				$start 	= ($pages-4);
			}
			for($i=$start; $i<=$end; $i++){
				if($i<1) continue;
				if($i>$pages) break;
				if($pageno == $i)
				{
					$view.='<li class="page-item active">';
						$view.='<span class="page-link">'.$i.'<span class="sr-only">(current)</span></span>';
					$view.='</li>';
				}
				else
				{				
					$view.='<li class="page-item"><a href="javascript:void(0);" class="page-link page" onclick="elenco_lotti('.$i.')" >'.$i.'</a></li>';
				}
			}
			
			
			if($pageno < $pages)
				$view.='<li class="page-item"><a href="javascript:void(0);" class="page-link page" onclick="elenco_lotti('.($pageno+1).')" >></a></li><li href="javascript:void(0);" class="page-item"><a href="javascript:void(0);" class="page-link page" onclick="elenco_lotti('.$pages.')" >&#8811;</a>';

			
				
	$view.="</ul>";
	
	$rows['view']=$view;


	print json_encode($rows);
	exit;
	
	
    $view.="<ul class='pagination'>";
        $view.="<li><a href='javascript:void(0)' onclick=\"elenco_lotti(1)\">First</a></li>";
		$dis="";
		if($pageno<=1) $dis="disabled";
        $view.="<li class='$dis'>";
			if($pageno <= 1) $page=1; else $page=$pageno-1;
            $view.="<a href='javascript:void(0)' onclick=\"elenco_lotti($page)\">Prev</a>";
        $view.="</li>";
		$dis="";
		if($pageno >= $total_pages) $dis="disabled";
        $view.="<li class='$dis'>";
		if($pageno >= $total_pages) $page=1; else  $page=$pageno+1;
           $view.="<a href='javascript:void(0)' onclick=\"elenco_lotti($page)\">Next</a>";
        $view.="</li>";
        $view.="<li><a href='javascript:void(0)' onclick=\"elenco_lotti($total_pages)\">Last</a></li>";
    $view.="</ul>";	
	$rows['view']=$view;
	
	
	print json_encode($rows);
	exit;

	
}

if ($operazione=="cercalotto") {
	$lotto_rapido=$_POST['lotto_rapido'];
	$lotto_rapido=addslashes($lotto_rapido);
	$select_tipo_ric=$_POST['select_tipo_ric'];
	if (strlen($lotto_rapido)==0) $lotto_rapido="??---///";
	$cond="DBlotto='$lotto_rapido'";
	if ($select_tipo_ric=="2") $cond="DBcodice='$lotto_rapido'";
	if ($select_tipo_ric=="3") $cond="DBprodotto like '%$lotto_rapido%'";
	if ($select_tipo_ric=="4") $cond="DBprot='$lotto_rapido'";
	if ($select_tipo_ric=="5") $cond="DBdata='$lotto_rapido'";

	$mysqli =new mysqli($servJ,$utJ,$passJ,$databaseJ);
	
	
	$sql="SELECT id,DBcontrollo,DBtipo,DBlotto lotto, DBprot, DBcodice codice, DBprodotto prodotto, DBscadenza scadenza, DBdisp disp,DBquantita quantita,DBverifica verifica,campioni,DBoperatore,user_id,save_quality,close_quality FROM $tb_lotti WHERE $cond order by id desc LIMIT 0,150";
	$result=$mysqli->query($sql);
	$res=array();	
	while($row = $result->fetch_array()){
		$res['dati'][]=$row;
	}	
	if (count($res)==0) {
		$res=elenco_lotti_steril($lotto_rapido);
		if (count($res)==0) {
			echo "NO";
			exit;
		} else {
			$res['header']="lotti_steril";
	print json_encode($res);
	exit;
		
		}	
		
	} else $res['header']="lotti_standard";
	print json_encode($res);
	exit;
}

if ($operazione=="save_multi") {

	
	$date_next=$_POST['date_next'];
	$ora = date('H:i:s', time());
	$datx=$date_next;
	$giorno=substr($datx,8,2);
	$mese=substr($datx,5,2);
	$annoatt=substr($datx,2,2);
	$operatore=$_SESSION['operatore'];
	$id_user=$_SESSION['id_user'];		

	$mysqli =new mysqli($servJ,$utJ,$passJ,$databaseJ);
	
	$resp=array();
	$resp['header']['error']=false;
	$resp['header']['msg_error']="";
	
	$tipo=$_POST['tipo'];
	$dati=$_POST['dati'];
	$alert=0;$risp="";
	//prima scansione dei codici per verificare eventuale esistenza di protocolli
	for ($sca=1;$sca<=count($dati);$sca++){
		$prot=$dati[$sca]['pro'];
		$qwx ="SELECT DBlotto,DBcodice,DBcontrollo,DBdata from $tb_lotti where DBprot='$prot' and DBcontrollo<>'!';";
		$result=$mysqli->query($qwx);
		$res = $result->fetch_row();
		if (strlen($res[0])!=0) {
			$alert=1;
			$risp.="<h5><b>Il protocollo $prot risulta associato al lotto $res[0] - codice $res[1] del $res[3]</b><h5>";
		}
	}	
	if ($alert==1) {
		$resp['header']['error']="PROT_IN_USE";
		$resp['header']['msg_error']=$risp;
		print json_encode($resp);
		exit;
	}	
	for ($sca=1;$sca<=count($dati);$sca++){
		$quantita=$dati[$sca]['qta'];
		$prot=$dati[$sca]['pro'];
		$controllo=$dati[$sca]['alternativa'];
		$codice=$dati[$sca]['codice'];
		$prodotto=$dati[$sca]['prodotto'];
		$scad=$dati[$sca]['scadenza'];
		$disp=$dati[$sca]['disp'];
		$marcatura_ce=$dati[$sca]['marcatura_ce'];
		$range_temp=$dati[$sca]['range_temp'];
		$sigla_custom=$dati[$sca]['sigla_custom'];
		$descrizione_custom=$dati[$sca]['descrizione_custom'];
		$campioni=$dati[$sca]['campioni'];
		if (strlen($campioni)==0) $campioni= null;


		$info_gtin=gtin($codice);
		$gtin10=$info_gtin['gtin10'];
		$gtin1=$info_gtin['gtin1'];


		//DATI DA VALORIZZARE CORRETTAMENTE!!!!
		
		$cq_pf="";

		$lotto_m=$_POST['lotto_m'];
		$fl_lm=0;
		if (strlen($lotto_m)!=0) $fl_lm=1;		
		///////////////////////////////////////

		$qwx ="SELECT id_lotto from $tb_lotti where DBdata='$datx' and DBtipo='$tipo' and lotto_m<>1 order by id_lotto desc;";
		$result=$mysqli->query($qwx);
		$lo = $result->fetch_row();

		$id_lotto=$lo[0]+1+$off;
		if ($tipo=='2') $id_lottox=$id_lotto+500; else $id_lottox=$id_lotto;

		if (strlen($id_lottox)==1) $id_lottox="00$id_lottox";
		if (strlen($id_lottox)==2) $id_lottox="0$id_lottox";
		$lotto="$mese$giorno$annoatt$id_lottox";
		if (strlen($lotto_m)!=0) $lotto=$lotto_m;
						
		if ($scad<>0) 
			$data_scad1 = date("Y-m-d",mktime(0,0,0,$mese ,$giorno+$scad ,$annoatt));	
		else 
			$data_scad1="1980-01-01";
		
		if ($disp<>0) 
			$data_disp1 = date("Y-m-d",mktime(0,0,0,$mese ,$giorno+$disp ,$annoatt));	
		else 
			$data_disp1="1980-01-01";

		
	
		$sigla_arr=get_string_between($prodotto,"<",">");
		$sigla=$sigla_arr['contenuto'];
		
		
		$info_regole=regole($codice);
		

		$ti="PF";
		if (substr($codice,0,1)=="$" || substr($codice,0,1)=="*") $ti="SL";

		//Prima faccio la verifica diretta sul codice...
		$sql="SELECT codici_check
			FROM check_codici_assoc_single
			WHERE codice='$codice' ";

		$result=$mysqli->query($sql);
		$res = $result->fetch_row();
		$codici_check = $res[0];
	
		if ($ti=="PF") {
			if (substr($codice,0,2)=="75") $codici_check="6;7;8";
			else $codici_check="6";
		}	
		else {
			if (strlen($codici_check)==0) {
				//...altrimenti tramite range di regole sui codici
				$codice_num=intval(substr($codice,1));
				$sql="SELECT * FROM check_codici_assoc WHERE tipo_codice='$ti'";
				$result=$mysqli->query($sql);
				$codici_check="";
				
				while($results = $result->fetch_array()){
					$cod_da=$results['cod_da'];
					$cod_a=$results['cod_a'];
					$codici_check=$results['codici_check'];
					if ($codice_num>=$cod_da && $codice_num<=$cod_a) {				
						break;
					}
				} 
			}	
		}
		if (strpos($codice,"-")>0) $codici_check="";
		else {
			$arrc=explode(";",$codici_check);
			$codici_check="";
			for ($kx=0;$kx<=count($arrc)-1;$kx++) {
				$elem=$arrc[$kx];
				if (strlen($codici_check)>0) $codici_check.=";";
				$codici_check.="|$elem|";
			}
		}
		
		$DBmodelli=$info_regole['DBmodelli'];
		$DBmodelli1=$info_regole['DBmodelli1'];

		$qwx="INSERT INTO $tb_lotti(DBtipo,id_lotto, DBlotto, DBcodice, DBprodotto, DBquantita, DBprot, DBcontrollo, DBcq_pf, DBoperatore, DBscadenza, DBdisp, user_id, DBdata, lotto_m, marcatura_ce, range_temp, sigla_custom, descrizione_custom, campioni, gtin1, gtin10, sigla_interna, DBmodelli, DBmodelli1,controlli_cq) VALUES ('$tipo', $id_lotto, '$lotto', '$codice', '$prodotto', '$quantita', '$prot', '$controllo', '$cq_pf', '$operatore', '$data_scad1', '$data_disp1', $id_user, '$datx', $fl_lm, '$marcatura_ce', '$range_temp', '$sigla_custom','$descrizione_custom',  NULLIF('$campioni', ''), '$gtin1', '$gtin10','$sigla', '$DBmodelli', '$DBmodelli1','$codici_check')";
		

		$result=$mysqli->query($qwx);		
		$id_lotto = $mysqli->insert_id;
		
		$manuale="";
		if (strlen($lotto_m)!=0) $manuale="(Lotto manuale)";
		$descr="Data:$datx $ora;Codice:$codice $manuale;Descrizione:$prodotto;Scadenza:$data_scad1;Disp:$data_disp1;ID_user:$id_user;Qta:$quantita;Protocollo:$prot;Campioni:$campioni";
		$qwx = "INSERT INTO log_lotti (id_lotto,operazione,codice,descrizione,operatore,tipo,data_op) VALUES($id_lotto,1,'$codice','$descr','$operatore','$tipo','$datx')";		
		$result=$mysqli->query($qwx);
		
		if ($data_scad2=="1980-01-01") $data_scad2="--";
		if ($data_disp2=="1980-01-01") $data_disp2="--";
		$resp['infolotti'][$lotto]['operatore']=$operatore;
		$resp['infolotti'][$lotto]['user_id']=$id_user;
		$resp['infolotti'][$lotto]['id']=$id_lotto;
		$resp['infolotti'][$lotto]['alternativa']=$controllo;
		$resp['infolotti'][$lotto]['codice']=$codice;
		$resp['infolotti'][$lotto]['prodotto']=$prodotto;
		$resp['infolotti'][$lotto]['scadenza']=$data_scad1;
		$resp['infolotti'][$lotto]['disp']=$data_disp1;
		$resp['infolotti'][$lotto]['DBprot']=$prot;
		$resp['infolotti'][$lotto]['DBquantita']=$quantita;
		$resp['infolotti'][$lotto]['campioni']=$campioni;
	}	
	

	print json_encode($resp);
	exit;
}

if ($operazione=="load_codici") {
		$codice=$_POST['codice'];
		$tipo_p="PF";
		if (substr($codice,0,1)=="$" || substr($codice,0,1)=="*") $tipo_p="SL";

		$mysqli =new mysqli($servJ,$utJ,$passJ,$databaseJ);
		$lottom="";
		if ($codice=="$58001" || $codice=="$58002" || $codice=="$58003") {
			$sql="SELECT DBlotto FROM `impegnolotti` 
					WHERE DBlotto like '9114%'
					ORDER BY id desc 
					LIMIT 0,1";

			$rx=$mysqli->query($sql);
			$inf = $rx->fetch_array();
			$inf = array_map('utf8_encode', $inf);
			$lottom=$inf['DBlotto'];
			if (strlen($lottom)==0) $lottom="911400000";
			$lottom=intval($lottom)+1;
		}
		
		//Verifico se il codice deve richiedere i 'campioni'		
		
		//Prima faccio la verifica diretta sul codice...
		$sql="SELECT id
			FROM check_codici_assoc_single
			WHERE codice='$codice' and (codici_check like '1;%' or codici_check='1') ";

		$result=$mysqli->query($sql);
		$res = $result->fetch_row();
		$num = $res[0];
		
		if (strlen($num)!=0) 
			$campioni="S";
		else {
			//...altrimenti tramite range di regole sui codici
			$sql="SELECT * FROM `check_codici_assoc`
				WHERE tipo_codice='$tipo_p' and (codici_check like '1;%' or codici_check='1') ";
			$result=$mysqli->query($sql);
			
			$codice_ref=$codice;
			if ($tipo_p=="SL") $codice_ref=substr($codice,1);
			$codice_ref=intval($codice_ref);
			$campioni="N";
			while($results = $result->fetch_array()){
				$cod_da=$results['cod_da'];
				$cod_a=$results['cod_a'];
				if ($codice_ref>=$cod_da && $codice_ref<=$cod_a) {
					$campioni="S";
					break;
				}
				
			}
		}
		if (strpos($codice,"-")>0) $campioni="N";
		
		///////////fine verifica campioni
	

		$mysqli =new mysqli($servT,$utT,$passT,$databaseT);
		
		
		$sql="SELECT COUNT(A.COD_ART) as total from ART_ANA AS A WHERE A.COD_ART like '$codice-%'";
		$num=0;
		if ($result = $mysqli->query($sql)) {
			$res = $result->fetch_row();
			$num = $res[0];
		}	

		$ordby="ORDER BY A.COD_ART";
		$codice_fam=$codice;
		if ($codice=="$78630") {
			$codice_fam="$78618";
			$num=10;
			$ordby="ORDER by 
				CASE ucase(A.COD_ART)
				WHEN '$78630' then 1
				END desc, A.COD_ART";
		}	

		$oper="A.COD_ART = '$codice'";
		if ($num>1) $oper="A.COD_ART = '$codice' or A.COD_ART like '$codice_fam-%'";
		
		
		//29.06.2022 - modifica da Fernando:
		/*
			L'impegno lotti deve associare i lotti "a cascata" dal $7xxxx-01 a tutti gli altri.
			L'operatore impegnerà quindi il cartellino $7xxxx-01 e dovrà ottenere a cascata i lotti di tutti gli altri cartellini del sistema in questione.
			In pratica la funzionalità resta invariata, deve solo essere scalata dal $7xxxx (padre) al $7xxxx-01 (primo figlio).
		*/
		
		if (substr($codice,0,3)=="$71" || substr($codice,0,3)=="$72" || substr($codice,0,3)=="$73" || substr($codice,0,3)=="$74" || substr($codice,0,3)=="$76") {
			
			if (strpos($codice,"-01")>0) {
				$codice_fam=str_replace("-01","",$codice);
				$oper="A.COD_ART = '$codice' or A.COD_ART like '$codice_fam-%'";
			}	
			else $oper="A.COD_ART = '$codice'";
		}
				



		$qwx="SELECT A.COD_ART codice,A.DES_ART as descrizione, B.GGSCAD as scadenza, B.GGDISP as disp,B.MARCATURA_CE, B.RANGE_TEMP, B.SIGLA AS SIGLA_CUSTOM, B.DESCRIZIONE AS DESCRIZIONE_CUSTOM from ART_ANA AS A LEFT OUTER JOIN ART_USER AS B on A.COD_ART=B.COD_ART WHERE $oper $ordby";
		$campioni_back=$campioni;
		
		$rows=array();
		$result=$mysqli->query($qwx);
		while($results = $result->fetch_array()){
			if ($results['codice']=="$92180-01A") continue;
			$results['lottom']=$lottom;	
			if (strpos($results['codice'],"-")>0) $campioni="N";
			else $campioni=$campioni_back;
			
			$results['campioni']=$campioni;
			$prodotto = array_map('utf8_encode', $results);	
			$rows[]=$prodotto;
		} 
		
			
		
		print json_encode($rows);
		exit;
	
}


function conv_char($string){

	 $string=iconv('ISO-8859-1', 'UTF-8', $string);
	 return $string;
}

function get_string_between($string, $start, $end){   
    $ini = strpos($string, $start);
    
	$arr['start']=$ini;
	if ($ini===false) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
	$arr['contenuto']=substr($string, $ini, $len);
	$arr['end']=$len+strlen($end)+strlen($start);
	return $arr;
}

function regole($codice) {
	
	
	$DBmodelli=null;$DBmodelli1=null;
	if ((substr($codice,0,3)=="$10" || substr($codice,0,3)=="$11" || substr($codice,0,3)=="$12" || substr($codice,0,3)=="$13" || substr($codice,0,3)=="$14" || substr($codice,0,3)=="$18") && strlen($codice)<=9) {
		$DBmodelli=$codice;
		$DBmodelli=substr($DBmodelli,1).".ciff";$DBmodelli=addslashes($DBmodelli);
		$DBmodelli1="piastre.ciff";
	}	

	if (substr($codice,0,3)=="$15" && strlen($codice)==6) $DBmodelli1="rodac.ciff";
	if (substr($codice,0,3)=="$16" && strlen($codice)==6) $DBmodelli1="p60.ciff";
	if (substr($codice,0,3)=="$92" && strlen($codice)==6) $DBmodelli1="mic.ciff";
	
	//mod. 22.12.2020
	if ((substr($codice,0,3)=="$90" || substr($codice,0,3)=="$91" || substr($codice,0,3)=="$92" || substr($codice,0,3)=="$93") && strlen($codice)==5) {
		$DBmodelli=str_replace("$","",$codice);
		$DBmodelli.=".ciff";
		$DBmodelli1="ant.ciff";
	}
	if (substr($codice,0,2)=="$5" && (strlen($codice)>=6 && strlen($codice)<=8)) $DBmodelli1="slide.ciff";

	if ($codice=="$11053" || $codice=="$11210/o"  || $codice=="$11210/O" || $codice=="$15335P" || $codice=="$15342P" || $codice=="$15357P" || $codice=="$15369P" || $codice=="$10128B") $DBmodelli1="piastre_nl.ciff";


	if (substr($codice,0,2)=="$2" && strlen($codice)==6) $DBmodelli1="provette.ciff";
	if (substr($codice,0,2)=="$3" && strlen($codice)==6) $DBmodelli1="provette.ciff";
	if (substr($codice,0,2)=="$2" && strlen($codice)==7) $DBmodelli1="provette.ciff";
	if (substr($codice,0,2)=="$2" && strlen($codice)==8 && substr( $codice, 6, 1 ) == "/") $DBmodelli1="provette.ciff";

	
	
	if ($codice == "$35000" || $codice == "$34070" || $codice == "$34071" || $codice == "$34072" || $codice == "$34073" || $codice == "$34074" || $codice == "$34075" || $codice == "$34076" || $codice == "$34078M" || $codice == "$34080/O" || $codice == "$34094") $DBmodelli1="provette.ciff";
	


	if ($codice == "$20075" || $codice == "$20077" || $codice == "$20081" || $codice == "$20084" || $codice == "$20089" || $codice == "$20092" || $codice == "$20095D" || $codice == "$20095K" || $codice == "$20095L" || $codice == "$20095M" || $codice == "$20121" || $codice == "$20123" || $codice == "$20141" || $codice == "$20156" || $codice == "$20158C" || $codice == "$20158F" || $codice == "$20195" || $codice == "$20199" || $codice == "$20200" || $codice == "$20200M" || $codice == "$20202" || $codice == "$20341" || $codice == "$21113P" || $codice == "$27500" || $codice == "$27501" || $codice == "$27502" || $codice == "$27503" || $codice == "$80351" || $codice == "$27504" || $codice == "$630600" || $codice == "$80355" || $codice == "$80356") $DBmodelli1="provette_nl.ciff";


	if (
	(strlen( $codice ) ==4 && substr( $codice, 0, 1 ) == "$" && substr( $codice, 1, 1 ) == "2") || 
	(strlen( $codice ) ==4 && substr( $codice, 0, 1 ) == "$" && substr( $codice, 1, 1 ) == "3") ||
	(strlen( $codice ) ==6 && substr( $codice, 0, 1 ) == "$" && substr( $codice, 1, 2 ) == "35") ||
	(strlen( $codice ) ==8 && substr( $codice, 0, 1 ) == "$" && substr( $codice, 1, 2 ) == "36" && substr( $codice, 6, 1 ) == "/") ||
	(strlen( $codice ) ==6 && substr( $codice, 0, 1 ) == "$" && substr( $codice, 1, 2 ) == "37") ||
	($codice == "$27001" || $codice == "$27002" || $codice == "$20079" || $codice == "$20095")
	) $DBmodelli1="null";


	//12.05.2022 regola migliorativa per tutti i $34...$35...$36..$37 --->provette.ciff
	if (substr($codice,0,3)=="$34" || substr($codice,0,3)=="$35" || substr($codice,0,3)=="$36" || substr($codice,0,3)=="$37") $DBmodelli1="provette.ciff";
	

	if (substr($codice,0,3)=="$15" && strlen($codice)==6) $DBmodelli="clean_ls.ciff";
	if (substr($codice,0,3)=="$15" && strlen($codice)==7) $DBmodelli="clean_ls.ciff";
	if (substr($codice,0,3)=="$15" && strlen($codice)==8) $DBmodelli="clean_ls.ciff";
	if (substr($codice,0,3)=="$16" && strlen($codice)==6) $DBmodelli="clean_ls.ciff";
	if (substr($codice,0,3)=="$16" && strlen($codice)==7) $DBmodelli="clean_ls.ciff";
	if (substr($codice,0,3)=="$16" && strlen($codice)==8) $DBmodelli="clean_ls.ciff";
	if (substr($codice,0,3)=="$17" && strlen($codice)==6) $DBmodelli="clean_ls.ciff";
	if (substr($codice,0,3)=="$17" && strlen($codice)==7) $DBmodelli="clean_ls.ciff";
	if (substr($codice,0,3)=="$17" && strlen($codice)==8) $DBmodelli="clean_ls.ciff";

	if ($codice == "$16351MOD") $DBmodelli="clean_ls.ciff";	
	if ($codice == "$162") $DBmodelli=null;	
	if ($codice == "$87500" || $codice == "$87505" || $codice == "$87507" || $codice == "$87513") $DBmodelli="edm_ls.ciff";	
	if ($codice == "$10037S") {$DBmodelli="10037S.ciff";$DBmodelli1="piastre.ciff";}

	if (substr($codice,0,2)=="$7" && strlen($codice)==6) $DBmodelli="sistemib.ciff";
	if (substr($codice,0,3)=="$82" && strlen($codice)==6) $DBmodelli="provette.ciff";


	if ( strlen($codice)==6 && substr($codice, 0, 3 ) == "$91" && (  substr($codice,5,1)=="0" ||  substr($codice,5,1)=="1" ||  substr($codice,5,1)=="2" ||  substr($codice,5,1)=="3" ||  substr($codice,5,1)=="4" ||  substr($codice,5,1)=="5" ||  substr($codice,5,1)=="6" ||  substr($codice,5,1)=="7" ||  substr($codice,5,1)=="8" ||  substr($codice,5,1)=="9" )) $DBmodelli1="provette.ciff";
	
	if ( strlen( $codice ) ==6 && substr( $codice, 0, 3 ) == "$91" && (  substr($codice,5,1)=="0" ||  substr($codice,5,1)=="1" ||  substr($codice,5,1)=="2" ||  substr($codice,5,1)=="3" ||  substr($codice,5,1)=="4" ||  substr($codice,5,1)=="5" ||  substr($codice,5,1)=="6" ||  substr($codice,5,1)=="7" ||  substr($codice,5,1)=="8" ||  substr($codice,5,1)=="9" )) $DBmodelli1="provette.ciff";

	if (strlen( $codice ) ==6 && substr( $codice, 0, 1 ) =="$" && substr( $codice, 1, 2 ) == "92") {
		$DBmodelli=$codice.".ciff";
		$DBmodelli=str_replace("$","",$DBmodelli);
	}	
	
	if ($codice == "$630601") $DBmodelli1="630601.ciff";	

	if (strlen( $codice ) ==7 && substr( $codice, 0, 3) =="$88") $DBmodelli1="provettecf.ciff";
	if (strlen( $codice ) ==6 && substr( $codice, 0, 3) =="$88") $DBmodelli1="provettecf.ciff";

	
	if (strlen( $codice )== 6 && substr( $codice, 0, 1 ) == "$" && substr( $codice, 1, 1 ) == "8" && substr( $codice, 2, 1 ) == "0" && substr( $codice, 3, 1 ) == "4") $DBmodelli1="provettecf.ciff";

	if ($codice == "$080275A") $DBmodelli1="provettecf.ciff";
	
	if ($codice == "$15335P") $DBmodelli="15335P.ciff";
	if ($codice == "$15342P") $DBmodelli="15342P.ciff";
	if ($codice == "$15357P") $DBmodelli="15357P.ciff";
	if ($codice == "$15369P") $DBmodelli="15369P.ciff";




	if (substr($codice,0,2)=="62" && strlen($codice)==6) $DBmodelli1="disidratati100.ciff";
	if (substr($codice,0,2)=="65" && strlen($codice)==6) $DBmodelli1="disidratati100.ciff";

	if (substr($codice,0,2)=="61" && strlen($codice)==6) $DBmodelli1="disidratati500.ciff";
	if (substr($codice,0,2)=="64" && strlen($codice)==6) $DBmodelli1="disidratati500.ciff";


	if (substr($codice,0,3)=="$06" && strlen($codice)==8 && substr($codice,7,1)=="L") $DBmodelli=substr($codice,1).".ciff";


	if (substr($codice,0,3)=="$06" && strlen($codice)==8 && substr($codice,7,1)=="L") $DBmodelli=substr($codice,1).".ciff";

	if ($codice=="$061999L/1") $DBmodelli="061999-1.ciff";
	if ($codice=="$061999L/2") $DBmodelli="061999-2.ciff";
	if ($codice=="$061999L/3") $DBmodelli="061999-3.ciff";
	if ($codice=="$061999L/4") $DBmodelli="061999-4.ciff";


	if (substr($codice,0,2)=="$7" && strlen($codice)==6) $DBmodelli=$codice.".ciff";


	//RICORDARSI DI VALORIZZARE CORRETTAMENTE ALCUNI DATI CHE PROVENGONO DAL $_POST CHE ORA HO INIZIALIZZATO MANUALMENTE (VEDI IN $operazione==save_multi)


	$DBmodelli=str_replace("/","-",$DBmodelli);

	$info_regole=array();
	$info_regole['DBmodelli']=$DBmodelli;
	$info_regole['DBmodelli1']=$DBmodelli1;
	return $info_regole;
}

function gtin($codice) {
	global $servJ,$utJ,$passJ,$databaseJ;

	$mysqli =new mysqli($servJ,$utJ,$passJ,$databaseJ);
	//assegnazione codici GTIN x Videojet
	$codice=addslashes($codice);
	$gtin1="";$gtin10="";
	$sql ="SELECT * from gtin WHERE codice='$codice' LIMIT 0,2;";
	$result=$mysqli->query($sql);
	while($row = $result->fetch_array()){
		$tipo_gtin=$row['tipo'];	
		$gtin=$row['gtin'];
		if ($tipo_gtin=="gtin10") $gtin10=$gtin;
		if ($tipo_gtin=="gtin1") $gtin1=$gtin;
	}	
	$arr=array();
	$arr['gtin10']=$gtin10;
	$arr['gtin1']=$gtin1;
	return $arr;
}


function elenco_lotti_steril($lotto_rapido=NULL) {
		$cond="I.lotto='$lotto_rapido'";
		if ($lotto_rapido==NULL) $cond="1";
		$n_rec_inpage=$_POST['n_rec_inpage'];
		$stato_scheda=$_POST['stato_scheda'];
		$filtro_autoclave=$_POST['filtro_autoclave'];
		$da_data_s=$_POST['da_data_s'];
		$a_data_s=$_POST['a_data_s'];
	
		$cond_data="";
		if (strlen($da_data_s)==10) $cond_data=" and I.data>='$da_data_s'";
		if (strlen($a_data_s)==10) $cond_data=" and I.data<='$a_data_s'";
		if (strlen($da_data_s)==10 && strlen($a_data_s)==10) $cond_data=" and (I.data>='$da_data_s' and I.data<='$a_data_s')";

		global $servJ,$utJ,$passJ,$databaseJ;
		
		$limit="";
		if (strlen($n_rec_inpage)==0) $n_rec_inpage=50;
		if (strlen($cond_data)==0) $limit=" LIMIT 0,$n_rec_inpage";
		if ($n_rec_inpage=="1") $limit="";
		if (strlen($stato_scheda)==0) $stato_scheda=100;
		if ($stato_scheda!=100) $cond.=" and I.completo='$stato_scheda' ";
		if (strlen($filtro_autoclave)!=0 && $filtro_autoclave!="0") {
			$cond.=" and autoclave='$filtro_autoclave' ";
		}
		
		$mysqli =new mysqli($servJ,$utJ,$passJ,$databaseJ);
		/*
		$sql="SELECT I.*,M.codice_materiale,M.qta,M.lotto_materiale FROM impegnolotti_steril I
		INNER JOIN materiali_impegni M ON I.lotto=M.lotto
		WHERE $cond $cond_data
		GROUP BY I.lotto
		ORDER BY I.data desc,I.lotto
		$limit";
		*/
		//GROUP BY M.codice_materiale
		
		$sql="SELECT I.*,M.codice_materiale,M.qta,M.lotto_materiale,S.descrizione FROM impegnolotti_steril I
		INNER JOIN materiali_impegni M ON I.lotto=M.lotto
		LEFT JOIN materiali_steril S ON M.codice_materiale=S.codice
		WHERE $cond $cond_data
		GROUP BY I.lotto,M.id
		ORDER BY I.data desc,I.lotto
		$limit";

		$result=$mysqli->query($sql);
		$res=array();
		
		/* esempio risposta multipla (richiesta di tutto l'archivio)	{"body":{"030320S125001":{"lotto":"030320S125001","autoclave":"AN03","date_next":"2020-03-03","stab_reparto":"S125","tempo":"10","temperatura":"20","materiali":{"codice_materiale":["001","002"],"qta":["1","3"],"lotto_materiale":["2","4"]}},"030320S125002":{"lotto":"030320S125002","autoclave":"AN04","date_next":"2020-03-03","stab_reparto":"S125","tempo":"A","temperatura":"B","materiali":{"codice_materiale":{"2":"001","3":"002"},"qta":{"2":"100","3":"300"},"lotto_materiale":{"2":"200","3":"400"}}}},"header":"lotti_steril"}	*/
		
		$codice_materiale="";$qta="";$lotto_materiale="";
		$indice=0;$ind=0;
		$lotto_old="?";
		$rows=array();

		while($row = $result->fetch_array()){
			$row = array_map('utf8_encode', $row);	
			$lotto=$row['lotto'];
			if ($lotto_old!=$lotto) {
				$indice=0;
				$lotto_old=$lotto;
			}	
			$autoclave=$row['autoclave'];
			$arr_info=explode(";",$autoclave);
			$codice_materiale=$row['codice_materiale'];
			$descrizione=$row['descrizione'];
			$qta=$row['qta'];
			$lotto_materiale=$row['lotto_materiale'];
			$inizio_ora_steril=$row['inizio_ora_steril'];
			$inizio_temp_steril=$row['inizio_temp_steril'];
			if ($inizio_ora_steril==NULL || strlen($inizio_ora_steril)==0) $inizio_ora_steril="?";
			if ($inizio_temp_steril==NULL || strlen($inizio_temp_steril)==0) $inizio_temp_steril="?";

			$fine_ora_steril=$row['fine_ora_steril'];
			$fine_temp_steril=$row['fine_temp_steril'];
			if ($fine_ora_steril==NULL || strlen($fine_ora_steril)==0) $fine_ora_steril="?";
			if ($fine_temp_steril==NULL || strlen($fine_temp_steril)==0) $fine_temp_steril="?";
			$file_pdf_scontrino=$row['file_pdf_scontrino'];
			if ($file_pdf_scontrino==NULL) $file_pdf_scontrino="?";
			$completo=$row['completo'];
			
			
			//il lotto quì si ripete diverse volte uguale in funzione dei materiali (vedi join)
			//quindi le diverse iterazioni produrranno sempre stessi dati indicizzati da $lotto
			$res['body'][$lotto]['lotto']=$lotto;
			$res['body'][$lotto]['autoclave']=$arr_info[2];
			$res['body'][$lotto]['date_next']=$row['data'];
			$res['body'][$lotto]['stab_reparto']=$row['stab_reparto'];
			$res['body'][$lotto]['tempo']=$row['tempo'];
			$res['body'][$lotto]['temperatura']=$row['temperatura'];					
			$res['body'][$lotto]['inizio_ora_steril']=$inizio_ora_steril;
			$res['body'][$lotto]['inizio_temp_steril']=$inizio_temp_steril;
			$res['body'][$lotto]['fine_ora_steril']=$fine_ora_steril;
			$res['body'][$lotto]['fine_temp_steril']=$fine_temp_steril;
			$res['body'][$lotto]['file_pdf_scontrino']=$file_pdf_scontrino;
			$res['body'][$lotto]['completo']=$completo;
			//quì invece per ogni lotto ci saranno diversi materiali (vedi $indice)
			$res['body'][$lotto]['materiali']['codice_materiale'][$indice]=$codice_materiale;
			$res['body'][$lotto]['materiali']['descrizione'][$indice]=$descrizione;
			$res['body'][$lotto]['materiali']['qta'][$indice]=$qta;
			$res['body'][$lotto]['materiali']['lotto_materiale'][$indice]=$lotto_materiale;
			$indice++;
		}	
		return $res;
}

function codici_check($codice) {
	global $servJ,$utJ,$passJ,$databaseJ;
	
	$mysqli =new mysqli($servJ,$utJ,$passJ,$databaseJ);


	$tipo="PF";
	if (substr($codice,0,1)=="$" || substr($codice,0,1)=="*") $tipo="SL";

	//Prima faccio la verifica diretta sul codice...
	$sql="SELECT codici_check
		FROM check_codici_assoc_single
		WHERE codice='$codice' ";

	$result=$mysqli->query($sql);
	$res = $result->fetch_row();
	$codici_check = $res[0];
	$resp="N";
	if ($tipo=="PF") {
		$resp="S";
		if (substr($codice,0,2)=="75") $codici_check="6;7;8";
		else $codici_check="6";
	}
	else {
		if (strlen($codici_check)==0) {
			//...altrimenti tramite range di regole sui codici
			$codice_num=intval(substr($codice,1));
			$sql="SELECT * FROM check_codici_assoc WHERE tipo_codice='$tipo'";
			$result=$mysqli->query($sql);

			$info=array();
			$resp="N";$codici_check="";
			while($results = $result->fetch_array()){
				$cod_da=$results['cod_da'];
				$cod_a=$results['cod_a'];
				$codici_check=$results['codici_check'];
				if ($codice_num>=$cod_da && $codice_num<=$cod_a) {
					$resp="S";
					break;
				}
			} 
		} else $resp="S";
	}
	if (strpos($codice,"-")>0) {
		$resp="N";
		$codici_check="";
	}
	$info['resp']=$resp;
	$info['codici_check']=$codici_check;
	return $info;
}	


?>