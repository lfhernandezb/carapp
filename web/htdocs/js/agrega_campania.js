	$(document).ready(function() {
		
	    //$('.ask').jConfirmAction();

		// crea datepicker
		new JsDatePick({
			useMode:2,
			target:"fecha_inicio",
			dateFormat:"%Y-%m-%d",
			imgPath:"jsdatepick-calendar/img/"
			/*
			selectedDate:{				
				day:1,						
				month:1,
				year:2012
			},
			yearsRange:[2012,2020],
			limitToToday:false,
			cellColorScheme:"ocean_blue",
			weekStartDay:1*/
		});

		// solamente se aceptan numeros en textboxes de esta clase
        $('.number_input').keydown(function(e)
        {
            var key = e.charCode || e.keyCode || 0;
            // allow backspace, tab, delete, arrows, numbers and keypad numbers ONLY
            return (
                key == 8 || 
                key == 9 ||
                key == 46 ||
                (key >= 37 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
        });

        $('#agrega_campania').validate({
			rules: {
				descripcion: {
					required: true
				},
				detalle: {
					required: true
				},
				fecha_inicio: {
					required : true,
					date: true
				},
				dias: {
					required: true,
					number: true
				},
				periodicidad: {
					required: true,
					number: true
				},
				numero_impresiones: {
					required: true,
					number: true
				}
			}, // end of rules
			messages: {
				descripcion: {
					required: "Favor ingrese descripci&oacute;n",
				},
				detalle: {
					required: "Favor ingrese detalle en formato JSON",
				},
				fecha_inicio: {
					required: "Favor ingrese la fecha de inicio de la campa&ntilde;a",
					date: "Favor ingrese una fecha v&aacute;lida"
				},
				dias: {
					required: "Favor ingrese el n&uacute;mero de d&iacute;as que la campa&ntilde;a estar&aacute; activa",
					number: "El n&uacute;mero de d&iacute;as debe ser un valor num&eacute;rico"
				},
				periodicidad: {
					required: "Favor ingrese la periodicidad",
					number: "La periodicidad debe ser un valor num&eacute;rico"
				},
				numero_impresiones: {
					required: "Favor ingrese el n&uacute;mero de veces que la notificaci&oacute;n ser&aacute; mostrada",
					number: "El n&uacute;mero de veces debe ser un valor num&eacute;rico"
				}
			}, // end of messages
		    errorPlacement: function(error, element) {
		             if (element.attr("type") == "checkbox")
		               error.insertAfter(checkboxes[checkboxes.size() - 1].nextSibling.nextSibling);
		             else if (element.is("textarea"))
		               error.insertAfter(element);
		             else
		               error.insertAfter(element.parent().next());
		    } 			
		}); // end validate
				
		$.NiceJForms.build({
			imagesPath:"nicejforms/css/images/default/"
		});

		$('#descripcion').focus();

	}); // end ready
