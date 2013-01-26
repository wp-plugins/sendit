    jQuery(document).ready(function(){
		jQuery('.rm_options').slideUp();
		// place meta box before standard post edit field

    
        jQuery('#template_choice').insertBefore('#titlewrap');
 	    

 	    jQuery(".scroll").click(function(event){		
	    event.preventDefault();
	    jQuery('html,body').animate({scrollTop:jQuery(this.hash).offset().top}, 500);
	    });
	
	
	
		
		jQuery('.rm_section h3').click(function(){		
			if(jQuery(this).parent().next('.rm_options').css('display')=='none')
				{	jQuery(this).removeClass('inactive');
					jQuery(this).addClass('active');
					jQuery(this).children('img').removeClass('inactive');
					jQuery(this).children('img').addClass('active');
					
				}
			else
				{	jQuery(this).removeClass('active');
					jQuery(this).addClass('inactive');		
					jQuery(this).children('img').removeClass('active');			
					jQuery(this).children('img').addClass('inactive');
				}
				
			jQuery(this).parent().next('.rm_options').slideToggle('slow');	
			
		});
});