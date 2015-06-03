	$(document).ready(function() {
		
		//$('.ask').jConfirmAction();
				
		paginacion();
		
		$('#dialog').dialog({
		    autoOpen: false,
		    height: 280,
		    modal: true,
		    resizable: false,
		    buttons: {
		    	'Cerrar': function() {
		    		$(this).dialog('close');
		      	} /*,
		    	'Change Rating': function() {
		    		$(this).dialog('close');
		      		// Update Rating
		    	}
		    	*/
		  	}
		});
		
		$.NiceJForms.build({
			imagesPath:"nicejforms/css/images/default/"
		});
		
	});
		
    function exportar() {
    	/*
        $("#export_plataforma").val($("#plataforma").val());
        $("#export_region").val($("#region").val());
        $("#export_ciudad").val($("#ciudad").val());
        $("#export_re").val($("#re").val());

        $("#export_fabricante").val($("#fabricante").val());
        $("#export_modelo").val($("#modelo").val());
        */
        $("#export").submit();
    }

	
	/*
	function eliminaRepuesto(id_repuesto) {
		var self = this;
	
	    if($(this).next('div.question').length <= 0)  
	        $(this).after('<div class="question">Are you sure?<br> <span class="yes">Yes</span><span class="cancel">Cancel</span></div>');  
	  
	    $('.question').animate({opacity: 1}, 300);  
	  
	    $('.yes').live('click', function(){  
			// 
			var data;
			var str;
			
	        $.ajax({
	             async: false,
	             url: "<{$receiver}>?do=Ajax&req=delRepuesto&id_repuesto=" + id_repuesto,
	             type: 'GET',
	             dataType: 'json',
	             error: function(xhr){
	             	alert("An error occured: " + xhr.status + " " + xhr.statusText);
	             },
	             success: function(output_string){
	             	//alert(output_string);
	                 data = output_string;
	             }
	        });
	
	        if (data.respuesta == '1') {
	        	$(self).parent().parent().hide();
	        }
	    });  
	  
	    $('.cancel').live('click', function(){  
	        $(this).parents('div.question').fadeOut(300, function() {  
	            $(this).remove();  
        	});  	
		});
	}	
	*/
