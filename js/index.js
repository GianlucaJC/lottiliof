(function($) {
	$( "#lotto_rapido" ).keypress(function(event) {
			if ( event.which == 13 ) {
				event.preventDefault();
				cercalotto()
			}
	})

	
})(jQuery); // End of use strict




function cercalotto() {
	operazione="cercalotto";
	lotto_rapido=$("#lotto_rapido").val();
	select_tipo_ric=$("#select_tipo_ric").val();
	
	var url = "ajax.php";
	$.ajax({
		type: "POST",
		url: url,
		data:{operazione:operazione,lotto_rapido:lotto_rapido,select_tipo_ric:select_tipo_ric},
		beforeSend:function(){
			html="";
			html+="<div class='spinner-border' role='status'>";
				html+="<span class='sr-only'>Loading...</span>";
			html+="</div>";
			$("#stampa_lotti").html(html)
		},
		success: function (data){
			if (data.trim()=="NO") {
				html="";
				html+="<div class='alert alert-warning' role='alert'>";
					html+="Lotto non trovato!";
				html+="</div>";
				$("#stampa_lotti").html(html)
				return;
			}
			
			record = jQuery.parseJSON( data );
			var head=record.header;
			
			if (head=="lotti_steril") {
				/*	{"body":{"030320S125002":{"lotto":"030320S125002","autoclave":"AU03","date_next":"2020-03-03","stab_reparto":"S125","tempo":"67","temperatura":"7","materiali":{"codice_materiale":["001","003"],"qta":["1","3"],"lotto_materiale":["2","4"]}}},"header":"lotti_steril"}
				*/		
				html="";
				$.each(record.body, function (lotto, data) {
					html+=row_view_steril(record,lotto)
					$("#stampa_lotti").html(html)
				})					
					
				return false;
			} 
 

			html="";
			html+="<p>Max 150 records restituiti</p>";
			html+="<div class='list-group'>";
			
			for (sca=0;sca<=record['dati'].length-1;sca++) {			
				user_id=record['dati'][sca].user_id
				operatore=record['dati'][sca].DBoperatore
				id_lotto=record['dati'][sca].id
				controllo=record['dati'][sca].DBcontrollo
				tipo=record['dati'][sca].DBtipo
				key=record['dati'][sca].lotto
				codice=record['dati'][sca].codice
				prodotto=record['dati'][sca].prodotto
				scadenza=record['dati'][sca].scadenza
				disp=record['dati'][sca].disp
				protocollo=record['dati'][sca].DBprot
				quantita=record['dati'][sca].quantita
				verifica=record['dati'][sca].verifica
				campioni=record['dati'][sca].campioni
				save_q=record['dati'][sca].save_quality
				close_q=record['dati'][sca].close_quality
				html+=view_row(user_id,operatore,id_lotto,tipo,key,codice,prodotto,scadenza,disp,protocollo,quantita,controllo,verifica,campioni,save_q,close_q)
			}	
			html+="</div>";	

			$("#stampa_lotti").html(html)

		}
	});
	
}

function cerca_per(value) {
	txt="Cerca lotto...";$("#lotto_rapido").get(0).type="text";
	if (value=="2") txt="Cerca codice...";
	if (value=="3") txt="Cerca prodotto...";
	if (value=="4") txt="Cerca protocollo...";
	if (value=="5") {txt="Cerca data liberazione...";$("#lotto_rapido").get(0).type="date";}
	$("#lotto_rapido").val('');
	$("#lotto_rapido").attr("placeholder",txt);
}

function set_convalida(id_lotto,tipo) {
	operazione="convalida";
	
	var url = "ajax.php";
	$.ajax({
		type: "POST",
		url: url,
		data:{operazione:operazione,id_lotto:id_lotto,tipo:tipo},
		beforeSend:function(){
			if (tipo=="1") {
				$("#button-verifica"+id_lotto).text("Attendere...")
				$("#button-verifica"+id_lotto).prop( "disabled", true );
			} else {
				$("#button-rimuovi-verifica"+id_lotto).text("Attendere...")
				$("#button-rimuovi-verifica"+id_lotto).prop( "disabled", true );
			}				
		},
		success: function (data){
			if (data.trim()=="OK") {
				$("#div_cont_lotto"+id_lotto).hide();
				$("#lottoRow"+id_lotto).css('background-color', 'transparent');    
				$( "#lottoRow"+id_lotto).removeClass( "bg-light" ); 
				if (tipo=="1") {
					$( "#lottoRow"+id_lotto).addClass( "bg-success text-white" );
					$("#button-verifica"+id_lotto).text("Carica")
					$("#button-verifica"+id_lotto).prop( "disabled", false );
				} else {
					$( "#lottoRow"+id_lotto).removeClass( "bg-success text-white" );
					$("#button-rimuovi-verifica"+id_lotto).text("Rimuovi Caricamento")
					$("#button-rimuovi-verifica"+id_lotto).prop( "disabled", false );
				}
			}			

		}
	});
}

function view_row(user_id,operatore,id_lotto,tipo,lotto,codice,prodotto,scadenza,disp,protocollo,quantita,controllo,verifica,campioni,save_q,close_q) {
	lotto_vis=lotto
	
	prodotto = prodotto.replace(/(<([^>]+)>)/ig,"")
	prodotto = prodotto.replace("\n","");
	prodotto=escapeHtml(prodotto);
	prodotto=prodotto.trim();
	console.log(prodotto);
	
	bg="bg-light"
	if (tipo=="2") bg="";
	//verifica viene settato dalla procedura di caricamento	
	/*
	if (verifica=="S") {bg="bg-success text-white";}
	if (verifica=="C") {bg="bg-warning text-white";}
	if (verifica=="X") {bg="bg-danger text-white";}
	*/
	if (!save_q) save_q="";
	if (save_q.length!=0 || verifica=="C")  {bg="bg-warning text-white";}
	if ((!save_q || save_q=="") && verifica=="S") {bg="bg-success text-white";}
	if (verifica=="C" && close_q=="1") {bg="bg-success text-white";}
	if (save_q=="X" && verifica=="C") {bg="bg-danger text-white";}
	if (controllo=="!") {bg="bg-secondary";lotto_vis="<del>"+lotto+"</del>";}
	
	
	adminLOTTI=$("#adminLOTTI").val();
	if (adminLOTTI!="1") {
		if (controllo=="!") bg+=" disabled ";
	}	
	
	html="";

	html+="<div id='div_cont_lotto"+id_lotto+"' class='mt-1'>"; 
		if ($('#btn_convalida').length != 0 && !($("#btn_convalida").hasClass( "btn-outline-primary" ))) {
			html+="<div class='container p-0 ml-0 mb-1 mt-5'>";
				html+="<div class='row'>";
				
					
					html+="<div class='col-sm-6 col-lg-6'>";
						html+="<button class='btn btn-info btn-block' type='button' id='button-verifica"+id_lotto+"' onclick='set_convalida("+id_lotto+",1)'>Carica</button>";
					html+="</div>";	
					html+="<div class='col-sm-6 col-lg-6'>";
						html+="<button class='btn btn-warning btn-block' type='button' id='button-rimuovi-verifica"+id_lotto+"' onclick='set_convalida("+id_lotto+",2)'>Rimuovi Caricamento</button>";
					html+="</div>";
				
					
					
					html+="<div class='col-sm-12 col-lg-6'>";
						html+="Operatore: <b>"+operatore+"</b>";
					html+="</div>";
				html+="</div>";	
			html+="</div>";	
		}	
		


		if (disp=="1980-01-01") disp="ND";
		html+="<a href='javascript:void(0)' onclick=\"load_etic("+tipo+","+user_id+","+id_lotto+",'"+lotto+"','"+codice+"','"+prodotto+"','"+scadenza+"','"+disp+"','"+quantita+"','"+protocollo+"','"+campioni+"')\" class='list-group-item list-group-item-action "+bg+"' id='lottoRow"+id_lotto+"'>";
			html+="<div class='d-flex w-100 justify-content-between'>";
				html+="<h4 class='mb-1'><font color='blue'>"+lotto_vis+"</font></h4>";
				html+="<h2><font color='blue'>"+codice+"</font></h2>";
			html+="</div>";
			html+="<p class='mb-1'><b>"+prodotto+"</b></p>";
			html+="<strong>";
				html+="Scadenza: <font color='blue'>"+scadenza+"</font> - Disponibilità: <font color='blue'>"+disp+"</font> - Protocollo: <font color='blue'>"+protocollo+"</font>  - Quantità: <font color='blue'>"+quantita+"</font>";

				if (campioni && campioni.length!=0) html+=" - Campioni: <font color='blue'>"+campioni;
				if (controllo && controllo.length!=0 && controllo!="!") html+=" - Alternativa: <font color='blue'>"+controllo+"</font>";
			html+="</strong>";
		html+="</a>";
	html+="</div>";
	console.log(html)
	return html
}


