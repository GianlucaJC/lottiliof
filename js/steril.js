$(window).keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13') {
        event.preventDefault();
        console.debug('Prevent Submit.');
    }
});

(function() {
	'use strict';
	window.addEventListener('load', function() {
		// Fetch all the forms we want to apply custom Bootstrap validation styles to
		var forms = document.getElementsByClassName('needs-validation_lottos');
		// Loop over them and prevent submission
		var validation = Array.prototype.filter.call(forms, function(form) {
			form.addEventListener('submit', function(event) {
				if (form.checkValidity() === false) {
					event.preventDefault();
					event.stopPropagation();
				}	else {
					event.preventDefault();
					event.stopPropagation();
					new_lotto_steril();
				}	
				form.classList.add('was-validated');
			}, false);
		});
	}, false);
})();



function new_lotto_steril() {
	date_next=$("#date_next").val();
	autoclave=$("#autoclave").val();
	
	tempo=$("#tempo").val();
	temperatura=$("#temperatura").val();
	data=serialize_steril();
	$("#btn-crea-lottos").prop("disabled",true)
	$("#btn-crea-lottos").text("Attendere. Operazione in corso...")
	operazione="new_lotto_steril";
	var url = "ajax.php";
	$.ajax({
		type: "POST",
		url: url,
		data:{operazione:operazione,date_next:date_next,autoclave:autoclave,dati:data,tempo:tempo,temperatura:temperatura},
		beforeSend:function(){
			html="";
			html+="<div class='spinner-border' role='status'>";
				html+="<span class='sr-only'>Creazione in corso...</span>";
			html+="</div>";
			$("#stampa_lotti").html(html)
		},
		success: function (data){
			$("#btn-crea-lottos").prop("disabled",false)
			$("#btn-crea-lottos").text("Crea Lotto")
			record = jQuery.parseJSON( data );
			if (record.header.resp=="KO") {
				html="";
				html+="<div class='alert alert-warning mt-3' role='alert'>";
					html+="Problema occorso durante la creazione del lotto!";
				html+="</div>";
			}
			else {
				html="";
				$.each(record.body, function (lotto, data) {
					html+=row_view_steril(record,lotto)
					$("#stampa_lotti").html(html)
				})			
				
			}	
			$("#stampa_lotti").html(html)
			$("#div_materiali").empty()
			$("#div_ciclo_materiali").empty();
			$(".info_materiali").hide()
			var selects = $('select[id="materiale"]')
			selects.find(":disabled").prop("disabled", false).each(function() {
				  var codice = $(this).val()
				  $("#materiale option[value='"+codice+"']").removeAttr('disabled')
			});
			add_materiali.elementi=new Object()		
			
		}
	});	
}

function etic(lotto_steril) {
	operazione="etic_steril"
	codice_materiale=etic.obj_m.codice_materiale[0]
	lotto_materiale=etic.obj_m.lotto_materiale[0]

	
	var url = "etic.php";
	$.ajax({
		type: "POST",
		url: url,
		data:{operazione:operazione,codice_materiale:codice_materiale,lotto_materiale:lotto_materiale,lotto_steril:lotto_steril},
		beforeSend:function(){
		},
		success: function (data){
			div_ref="div_lbl_steril";
			lbl="label/steril.pdf";
			html="<a href='"+lbl+"' target='_blank'>Apri etichetta</a>";
			$("#"+div_ref).html(html)
		}
	});		
}

