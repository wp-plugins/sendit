<?php include("../../../wp-blog-header.php");


/**AZIONE DI INSERIMENTO DALLA FORM DEL SITO**/

function AggiungiEmail() {
    global $_POST;
    global $wpdb;

    
    $table_email = $wpdb->prefix . "nl_email";
    
    //messaggio di successo
     $successo="<div id=\"message\" class=\"updated fade\"><p><strong>".__('Subscription completed now Check your email and confirm', 'sendit')."</p></div>";
    //messaggio di errore
    $errore="<div id=\"message\" class=\"updated fade\"><p><strong>".__('not valid email address', 'sendit')."</strong></p></div>";
    
    if(isset($_POST['email_add'])):   
    
    if (!ereg("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $_POST['email_add'])) :
       
               die($errore); 

      else :

        
        $user_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_email where email ='$_POST[email_add]' and id_lista = '$_POST[lista]';");
        
            if($user_count>0) :
                $errore_presente = "<div class=\"error\">".__('email address already present', 'sendit')."</div>";
                die($errore_presente);
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
                    
                    $headers= "MIME-Version: 1.0\n" .
		        	"From: ".$templaterow->email_lista." <".$templaterow->email_lista.">\n" .
		        	"Content-Type: text/html; charset=\"" .
					get_option('blog_charset') . "\"\n";                

                    
                    $header= $templaterow->header;
                    //$messaggio= $templaterow->welcome;
                    $welcome = __('Welcome to newsletter by: ', 'sendit').get_bloginfo('blog_name');
                    $messaggio= "<h3>".$welcome."</h3>";
                    $messaggio.=__('To confirm your subscription please follow this link', 'sendit').":<br />
                    <a href=\"".$sendit_root."confirmation.php?action=confirm&c=".$code."\">".__('Confirm here', 'sendit')."</a>";
                    $footer= $templaterow->footer;
                    
                    $content_send = $header.$messaggio.$footer;
                    #### Creo object PHPMailer e imposto le COSTANTI SMTP PHPMAILER
                    //$mail = new PHPMailer();

					if(wp_mail($_POST['email_add'], $welcome ,$content_send, $headers, $attachments)):
                         //admin notification
                         wp_mail($templaterow->email_lista, __('New subscriber for your newsletter: ', 'sendit').get_bloginfo('blog_name'), __('New subscriber for your newsletter: '.$_POST['email_add'], 'sendit').get_bloginfo('blog_name'));
                         die($successo);
                     else :
						 //echo $errore;
                         die($errore);
                     endif;

                endif;

            endif;    
    
    endif;


}


add_action('phpmailer_init','phpmailer_init_smtp');


if (!function_exists('phpmailer_init_smtp')) {
	
	// This code is copied, from wp-includes/pluggable.php as at version 2.2.2
	function phpmailer_init_smtp($phpmailer) {


		
		// Set the mailer type as per config above, this overrides the already called isMail method
		if(get_option('sendit_smtp_host')!='') {
			$phpmailer->Mailer = 'smtp';			
			// If we're sending via SMTP, set the host
			$phpmailer->Host = get_option('sendit_smtp_host');
			// If we're using smtp auth, set the username & password SO WE USE AUTH
			if (get_option('sendit_smtp_username')!='') {
				$phpmailer->SMTPAuth = TRUE;
				$phpmailer->Username = get_option('sendit_smtp_username');
				$phpmailer->Password = get_option('sendit_smtp_password');
			}
		}
		
		// You can add your own options here, see the phpmailer documentation for more info:
		// http://phpmailer.sourceforge.net/docs/
		
		// Stop adding options here.
		
	} // End of phpmailer_init_smtp() function definition

}

AggiungiEmail();



?>