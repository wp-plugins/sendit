<?php include("../../../wp-blog-header.php"); 


/**AZIONE DI INSERIMENTO DALLA FORM DEL SITO**/

function AggiungiEmail() {
    global $_POST;
	global $wpdb;
	
    $table_email = $wpdb->prefix . "nl_email";
    
    //messaggio di successo
	 $successo="'<div id=\"message\" class=\"updated fade\"><p><strong>".__('Thank you for subscribe. You will receive an email shortly with confirmation link!', 'sendit')."</p></div>'";
	//messaggio di errore
	$errore="'<div id=\"message\" class=\"updated fade\"><p><strong>".__('not valid email address', 'sendit')."</strong></p></div>'";
    
    if(isset($_POST['email_add'])):   
    
    if (!ereg("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $_POST['email_add'])) :
	   
	   		die( "document.getElementById('dati').innerHTML = $errore;" );

	  else :

    	
    	$user_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_email where email ='$_POST[email_add]' and id_lista = '$_POST[lista]';");
    	
    		if($user_count>0) :
    			$errore_presente = "'<div class=\"error\">".__('email address already present', 'sendit')."</div>'";
    			die( "document.getElementById('dati').innerHTML = $errore_presente;" );
    		else :
			
				//genero stringa univoca x conferme sicure
				$code = md5(uniqid(rand(), true));
				
	 			$wpdb->query("INSERT INTO $table_email (email, id_lista, magic_string, accepted) VALUES ('$_POST[email_add]', '$_POST[lista]','$code','n')");
	 				   
	 			/*qui mando email*/
				
				$table_liste = $wpdb->prefix . "nl_liste";
				
					 $templaterow=$wpdb->get_row("SELECT * from $table_liste where id_lista = '$_POST[lista]' ");
					//costruisco il messaggio come oggetto composto da $gheader $messagio $ footer
					
					//utile anzi fondamentale
					$plugindir   = "sendit/";
					$sendit_root = get_option('siteurl') . '/wp-content/plugins/'.$plugindir;
					$siteurl = get_option('siteurl');
					
					$header= $templaterow->header;
					//$messaggio= $templaterow->welcome;
					$welcome = __('Welcome to newsletter by: ', 'sendit').get_bloginfo('blog_name');
					$messaggio= "<h3>".$welcome."</h3>";
					$messaggio.=__('To confirm your subscription please follow this link', 'sendit').":<br />
					<a href=\"".$sendit_root."confirmation.php?action=confirm&c=".$code."\">".__('Confirm here', 'sendit')."</a>";
					$footer= $templaterow->footer;
					
					$content_send = $header.$messaggio.$footer;
				require_once('mailerclass.php');
				
					
					 /*INTESTAZIONI EMAIL */
					 $headers =  "MIME-Version: 1.0\n";
			         $headers .= "From: ".$templaterow->email_lista." <".$templaterow->email_lista.">\n";
			         $headers .= "Content-Type: text/html; charset=\"iso-8859-1\"\n";
					 $headers .= "Content-Transfer-Encoding: 7bit\n\n";
					 
					 $admin_mail_message = __('New subscriber for your newsletter: ', 'sendit'). get_bloginfo('blog_name');		 
					 $mail_to_admin = new minimail($templaterow->email_lista,$admin_mail_message.get_bloginfo('url').': '.$_POST['email_add'],$_POST['email_add'].__(' subscribe to your mailing list:   ').get_bloginfo('url'), $headers);

 //invio con classe a chi si è iscritto con il link x conferma 
					 $mail_to_user = new minimail($_POST['email_add'], $welcome, $content_send, $headers); 

					 

					
					//uso la classe
					 //print ($mail->send()) ? $successo : $errore; 
					 
					 if($mail_to_user) : 
					 
					 	//echo $successo ; 
					 	die( "document.getElementById('dati').innerHTML = $successo;" );



					 else : 
					 	
					 	//echo $errore; 
					 	die( "document.getElementById('dati').innerHTML = $errore;" );


					 endif;
					
					/* invio tradizzionale 
					 if(mail($_POST['email_add'], "Benvenuto nella Newsletter!", $content_send, $headers)) {
					 		
						echo '<div id="message" class="updated fade"><p><strong>Grazie per esserti iscritto alla mia mailing list!</strong></p></div>';
						
					 }
					 
					 */
					

				endif;

			endif;	
    
    endif;


}






AggiungiEmail();



?>