function view_detail_steril(lotto) {
	/*	{"body":{"030320S125002":{"lotto":"030320S125002","autoclave":"AU03","date_next":"2020-03-03","stab_reparto":"S125","tempo":"67","temperatura":"7","materiali":{"codice_materiale":["001","003"],"qta":["1","3"],"lotto_materiale":["2","4"]}}},"header":"lotti_steril"}
	*/
	
  //obj proviene da row_view_steril()	
  obj_u=view_detail_steril.obj.body[lotto]	
  obj_m=view_detail_steril.obj.body[lotto].materiali
  etic.obj_m=obj_m;
  
  mod29.obj_u=obj_u;
  mod29.obj_m=obj_m;
  
  html="";
  html+="<div class='modal fade' id='modalStampe' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>";
    html+="<div class='modal-dialog modal-xl' role='document'>";
      html+="<div class='modal-content'>";
       
	   html+="<div class='modal-header'>";
			html+="<div class='container alert alert-primary' role='alert'>";
				html+="<h3 class='modal-title'>Lotto: "+obj_u.lotto+"</h3>";
				html+="<input type='hidden' id='lotto_view' value='"+obj_u.lotto+"'>";
				
				//if (obj_m.codice_materiale.length<=1) {
					
					html+="<button  onclick=\"etic('"+lotto+"')\" type='button' class='btn btn-primary'>";
						html+="<i class='fas fa-print'></i> Crea Etichetta";
					html+="</button>";
					html+="<p class='mt-3 mb-2' style='display:block' id='div_lbl_steril'></p>";
				//}
			html+="</div>";
			
          html+="<button class='close' type='button' data-dismiss='modal' aria-label='Close'>";
            html+="<span aria-hidden='true'>×</span>";
          html+="</button>";
        html+="</div>";        
		
		html+="<div class='card'>";
		  html+="<div class='card-body'>";
			html+="<h5 class='card-title'>Autoclave: <b>"+obj_u.autoclave+"</b> - Data impegno: <b>"+obj_u.date_next+"</b></h5>";
			html+="<div class='row'>";
				for (sca=0;sca<=obj_m.codice_materiale.length-1;sca++) {
				  html+="<div class='col-sm-6 mt-2'>";
					html+="<div class='card'>";
					  html+="<div class='card-body'>";
						html+="<h4 class='card-title'>Codice Materiale: <b>"+obj_m.codice_materiale[sca]+"</b></h4>";

						html+="<p class='card-text'>";
							html+="Quantità: <b>"+obj_m.qta[sca]+"</b> ";
							html+="Lotto Materiale: <b>"+obj_m.lotto_materiale[sca]+"</b> ";
						html+="</p>";	
					  html+="</div>";
					html+="</div>";
				  html+="</div>";
				} 
			html+="</div>";
					
			html+="<div class='alert alert-info mt-3' role='alert'>";
				html+="Dati ciclo di sterilizzazione impostato:";
				html+="Tempo: <b>"+obj_u.tempo+"</b> ";
				html+="Temperatura: <b>"+obj_u.temperatura+"°C</b> ";
			html+="</div>";


			html+="<div class='card'>";
			  html+="<div class='card-header'>";
				html+="Dati di chiusura";
			  html+="</div>";
			  html+="<div class='card-body'>";
				html+="<p class='card-text'><i>Informazioni utili alla chiusura del ciclo di sterilizzazione.</i></p>";

				html+="<table class='table table-hover'>";
					html+="<thead>";
						html+="<tr>";
							html+="<th scope='col'></th>";
							html+="<th scope='col'>Ora</th>";
							html+="<th scope='col'>Temperatura</th>";
						html+="</tr>";
					html+="<thead>";
					
					html+="<tbody>";					
						html+="<tr>";
							html+="<td>Inizio Sterilizzazione</td>";
							html+="<td>";
								ds="";
								value=obj_u.inizio_ora_steril
								if (!value) value="?";
								if (value!="?") ds="disabled"; else value="";
								html+="<input type='time' name='inizio_ora_steril' id='inizio_ora_steril' "+ds+" value='"+value+"'>";
							html+="</td>";	
							html+="<td>";
								ds="";
								value=obj_u.inizio_temp_steril
								if (!value) value="?";
								if (value!="?") ds="disabled"; else value="";
								html+="<input type='text' name='inizio_temp_steril' id='inizio_temp_steril' placeholder='°C' "+ds+" value='"+value+"'>";
							html+="</td>";
						html+="</tr>";
						
						html+="<tr>";
							html+="<td>Fine Sterilizzazione</td>";
							html+="<td>";
								ds="";
								value=obj_u.fine_ora_steril;
								if (!value) value="?";
								if (value!="?") ds="disabled"; else value="";
								html+="<input type='time' name='fine_ora_steril' id='fine_ora_steril' "+ds+" value='"+value+"'>";
							html+="</td>";	
							html+="<td>";
								ds="";
								value=obj_u.fine_temp_steril;
								if (!value) value="?";
								if (value!="?") ds="disabled"; else value="";
								html+="<input type='text' name='fine_temp_steril' id='fine_temp_steril' placeholder='°C' "+ds+" value='"+value+"'>";
							html+="</td>";
						html+="</tr>";
					html+="</tbody>";	
				html+="</table>";  

				//view per upload
			 	file_pdf_scontrino=obj_u.file_pdf_scontrino
				if (file_pdf_scontrino!="1" && file_pdf_scontrino!="2") {
					html+="<form method='post' id='MyUploadForm' action='ajax.php?upload=1&lotto="+obj_u.lotto+"' enctype='multipart/form-data'>";
						html+="<div class='container' id='div_upload'>";
							
							html+="<div class='row'>";
								html+="<div class='col-sm-6'>";
									html+="<label for='userfile'>File PDF (scontrino rilasciato da Autoclave)</label>";
									html+="<input type='file' name='userfile' id='userfile' class='form-control'>";
								html+="</div>";		
								html+="<div class='col-sm-6'>";
									html+="<label for='no_scontrino'>Scontrino non disponibile</label>";
									html+="<input type='checkbox' name='no_scontrino' id='no_scontrino' style='width:auto' class='form-control'>";
								html+="</div>";		
							html+="</div>";	
							
							html+="<div class='row mt-2'>";
								html+="<div class='col-sm-12'>";
									html+="<input type='submit' id='submit-btn' class='btn btn-lg btn-primary' value='Invia al server'>";
									html+="<div id='loading-img' class='ml-4 spinner-border' style='display:none' role='status'>";
									html+="<span class='sr-only'>Creazione in corso...</span>";
									html+="</div>";
									html+="<div id='progressbox' class='mt-4'><div id='progressbar'></div ><div id='statustxt'>0%</div></div>";
									html+="<div id='output' class='mt-2'></div>";
								html+="</div>";
								html+="<div id='preview'></div>";
							html+="</div>";
							
						html+="</div><hr>";
					html+="</form>";
				} else {
					if (file_pdf_scontrino!="1") 
						html+="<hr><strong>Scontrino Autoclave non disponibile</strong>"
					else
						html+="<hr><a href='upload/"+obj_u.lotto+".pdf' target='_blank'>PDF scontrino Autoclave</a>";					
				}
				html+="<div id='resp_save_close' class='mt-2'></div><hr>";				
				//fine view
				if (obj_u.completo=="0" )
					html+="<center><div class='mt-2' id='div_save_close'><a href='javascript:void(0)' onclick=\"save_close('"+obj_u.lotto+"')\" class='ml-4 btn btn-primary'>Salva</a></div></center>";
				else {
					html+="<div class='mt-2'>";
						html+="<a href='javascript:void(0)' class='btn btn-success' onclick='mod29()'>Apri File MOD.29</a>";
						html+="<div id='div_mod29' class='mt-2' style='display:none'>";
							html+="<div class='spinner-border' role='status'>";
								html+="<span class='sr-only'>Loading...</span>";
							html+="</div>";
						html+="</div>";
					html+="</div>";
				}	
					
			  html+="</div>";
			html+="</div>";
			
			
		  html+="</div>";
		html+="</div>";
		
        html+="<div class='modal-footer'>";
          html+="<button class='btn btn-secondary' type='button' data-dismiss='modal'>Chiudi</button>";
        html+="</div>";
      html+="</div>";
    html+="</div>";
  html+="</div>";
  $("#div_modal_generic").html(html)
  $("#modalStampe").modal("show");
	
	var options = {
		target:   '#output',   // target element(s) to be updated with server response
		beforeSubmit:  beforeSubmit,  // pre-submit callback
        success: afterSuccess,
		error: function (response) {
			$('#submit-btn').show();
			$('#loading-img').hide(); 
			$('#progressbox').delay( 1000 ).fadeOut(); //hide progress bar
			
			html+="<div class='alert alert-warning mt-3' role='alert'>";
				html+="<b>Attenzione!</b> Problema occorso durante l'invio del file al server";
			html+="</div>";
			$('#output').html(html);
		},
		uploadProgress: OnProgress, //upload progress callback
		resetForm: true        // reset the form after successful submit
	};
	
	$('#MyUploadForm').submit(function() {
        event.preventDefault();
		$(this).ajaxSubmit(options);           
		return false;
	});

	
}

