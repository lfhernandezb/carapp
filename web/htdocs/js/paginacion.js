function paginacion() {
	
	// paginacion
	
    //getting the amount of elements inside content div  
    var number_of_items = $('#record-number').val(); //$('tbody').children().size();  

    //how much items per page to show  
    var show_per_page = $('#show_per_page').val(); //Math.max(Math.ceil(number_of_items/desired_pages), 10);  
    
    //calculate the number of pages we are going to have  
    var number_of_pages = $('#page_number').val();  //Math.ceil(number_of_items/show_per_page);
    
    //current page
    var page_num = $('#current_page').val();
  
    //set the value of our hidden input fields  
    //$('#current_page').val(0);  
    //$('#show_per_page').val(show_per_page);  
  
    //now when we got all we need for the navigation let's make it '  
  
    /* 
    what are we going to have in the navigation? 
        - link to previous page 
        - links to specific pages 
        - link to next page 
    */
    
    /*
     <span class="disabled"><< prev</span>
     <span class="current">1</span>
     <a href="">2</a>
     <a href="">3</a>
     <a href="">4</a>
     <a href="">5</a>…<a href="">10</a><a href="">11</a><a href="">12</a>...<a href="">100</a><a href="">101</a><a href="">next >></a>
     */
    /* original
    var navigation_html = '<a class="previous_link" href="javascript:previous();">Prev</a>';  
    var current_link = 0;  
    while(number_of_pages > current_link){  
        navigation_html += '<a class="page_link" href="javascript:go_to_page(' + current_link +')" longdesc="' + current_link +'">'+ (current_link + 1) +'</a>';  
        current_link++;  
    }  
    navigation_html += '<a class="next_link" href="javascript:next();">Next</a>';  
    */
    
    var navigation_html = '<a class="previous_link" href="javascript:previous();"><< prev</a>';  
    var current_link = 0;  
    while(number_of_pages > current_link){  
        navigation_html += '<a class="page_link" href="javascript:go_to_page(' + current_link +')" longdesc="' + current_link +'">'+ (current_link + 1) +'</a>';  
        current_link++;  
    }  
    navigation_html += '<a class="next_link" href="javascript:next();">next >></a>';  
    
    $('.pagination').html(navigation_html);  
  
    //add active_page class to the first page link  
    //$('.pagination .page_link:first').addClass('current');  
    
    // update current page
    $('.page_link[longdesc=' + page_num +']').addClass('current').siblings('.current').removeClass('current');
  
    //hide all the elements inside content div  
    //$('tbody').children().hide(); //css('display', 'none');  
  
    //and show the first n (show_per_page) elements  
    //$('tbody').children().slice(0, show_per_page).show(); //css('display', 'block');
    
    // fin paginacion

}

// paginacion

function previous(){  
	  
    new_page = parseInt($('#current_page').val()) - 1;  
    //if there is an item before the current active link run the function  
    if($('.current').prev('.page_link').length==true){  
        go_to_page(new_page);  
    }  
  
}  
  
function next(){  
    new_page = parseInt($('#current_page').val()) + 1;  
    //if there is an item after the current active link run the function  
    if($('.current').next('.page_link').length==true){  
        go_to_page(new_page);  
    }  
  
}  

// fin paginacion	
