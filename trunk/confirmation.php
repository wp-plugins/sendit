<?php

include("../../../wp-blog-header.php"); 

/*
  QUESTO FILE AGGIORNA LO STATO DI CHI CONFERMA LA MAIL DI ISCRIZIONE
 */
function ComfermaEmail() {
	
	
    global $_GET;
	global $wpdb;
	
    $table_email = $wpdb->prefix . "nl_email";
    
    if($_GET['action']=="confirm"):   
    	
    	$user_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_email where magic_string ='$_GET[c]';");
    	
    		if($user_count<1) :
    			echo "<div class=\"error\">Indirizzo email non presente o qualcosa non sta funzionando!</div>";
    		else :
				
	 			$wpdb->query("UPDATE $table_email set accepted='y' where magic_string = '$_GET[c]'");				
				$table_liste = $wpdb->prefix . "nl_liste";
				
					 $templaterow=$wpdb->get_row("SELECT * from $table_liste where id_lista = '$_GET[lista]' ");
					
					
					//utile anzi fondamentale
					$plugindir   = "sendit/";
					$sendit_root = get_option('siteurl') . '/wp-content/plugins/'.$plugindir;
			
			
			
			/*
			 * QUI potete ridisegnare il vs TEMA
			 */		
				
				
				get_header();
					 
					 	echo '<div id=\"content\">';
							echo '<div id="message" class="updated fade"><br /><br />
							<h2><strong>'.__("Thank you for subscribe our newsletter!", "sendit").'<br />'.__("you will be updated", "sendit").'</strong></h2></div>';
						echo '</div>';
					echo '</div>';
				
				get_footer();
						
			endif;	
    
    endif;


}






//coprocessa la conferma ridisegnando il tema
ComfermaEmail();


?>