function mod29() {
  console.log(mod29.obj_u)
  console.log(mod29.obj_m)
  obj_u = JSON.stringify(mod29.obj_u); 
  obj_m = JSON.stringify(mod29.obj_m); 
	operazione="mod29";
	var url = "ajax.php";
	$.ajax({
		type: "POST",
		url: url,
		data:{operazione:operazione,obj_u:obj_u,obj_m:obj_m},
		beforeSend:function(){
			$("#div_mod29").show();
		},
		success: function (data){
			html="";
			if (data.trim()=="OK"){
				$("#div_mod29").hide();
				window.open("mod29.pdf");
			}
		}
	});	
  
}

function save_close(lotto) {
	operazione="save_close";
	inizio_ora_steril=$("#inizio_ora_steril").val();
	inizio_temp_steril=$("#inizio_temp_steril").val();
	fine_ora_steril=$("#fine_ora_steril").val();
	fine_temp_steril=$("#fine_temp_steril").val();
	no_scontrino=0
	if ($('#no_scontrino').is(":checked")) no_scontrino=1;
	
	var url = "ajax.php";
	$.ajax({
		type: "POST",
		url: url,
		data:{operazione:operazione,lotto:lotto,inizio_ora_steril:inizio_ora_steril,inizio_temp_steril:inizio_temp_steril,fine_ora_steril:fine_ora_steril,fine_temp_steril:fine_temp_steril,no_scontrino:no_scontrino},
		beforeSend:function(){
			html="";
			html+="<div class='spinner-border' role='status'>";
				html+="<span class='sr-only'>Loading...</span>";
			html+="</div>";
			$("#resp_save_close").html(html);
			
		},
		success: function (data){
			save=1
			if (inizio_ora_steril.length!=0) $("#inizio_ora_steril").prop("disabled",true); else save=0;
			if (inizio_temp_steril.length!=0) $("#inizio_temp_steril").prop("disabled",true); else save=0;
			if (fine_ora_steril.length!=0) $("#fine_ora_steril").prop("disabled",true); else save=0;
			if (fine_temp_steril.length!=0) $("#fine_temp_steril").prop("disabled",true); else save=0;
			$("#div_save_close").show();
			if (save==1) $("#div_save_close").hide();
		
			html="";
			if (data.trim()=="OK"){
				html="";
				html+="<div class='alert alert-success mt-3' role='alert'>";
					html+="<b>Operazione effettuata!</b> Dati salvati con successo";
				html+="</div>";
			} else {
				html="";
				html+="<div class='alert alert-warning mt-3' role='alert'>";
					html+="<b>Attenzione!</b> Problema occorso durante il salvataggio";
				html+="</div>";
			}
				
			$("#resp_save_close").html(html);
			archivio_steril(lotto);
			if (data.trim()=="OK") $("#modalStampe").modal("hide");
				

		}
	});	
}

