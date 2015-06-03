function recrea_nf_select(select_node, index) {
	// re-genero select niceform fabricante
	var select_name = $(select_node).attr('id');
	
	// elimino el nodo 
    $('#nfSelectOptions'+index.toString()).remove();

	// lo re-creo
    var opt = document.createElement('ul');

	$(opt)
		.attr({id: 'nfSelectOptions'+index.toString()})
		.addClass('NFSelectOptions');

	//get select's options and add to options div
	$('#'+select_name+' option').each(function(k) {
		var optionHolder = document.createElement('li');
		var optionLink = document.createElement('a');
		var optionTxt = document.createTextNode(this.text);
		
		$(optionLink)
			//.addClass('NFOptionActive')
			.attr({id: 'nfSelectText'+k})
			.attr({href:'#'})
			.css({cursor:'pointer'})
			.append(optionTxt)
			.bind('click', {who: index, id:select_name, option:k, select:index}, function(e){
				//self.showOptions(e);
				jQuery.NiceJForms.selectMe(select_name, k, index);
				jQuery.NiceJForms.hideOptions(e);
			});
		
		$(optionHolder).append(optionLink);
		$(opt).append(optionHolder);
		
		//check for pre-selected items
		if($(this).attr('selected') == 'selected') {
			// show selected option on select caption
			$('#nfSelectRight'+index.toString()).text($(this).text());
			
			// **** self.selectMe($(jQuery.NiceJForms.selects[q]).attr("id"), w, q);
		}
	});

	$('#nfSelectTarget'+index.toString()).append(opt);
	
}