var entityMap = {
  '&': '&amp;',
  '<': '&lt;',
  '>': '&gt;',
  '"': '',
  "'": '',
  '/': '&#x2F;',
  '`': '&#x60;',
  '=': '&#x3D;'
};

function escapeHtml (string) {
  return String(string).replace(/[&<>"'`=\/]/g, function (s) {
    return entityMap[s];
  });
}

function archivio() {
	$(".tabelle").hide()
	$("#nav_steril").hide()
	adminLOTTI=$("#adminLOTTI").val();
	$("#collapseCardInfo").collapse('hide')
	$("#collapseCardLotto").collapse('hide')
	html="";
	html+="<div class='container-fluid p-0'>";
	  
	  html+="<div class='row'>";
		
		html+="<div class='col-sm-3 col-lg-3'>";
			html+="<label class='' for='select_tipo'>Tipo Prodotto</label>";
			html+=" <select class='custom-select' id='select_tipo' onchange=\"elenco_lotti(1)\">";
			  html+="<option selected value='0'>Tutti</option>";
			  html+="<option value='1'>Semilavorato</option>";
			  html+="<option value='2'>Prodotto Finito</option>";
			  html+="<option value='3'>Sterilizzazione Tubi</option>";
			html+="</select>";
		html+="</div>";
		
		html+="<div class='col-sm-3 col-lg-3'>";
			html+="<label class=' for='select_tipo_view'>Visualizzazione</label>";
			html+=" <select class='custom-select' id='select_tipo_view' onchange=\"elenco_lotti(1)\">";
			  html+="<option selected value='0'>Tutti</option>";
			  html+="<option value='1'>Solo NON caricati</option>";
			  html+="<option value='2'>Solo caricati</option>";
			  html+="<option value='3'>Non conformi</option>";
			html+="</select>";
		html+="</div>";

		html+="<div class='col-sm-3 col-lg-3'>";
			html+="<label class=' for='tipo_cq'>Controlli CQ</label>";
			html+=" <select class='custom-select' id='tipo_cq' onchange=\"elenco_lotti(1)\">";
			  html+="<option selected value='0'>Tutti</option>";
			  html+="<option value='1'>Ricezione Campioni</option>";
			  html+="<option value='2'>No Ricezione campioni</option>";
			  html+="<option value='3'>Controllo Performance</option>";
			  html+="<option value='4'>No Controllo Performance</option>";
			  html+="<option value='5'>Controllo Stato Microbiologico</option>";
			  html+="<option value='6'>No Controllo Stato Microbiologico</option>";
			  html+="<option value='7'>Emissione Certificato</option>";
			  html+="<option value='8'>No Emissione Certificato</option>";
			html+="</select>";
		html+="</div>";

		html+="<div class='col-sm-3 col-lg-3'>";
			html+="<label class=' for='select_ord'>Ordinamento</label>";
			html+=" <select class='custom-select' id='select_ord' onchange=\"elenco_lotti(1)\">";
			  html+="<option value='1'>Dal più vecchio</option>";
			  html+="<option value='2'>Dal più recente</option>";
			html+="</select>";
		html+="</div>";
		

	  html+="</div>";
	  
	    html+="<div class='row mt-2'>";
			html+="<div class='col-sm-4 col-lg-4'>";
					html+="<label class='' for='da_data'>Da data</label>";
					html+="<input type='date' class='form-control' id='da_data' aria-label='da data' aria-describedby='da data'>";
			html+="</div>";
			html+="<div class='col-sm-4 col-lg-4'>";
					html+="<label class='' for='da_data'>Da data</label>";
					html+="<input type='date' class='form-control' id='a_data' aria-label='a data' aria-describedby='a data'>";
			html+="</div>";	   
			html+="<div class='col-sm-4 col-lg-4'>";
				html+="<label class='' for='da_data'>Cerca</label>";
				html+="<button class='btn btn-info btn-block' type='button' id='button-cerca_date' onclick='elenco_lotti(1)'>Cerca</button>";
			html+="</div>";		
			
	    html+="</div>";	

	    if (adminLOTTI=="1") {
			html+="<div class='row mt-3 text-center'>";
				html+="<div class='col-sm-12 col-lg-12'>";
					html+=" <button type='button' id='btn_convalida' class='btn btn-outline-primary' onclick='convalida()'>Procedura Caricamento</button>"; 
					html+=" <button type='button' id='btn_stampa' class='btn btn-success' onclick='stampa()'>Stampa</button>"; 
				html+="</div>";
			html+="</div>";	
			html+="<div class='row mt-3 text-center'>";
				html+="<div class='col-sm-12 col-lg-12' id='div_stampa_pdf'></div>";
			html+="</div>";
		}
	  
	html+="</div>";
	

	  

	html+="<div id='res_archivio' class='mt-2'></div>";
	$("#stampa_lotti").html(html);
	elenco_lotti(1)
	
}



function stampa() {
	$("#btn_stampa").text( "Attendere..." );
	$("#btn_stampa").prop( "disabled", true );
	operazione="stampa";
	tipo=$("#select_tipo").val()
	tipo_view=$("#select_tipo_view").val()
	da_data=$("#da_data").val()
	a_data=$("#a_data").val()	
	if ($("#da_data").val().length!=10) {
		$("#btn_stampa").text( "Stampa" );
		$("#btn_stampa").prop( "disabled", false );

		html=""
		html+="<div class='alert alert-warning mt-3' role='alert'>";
			html+="Definire correttamente la data";
		html+="</div>";
		$("#div_stampa_pdf").html(html);
		return false;
	}
	if (tipo.length==0 || tipo=="0") {
		$("#btn_stampa").text( "Stampa" );
		$("#btn_stampa").prop( "disabled", false );

		html=""
		html+="<div class='alert alert-warning mt-3' role='alert'>";
			html+="Definire correttamente il tipo di prodotto per il quale si vuole la stampa!";
		html+="</div>";
		$("#div_stampa_pdf").html(html);
		return false;
	}
	var url = "ajax.php";
	$.ajax({
		type: "POST",
		url: url,
		data:{operazione:operazione,tipo:tipo,tipo_view:tipo_view,da_data:da_data,a_data:a_data},
		beforeSend:function(){
			html="";
			html+="<div class='spinner-border' role='status'>";
				html+="<span class='sr-only'>Loading...</span>";
			html+="</div>";
			$("#div_stampa_pdf").html(html);
			
		},
		success: function (data){
			$("#btn_stampa").text( "Stampa" );
			$("#btn_stampa").prop( "disabled", false );
			
			html="<a href='stampa.pdf' target='_blank' onclick=\"$('#div_stampa_pdf').empty()\" class='btn btn-primary'>Apri Pdf</a>";
			$("#div_stampa_pdf").html(html);
		}
	});		
}

function convalida() {
	if (!($("#btn_convalida").hasClass( "btn-primary" ))) 	{
		$("#btn_convalida").removeClass( "btn-outline-primary" )
		$("#btn_convalida").addClass( "btn-primary" )
	} else {
		$("#btn_convalida").removeClass( "btn-primary" )
		$("#btn_convalida").addClass( "btn-outline-primary" )
	}
	elenco_lotti(1)
}