function log_e(id_lotto) {
	operazione="log_eventi";
	var url = "ajax.php";
	$.ajax({
		type: "POST",
		url: url,
		data:{operazione:operazione,id_lotto:id_lotto,tipo:"steril"},
		beforeSend:function(){
			html="";
			html+="<div class='modal fade' id='modalLog' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>";
				html+="<div class='modal-dialog modal-xl' role='document'>";
				  html+="<div class='modal-content'>";
				   
					html+="<div class='modal-header'>";
						html+="<div class='container alert alert-primary' role='alert'>";
							html+="<h3 class='modal-title'>Lotto: "+id_lotto+"</h3>";
						html+="</div>";
					  html+="<button class='close' type='button' data-dismiss='modal' aria-label='Close'>";
						html+="<span aria-hidden='true'>×</span>";
					  html+="</button>";
					html+="</div>";
					
					html+="<div id='div_log_e' class='container mt-3 mb-3'>";
						html+="<div class='spinner-border' role='status'>";
							html+="<span class='sr-only'>Loading...</span>";
						html+="</div>";
					html+="<div>";
					
				  html+="</div>"; 	
				html+="</div>"; 
			html+="</div>"; 
			
			$("#div_modal_generic").html(html)
			$("#modalLog").modal("show");
		},
		success: function (data){
			/*
			record = jQuery.parseJSON( data );
			resp=record.header.resp
			record.body.descrizione
			*/
			html="";
			html+="<div class='alert alert-info' role='alert'>";
				html+="Log Eventi sul lotto selezionato";
			html+="</div>";
			record = jQuery.parseJSON( data );
			if (record.body) {
				for (sca=0;sca<=record.body.length-1;sca++) {
					operazione=record.body[sca].operazione
					data_mov=record.body[sca].data
					operatore=record.body[sca].operatore
					descr_ret=record.body[sca].descr_ret
					descr_operazione="";
					bg="bg-light";
					if (operazione=="1") {descr_operazione="Creazione del lotto";bg="bg-white";}
					if (operazione=="2") {descr_operazione="Modifica del lotto";bg="bg-light"}
					if (operazione=="3") {descr_operazione="Eliminazione del lotto";bg="bg-warning"}


					html+="<a href='javascript:void(0)' class='list-group-item list-group-item-action "+bg+" mb-2'>";
						html+="<div class='d-flex w-100 justify-content-between'>";
							html+="<h4 class='mb-1'><font color='blue'>"+descr_operazione+"</font></h4>";
							html+="<h4>"+data_mov+"</h4>";
						html+="</div>";
						html+="<p class='mb-1'>"+descr_ret+"</p>";
						html+="<small>Operazione effettuata da: <font color='blue'>"+operatore+"</font></small>";
					html+="</a>";	
				}
			} else {
				html=""
				html+="<div class='alert alert-warning' role='alert'>";
				  html+="Informazioni sul log non disponibili!";
				html+="</div>";
			}

			$("#div_log_e").html(html);
		}
	});		
	
	
}

