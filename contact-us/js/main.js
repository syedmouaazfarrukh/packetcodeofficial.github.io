
(function ($) {
    "use strict";

    /*==================================================================
    [ Validate ]*/
    var input = $('.validate-input .input100');

    $('.validate-form').on('submit',function(){
        var check = true;

        for(var i=0; i<input.length; i++) {
            if(validate(input[i]) == false){
                showValidate(input[i]);
                check=false;
            }
        }

        return check;
    });


    $('.validate-form .input100').each(function(){
        $(this).focus(function(){
           hideValidate(this);
        });
    });

    function validate (input) {
        if($(input).attr('type') == 'email' || $(input).attr('name') == 'email') {
            if($(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
                return false;
            }
        }
        else {
            if($(input).val().trim() == ''){
                return false;
            }
        }
    }

    function showValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).addClass('alert-validate');
    }

    function hideValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).removeClass('alert-validate');
    }
    
    /**	4. AJAX - CONTACT
 *****************************************************/	
	
		$("#contact-form").submit(function(e) {
			 
			e.preventDefault();
			var postdata = $(this).serialize();
			
			$.ajax({
				
				type: "POST",
				url: "php/contact.php",
				data: postdata,
				dataType: "json",
				success: function(json) {
					 
					$("#contact-form.error input, #contact-form.error textarea").removeClass("active");
					
					setTimeout(function(){
						
						if (json.nameMessage !== "") {
							
							$("#contact-form-name").addClass("active").attr("placeholder",json.nameMessage);
						    $("#contact-form").addClass("error");
							
						}
						
						if (json.emailMessage !== "") {
							
						   $("#contact-form-email").addClass("active").val("").attr("placeholder",json.emailMessage);
						   $("#contact-form").addClass("error");
						   
						}
						
						if (json.messageMessage !== "") {
							
							$("#contact-form-message").addClass("active").attr("placeholder",json.messageMessage);
						    $("#contact-form").addClass("error");
							
						}
						
					}, 50);
						
					if (json.nameMessage === "" && json.emailMessage === "" && json.messageMessage === "") {
						
						$('#contact-form').removeClass("error").addClass("success");
						$('#contact-form textarea, #contact-form input').attr("placeholder","");
						$('#contact-form textarea').attr("placeholder",json.succesMessage);
						$('#contact-form input, #contact-form button, #contact-form textarea').val('').prop('disabled', true);
						
					}
					
				},
                error: function(e){
                    console.log(e);
                }
				
			});
			
		});

})(jQuery);