function elenco_lotti(page) {
	$("#div_stampa_pdf").empty();
	operazione="elenco_lotti";
	tipo=$("#select_tipo").val()
	tipo_view=$("#select_tipo_view").val()
	tipo_cq=$("#tipo_cq").val()
	da_data=$("#da_data").val()
	a_data=$("#a_data").val()
	select_ord=$("#select_ord").val()
	
	var url = "ajax.php";
	$.ajax({
		type: "POST",
		url: url,
		data:{operazione:operazione,pageno:page,tipo:tipo,da_data:da_data,a_data:a_data,tipo_view:tipo_view,tipo_cq:tipo_cq,select_ord:select_ord},
		beforeSend:function(){
			html="";
			html+="<div class='spinner-border' role='status'>";
				html+="<span class='sr-only'>Loading...</span>";
			html+="</div>";
			$("#res_archivio").html(html);
			
		},
		success: function (data){
			record = jQuery.parseJSON( data );
			html="";
			html+=record.view; //paginazione

			if (record.header.total_rows!=0) {
			
				html+="<span class='badge badge-secondary'>"+record.header.total_rows+"</span> records suddivisi in "+record.header.total_pages+" pagine da 20 prodotti";
				
				
				html+="<div class='list-group mt-2'>";			
				
					for (sca=0;sca<=record.body.length-1;sca++) {
						user_id=record.body[sca].user_id
						operatore=record.body[sca].DBoperatore
						id_lotto=record.body[sca].id
						controllo=record.body[sca].DBcontrollo
						tipo=record.body[sca].DBtipo
						lotto=record.body[sca].DBlotto
						codice=record.body[sca].DBcodice
						prodotto=record.body[sca].DBprodotto
						scadenza=record.body[sca].DBscadenza
						disp=record.body[sca].DBdata
						protocollo=record.body[sca].DBprot
						quantita=record.body[sca].DBquantita
						verifica=record.body[sca].DBverifica
						campioni=record.body[sca].campioni
						save_q=record.body[sca].save_quality
						close_q=record.body[sca].close_quality
						html+=view_row(user_id,operatore,id_lotto,tipo,lotto,codice,prodotto,scadenza,disp,protocollo,quantita,controllo,verifica,campioni,save_q,close_q)	
					}	
				html+="</div>";
			} else {
				html=""
				html+="<div class='alert alert-warning mt-3' role='alert'>";
					html+="Nessun record trovato!";
				html+="</div>";

			}	
			$("#res_archivio").html(html);
		}
	});		
}


function myserialize() {
	data={}
	$(".code_to_reserve").each(function() {
		elem=this.id
		id=elem.split("_")[1]		
		value=$( "#"+elem ).val()
		if (value=="1") {
			codice=$("#codice_"+id).val();
			prodotto=$("#prodotto_"+id).val();
			qta=$("#quantita_"+id).val();
			alternativa=$("#alternativa_"+id).val();
			pro=$("#protocollo_"+id).val();
			scadenza=$("#scadenza_"+id).val();
			disp=$("#disp_"+id).val();
			marcatura_ce=$("#marcatura_ce_"+id).val()
			range_temp=$("#range_temp_"+id).val()
			sigla_custom=$("#sigla_custom_"+id).val()
			descrizione_custom=$("#descrizione_custom_"+id).val()
			campioni=$("#campioni_"+id).val()
			
			ord=$("#span_badge"+id).html()
			data[ord]={}
			data[ord].codice=codice
			data[ord].prodotto=prodotto
			data[ord].qta=qta
			data[ord].alternativa=alternativa
			data[ord].pro=pro
			data[ord].scadenza=scadenza
			data[ord].disp=disp
			data[ord].marcatura_ce=marcatura_ce
			data[ord].range_temp=range_temp
			data[ord].sigla_custom=sigla_custom
			data[ord].descrizione_custom=descrizione_custom
			data[ord].campioni=campioni
		}
		
	})
	return data
}

function submit_multi(){
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
			event.preventDefault();
			event.stopPropagation();
			html="";
			html+="<div class='alert alert-warning' role='alert'>";
				html+="Valorizzare correttamente i campi evidenziati";
			html+="</div>";
			$("#wait_prenota").html(html);	
			$("#wait_prenota").show();						
		  
        } else {
			event.preventDefault();
			data=myserialize();
			tipo=$("#tipo_prodotto").val();
			date_next=$("#date_next").val()
			lotto_m=$("#lotto_m").val()
			operazione="save_multi"
			var url = "ajax.php";

			$("#btn_prenota").addClass( "btn-outline-success" );
			$("#btn_prenota").removeClass( "btn-success" );
			$("#btn_prenota").prop( "disabled", true );
			
			$.ajax({
				type: "POST",
				url: url,
				//data:  $( "#form_main_input" ).serialize(),
				data:{tipo:tipo,date_next:date_next,dati:data,lotto_m:lotto_m,operazione:operazione},
				beforeSend:function(){
					html="";
					html+="<div class='spinner-border' role='status'>";
						html+="<span class='sr-only'>Loading...</span>";
					html+="</div>";
					$("#wait_prenota").html(html)
					$("#wait_prenota").show();
				},
				success: function (data){
					record = jQuery.parseJSON( data );
					if (record.header.error=="PROT_IN_USE") {
						html="";
						html+="<div class='alert alert-warning' role='alert'>";
						  html+=record.header.msg_error;
						html+="</div>";
						$("#wait_prenota").html(html);
						$("#btn_prenota").addClass( "btn-success" );
						$("#btn_prenota").removeClass( "btn-outline-success" );
						$("#btn_prenota").prop( "disabled", false );						
						return;
					}
					
					tipo=$("#tipo_prodotto").val();
					record = jQuery.parseJSON( data );
					verifica="";save_q="";close_q="";
					html="";
					html+="<div class='list-group'>";
						$.each(record.infolotti, function(key, value){
							user_id=record.infolotti[key].user_id
							operatore=record.infolotti[key].operatore
							controllo=record.infolotti[key].alternativa
							id_lotto=record.infolotti[key].id
							codice=record.infolotti[key].codice
							prodotto=record.infolotti[key].prodotto
							scadenza=record.infolotti[key].scadenza
							disp=record.infolotti[key].disp
							protocollo=record.infolotti[key].DBprot
							quantita=record.infolotti[key].DBquantita
							campioni=record.infolotti[key].campioni
							
							html+=view_row(user_id,operatore,id_lotto,tipo,key,codice,prodotto,scadenza,disp,protocollo,quantita,controllo,verifica,campioni,save_q.close_q)
						});
					html+="</div>";	
					
					$("#stampa_lotti").html(html)
					$("#wait_prenota").hide();
					$('#modal_resp').modal('hide')
					$("#date_next").val('');
					$("#tipo_prodotto").val('');
					$("#codice").val('')
					
				}
			});
		
			
		}
        form.classList.add('was-validated');
      }, false);
    });

}

function crea_etic(tipo,lotto,codice,descr,data_p,scadenza) {
	operazione="crea_etic";
	var url = "etic.php";
	$.ajax({
		type: "POST",
		url: url,
		data:{operazione:operazione,lotto:lotto,codice:codice,descr:descr,data_p:data_p,scadenza:scadenza},
		beforeSend:function(){
			html="";
			html+="<div class='spinner-border' role='status'>";
				html+="<span class='sr-only'>Loading...</span>";
			html+="</div>";
			div_ref="div_lbl_small";
			if (tipo==2) div_ref="div_lbl_large";
			if (tipo==3) div_ref="div_lbl_solution";
			$("#"+div_ref).html(html)
			$("#"+div_ref).show();
			
		},
		success: function (data){

			data=data.trim()
			div_ref="div_lbl_small";lbl="label/small"+data+".pdf"
			if (tipo==2) {div_ref="div_lbl_large";lbl="label/large"+data+".pdf"}
			if (tipo==3) {div_ref="div_lbl_solution";lbl="label/solution"+data+".pdf"}
			
			html="<a href='"+lbl+"' target='_blank' class='btn btn-primary'>Apri etichetta</a>";
			//html="<a href='"+lbl+"' target='_blank'>Apri etichetta</a>";
			$("#"+div_ref).html(html)
			

		}
	});	
}

function elimina_lotto(id_lotto) {
	$("#btn_canc_lotto").text( "Attendere..." );
	$("#btn_annulla_canc_lotto").prop( "disabled", true );
	$("#btn_canc_lotto").prop( "disabled", true );
	operazione="cancella_lotto";
	var url = "ajax.php";
	$.ajax({
		type: "POST",
		url: url,
		data:{operazione:operazione,id_lotto:id_lotto},
		beforeSend:function(){

		},
		success: function (data){
			if (data.trim()=="OK") {
				html="";
				html+="<div class='alert alert-success' role='alert'>";
				  html+="<b>Lotto Cancellato con successo!</b>";
				html+="</div>";
				$("#div_body_main").html(html)
				$( "#lottoRow"+id_lotto).css('text-decoration', 'line-through');
				dele_classi(id_lotto)
				$( "#lottoRow"+id_lotto).addClass( "bg-warning text-white" );
				$( "#lottoRow"+id_lotto).addClass( "disabled" );
			} else {
				html="";
				html+="<div class='alert alert-warning' role='alert'>";
				  html+="<b>Problema occorso durante la cancellazione!</b>";
				html+="</div>";
				$("#div_body_main").html(html)
			}
			

		}
	});	
}