function row_view_steril(record,lotto){	
	html="";
	view_detail_steril.obj=record;
	
	/*	{"body":{"030320S125002":{"lotto":"030320S125002","autoclave":"AU03","date_next":"2020-03-03","stab_reparto":"S125","tempo":"67","temperatura":"7","materiali":{"codice_materiale":["001","003"],"qta":["1","3"],"lotto_materiale":["2","4"]}}},"header":"lotti_steril"}
	*/

	bg="bg-success"
	
	inizio_ora_steril=record.body[lotto].inizio_ora_steril
	inizio_temp_steril=record.body[lotto].inizio_temp_steril
	fine_ora_steril=record.body[lotto].fine_ora_steril
	fine_temp_steril=record.body[lotto].fine_temp_steril
	
	completo=record.body[lotto].completo
	if (!completo || completo.length==0 || completo=="0") bg="bg-warning";
	
	
	html+="<a class='list-group-item list-group-item-action "+bg+"' href='javascript:void(0)' onclick=\"view_detail_steril('"+lotto+"')\">";
		html+="<div class='d-flex w-100 justify-content-between'>";
			html+="<h4 class='mb-1'><font color='blue'>"+lotto+"</font></h4>";
			html+="<h2><font color='blue'>"+record.body[lotto].date_next+"</font></h2>";
		html+="</div>";
		html+="<p class='mb-1'></p>";
		html+="<strong>Tempo: <font color='blue'>"+record.body[lotto].tempo+"</font> - Temperatura: <font color='blue'>"+record.body[lotto].temperatura+"°C</font> ";
		html+="</strong>";
	html+="</a>";
	adminLOTTI=$("#adminLOTTI").val();
	if (adminLOTTI=="1") html+="<div class='mt-1 mb-2' style='text-align:right'><a href='javascript:void(0)' onclick=\"log_e('"+lotto+"');\" >Log eventi</a></div>";
	
	return html
}
function archivio_steril(lotto) {
	$(".tabelle").hide()
	html="";
	n_rec_inpage=$("#n_rec_inpage").val()
	stato_scheda=$("#stato_scheda").val()
	filtro_autoclave=$("#filtro_autoclave").val()
	$("#nav_steril").show();
	html="<div id='res_archivio' class='mt-2'></div>";
	$("#stampa_lotti").html(html);
	
	da_data_s=$("#da_data_s").val();
	a_data_s=$("#a_data_s").val()	
	
	operazione="elenco_lotti_steril";
	
	var url = "ajax.php";
	$.ajax({
		type: "POST",
		url: url,
		data:{operazione:operazione,lotto:lotto,da_data_s:da_data_s,a_data_s:a_data_s,n_rec_inpage:n_rec_inpage,stato_scheda:stato_scheda,filtro_autoclave:filtro_autoclave},
		beforeSend:function(){
			html="";
			html+="<div class='spinner-border' role='status'>";
				html+="<span class='sr-only'>Loading...</span>";
			html+="</div>";
			$("#res_archivio").html(html);
			
		},
		success: function (data){
			html="";
			record = jQuery.parseJSON( data );
			/*	{"body":{"030320S125002":{"lotto":"030320S125002","autoclave":"AU03","date_next":"2020-03-03","stab_reparto":"S125","tempo":"67","temperatura":"7","materiali":{"codice_materiale":["001","003"],"qta":["1","3"],"lotto_materiale":["2","4"]}}},"header":"lotti_steril"}
			*/			
			html+="<div class='list-group mt-2'>";
				$.each(record.body, function (lotto, data) {
					html+=row_view_steril(record,lotto)
				})	
				$("#res_archivio").html(html);
			html+="</div>";
		}
	});
}




