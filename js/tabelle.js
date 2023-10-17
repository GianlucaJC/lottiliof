(function() {
	'use strict';
	window.addEventListener('load', function() {
		// Fetch all the forms we want to apply custom Bootstrap validation styles to
		var forms = document.getElementsByClassName('needs-validation_AC');
		// Loop over them and prevent submission
		var validation = Array.prototype.filter.call(forms, function(form) {
			form.addEventListener('submit', function(event) {
				if (form.checkValidity() === false) {
					event.preventDefault();
					event.stopPropagation();
				}	else {
					new_AC();
				}	
				form.classList.add('was-validated');
			}, false);
		});
	}, false);
	

	window.addEventListener('load', function() {
		// Fetch all the forms we want to apply custom Bootstrap validation styles to
		var forms = document.getElementsByClassName('needs-validation_M');
		// Loop over them and prevent submission
		var validation = Array.prototype.filter.call(forms, function(form) {
			form.addEventListener('submit', function(event) {
				if (form.checkValidity() === false) {
					event.preventDefault();
					event.stopPropagation();
				}	else {
					new_M();
				}	
				form.classList.add('was-validated');
			}, false);
		});
	}, false);	
	
})();

(function($) {

})(jQuery); // End of use strict


function new_AC() {
	$("#div_wait_AC").show();
	$("#sub_AC").val('1');
}

function new_M() {
	$("#div_wait_M").show();
	$("#sub_M").val('1');
}

function tab_autoclavi() {
	$("#collapseCardInfo").collapse('hide')
	$("#collapseCardLotto").collapse('hide')
	
	$("#res_archivio").empty();
	$("#nav_steril").hide();
	$("#stampa_lotti").empty();
	$(".tabelle").hide()
	$("#card_AC").show()
}

function tab_materiali() {
	$("#collapseCardInfo").collapse('hide')
	$("#collapseCardLotto").collapse('hide')

	$("#res_archivio").empty();
	$("#nav_steril").hide();
	$("#stampa_lotti").empty();
	$(".tabelle").hide()
	$("#card_Materiali").show()
}

function tab_check() {
	$("#collapseCardInfo").collapse('hide')
	$("#collapseCardLotto").collapse('hide')

	$("#res_archivio").empty();
	$("#nav_steril").hide();
	$("#stampa_lotti").empty();
	$(".tabelle").hide()
	$("#card_Check").show()
}