function dele_classi(id_lotto) {
	classi=$("#lottoRow"+id_lotto).attr("class").split(/\s+/);
	for (sca=0;sca<=classi.length-1;sca++) {
		classe=classi[sca]
		if (classe!="list-group-item" && classi!="list-group-item-action") {
			$("#lottoRow"+id_lotto).removeClass( classe );			
		}
	}	
}

function check_modifica() {
	var forms = document.getElementsByClassName('needs-validation_edit');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
		$("#resp_oper_edit").empty(); 
        if (form.checkValidity() === false) {
			event.preventDefault();
			event.stopPropagation();
        } else {
			event.preventDefault();
			salva_modifica()	
			
		}
        form.classList.add('was-validated');
      }, false);
    });
}

function salva_modifica() {
	userID=$("#userID").val();
	operazione="salva_modifica";
	E_id_lotto=$("#E_id_lotto").val();
	E_codice=$("#E_codice").val();
	E_quantita=$("#E_quantita").val();
	E_protocollo=$("#E_protocollo").val();
	E_campioni=$("#E_campioni").val();
	E_lotto=$("#lotto_edit").val();	
	E_tipo=$("#tipo_edit").val();	

	
	$("#resp_oper_edit").empty();
	var url = "ajax.php";
	$.ajax({
		type: "POST",
		url: url,
		data:{operazione:operazione,E_tipo:E_tipo,E_lotto:E_lotto,E_id_lotto:E_id_lotto,E_codice:E_codice,E_quantita:E_quantita,E_protocollo:E_protocollo,E_campioni:E_campioni},
		beforeSend:function(){
			$("#btn_edit").text("Attendere...")
			$("#btn_edit").prop("disabled",true)
		},
		success: function (data){
			$("#btn_edit").text("Salva modifica")
			$("#btn_edit").prop("disabled",false)
			record = jQuery.parseJSON( data );
			resp=record.header.resp
			if (resp=="OK") {
				html="";
				html+="<div class='alert alert-success' role='alert'>";
				  html+="Modifica Effettuata con successo";
				html+="</div>";
				$("#resp_oper_edit").html(html);
					//rifletto la modifica su una unica row passando i dati appena editati
					html="";
					html+="<div class='list-group'>";
						user_id=userID
						tipo=1
						key=E_lotto
						verifica=0
						controllo=""
						save_q=""
						close_q="";
						id_lotto=E_id_lotto
						codice=E_codice
						prodotto=record.body.descrizione
						scadenza=record.body.scadenza
						disp=record.body.disp
						protocollo=E_protocollo
						quantita=E_quantita
						campioni=E_campioni
						html+=view_row(user_id,operatore,id_lotto,tipo,key,codice,prodotto,scadenza,disp,protocollo,quantita,controllo,verifica,campioni,save_q,close_q)
					html+="</div>";	
					$("#stampa_lotti").html(html)	
					if ($("#lotto_rapido").val()>0) cercalotto()
					
			} else {
				if (record.header.cod_err.length!=0) {
					html="";
					html+="<div class='alert alert-warning' role='alert'>";
					  html+=record.header.error;
					html+="</div>";
					$("#resp_oper_edit").html(html);					
				} else {	
					html="";
					html+="<div class='alert alert-warning' role='alert'>";
					  html+="Problema occorso durante la modifica";
					html+="</div>";
					$("#resp_oper_edit").html(html);
				}
			}

		}
	});	

}

function cerca_codice_edit() {
	operazione="edit_lotto";
	E_id_lotto=$("#E_id_lotto").val();
	E_codice=$("#E_codice").val();
	E_quantita=$("#E_quantita").val();
	E_protocollo=$("#E_protocollo").val();
	$("#resp_oper_edit").empty();
	$("#btn_edit").prop("disabled",true)
	var url = "ajax.php";
	$.ajax({
		type: "POST",
		url: url,
		data:{operazione:operazione,E_codice:E_codice},
		beforeSend:function(){
			$("#div_resp_code").empty();
			$("#btn_cerca_cod").text("Attendere...")
			$("#btn_cerca_cod").prop("disabled",true)
		},
		success: function (data){
			$("#btn_cerca_cod").text("Cerca codice")
			$("#btn_cerca_cod").prop("disabled",false)
			record = jQuery.parseJSON( data );
			resp=record.header.resp
			num_rec=record.header.num_rec
			if (resp=="OK") {
				$("#div_campioni").hide()
				console.log("campioni",record.body.campioni)
				if (record.body.campioni=="S") $("#div_campioni").show()
				else $("#E_campioni").val('');
				if (parseInt(num_rec)>0) {
					html="";
					html+="<small>"+record.body.descrizione+"</small>"
					
					$("#div_resp_code").html(html);
					$("#btn_edit").prop("disabled",false)
				}	
				else {
					html="";
					html+="<div class='alert alert-warning mt-3' role='alert'>";
					  html+="Codice non trovato!";
					html+="</div>";
					$("#div_resp_code").html(html);
				}	
			} else {
					html="";
					html+="<div class='alert alert-warning mt-3' role='alert'>";
					  html+="Problema occorso durante la ricerca!";
					html+="</div>";
					$("#div_resp_code").html(html);				
			}

		}
	});	
}

function edit_lotto(tipo,id_lotto,lotto,codice,prodotto,quantita,protocollo,campioni) {
	html="";
	html+="<form class='needs-validation_edit' novalidate>";
		html+="<input type='hidden' id='lotto_edit' value='"+lotto+"'>";
		html+="<input type='hidden' id='tipo_edit' value='"+tipo+"'>";

		html+="<div class='form-row'>";
			html+="<input type='hidden' id='E_id_lotto' value="+id_lotto+">";
			html+="<div class='col-md-3 mb-3'>";
				html+="<label for='E_quantita'>Codice</label>";
				html+="<input class='form-control' placeholder='(codice attuale:"+codice+")' id='E_codice'  aria-label='Codice' aria-describedby='codice' required value='"+codice+"'>";
				html+="<div id='div_resp_code'></div>";
			html+="</div>";

			html+="<div class='col-md-3 mb-3'>";
				html+="<label for='E_quantita'>Quantità</label>";
				html+="<input class='form-control' placeholder='(quantità attuale:"+quantita+")' id='E_quantita'  aria-label='Quantita' aria-describedby='qta' required value='"+quantita+"'>";
			html+="</div>";

			html+="<div class='col-md-3 mb-3'>";
				html+="<label for='E_protocollo'>Protocollo</label>";
				html+="<input class='form-control prot' placeholder='(protocollo attuale:"+protocollo+")' id='E_protocollo' aria-label='Protocollo' aria-describedby='protocollo' required value='"+protocollo+"'>";
			html+="</div>";
			
			console.log("campioni",campioni)
			camp_disp="block";
			if (!campioni || campioni.length==0 || campioni=="null" || campioni=="NULL") {
				campioni="";camp_disp="none"
			}	
			
			
			html+="<div class='col-md-3 mb-3' id='div_campioni' style='display:"+camp_disp+"'>";
				html+="<label for='E_campioni'>Campioni</label>";
				html+="<input class='form-control' placeholder='(campioni attuali:"+campioni+")' id='E_campioni' aria-label='Campioni' aria-describedby='campioni'  value='"+campioni+"'>";
			html+="</div>";
		
	html+="</div>";	
	
	html+="<div class='mt-3 mb-4'>";
		html+="<button class='btn btn-primary' type='button' onclick='cerca_codice_edit()' id='btn_cerca_cod'>Cerca codice</button>";
		html+="<button class='btn btn-success ml-3' type='submit' id='btn_edit' disabled>Salva modifica</button>";
	html+="</div>";
	html+="<div class='mt-3' id='resp_oper_edit'></div>";

	html+="</form>";
	$("#div_body_main").html(html)
	check_modifica();
}