function serialize_steril() {
	ord=0;
	data={}
	$(".dati_steril").each(function() {
		elem=this.id
		rif=elem.split("_")[0]
		id=elem.split("_")[1]
		if (rif=="steril") {	
			cod_materiale=$("#cod_materiale"+id).val();
			qta_materiale=$("#qta_materiale"+id).val();
			lotto_materiale=$("#lotto_materiale"+id).val();
			data[ord]={}
			data[ord].cod_materiale=cod_materiale
			data[ord].qta_materiale=qta_materiale
			data[ord].lotto_materiale=lotto_materiale
			ord++
		}
		
	})
	return data
}


function enable_add() {
	materiale=$("#materiale").val()
	if (materiale.length>0 && materiale!="0") {
		$("#btn_add_materiale").prop("disabled",false)
		$("#btn_add_materiale").removeClass( "btn-outline-primary" )
		$("#btn_add_materiale").addClass( "btn-primary" )
	} else {
		$("#btn_add_materiale").prop("disabled",true)
		$("#btn_add_materiale").removeClass( "btn-primary" )
		$("#btn_add_materiale").addClass( "btn-outline-primary" )
	}		
}

function add_materiali() {
	$("#stampa_lotti").empty()
	if (typeof add_materiali.elementi=='undefined') add_materiali.elementi=new Object()
	$(".info_materiali").show();
	$("#div_nota").remove();
	
	codice=$("#materiale").val();
	//29.07.2020: Matteo ha richiesto che i codici da inserire nei materiali possono ripetersi
	//prima popolavo l'array con il codice per evitare che si ripetessero. Ora l'array lo popolo con un codice sempre diverso
	
	cod_view=$("#materiale_hide option[value='"+codice+"']").text();

codice=uniqid(7);
	
	
	
	descrizione=$("#materiale option:selected").text();
	
	if (add_materiali.elementi[codice]) return false; //dopo la richiesta di Matteo questo controllo diventa superfluo
	
	add_materiali.elementi[codice]={}
	add_materiali.elementi[codice]=descrizione
	
	$("#materiale option[value='"+codice+"']").attr('disabled','disabled')
	
	html="";
	html+="<nav class='mt-2 navbar navbar-expand-lg navbar-light bg-light' id='nav_materiale"+codice+"'>";
		html+="<input type='hidden' id='steril_"+codice+"' class='dati_steril'>";
			html+="<ul class='navbar-nav mr-auto mt-2 mt-lg-0'>";
			  html+="<li class='nav-item active'>";
				html+="<font color='blue'>"+cod_view+"</font>";
			  html+="</li>";
			  html+="<li class='nav-item active ml-4'>";
				html+="<i>"+descrizione+"</i>";
			  html+="</li>";
			  html+="<li>";
				html+="<input type='hidden' id='cod_materiale"+codice+"' value='"+cod_view+"'>";
				html+="<input type='text'  style='width:80px' class='ml-3 form-control bg-light dati_steril' placeholder='Q.tà' aria-label='qta-materiale' aria-describedby='basic-addon2' id='qta_materiale"+codice+"' required>";
			  html+="</li>";
			  html+="<li>";
				html+="<input type='text' style='width:160px' class='ml-3 form-control bg-light dati_steril' placeholder='Lotto' aria-label='lotto-materiale' aria-describedby='basic-addon2' id='lotto_materiale"+codice+"' required>";
			  html+="</li>";
			html+="</ul>";
			  html+="<button type='button' title='"+codice+"' id='btn-remove-materiali"+codice+"' class='ml-3 btn btn-outline-secondary btn-remove-materiali'>Elimina</button>";
	html+="</nav>";	

	html+="<div id='div_nota' class='alert alert-info mt-3' role='alert'>";
		html+="Per ciascun ciclo di sterilizzazione inserire almeno una striscia di nastro indicatore di sterilizzazione (ref.P11667)";
	html+="</div>";

	$("#div_materiali").append(html)
	
	
	$(".btn-remove-materiali").click(function() {
		cod=this.title
		delete add_materiali.elementi[cod];
		$("#materiale option[value='"+cod+"']").removeAttr('disabled')
		$("#nav_materiale"+cod).remove()
		if(Object.keys(add_materiali.elementi).length==0) {$("#div_ciclo_materiali").empty();$(".info_materiali").hide()}

	})

	if(Object.keys(add_materiali.elementi).length==1) {
		html=""
		html+="<h5 class='ml-3'>Dati ciclo di sterilizzazione impostato:</h5>";
		html+="<div class='input-group mb-3'>";
			html+="<input type='text' class='ml-3 form-control bg-light floatNumber' placeholder='Tempo (minuti)' aria-label='tempo' aria-describedby='basic-addon2' id='tempo' required>";
			html+="<input type='text' style='width:160px' class='ml-3 mr-3 form-control bg-light' placeholder='Temperatura (°C)' aria-label='temperatura' aria-describedby='basic-addon2' id='temperatura' required>";
		html+="</div>";
			html+="<div class='mt-2'>";
				html+="<button class='btn btn-outline-success btn-block' type='submit' id='btn-crea-lottos'  onclick=''>Crea Lotto</button>";
			html+="</div>";
		$("#div_ciclo_materiali").html(html);
		$('.floatNumber').on('input', function() {
			this.value = this.value.replace(/[^0-9.]/g,'').replace(/(\..*)\./g, '$1');
		});
		
	}	



	/*
	$("#btn_add_materiale").prop("disabled",true)
	$("#btn_add_materiale").removeClass( "btn-primary" )
	$("#btn_add_materiale").addClass( "btn-outline-primary" )	
	*/
	$("#materiale").val("0");
}


