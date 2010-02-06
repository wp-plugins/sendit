<?php include("../../../wp-blog-header.php");


/**AZIONE DI INSERIMENTO DALLA FORM DEL SITO**/

function AggiungiEmail() {
    global $_POST;
    global $wpdb;
    require_once('class.phpmailer.php');
    include_once('class.smtp.php');
    
    $table_email = $wpdb->prefix . "nl_email";
    
    //messaggio di successo
     $successo="'<div id=\"message\" class=\"updated fade\"><p><strong>".__('Thank you for subscribing. You will receive an email shortly with confirmation link!', 'sendit')."</p></div>'";
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
                    #### Creo object PHPMailer e imposto le COSTANTI SMTP PHPMAILER
                    $mail = new PHPMailer();

                    if(get_option('sendit_smtp_host')!='') :    
                    //print_r($mail);
                        $mail->IsSMTP(); // telling the class to use SMTP
                        
                        
                        $mail->Host = get_option('sendit_smtp_host'); // Host
                        $mail->Hostname = get_option('sendit_smtp_hostname');// SMTP server hostname
                        $mail->Port  = get_option('sendit_smtp_port');// set the SMTP port
                        
                        if(get_option('sendit_smtp_auth')=='1'):    
                            $mail->SMTPAuth = true;     // turn on SMTP authentication
                            $mail->Username = get_option('sendit_smtp_username');  // SMTP username
                            $mail->Password = get_option('sendit_smtp_password'); // SMTP password
                        else :
                            $mail->SMTPAuth = false;// disable SMTP authentication
                        endif;
                    endif;
                    
                    $mail->SetFrom($templaterow->email_lista);
                    //$mail->AddReplyTo('pinobulini@gmail.com');
                    $mail->Subject = $welcome;
                    $mail->AltBody = " To view the message, please use an HTML compatible email viewer!";
                    // optional, comment out and test
                    $mail->MsgHTML($content_send);
                    
                     $admin_mail_message = __('New subscriber for your newsletter: ', 'sendit'). get_bloginfo('blog_name');        

                     $mail->AddAddress($_POST['email_add']);
                     if($mail->Send()) :
                        //echo $successo ;
                         die( "document.getElementById('dati').innerHTML = $successo;" );
                     else :
                         
                         //echo $errore;
                         die( "document.getElementById('dati').innerHTML = $errore;" );
                     endif;
                    //notifica a admin
                    $mail->ClearAddresses();
                    $mail->AddAddress($templaterow->email_lista);
                    $mail->Subject = $admin_mail_message;
                    $mail->AltBody = __('New subscriber for your newsletter: ', 'sendit').get_bloginfo('blog_name');
                    // optional, comment out and test
                    $mail->MsgHTML($_POST['email_add'].__(' subscribe to your mailing list:   ').get_bloginfo('url'));
                    $mail->Send();
                endif;

            endif;    
    
    endif;


}






AggiungiEmail();



?>