function close_control(class_rd,class_rd_c,lotto) {
	if (!confirm("Sicuri di concludere il controllo? L'operazione è irreversibile!")) return false;
	req_all=true
	//verifica se i check sono stati apposti
	for (var key in class_rd) {		
		req=false;
		$("."+key).each(function() {
			if ($(this).prop( "checked" )) req=true
		})
		if (req==false) {
			req_all=false
			break;
		}
	}	
	if (req_all==false) {
		alert("Attenzione! Il controllo non può essere chiuso senza stabilire le conformità/non conformità richieste")
		return false
	}

	//verifica se sono state apposte le date
	for (var key in class_rd_c) {
		req=false;
		$("."+key).each(function() {
			id_ref=this.id
			console.log("id_ref",id_ref,"len",$(this).val().length)
			if ($(this).val().length!=0) req=true;
		})
		if (req==false) {
			req_all=false
			break;
		}
	}	
	
	
	if (req_all==false) {
		alert("Attenzione! Il controllo non può essere chiuso senza valorizzare le date delle conformità/non conformità richieste")
		return false
	}

	
	ids="";
	$(".ck_all").each(function() {
		if ($( this ).prop( "checked" )) {
			id_ref=this.id
			id = id_ref.replace("ck", "");
			if (ids.length!=0) ids+=";"
			ids+=id
		}
	})
	
	operazione="close_control";
	var url = "ajax.php";
	$.ajax({
		type: "POST",
		url: url,
		data:{operazione:operazione,lotto:lotto,ids:ids},
		beforeSend:function(){
			html="";
			html+="<div class='alert alert-info' role='alert'>";
				html+="<div class='text-center'>";
				  html+="<div class='spinner-border' role='status'>";
					html+="<span class='sr-only'>Loading...</span>";
				  html+="</div>";
				html+="</div>";
			html+="</div>";
			$("#w_save").html(html);
		},
		success: function (data){
			$("#w_save").empty();
			record = jQuery.parseJSON( data );
			
			if (!record || record.header!="OK") 
				alert("Problemi occorsi durante il salvataggio")
			else {
				$("#lotto_rapido").val(lotto);
				cercalotto()
				$("#lotto_rapido").val('');
				$('#modalStampe').modal('hide')
			}	
			
		}
	});	
	
}

function control_check(id) {
	check=false
	for (var key in class_rd) {		
		req=false;
		num=0;num_true=0
		$("."+key).each(function() {
			num++
			if ($(this).prop( "checked" )) {
				num_true++
			}	
		})
		if (num_true==num && num_true!=1) {
			check=true
			break;
		}
	}	
	if (check==true) {
		$("#"+id).prop( "checked", false )
		alert("Attenzione! Non puoi selezionare entrambi i check!");
	}

}

function save_quality(lotto,codice,class_rd,class_rd_c) {
	ids="";
	d_check="";
	$("#lotto_rapido").val(lotto)
	$("#select_tipo_ric").val('1')
	$(".ck_all").each(function() {
		if ($( this ).prop( "checked" )) {
			id_ref=this.id
			id_data = id_ref.replace("ck", "data_c");
			id = id_ref.replace("ck", "");
			if (ids.length!=0) ids+=";"
			
			ids+=id+"|"+$("#"+id_data).val();
			
		}
	})

	console.log("ids",ids,"lotto",lotto,"codice",codice);
	operazione="save_quality";
	var url = "ajax.php";
	$.ajax({
		type: "POST",
		url: url,
		data:{operazione:operazione,lotto:lotto,codice:codice,ids:ids},
		beforeSend:function(){
			html="";
			html+="<div class='alert alert-info' role='alert'>";
				html+="<div class='text-center'>";
				  html+="<div class='spinner-border' role='status'>";
					html+="<span class='sr-only'>Loading...</span>";
				  html+="</div>";
				html+="</div>";
			html+="</div>";
			$("#w_save").html(html);
		},
		success: function (data){
			$("#w_save").empty();
			record = jQuery.parseJSON( data );
			if (class_rd=="0") {
				if (!record || record.header!="OK") 
					alert("Problemi occorsi durante il salvataggio")
				else {
					cercalotto()
					//alert("Dati salvati con successo!")
				}
			} else close_control(class_rd,class_rd_c,lotto)
		}
	});			
	
		
	
}

function qualita(close_quality,lotto,id_lotto,codice,verifica) {	
	html="";
	operazione="load_qualita";
	var url = "ajax.php";
	$.ajax({
		type: "POST",
		url: url,
		data:{operazione:operazione,lotto:lotto,id_lotto:id_lotto,codice:codice},
		beforeSend:function(){
			html="";
			html+="<div class='alert alert-info' role='alert'>";
				html+="<div class='text-center'>";
				  html+="<div class='spinner-border' role='status'>";
					html+="<span class='sr-only'>Loading...</span>";
				  html+="</div>";
				html+="</div>";
			html+="</div>";
			$("#div_body_main").html(html);
		},
		success: function (data){
			record = jQuery.parseJSON( data );
			class_rd={};class_rd_c={}
			$.each(record, function(key, value){
				class_required=record[key].class_required
				if (class_required.length!=0) {
					class_rd[class_required]=class_required
					class_rd_c[class_required+"_D"]=class_required+"_D"
				}
			})	
										
			dis=""
			if (close_quality=="1") dis="disabled";
			html="";
			html+="<div class='alert alert-info' role='alert'>";
				html+="Elenco controlli da effettuare associati al lotto<hr>";
			
				html+="<div id='wait_save' 	style='display:none;max-height:150px;overflow:auto'>";
				html+="</div>";
				if (close_quality=="1") {
						html+="<p class='font-weight-bold'>Procedura di controllo conclusa</p>";
				} else {
						save="";
						save+="<button class='btn btn-primary' type='submit' onclick=\"save_quality('"+lotto+"','"+codice+"','0','0')\" id='btn_save_quality'>Salva</button> ";
				
						if (verifica=="C") {
							save+="<button class='btn btn-success' type='submit' onclick=\"save_quality('"+lotto+"','"+codice+"',class_rd,class_rd_c)\" id='btn_close_quality'>Chiudi controllo</button>";
							save+=" <span id='w_save'></span>";
						}	
						$("#div_modal_save").html(save)
				}
				
			html+="</div>";
			
			html+="<table class='table table-hover'>";
				class_old="?"
				elem=0;
				$.each(record, function(key, value){
					elem++;
					check=record[key].check
					lbl_check=record[key].lbl_check
					
					check_id=record[key].check_id
					conformita=record[key].conformita
					class_required=record[key].class_required
					
					if (class_old!=class_required) {
						if (elem>1) {
								html+="</td>";
							html+="</tr>";
						}

						html+="<tr>";
							html+="<td>"+check+"</td>";
							html+="<td>"
								html+="<input type='date' class='data_check_all "+class_required+"_D' id='data_c"+key+"' value='"+record[key].data_check+"'>"
							html+="</td>";
							html+="<td>";
					}	
					value_check="";
					if (check_id && check_id.length!=0) value_check="checked";
					html+=" <div class='form-check'>";
					  html+="<input class='form-check-input ck_all "+class_required+"' type='checkbox' "+value_check+" id='ck"+key+"' "+dis+" onchange=\"control_check('ck"+key+"')\">";
					  html+="<label class='form-check-label' for='ck_value1'>"+lbl_check+"</label>";
					html+="</div>";
					
					
					
					console.log("check",check);

					class_old=class_required
				})
					html+="</td>";
				html+="</tr>";				
			html+="</table>";	
			html+="</html>";	
				
			
			$("#div_body_main").html(html);
			
		}
	});		
}

function check_qualita(lotto,id_lotto,codice) {
	operazione="check_qualita"
	var url = "ajax.php";
	$.ajax({
		type: "POST",
		url: url,
		data:{operazione:operazione,codice:codice,lotto:lotto},
		beforeSend:function(){
		},
		success: function (data){
			record = jQuery.parseJSON( data );
			$("#wait_extra").remove()
			if (record.resp=="S") {
				html=""
				html+="<div class='col-sm-4'>";
					html+="<div class='card h-100'>";
					  html+="<div class='card-body'>";
						html+="<h5 class='card-title'>Controllo Qualità</h5>";
						html+="<p class='card-text text-justify'>Operazione riservata ad utenza Administrator.</p>";
						html+="<div class=''><a href='javascript:void(0)' onclick=\"qualita('"+record.close_quality+"','"+lotto+"','"+id_lotto+"','"+codice+"','"+record.verifica+"')\" class='btn btn-primary'>Procedi</a></div>";
					  html+="</div>";
					html+="</div>";
				html+="</div>";
				$("#div_extra_option").prepend(html)
			}

		}
	});		
}