//function relative all'upload scontrino autoclave
//function after successful file upload (when server response)
function afterSuccess(response){
	$('#submit-btn').show(); //hide submit button
	$('#loading-img').hide(); //hide submit button
	$('#progressbox').delay( 1000 ).fadeOut(); //hide progress bar
	html+="<div class='alert alert-success mt-3' role='alert'>";
		html+="<b>Operazione effettuata!</b> Il File è stato inviato con successo al server";
	html+="</div>";
	lotto=$("#lotto_view").val();
	html+="<hr><a href='upload/"+lotto+".pdf' target='_blank'>PDF scontrino Autoclave</a>";

	$('#div_upload').html(html);
	
}

//function to check file size before uploading.
function beforeSubmit(){
    //check whether browser fully supports all File API
   if (window.File && window.FileReader && window.FileList && window.Blob){
		
		if( !$('#userfile').val()) //check empty input filed
		{
			$("#output").html("Devi specificare il file da inviare al server!");
			return false
		}
		
		var fsize = $('#userfile')[0].files[0].size; //get file size
		var ftype = $('#userfile')[0].files[0].type; // get file type
		

		//allow file types 
		switch(ftype){
            
			case 'image/gif': 
			case 'image/jpeg': 
			case 'image/pjpeg':
			case 'application/pdf':
			/*
			case 'text/plain':
			case 'text/html':
			case 'application/x-zip-compressed':			
			case 'application/msword':
			case 'application/vnd.ms-excel':
			case 'video/mp4':
			*/
                break;
            default:
                $("#output").html("<b>"+ftype+"</b> Tipo di file non supportato!");
				return false
        }
		
		//Allowed file size is less than 5 MB (1048576)
		if(fsize>5242880){
			$("#output").html("<b>"+bytesToSize(fsize) +"</b> File troppo grande! <br />Il file non deve superare i 5 MB.");
			return false
		}
				
		$('#submit-btn').hide(); //hide submit button
		$('#loading-img').show(); //hide submit button
		$("#output").html("");  
	}
	else {
		//Output error to older unsupported browsers that doesn't support HTML5 File API
		$("#output").html("Problema con le impostazioni del browser!");
		return false;
	}
}

//progress bar function
function OnProgress(event, position, total, percentComplete){
    //Progress bar
	$('#progressbox').show();
    $('#progressbar').width(percentComplete + '%') //update progressbar percent complete
    $('#statustxt').html(percentComplete + '%'); //update status text
    if(percentComplete>50){
            $('#statustxt').css('color','#000'); //change status text to white after 50%
    }
}

//function to format bites bit.ly/19yoIPO
function bytesToSize(bytes) {
   var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
   if (bytes == 0) return '0 Bytes';
   var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
   return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}

function uniqid(length){
  var dec2hex = [];
  for (var i=0; i<=15; i++) {
    dec2hex[i] = i.toString(16);
  }

  var uuid = '';
  for (var i=1; i<=36; i++) {
    if (i===9 || i===14 || i===19 || i===24) {
      uuid += '-';
    } else if (i===15) {
      uuid += 4;
    } else if (i===20) {
      uuid += dec2hex[(Math.random()*4|0 + 8)];
    } else {
      uuid += dec2hex[(Math.random()*16|0)];
    }
  }

  if(length) uuid = uuid.substring(0,length);
  return uuid;
}

//fine upload