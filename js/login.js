(function($) {
	check_validate_form()
})(jQuery); // End of use strict


function check_validate_form(){
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
			event.preventDefault();
			event.stopPropagation();
        } else {
			event.preventDefault();
			login()	
			
		}
        form.classList.add('was-validated');
      }, false);
    });

}


function login() {
	$("#btn_login").text( "Attendere..." );
	$("#btn_login").prop( "disabled", true );
	
	$("#resp_login").empty()
	operazione="login";
	user=$("#user").val();
	pass=$("#pass").val();
	
	var url = "ajax.php";
	$.ajax({
		type: "POST",
		url: url,
		data:{operazione:operazione,user:user,pass:pass},
		beforeSend:function(){
		
		},
		success: function (data){
			record = jQuery.parseJSON( data );
			if (record.header.login=="OK") {
				location.href='index.php';
			} else {
				$("#btn_login").text( "Login" );
				$("#btn_login").prop( "disabled", false );
				html=""
				html+="<div class='alert alert-warning' role='alert'>";
				  html+=record.header.error;
				html+="</div>";

				$("#resp_login").html(html)
			}
			
		}
	});	

	
}