function load_etic(tipo,user_id,id_lotto,key,codice,prodotto,scadenza,data_p,quantita,protocollo,campioni) {
  
  check_qualita(key,id_lotto,codice)	
  userID=$("#userID").val()
  adminLOTTI=$("#adminLOTTI").val()
  html="";
  html+="<div class='modal fade' id='modalStampe' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>";
    html+="<div class='modal-dialog modal-xl' role='document'>";
      html+="<div class='modal-content'>";
        html+="<div class='modal-header'>";
			html+="<div class='container alert alert-primary' role='alert'>";
				html+="<h3 class='modal-title'>Lotto: "+key+"</h3>";
				//html+=" - Codice: "+codice+"</h5>";
			html+="</div>";
          html+="<button class='close' type='button' data-dismiss='modal' aria-label='Close'>";
            html+="<span aria-hidden='true'>×</span>";
          html+="</button>";
        html+="</div>";        
		
		html+="<div id='div_body_main' class='container mt-3'>";
			html+="<div class='modal-body'>";
				html+="<div class='row'>";
				  html+="<div class='col-sm-4'>";
					html+="<div class='card  h-100' id='card_elimina'>";
					  html+="<div class='card-body'>";
						html+="<h5 class='card-title'>Cancella Lotto</h5>";
						html+="<p class='card-text text-justify'>Solo chi ha impegnato il lotto può effettuare questa operazione (oppure un utente con credenziali di Administrator). L'operazione è anche reversibile ma a carico dell'amministratore di sistema</p>";
						html+=" <button onclick=\"$('#div_conferma_canc_lotto').show()\" class='btn btn-primary' type='button'>Cancella Lotto</button>";						
						
						html+="<div id='div_conferma_canc_lotto' class='mt-3' style='display:none'>";
							js="";
							if (user_id==userID || adminLOTTI=="1") js="onclick=\"elimina_lotto("+id_lotto+")\""
							html+="<button "+js+" class='btn btn-success' type='button' id='btn_canc_lotto'>";
								html+=" Conferma";
							html+="</button>";
							
							html+=" <button onclick=\"$('#div_conferma_canc_lotto').hide()\" class='btn btn-secondary' type='button' id='btn_annulla_canc_lotto'>Annulla</button>";
						html+="</div>";
					  html+="</div>";
					html+="</div>";
				  html+="</div>";
				  
				  html+="<div class='col-sm-4'>";
					html+="<div class='card h-100' id='card_edit'>";
					  html+="<div class='card-body'>";
						html+="<h5 class='card-title'>Modifica Dati</h5>";
						html+="<p class='card-text text-justify'>Solo chi ha impegnato il lotto ha facoltà di modificare Codice, Quantità, Protocollo</p>";
						js="";

						if (user_id==userID || adminLOTTI=="1") 
							js="onclick=\"edit_lotto("+tipo+","+id_lotto+",'"+key+"','"+codice+"','"+prodotto+"','"+quantita+"','"+protocollo+"','"+campioni+"')\"";
						html+=" <button "+js+" class='btn btn-primary' type='button'>Modifica dati</button>";
					  html+="</div>";
					html+="</div>";
				  html+="</div>";
				 
				 html+="<div class='col-sm-4'>";
					html+="<div class='card h-100'>";
					  html+="<div class='card-body'>";
						html+="<h5 class='card-title'>Stampa Etichette</h5>";
						html+="<p class='card-text text-justify'>Creazione file di stampa per le modalità: Piccola, Grande, Soluzioni.</p>";
						html+="<div class=''><a href='javascript:void(0)' onclick=\"$('#div_print_etic').toggle();\" class='btn btn-primary'>Stampa Etichette</a></div>";
					  html+="</div>";
					html+="</div>";
				  html+="</div>";
				html+="</div>";
				
				if (adminLOTTI=="1") {
					html+="<div class='row mt-2' id='div_extra_option'>";
						html+="<div id ='wait_extra' class='spinner-border' role='status'>";
							html+="<span class='sr-only'>Loading...</span>";
						html+="</div>";					
						html+="<div class='col-sm-4'>";
							html+="<div class='card h-100'>";
							  html+="<div class='card-body'>";
								html+="<h5 class='card-title'>Log operazioni</h5>";
								html+="<p class='card-text text-justify'>Operazione riservata ad utenza Administrator.<br>Elenco operazioni effettuate sul lotto impegnato: creazione, modifica, cancellazione (Chi ha fatto cosa e quando)</p>";
								html+="<div class=''><a href='javascript:void(0)' onclick=\"log_eventi("+id_lotto+",'"+codice+"')\" class='btn btn-info'>Vai al Log eventi</a></div>";
							  html+="</div>";
							html+="</div>";
						html+="</div>";

					html+="</div>";
				}					
				
				
				
				

				
			html+="</div>";
			
			html+="<div class='modal-body' style='display:none' id='div_print_etic'>";
				html+="<div class='list-group'>";
					html+="<a href='javascript:void(0)' onclick=\"crea_etic(1,'"+key+"','"+codice+"','"+prodotto+"','"+data_p+"','"+scadenza+"')\" class='list-group-item list-group-item-action list-group-item-secondary'>";
						html+="<div class='d-flex w-100 justify-content-between'>";
							html+="<h5 class='mb-1'>"+key+"</h5>";
							html+="<small>"+codice+"</small>";
						html+="</div>";
						html+="<p class='mb-1'>"+prodotto+"</p>";
						html+="<strong><font color='blue'>Crea Etichetta Piccola</font></strong>";
					html+="</a>";
					html+="<p class='mt-1 mb-2' style='display:block' id='div_lbl_small'></p>";

					html+="<a href='javascript:void(0)' onclick=\"crea_etic(2,'"+key+"','"+codice+"','"+prodotto+"','"+data_p+"','"+scadenza+"')\" class='list-group-item list-group-item-action list-group-item-secondary'>";
						html+="<div class='d-flex w-100 justify-content-between'>";
							html+="<h5 class='mb-1'>"+key+"</h5>";
							html+="<small>"+codice+"</small>";
						html+="</div>";
						html+="<p class='mb-1'>"+prodotto+"</p>";
						html+="<strong><font color='blue'>Crea Etichetta Grande</font></strong>";
					html+="</a>";
					html+="<p class='mt-1 mb-2' style='display:block' id='div_lbl_large'></p>";
					
					html+="<a href='javascript:void(0)' onclick=\"crea_etic(3,'"+key+"','"+codice+"','"+prodotto+"','"+data_p+"','"+scadenza+"')\" class='list-group-item list-group-item-action list-group-item-secondary'>";
						html+="<div class='d-flex w-100 justify-content-between'>";
							html+="<h5 class='mb-1'>"+key+"</h5>";
							html+="<small>"+codice+"</small>";
						html+="</div>";
						html+="<p class='mb-1'>"+prodotto+"</p>";
						html+="<strong><font color='blue'>Crea Etichetta Soluzioni</font></strong>";
					html+="</a>";
					html+="<p class='mt-1' style='display:none' id='div_lbl_solution'></p>";
					
				html+="</div>";
			html+="</div>";
		html+="</div>";	
		
		
		
        html+="<div class='modal-footer'>";     
			html+="<div id='div_modal_save'></div>";
			html+="<button class='btn btn-secondary' type='button' data-dismiss='modal' onclick=\"$('#div_modal_save').empty()\">Chiudi</button>";
        html+="</div>";
      html+="</div>";
    html+="</div>";
  html+="</div>";
  $("#div_modal_generic").html(html)

  if (user_id!=userID && adminLOTTI!="1") {
	$("#card_elimina").find('*').prop('disabled', true); 
	$("#card_edit").find('*').prop('disabled', true); 
  }
  $("#modalStampe").modal("show");

}

function log_eventi(id_lotto) {	
	html="";
	operazione="log_eventi";
	var url = "ajax.php";
	$.ajax({
		type: "POST",
		url: url,
		data:{operazione:operazione,id_lotto:id_lotto,tipo:"lotti"},
		beforeSend:function(){
			html="";
			html+="<div class='alert alert-info' role='alert'>";
				html+="<div class='text-center'>";
				  html+="<div class='spinner-border' role='status'>";
					html+="<span class='sr-only'>Loading...</span>";
				  html+="</div>";
				html+="</div>";
			html+="</div>";
			$("#div_body_main").html(html);
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

			$("#div_body_main").html(html);
		}
	});		
}



function load_modal() {
	html="";

	html+="<form class='needs-validation' novalidate id='form_main_input' autocomplete='off'>";
		html+="<div class='modal-content container'>";
			html+="<div class='modal-header'>";
				html+="<div id='div_lbl_codici' class='container-fluid'>";
					html+="<h5 class='modal-title'>Codice individuato</h5>";
				html+="</div>";
			  html+="<button class='close' type='button' data-dismiss='modal' aria-label='Close'>";
				html+="<span aria-hidden='true'>×</span>";
			  html+="</button>";
		html+="</div>";
		
		html+="<div class='modal-body' id='body_resp'>";
			html+="<div class='text-center'>";
			  html+="<div class='spinner-border' role='status'>";
				html+="<span class='sr-only'>Loading...</span>";
			  html+="</div>";
			html+="</div>";
		html+="</div>";
			html+="<div class='modal-footer' id='footer_resp'>";
			html+="</div>";
		html+="</div>";
	html+="</form>";
	return html	
}
function cerca_codice() {
	tipo_prodotto=$("#tipo_prodotto").val();
	codice=$("#codice").val();
	if (codice.length==0) return false;
	tipo=1
	char_cod=codice.substr(0,1);
	if (char_cod=="0" || char_cod=="1" || char_cod=="2" || char_cod=="3" || char_cod=="4" || char_cod=="5" || char_cod=="6" || char_cod=="7" || char_cod=="8" || char_cod=="9") tipo=2;

	if (tipo_prodotto!=tipo) {
		html=load_modal()
		$("#resp_body").html(html);
		html=""
		html+="<div class='alert alert-warning' role='alert'>";
		  html+="Il tipo di prodotto specificato non è concorde con il codice specificato!";
		html+="</div>";
        html+="<div class='modal-footer'>";
          html+="<button class='btn btn-secondary' type='button' data-dismiss='modal'>Chiudi</button>";
        html+="</div>";
		$("#body_resp").html(html);
		$('#modal_resp').modal('show')
		return false;
	}
	html="Dopo aver creato il lotto (o famiglia di lotti), in questa area saranno disponibili le etichette pronte da stampare.	";
	$("#stampa_lotti").html(html);
	
	html=load_modal()
	$("#resp_body").html(html);
	$('#modal_resp').modal('show')
	load_codici()
	selcod.num_sel = 0;
	
}

function prenotazione() {
	//il submit effettivo è in submit_multi()
	
	$(".code_to_reserve").each(function() {
		elem=this.id
		id=elem.split("_")[1]		
		value=$( "#"+elem ).val()
		if (value=="1") {
			$("#quantita_"+id).attr("required", true);
			$("#protocollo_"+id).attr("required", true);			
			$("#quantita_"+id).addClass( "form-control" );
			$("#protocollo_"+id).addClass( "form-control" );
		} else {
			$("#quantita_"+id).attr("required", false);
			$("#protocollo_"+id).attr("required", false);
			$("#quantita_"+id).removeClass( "form-control" );
			$("#protocollo_"+id).removeClass( "form-control" );
		}			

	})
  		
}

function selcod(id,all_sel) {
	if ( typeof selcod.num_sel == 'undefined' ) selcod.num_sel = 0;
	elem="#btnconfirm_"+id

	value=$( elem ).val()
	if (all_sel=="NO") value=1;
	if (all_sel=="SI") value=0;
	if (value==0) {
		selcod.num_sel++;
		$("#quantita_"+id).attr("required", true);
		$("#protocollo_"+id).attr("required", true);
		$("#quantita_"+id).addClass( "form-control" );
		$("#protocollo_"+id).addClass( "form-control" );		
		
		$("#span_confirm"+id).html("Da prenotare");
		
		$("#span_badge"+id).addClass("badge badge-light");
		$("#span_badge"+id).html(selcod.num_sel);

		$( elem ).val(1)
		$( elem ).attr("title",selcod.num_sel);
		$( elem ).addClass( "btn-primary" );		
		$( elem).removeClass( "btn-outline-secondary" );
	} else {
		//selcod.num_sel--;
		//if (selcod.num_sel<0) selcod.num_sel=0;
		$("#quantita_"+id).removeAttr("required", false);
		$("#protocollo_"+id).removeAttr("required");
		$("#quantita_"+id).removeClass( "form-control" );
		$("#protocollo_"+id).removeClass( "form-control" );		

		
		$( elem ).attr("title","");
		
		$("#span_confirm"+id).html("Seleziona");
		$("#span_badge"+id).removeClass("badge badge-light");
		$("#span_badge"+id).html("");
		
		$( elem ).val(0)
		$( elem ).addClass( "btn-outline-secondary" );		
		$( elem).removeClass( "btn-primary" );
		
		arr=[]
		$(".code_to_reserve").each(function() {
			id=this.id
			title=this.title
			arr[title]=id
			//arr.id=title
		})	


		arr.sort(function(a, b){return a.title - b.title}); 



		sca=0
		for(var key in arr) {
			if (key.length!=0) {
				id=arr[key].split("_")[1]
				sca++
				$("#"+arr[key]).attr("title",sca);	
				$("#span_badge"+id).html(sca);
			}
		}
		selcod.num_sel=sca;
	}
	if (selcod.num_sel>0) {
		$("#btn_prenota").addClass( "btn-success" );
		$("#btn_prenota").removeClass( "btn-outline-success" );
		$("#btn_prenota").prop( "disabled", false );
	} else {
		$("#wait_prenota").empty();
		$("#btn_prenota").addClass( "btn-outline-success" );
		$("#btn_prenota").removeClass( "btn-success" );
		$("#btn_prenota").prop( "disabled", true );
	}

	
}


function selall(value) {
	all_sel="NO"
	selcod.num_sel = 0;

	if (value==0) {		
		all_sel="SI"
		$("#sel_all").val(1)
		$("#sel_all").addClass( "btn-success" );
		$("#sel_all").removeClass( "btn-outline-success" );

	} else {
		all_sel="NO"
		$("#sel_all").val(0)
		$("#sel_all").addClass( "btn-outline-success" );
		$("#sel_all").removeClass( "btn-success" );

	}
	

	$(".code_to_reserve").each(function() {
		elem=this.id
		id=elem.split("_")[1]
		selcod(id,all_sel)
	})	
	
}

function selcod_single() {
	if ( typeof load_codici.info_single_code == 'undefined' ) return false;
	
	descrizione=load_codici.info_single_code[0].descrizione
	$('#modal_resp').modal('hide')
}

function update_qta(id) {
	qta=$("#quantita_"+id).val()
	$.each($('.qta'),function(){
		id_curr=this.id.split("_")[1]
		if (id_curr>id) $("#quantita_"+id_curr).val(qta)
	})
}

function update_protocollo(id) {
	prot=$("#protocollo_"+id).val()
	$.each($('.prot'),function(){
		id_curr=this.id.split("_")[1]
		if (id_curr>id) $("#protocollo_"+id_curr).val(prot)
	})
}

function prot_incr(id) {
   prot=$("#protocollo_"+id).val()
   if (prot.split("-").length<3) {
		return false;
   }
   var stat_prot=prot.split("-")[0]+"-"+prot.split("-")[1]+"-";		 	
   var ris=prot.split("-")[2];		 
   
   
	fl=0;
	if (prot.length==0) fl=1;
	$.each($('.prot'),function(){
		id_curr=this.id.split("_")[1]
		if (id_curr>id)  {
			if (fl==1) $("#protocollo_"+id_curr).val('');
			else  {
				ris++;
			
				ris1=String(ris);
				lun=ris1.length;
				
				var risx="";
				for (x=lun;x<7;x++){
					risx=risx+"0"
				
				}
				risx=risx+ris;
				new_prot=stat_prot+risx;
				$("#protocollo_"+id_curr).val(new_prot);
			}
		}		 
	})	
}


function load_codici() {
	load_codici.info_single_code="";
	tipo_prodotto=$("#tipo_prodotto").val();
	if (! tipo_prodotto || tipo_prodotto.length==0 || tipo_prodotto=="0") {
		html=""
		html+="<div class='alert alert-warning' role='alert'>";
		  html+="Specificare il Tipo Prodotto!";
		html+="</div>";
        html+="<div class='modal-footer'>";
          html+="<button class='btn btn-secondary' type='button' data-dismiss='modal'>Chiudi</button>";
        html+="</div>";
		
		$("#body_resp").html(html);
		return false;
	}
	
	date_next=$("#date_next").val()
	if (!date_next || date_next.length==0) {
		html=""
		html+="<div class='alert alert-warning' role='alert'>";
		  html+="Valorizzare correttamente la data impegno!";
		html+="</div>";
		$("#body_resp").html(html);
		return false;
	}
	
	
	codice=$("#codice").val();
	operazione="load_codici";
	res_ajax=$.ajax({
		url:'ajax.php',
		type:'POST',
		data: {operazione:operazione,codice:codice},
		cache:false,
		beforeSend:function(){
		},
		success:function(data){
			record = jQuery.parseJSON( data );
			if (record.length==0) {
				html=""
				

				html+="<div class='alert alert-warning' role='alert'>";
				  html+="Nessun codice trovato!";
				html+="</div>";
			} else {	
				msg="";
				msg+="<h5 class='modal-title'>Codice individuato</h5>";
				$("#div_lbl_codici").html(msg)
				if (record.length>1) {
					msg="<div class='alert alert-primary' role='alert'>";
						msg+="<h5 class='modal-title'>Famiglia "+record[0].codice+" - Procedura Multiselezione</h5>";
					msg+="</div>";
					$("#div_lbl_codici").html(msg)
				}				

				col_campioni=false
				for (sca=0;sca<=record.length-1;sca++) {
					if (record[sca].campioni=="S") col_campioni=true
				}
				
				html="";

				html+="<table class='table table-hover'>";
				  html+="<thead>";
					html+="<tr>";
					  html+="<th scope='col'>#</th>";
					  html+="<th scope='col'>Codice</th>";
					  html+="<th scope='col'>Descrizione</th>";
					  html+="<th scope='col'>Scadenza</th>";
					  if (record.length>1 || 1==1) {
						html+="<th scope='col' style='width:150px'>Quantità</th>";
						html+="<th scope='col' style='width:210px'>Protocollo</th>";
						
						if (col_campioni==true)
							html+="<th scope='col' style='width:210px'>N° Campioni</th>";
					
						if (tipo_prodotto=="2") html+="<th scope='col' style='width:210px'>Alternativa</th>";
					  }
					  html+="<th style='text-align:center' scope='col'>";
						if (record.length>1) 					  
							html+="<button id='sel_all' onclick=\"selall(this.value)\" type='button' class='btn btn-outline-success' value='0'> Seleziona TUTTI</button>";							
						else 
							html+="Conferma";
												
					  html+="</th>";
					html+="</tr>";
				  html+="</thead>";
				  html+="<tbody>";

					
					
					for (sca=0;sca<=record.length-1;sca++) {
						scadenza=record[sca].scadenza
						disp=record[sca].disp

						marcatura_ce=record[sca].MARCATURA_CE
						range_temp=record[sca].RANGE_TEMP
						sigla_custom=record[sca].SIGLA_CUSTOM
						descrizione_custom=record[sca].DESCRIZIONE_CUSTOM
						
						date_next=$("#date_next").val()
						today = new Date(date_next);
						
						scad = addDate (today, scadenza)
						
						scad_gg="";
						html+="<tr>";
							html+="<th scope='row'>"+(sca+1)+"</th>";
							
							html+="<td><font color='blue'>"+record[sca].codice+"</font></td>";
							html+="<td>"+record[sca].descrizione+"</td>";
							html+="<td><h6><i>"+scad+"</i> <span class='badge badge-secondary'>"+scadenza+"</span></h6></td>";

							if (record.length>1 || 1==1) {									
								html+="<td>";
									html+="<div class='form-row'>";
										html+="<input type='hidden' id='codice_"+sca+"' value='"+record[sca].codice+"'>";
										html+="<input type='hidden' id='prodotto_"+sca+"' value='"+record[sca].descrizione+"'>";
										html+="<input type='hidden' id='scadenza_"+sca+"' value='"+scadenza+"'>";
										html+="<input type='hidden' id='disp_"+sca+"' value='"+disp+"'>";
										
										html+="<input type='hidden' id='marcatura_ce_"+sca+"' value='"+marcatura_ce+"'>";
										html+="<input type='hidden' id='range_temp_"+sca+"' value='"+range_temp+"'>";
										html+="<input type='hidden' id='sigla_custom_"+sca+"' value='"+sigla_custom+"'>";
										html+="<input type='hidden' id='descrizione_custom_"+sca+"' value='"+descrizione_custom+"'>";

										html+="<input class='form-control qta' placeholder='Quantità' id='quantita_"+sca+"'  aria-label='Quantita' aria-describedby='qta'>";
										
										
									html+="</div>";
									if (record.length>1) {		
										html+="<a href='#' onclick='update_qta("+sca+")' title='Estendi valore'>";
											html+="<i class='fas fa-caret-down'></i>";
										html+="</a>";
									}									
								html+="</td>";
								
								html+="<td>";
									html+="<div class='form-row'>";
										html+="<input class='form-control prot' placeholder='Protocollo' id='protocollo_"+sca+"' aria-label='Protocollo' aria-describedby='protocollo'>";
									html+="</div>";
									if (record.length>1) {	
										html+="<a href='#' onclick='prot_incr("+sca+")' title='Estendi valore incrementale'>";
											html+="<i class='fas fa-caret-square-down'></i>";
										html+="</a>";
									}	
								html+="</td>";	
								
								
								if (record[sca]) {
									if (record[sca].campioni=="S"){
										html+="<td>";
											html+="<div class='form-row'>";
												html+="<input class='form-control camp' placeholder='Campioni' id='campioni_"+sca+"' aria-label='campioni' aria-describedby='campioni'>";
											html+="</div>";
										html+="</td>";	
									} else html+="<td></td>";
								}	
								
								if (tipo_prodotto=="2") {
									html+="<td>";	
										html+="<div class='form-row'>";
											html+="<input class='form-control prot' placeholder='Alternativa' id='alternativa_"+sca+"' aria-label='Alternativa' aria-describedby='alternativa'>";
										html+="</div>";
									html+="</td>";
								}
							}

							html+="<td style='text-align:center' scope='col'>";
								if (record.length>1 || 1==1) {									
									html+="<button id='btnconfirm_"+sca+"' onclick=\"selcod("+sca+",0)\" type='button' class='btn btn-outline-secondary code_to_reserve' value='0'>";
										html+="<span id='span_confirm"+sca+"'>Seleziona</span>";
										html+=" <span id='span_badge"+sca+"'></span>";
									html+="</button>";
								} else {
									html+="<button id='btnconfirm_"+sca+"' onclick=\"selcod_single()\" type='button' class='btn btn-outline-secondary'>";
										html+="<span id='span_confirm'>Seleziona</span>";
									html+="</button>";
									load_codici.info_single_code=record;
								}
							html+="</td>";
						html+="</tr>";
					}
					
				  html+="</tbody>";
				html+="</table>";
				if (record.length==1) {
			
					lottom=record[0].lottom
					html+="<div class='col-md-4 mb-3'>";
						dispx="block"
						if (lottom.length!=0) {				
							html+="<label for='lotto_view_m'>Lotto Manuale</label>";
							html+="<input class='form-control' id='lotto_view_m' aria-label='Lotto manuale' aria-describedby='lotto manuale' value='"+lottom+"' disabled>";
							dispx="none";
						}
						
						
						html+="<div style='display:"+dispx+"'>";
							html+="<label for='lotto_m'>Lotto Manuale</label>";
							html+="<input class='form-control' id='lotto_m'  aria-label='Lotto manuale' aria-describedby='lotto manuale' value='"+lottom+"'>";
						html+="</div>";
					html+="</div>";
				}

			}
			
			
			$("#body_resp").html(html);
			
			if (record.length>1 || 1==1) $( "#modal_resp_head" ).addClass( "modal-dialog-scrollable" );		
			else $( "#modal_resp_head").removeClass( "modal-dialog-scrollable" );
			
			html="";
			html+="<div id='wait_prenota' style='display:none;max-height:150px;overflow:auto'>";
			html+="</div>";
			html+="<button class='btn btn-secondary' type='button' data-dismiss='modal'>Chiudi</button>";
			if (record.length>1 || 1==1) html+="<button class='btn btn-outline-success' type='submit' onclick='prenotazione()' id='btn_prenota' disabled >Prenota</button>";
			
			
			$("#footer_resp").html(html)
		
			
			submit_multi()
			
			
			
		},
		error: function(richiesta,stato,errori){
			html="Problema di connessione con il server!"
			$("#body_resp").html(html);
			console.log("richiesta",richiesta,"stato",stato,"errori",errori);
		}		  
	  
	  
	});		

}

function addDate(dateObject, numdays) {
	numdays=parseInt(numdays)
	dateObject.setDate (dateObject.getDate () + numdays);
	return dateObject.toLocaleDateString ();
}