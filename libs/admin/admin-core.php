<?php


function sendit_admin_head() {
  	wp_print_scripts( array('jquery-ui-draggable','jquery-ui-sortable' ));

    $siteurl = get_option('siteurl');
    $url = $siteurl . '/wp-content/plugins/sendit/sendit-admin.css';
    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
	echo '<style type="text/css">'.get_option('sendit_css').'</style>';
	?>
	<script type="text/javascript">
function addFormField() {
	var id = document.getElementById("id").value;
	var remove_div ='#campo'+id; 
	
	jQuery("#divTxt").append('<div class="campo" id="campo'+id+'"><p id="row'+id+'" class="leg"><label for="sendit_field['+id+'][name]"><?php echo __('Field name', 'sendit'); ?></label><input type="text" size="20" name="sendit_field['+id+'][name]" id="sendit_field['+id+'][name]"><label for="sendit_field['+id+'][class]"><?php echo __('Field css class', 'sendit'); ?></label><input type="text" size="20" name="sendit_field['+id+'][class]" id="sendit_field['+id+'][class]"><label for="sendit_field['+id+'][rules]"><?php echo __('Field rules', 'sendit'); ?></label><select name="sendit_field['+id+'][rules]" id="sendit_field['+id+'][rules]"><option value="required">required</option><option value="not_required">-------</option></select><a class="button-secondary remove_fields" href="#">Remove</a></p></div>');
	
	
	id = (id - 1) + 2;
	document.getElementById("id").value = id;
}


jQuery('.remove_fields').live('click', function(){
jQuery(this).closest('.campo').remove();
});

	jQuery(function() {
		jQuery( "#sortable" ).sortable();
		jQuery( "#sortable" ).disableSelection();
	});
</script>
<?php } 

function ManageLists() {
    global $_POST;
    global $wpdb;
    
    //nome tabella LISTE
    $table_liste = $wpdb->prefix . "nl_liste";
    
    if($_POST['newsletteremail']!="" AND $_POST['com']!="EDIT"):           
        $liste_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_liste where email_lista ='$_POST[newsletteremail]';");
           $wpdb->query("INSERT INTO $table_liste (email_lista, nomelista) VALUES ('$_POST[newsletteremail]', '$_POST[newslettername]')");
           echo '<div id="message" class="updated fade"><p><strong>'.__('Mailing list created succesfully!', 'sendit').'</strong></p></div>';   
    endif;
    
    if($_POST['com']=="EDIT") :    
    	$header = $_POST['header'];
    	$footer = $_POST['footer'];        
        $aggiorno= $wpdb->query("UPDATE $table_liste set email_lista = '$_POST[newsletteremail]', nomelista = '$_POST[newslettername]', header='$header', footer='$footer' where id_lista = '$_POST[id_lista]'");
        $msg =  '<div id="message" class="updated fade"><p><strong>'.__('Mailing list updated', 'sendit').'</strong></p></div>';
    elseif($_POST['com']=="ADD") :       
        $newemail = __('email@here', 'sendit');
        $newname = __('New mailing list', 'sendit');            
        $ins= $wpdb->query("insert into $table_liste (email_lista, nomelista) values('$newemail', '$newname')");        
        $msg = '<div id="message" class="updated fade"><p><strong>'.__('Mailing created successfully!', 'sendit').'</strong></p></div>';        
    elseif($_POST['com']=="DEL") :                    
        $ins= $wpdb->query("delete from $table_liste where id_lista = $_POST[id_lista]");        
        $msg = '<div id="message" class="updated fade"><p><strong>'.__('Mailing deleted successfully!', 'sendit').'</strong></p></div>';    
    endif;

    if(($_GET['update']==1)&&(!isset($_POST['com']))) :
      $listacorrente= $wpdb->get_row("select * from $table_liste where id_lista = '$_GET[id_lista]'");
      $com="EDIT";
    endif;

    if($_GET['delete']==1) :
       //div che avvisa prima della cancellazione con form
       $msg = "<div class=\"error\"class=\"wrap\"><p>".sprintf(__('Are You sure to delete %d list? it will delete all mailing list and subscribers data ', 'sendit'), $_GET['id_lista'])." ".$listacorrente->nomelista."</p>
                <form action=\"admin.php?page=lists-management\" method=\"post\" name=\"delml\">
                    <input type=\"hidden\" name=\"id_lista\" value = \"".$_GET['id_lista']."\">
                    <input type=\"submit\" name=\"com\" value = \"DEL\">
                </form>
                </div>";
    endif;

            echo "<div class=\"wrap\"class=\"wrap\"><h2>".__('Lists Management', 'sendit')." ".$listacorrente->nomelista."</h2>";
              
            
            
                    
            //esco il messaggio
                    echo $msg;        
            
                        
            
                    
                    $table_liste = $wpdb->prefix . "nl_liste";
                    $liste= $wpdb->get_results("select * from $table_liste");
                    
                    
                    echo "
                    <form action='$_SERVER[REQUEST_URI]' method='post' name='addml'><table class=\"wp-list-table widefat fixed posts\">
                    <input type='submit' class='button-primary sendit_actionbuttons' name='go' value='".__('Create new list', 'sendit')."'>
                   
                            <thead>
                            <tr>
                            <th colspan=\"2\">".__('Available lists', 'sendit')."
                            </th>
                            <th align=\"right\">
                            <label for='com'>
                            <input type='hidden' name='com' value='ADD'>
                            </label>
                            
                        </form></th>
                            </tr>
                            </thead>
                            <tbody>
                            ";
                    foreach ($liste as $lista) {
                        
                        echo "<tr>
                            <td><p>".__('Mailing list', 'sendit')." ". $lista->id_lista." - ". $lista->email_lista. " - "  .$lista->nomelista."</p></td>
                            <td><p><a class=\"button-secondary\" href=\"admin.php?page=lists-management&update=1&id_lista=".$lista->id_lista."\">".__('Edit', 'sendit')."</a></p></td>
                        	<td><p><a href=\"admin.php?page=lists-management&delete=1&id_lista=".$lista->id_lista."\">".__('Delete', 'sendit')."</a></td></p></tr>";
                        
                        }
                        
                        echo "</tbody></table>";
                        
            if($_GET['id_lista'] and !$_GET['delete']) :
                        
            echo "<form action='$_SERVER[REQUEST_URI]' method='post' >
            <p>".__('Newsletter options', 'sendit')."</p>
            <table>
                    
                    <tr>
                        <th scope=\"row\" width=\"200\"><label for=\"newsletteremail\">".__('from email', 'sendit')."</label><th>
                        <td><input type=\"text\" name=\"newsletteremail\" value=\"".$listacorrente->email_lista."\" ></td>
                    </tr>                    
                    <tr>
                    <th scope=\"row\" ><label for=\"newslettername\">".__('Newsletter name', 'sendit')."</label><th>
                    <td><input type=\"text\" name=\"newslettername\"  value=\"".$listacorrente->nomelista."\"><input type=\"hidden\" name=\"com\" value=\"".$com."\">
                    <input type=\"hidden\" name=\"id_lista\" value=\"".$_GET[id_lista]."\">
                    </td></tr>
                    <tr>
                        <th colspan=\"2\"><h2>".__('Template', 'sendit')."</h2>";
                        if(!is_plugin_active('sendit-pro-template-manager/sendit-pro-template-manager.php')):
                        	echo '<i>Tired to manage template from here? want more template management?
                        	try Sendit Pro Template manager and turn your old template into custom post types with a lot of features as header logo upload and</i>';
                        endif;
                        echo "<p>".__('Header and Footer (XHTML code)', 'sendit')."</p>
                        </th>
                    </tr>
                    <tr><th scope=\"row\" ><label for=\"header\">".__('Header', 'sendit')."</label><th>
                    <td><textarea name=\"header\" rows=\"5\" cols=\"50\">".$listacorrente->header."</textarea></td></tr>                    
                        <tr><th scope=\"row\" ><label for=\"footer\">".__('Footer', 'sendit')."</label><th>
                    <td><textarea name=\"footer\" rows=\"5\" cols=\"50\">".$listacorrente->footer."</textarea></td></tr>                   
                    <tr><th scope=\"row\" ><th>
                    <td><p class=\"submit\"><input type=\"submit\" class=\"button-primary\" name=\"salva\" value=\"".__('Save', 'sendit')."\"></p></td>
                    </tr>                    
                 </table>
                    </form>";
                endif;        
                echo "</div>";
    
}

function SmtpSettings()
{
    
    /*
    $mail->Host = get_option('sendit_smtp_host'); // Host
    $mail->Hostname = get_option('sendit_smtp_hostname');// SMTP server hostname
    $mail->Port  = get_option('sendit_smtp_port');// set the SMTP port
    */

    $markup= "<div class=\"wrap\"class=\"wrap\"><h2>".__('Sendit SMTP settings', 'sendit');
    
    if($_POST):
        update_option('sendit_smtp_host',$_POST['sendit_smtp_host']);
        update_option('sendit_smtp_hostname',$_POST['sendit_smtp_hostname']);
        update_option('sendit_smtp_port',$_POST['sendit_smtp_port']);
		
        update_option('sendit_smtp_authentication',$_POST['sendit_smtp_authentication']);
        update_option('sendit_smtp_username',$_POST['sendit_smtp_username']);
        update_option('sendit_smtp_password',$_POST['sendit_smtp_password']);
        update_option('sendit_smtp_ssl',$_POST['sendit_smtp_ssl']);
        
        //new from 1.5.0!!!
        update_option('sendit_sleep_time',$_POST['sendit_sleep_time']);
        update_option('sendit_sleep_each',$_POST['sendit_sleep_each']);
        

        
        $markup.='<div id="message" class="updated fade"><p><strong>'.__('Settings saved!', 'sendit').'</strong></p></div>';
    endif;
    $markup.='<h3>'.__('Smtp settings are required only if you want to send mail using an SMTP server','sendit').'</h3>
    <p>'.__('By default Sendit will send newsletter using the mail() function, if you sant to send mail using SMTP server you just have to type your settings here').'</p>
<form method="post" action="'.$_SERVER[REQUEST_URI].'">
<table class="form-table">
    <tr>
        <th><label for="sendit_smtp_host">SMTP host</label></th>
        <td><input name="sendit_smtp_host" id="sendit_smtp_host" type="text" value="'.get_option('sendit_smtp_host').'" class="regular-text code" /></td>
    </tr>

    <tr>
        <th><label for="sendit_smtp_port">SMTP port</label></th>
        <td><input name="sendit_smtp_port" id="sendit_smtp_hostname" type="text" value="'.get_option('sendit_smtp_port').'" class="regular-text code" /></td>
    </tr>
    <tr>
        <th colspan="2">
        <h3>'.__('Settings below are required only if SMTP server require authentication','sendit').'</h3>
        </th>
    </tr>    
    <tr>
        <th><label for="sendit_smtp_username">SMTP username</label></th>
        <td><input name="sendit_smtp_username" id="sendit_smtp_username" type="text" value="'.get_option('sendit_smtp_username').'" class="regular-text code" /></td>
    </tr>
    <tr>
        <th><label for="sendit_smtp_password">SMTP password</label></th>
        <td><input name="sendit_smtp_password" id="sendit_smtp_password" type="password" value="'.get_option('sendit_smtp_password').'" class="regular-text code" /></td>
    </tr>
    <tr>
        <th><label for="sendit_smtp_ssl">SMTP SSL</label></th>
        <td>
        	<select name="sendit_smtp_ssl" id="sendit_smtp_ssl">
        		<option value="'.get_option('sendit_smtp_ssl').'" selected="selected" />'.get_option('sendit_smtp_ssl').'</option>
        		<option value="">no</option>
        		<option value="ssl">SSL</option>
        		<option value="tls">TLS</option>
		</select>
        </td>
    </tr>


</table>
<div class="suggest">
<p>
<i>'.
__('Are you on panic for large mailing lists, bad delivery (spam etc)?','sendit').'<br />';

$markup.='<strong>Relax!</strong>'.__('Let SendGrid handle your email delivery used with Sendit. Get 25% off any plan by clicking my link.','sendit');

$markup.='<br /><a href="http://sendgrid.tellapal.com/a/clk/3Rv3Ng">http://sendgrid.tellapal.com/a/clk/3Rv3Ng</a><br />';

$markup.='SendGrid helps you reach more users instead of spam folders. Click this link to get your 25% discount on your first month\'s membership. Believe me you will be addicted!<br />';

$markup.='<a href="http://sendgrid.tellapal.com/a/clk/3Rv3Ng">http://sendgrid.tellapal.com/a/clk/3Rv3Ng</a>';

$markup.='<br />Best<br />Giuseppe</i>
</p></div>

<p class="submit">
    <input type="submit" name="submit" class="button-primary" value="'.__('Save settings', 'sendit').'" />
</p>
  </form>';

    $markup.='</div>';

    echo $markup;

}


function SenditMainSettings($c='')
{
    
	
    $markup= '<div class="wrap"class="wrap">';
    
    $markup.='<h2>'.__('Sendit General settings', 'sendit').'</h2>';
   
    $c=md5(uniqid(rand(), true));
    if($_POST):
        update_option('sendit_subscribe_button_text',stripslashes($_POST['sendit_subscribe_button_text']));        
        update_option('sendit_response_mode',stripslashes($_POST['sendit_response_mode']));        
        update_option('sendit_markup',stripslashes($_POST['sendit_markup']));        
        update_option('sendit_css',stripslashes($_POST['sendit_css']));
        update_option('sendit_unsubscribe_link',stripslashes($_POST['sendit_unsubscribe_link']));
        update_option('sendit_gravatar',stripslashes($_POST['sendit_gravatar']));
        
                
        $markup.='<div id="message" class="updated fade"><p><strong>'.__('Settings saved!', 'sendit').'</strong></p></div>';
        //$markup.='<div id="sendit_preview">'.sendit_markup(1).'</div>';
    endif;

    $markup.='<h3>'.__('Welcome to the new Sendit Newsletter plugin Panel').'</h3>';
    $markup.='<img style="float:left;margin:0 5px 0 0;"src="http://sendit.wordpressplanet.org/wp-content/uploads/sendit_big1.jpg" width="200" /><i>Welcome to the new Sendit plugin. Probably you were expeting the old form to send newsletter from here. As well i rebuilt and did a big refactoring of all plugin so its new. The new Sendit support custom post types so newsletters will be saved. The new plugin also containes a lot of functions requested directly by users so should be excited to test. You can finally built newsletter selecting content from posts (more than 1 post) just choosing from the <strong>custom meta box</strong></i><br />
  

<form method="post" action="'.$_SERVER[REQUEST_URI].'&c='.$c.'">
<table class="form-table">
    <tr>
        <th><label for="sendit_subscribe_button_text">Subscribtion button text</label></th>
        <td><input type="text" name="sendit_subscribe_button_text" id="sendit_subscribe_button_text" value="'.get_option('sendit_subscribe_button_text').'" /></td>
    </tr>
    <tr>
        <th><label for="sendit_unsubscribe_link">Show unsubscribe link on footer?</label></th>
        <td>
        	<select name="sendit_unsubscribe_link">
        		<option value="'.get_option('sendit_unsubscribe_link').'" selected="selected">'.get_option('sendit_unsubscribe_link').'</option>
        		<option value="no">no</option>
        		<option value="yes">yes</option>
        	</select> <small>(If not be sure you have an option to unsubscribe)</small>        	
        </td>
    </tr>
        <tr>
        <th><label for="sendit_gravatar">Show gravatar on subscriber list</label></th>
        <td>
        	<select name="sendit_gravatar">
        		<option value="'.get_option('sendit_gravatar').'" selected="selected">'.get_option('sendit_gravatar').'</option>
        		<option value="no">no</option>
        		<option value="yes">yes</option>
        	</select>        	
        </td>
    </tr>
    <tr>
        <th><label for="sendit_response_mode">'.__('Response mode', 'sendit').'</label></th>
        <td>
        	<select name="sendit_response_mode">
        		<option value="'.get_option('sendit_response_mode').'" selected="selected">'.get_option('sendit_response_mode').'</option>
        		<option value="alert">Alert</option>
        		<option value="ajax">Ajax</option>
        	</select>
		</td>
    </tr>
    <tr>
        <th><label for="sendit_markup">'.__('Subscription form Html markup', 'sendit').'</label></th>
        <td><textarea class="sendit_code source" rows="15" cols="70" name="sendit_markup" id="sendit_markup">'.get_option('sendit_markup').'</textarea></td>
    </tr>
    <tr>
        <th><label for="sendit_css">Subscription widget CSS markup</label></th>
        <td><textarea class="sendit_blackcss" rows="15" cols="70" name="sendit_css" id="sendit_css">'.get_option('sendit_css').'</textarea></td>
    </tr>

</table>


<p class="submit">
    <input type="submit" name="submit" class="button-primary sendit_actionbuttons" value="'.__('Save settings', 'sendit').'" />
</p>
  </form>';

    $markup.='</div>';

    echo $markup;

}


function MainSettings($c='')
{
    
	
    $markup= '<div class="wrap"class="wrap"><h2>'.__('Sendit', 'sendit').'</h2>';
    
    //new 2.2.0
    $markup.='
<div class="" id="sendit-banner">
	<span class="main">Welcome to the new Sendit '.SENDIT_VERSION.' control panel</span>
	<span>Enhance your newsletter plugin by adding pro plugins! Learn more. <a target="_blank" title="Get Sendit" href="http://sendit.wordpressplanet.org/" class="button-primary">take a tour</a> and don\'t forget to subscribe our newsletter to be updated</span>
	
	
</div>';
    
    
    
    //$markup.='<label>Preview Area</label><div class="preview"></div>';
    $c=md5(uniqid(rand(), true));
    if($_POST):
        update_option('sendit_subscribe_button_text',stripslashes($_POST['sendit_subscribe_button_text']));        
        update_option('sendit_markup',stripslashes($_POST['sendit_markup']));        
        update_option('sendit_css',stripslashes($_POST['sendit_css']));        
    endif;
	$markup.='<div class="sendit_box_list sendit_box_menu"><h2>'.__('Mailing lists', 'sendit').'</h2>
			  	<a href="'.admin_url( 'admin.php?page=lists-management').'" class="button-primary">'.__('Create and manage lists', 'sendit').'</a>
			  </div>
			  <div class="sendit_box_design sendit_box_menu"><h2>'.__('Main Settings', 'sendit').'</h2>
			  	<a href="'.admin_url( 'admin.php?page=sendit_general_settings').'" class="button-primary">'.__('Main Settings', 'sendit').'</a>
			  </div>
			  <div class="sendit_box_sendnewsletter sendit_box_menu"><h2>'.__('Send Newsletter', 'sendit').'</h2>
			  	<a href="'.admin_url( 'post-new.php?post_type=newsletter').'" class="button-primary">'.__('Create and send newsletter', 'sendit').'</a>
			  </div>';

	$markup.='<!-- start payment extensions --><div class="sendit_box_fields sendit_box_menu"><h2>'.__('Add more fields', 'sendit').'</h2>
			  	<a href="'.admin_url( 'admin.php?page=sendit_morefields_settings').'" class="button-primary">'.__('Add more fields', 'sendit').'</a>
			  </div>';
			  
	$markup.='<div class="sendit_box_export sendit_box_menu"><h2>'.__('Export mailing lists', 'sendit').'</h2>
			  	<a href="'.admin_url('admin.php?page=export-subscribers').'" class="button-primary">'.__('Save your list as CSV', 'sendit').'</a>
			  </div>';
	//new from 2.1.0 to hide cron settings if you dont have the scheduler active
	if (is_plugin_active('sendit-scheduler/sendit-cron.php')) {
	
	$markup.='<div class="sendit_box_cron sendit_box_menu"><h2>'.__('Cron Settings', 'sendit').'</h2>
			  	<a href="'.admin_url('admin.php?page=cron-settings').'" class="button-primary">'.__('Cron settings', 'sendit').'</a>
			  </div>';	
	} else {
		
		$markup.='<div class="sendit_box_cron sendit_box_menu"><h2>'.__('Cron Settings', 'sendit').'</h2>
			  	<a href="'.admin_url('admin.php?page=cron-settings').'" class="button-primary">'.__('Buy Sendit Scheduler', 'sendit').'</a>
			  </div>';		

	}
	
	$markup.='<div class="sendit_box_template sendit_box_menu"><h2>'.__('Newsletter Templates', 'sendit').'</h2>
		  	<a href="'.admin_url('admin.php?page=sendit_pro_template').'" class="button-primary">'.__('Template manager', 'sendit').'</a>
		  </div>';		
	
	
	$markup.='<div class="sendit_box_shop sendit_box_menu"><h2>'.__('Extend your plugin', 'sendit').'</h2>
		  	<a href="http://sendit.wordpressplanet.org" class="button-primary">'.__('Go to the shop', 'sendit').'</a>
		  </div>';	
	





    $markup.='</div>';

    echo $markup;

}

/**********PAGINA SEGMENTS **********/
function Iscritti() {
	require('pagination.class.php');
    global $_POST;
    global $wpdb;
    
    $table_email = $wpdb->prefix . "nl_email";
    
    if($_POST['delete'] && $_POST['email_handler']):   
		$id_emails = implode(",", $_POST['email_handler']);
        //echo $id_emails; 
           $delete=$wpdb->query("delete from $table_email where id_email in ($id_emails)");                    
           echo '<div id="message" class="updated fade"><p><strong>'.__("Email deleted succesfully!", "sendit").'</strong></p></div>';   
           //print_r($_POST);
   
    endif;
    
    if($_POST['sublist']):   
    	//echo $_GET['lista'];
        //$code = md5(uniqid(rand(), true));
        $id_emails = implode(",", $_POST['email_handler']);
        //echo $id_emails; 

        $emails=$wpdb->get_results("select * from $table_email where id_email in ($id_emails)");
		
		if(count($emails)>0):
		$newlist = $wpdb->insert(SENDIT_LIST_TABLE, array('list_parent' => $_GET['lista'], 'nomelista' => 'Sublist '.$_GET['lista'].' segmented', 'email_lista' => get_bloginfo('admin_email'), 'header' =>$header_default, 'footer'=>$footer_default) );
		$newlist_id=$wpdb->insert_id;
				
			foreach($emails as $email):
	        	$code = md5(uniqid(rand(), true));
	 			$insert=$wpdb->query("INSERT INTO $table_email (email,id_lista, magic_string, accepted) VALUES ('$email->email', $newlist_id, '$code', 'y')");
			endforeach;		
		endif;
			
          //print_r($emails);
          
           //$update=$wpdb->query("update $table_email set email = '$_POST[email]', magic_string='$_POST[code]', accepted = '$_POST[status]' where id_email = '$_POST[id_email]'");
           
           echo '<div id="message" class="updated fade"><p><strong>'.__('bo', 'sendit').'</p></div>';
    endif;



    //modifica provamoce
    if($_POST['update']):   
        //$code = md5(uniqid(rand(), true));
           
           $update=$wpdb->query("update $table_email set email = '$_POST[email]', magic_string='$_POST[code]', accepted = '$_POST[status]' where id_email = '$_POST[id_email]'");
           
           echo '<div id="message" class="updated fade"><p><strong>'.sprintf(__('email %s edited succesfully', 'sendit'), $_POST[email]).'</p></div>';   
           //print_r($_POST);
   
    endif;
    
 
 
     if($_POST['add_email']):   
        //$code = md5(uniqid(rand(), true));
           
           $update=$wpdb->query("update $table_email set email = '$_POST[email]', magic_string='$_POST[code]', accepted = '$_POST[status]' where id_email = '$_POST[id_email]'");
           
           echo '<div id="message" class="updated fade"><p><strong>'.sprintf(__('email %s edited succesfully', 'sendit'), $_POST[email]).'</p></div>';   
           //print_r($_POST);
   
    endif;
 
 
    
   
   //aggiunta indirizzo o indirizzi email dalla textarea
  if($_POST['emails_add']!=""):   
 
  //ver 1.1 multiaddress support
  $email_add= explode("\n", $_POST['emails_add']);
 

  foreach ($email_add as $key => $value) {
      
      //echo $value."<br />";
        
	//validation fix 1.5.6 (also there!) {2,4}    
      if (!ereg("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", trim($value))) :
       
               echo '<div id="message" class="error"><p><strong>indirizzo email '.$value.' non valido!</strong></p></div>';

      else :

        
            
            $user_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_email where email ='$value' and id_lista = '$_GET[lista]' order by email;");
            
                if($user_count>0) :
                    echo "<div class=\"error\"><p><strong>".sprintf(__('email %s already present', 'sendit'), $value)."</strong></p></div>";
                else :
                //genero stringa univoca x conferme e cancellazioni sicure
                    $code = md5(uniqid(rand(), true));
                    $wpdb->query("INSERT INTO $table_email (email,id_lista, magic_string, accepted) VALUES ('$value', '$_POST[id_lista]', '$code', 'y')");
                     echo '<div class="updated fade"><p><strong>'.sprintf(__('email %s added succesfully!', 'sendit'), $value).'</strong></p></div>';   
                 endif;    
        endif;
        
        
        
  }
  //fine ciclo for
        
        
        
     endif;

        
    $email_items = $wpdb->get_var("SELECT count(*) FROM $table_email where id_lista= '$_GET[lista]'"); // number of total rows in the database
	if($email_items > 0) {
		$p = new pagination;
		$p->items($email_items);
		$p->limit(20); // Limit entries per page
		$p->target("admin.php?page=lista-iscritti&lista=".$_GET['lista']);
		$p->currentPage($_GET[$p->paging]); // Gets and validates the current page
		$p->calculate(); // Calculates what to show
		$p->parameterName('paging');
		$p->adjacents(1); //No. of page away from the current page

		if(!isset($_GET['paging'])) {
			$p->page = 1;
		} else {
			$p->page = $_GET['paging'];
		}

		//Query for limit paging
		$limit = "LIMIT " . ($p->page - 1) * $p->limit  . ", " . $p->limit;

	} else {
		//echo "No Record Found";
	}

   
    
    $emails = $wpdb->get_results("SELECT id_email, id_lista, email, subscriber_info, magic_string, accepted FROM $table_email where id_lista= '$_GET[lista]' order by email $limit");
    //email confermat
    $emails_confirmed = $wpdb->get_results("SELECT id_email, id_lista, email, subscriber_info, magic_string, accepted FROM $table_email where id_lista= '$_GET[lista]' and accepted='y'");

    echo "<div class=\"wrap\"><h2>".__('Select List to Segment', 'sendit')."</h2>";
    
    
    //estraggo le liste
    $table_liste =  $wpdb->prefix . "nl_liste";   
    $liste = $wpdb->get_results("SELECT id_lista, nomelista FROM $table_liste ");
   // print_r($_POST);


   
   
    echo "<div class=\"table\">
			<table class=\"widefat  fixed\">
				<thead>
					<tr>
						<th>".__('id', 'sendit')."</th>
						<th class=".$css_list.">".__('mailing list', 'sendit')."</th>
						<th>".__('actions', 'sendit')."</th>

					</tr>
				</thead><tbody>";

            foreach ($liste as $lista) {

                if ($_GET['lista']==$lista->id_lista) : $selected=" class=\"updated fade\"";  else : $selected=""; endif;     

                echo "<tr >
                		<td>".$lista->id_lista."</td>
                		<td ".$selected."><a class=\"\" href=\"admin.php?page=lista-iscritti&lista=".$lista->id_lista."\">".$lista->nomelista."</a></td>
                		<td></td><tr>";
            }
        echo"</tbody></table>
        </div><br clear=\"all\ />";
    
    /*miglioro facendo comparire la form x aggiungere solo se selezionata una lista*/
    if ($_GET['lista']) :
        
        echo "<h3>".__('Manual Subscribe mailing list ', 'sendit')." ".$_POST['lista']."</h3>

                <label for=\"email_add\">".__('email address (one or more: default separator= line break)', 'sendit')."<br />
               <div id=\"dashboard-widgets\" class=\"metabox-holder\">
               <div class='postbox-container' style='width:49%;'>
				<div id=\"normal-sortables\" class=\"meta-box-sortables\">
				<div id=\"dashboard_right_now\" class=\"postbox \" >
					<div class=\"handlediv\" title=\"Fare clic per cambiare.\"><br /></div>
				<h3 class='hndle'><span>".__('Subscription','sendit')."</span></h3>
				<div class=\"inside\">
				        <p>".__('Copy here one or more email address', 'sendit')."</p>

					           <form id=\"add\" name=\"add\" method=\"post\">

                
                <textarea id=\"emails_add\" type=\"text\" value=\"\" name=\"emails_add\" rows=\"10\" cols=\"50\"/></textarea></label>
                 <input type=\"hidden\" name=\"id_lista\" value=\"".$_GET[lista]."\" /> 

                <input class=\"button\" type=\"submit\" value=\"".__('Add', 'sendit')."\"/>
                </p>
                            </form>
                </div>
               </div>
               </div>
               </div>
               </div>
               <br clear=\"all\" />";
        //posiziono la paginazione

		echo "<h3>".__('Subscribers', 'sendit')." n.".$email_items." (".__('Subscriptions confirmed', 'sendit').": ".count($emails_confirmed).")</h3>";
       if($p):
			echo $p->show();
		endif;

        
        echo "
        <br clear=\"all\" />
        <form action=\"\" method=\"post\">
			<p><i>".__('Tips: now you can handle multiple email and editing email address and status simply clicking on it', 'sendit')."</i></p>
			<table class=\"widefat post fixed\">
				<thead>
					<tr>";
					if(get_option('sendit_gravatar')=='yes'):
						echo "<th style=\"width:30px !important;\"></th>";
				    endif;
				   echo "<th style=\"width:30px !important;\"></th>
				   		<th style=\"width:300px !important;\">".__('email', 'sendit')."</th>
						<th style=\"width:100px !important;\">".__('status', 'sendit')."</th>
						<th>".__('Additional info', 'sendit')."</th>
					</tr>
				</thead>
    	
        ";
        
      
        foreach ($emails as $email) {
            
            //coloro le input per distinguere tra chi ha confermato e chi no
            if ($email->accepted=="y") { 
            	$style="style=\"vertical-align:middle; text-align:center; padding:0; background:#E4FFCF;\""; }
            elseif ($email->accepted=="n") { 
            	$style="style=\"vertical-align:middle; padding:0; text-align:center; background:#fffbcc;\""; }
            else { 
            	$style="style=\"vertical-align:middle; padding:0; text-align:center; background:#fd919b;\""; }
            
        	/*
        	//fare funzione per ricaare i valori ovunque            
        	$subscriber_info= json_decode($email->subscriber_info);  
        	$subscriber_options = explode("&", $subscriber_info->options);
        	$options='';
        
        	foreach($subscriber_options as $option):
        		$option=explode("=", $option);
        		if(!empty($option)):
        		//stampo solo i campi unserializzati senza email_add e lista
	        		if($option[0]!='email' and $option[0]!='lista'):
	        			$options.=$option[0];
	        			$options.=':';
	        			$options.='<br />';
	        			$options.='<input type="text" name="'.$option[0].'" value="'.urldecode($option[1]).'" />';
	        			$options.='<br />';
	        		endif;
        		endif;

        	endforeach;
        */
        if($email->accepted=='y') { $confirmed='confirmed'; } elseif($email->accepted=='d') {$confirmed='unsubscribed';} else {$confirmed='not confirmed';} 
        echo "<tr>	";
        		if(get_option('sendit_gravatar')=='yes'):
					echo "<td class=\"grav\" style=\"width:30px !important;\">".get_avatar($email->email,'24')."</td>";
				endif;
                
                echo "<td>
                        <input type=\"checkbox\" name=\"email_handler[]\" value=\"".$email->id_email."\">
                        </td>
                        <td id=\"email_".$email->id_email."\">

                        <input type='hidden' name='id_lista' value='".$email->id_lista."' />
                        <div class='editable' id='email-".$email->id_email."'>".$email->email."</div>
                        <input type='hidden' name='response_div' value='subscriber_".$email->id_lista."' />
                        </td>
                        <td   ".$style.">
                        <div class='edit_select' id='accepted-".$email->id_email."'>".$confirmed."</div>
                        </td>
                       <td>".subscriber_options($email->subscriber_info)."</td>

                       
            </tr>    ";
            
        
        }
    
    
    
    echo "		<tfoot>
					<tr>";
					
				if(get_option('sendit_gravatar')=='yes'):
					echo "<th style=\"width:30px !important;\"></th>";
				endif;
					echo "<th style=\"width:30px !important;\"></th>
						<th>".__('email', 'sendit')."</th>
						<th style=\"width:50px !important;\">".__('status', 'sendit')."</th>
						<th>".__('Additional info', 'sendit')."</th>
					</tr>
				</tfoot>
</table>
<div class=\"clear\"></div>
<input type=\"submit\" class=\"button-primary\" name=\"delete\" value=\"".__('Delete Selected emails', 'sendit')."\">

</form>
<br clear=\"all\" />";
    //ripeto la paginazione
    if($p):
			echo $p->show();
	endif;
    
    endif;    
    
    echo "</div>";
  
    
}





function gestisci_menu() {
/*++++++++++++++++Menu Handler+++++++++++++++++++++++++++++++*/
	global $wpdb;   
    add_menu_page(__('Send', 'sendit'), __('Sendit', 'sendit'), 8, __FILE__, 'MainSettings');
    add_submenu_page(__FILE__, __('Manage subscribers', 'sendit'), __('Manage subscribers', 'sendit'), 8, 'lista-iscritti', 'Iscritti');
    add_submenu_page(__FILE__, __('List Options', 'sendit'), __('Lists management', 'sendit'), 8, 'lists-management', 'ManageLists');   
    add_submenu_page(__FILE__, __('Main settings', 'sendit'), __('Main settings', 'sendit'), 8, 'sendit_general_settings', 'senditpanel_admin');

	/*2.0 export addon*/
	if (function_exists('sendit_morefields')) 
	{
		add_submenu_page(__FILE__, __('Fields settings', 'sendit'), __('Fields settings', 'sendit'), 8, 'sendit_morefields_settings', 'SenditMoreFieldSettings');
	}
	else
	{
		add_submenu_page(__FILE__, __('Fields list', 'sendit'), __('Fields settings', 'sendit'), 8, 'sendit_morefields_settings', 'sendit_morefields_screen');
	}
 

    add_submenu_page(__FILE__, __('SMTP settings', 'sendit'), __('SMTP settings', 'sendit'), 8, 'sendit_smtp_settings', 'SmtpSettings');  
    
    add_submenu_page(__FILE__, __('email import', 'sendit'), __('Import emails from comments', 'sendit'), 8, 'mass-import', 'ImportWpComments');
    
    
    add_submenu_page(__FILE__, __('email import', 'sendit'), __('Import emails from WP Users', 'sendit'), 8, 'import', 'ImportWpUsers');

 
	if ($wpdb->get_var("show tables like 'bb_press'") != '') :
		add_submenu_page(__FILE__, __('email import', 'sendit'), __('Import emails from BBpress', 'sendit'), 8, 'import-bb-users', 'ImportBbPress');
	endif;
	//fixed in 2.1.0 (permission denied)
	if (is_plugin_active('sendit-scheduler/sendit-cron.php')) {
	    //plugin is activated
	    add_submenu_page(__FILE__, __('Cron Settings', 'sendit'), __('cron settings', 'sendit'), 8, 'cron-settings', 'cron_settings');
	}
	else
	{
	    add_submenu_page(__FILE__, __('Cron Settings', 'sendit'), __('cron settings', 'sendit'), 8, 'cron-settings', 'buy_plugin_page');	
	}
	
	/*1.5.7 export addon*/
	if (function_exists('sendit_csv_export')) 
	{
		add_submenu_page(__FILE__, __('Export list', 'sendit'), __('Export list', 'sendit'), 8, 'export-subscribers', 'export_subscribers');
	}
	else
	{
		add_submenu_page(__FILE__, __('Export list', 'sendit'), __('Export list', 'sendit'), 8, 'export-subscribers', 'export_subscribers_screen');
	}
	
	/*2.1.1 template addon*/
	if (is_plugin_active('sendit-pro-template-manager/sendit-pro-template-manager.php')) 
	{
		add_submenu_page(__FILE__, __('Email Templates', 'sendit'), __('Newsletter template', 'sendit'), 8, 'sendit_pro_template', 'sendit_pro_template_screen');
	}
	else
	{
		add_submenu_page(__FILE__, __('Email Templates', 'sendit'), __('Newsletter template', 'sendit'), 8, 'sendit_pro_template', 'template_manager_screen');
	}
	
	
	
	/*version check*/
   $sendit_db_version = SENDIT_DB_VERSION;
   $installed_version = get_option('sendit_db_version');
   if($sendit_db_version!=$installed_version)
   {
   	 add_submenu_page(__FILE__, __('Upgrade Sendit', 'sendit'), __('Sendit upgrade', 'sendit'), 8, 'update-sendit', 'sendit_install');
   }
   
   
       

}

function subscriber_options($json)
{
     /*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     	custom fields loop and form input auto generation
     +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

	$sendit_morefields=get_option('sendit_dynamic_settings');
	$markup='';	
	$valori=json_decode($json);
	$info_string= $valori->options;
	
	$explodes=explode("&", $info_string);
	
	//print_r($explodes);
	
	foreach($explodes as $explode):
		$chiave=explode("=", $explode);
		if($chiave[1]!=''):
			if($chiave[0]!='email_add' and $chiave[0]!='lista' ):
				$markup.= $chiave[0];
	 			$markup.=': <strong>'. $chiave[1].'</strong> ';
	 			//$markup.= '<input type="text" name="subscriber_option['.$chiave[0].']" class="'.$v->class.' '.$v->rules.'" value="'.$chiave[1].'">'; 
	 		endif;	
		endif;
	endforeach;
 	
 	//$arr=json_decode($sendit_morefields);
 	//$c = array_combine((array) $explodes, (array) $arr);
 	//print_r($c);
 		return $markup; 	
}

$themename = "Sendit";
$shortname = "sndt";


function list_sendit_plugins() {
	/*	
	The final Hack to check my plugins!
	array (plugin name, path, buy_url, desc,img)
	*/
	
$siteurl = get_option('siteurl');
$file_dir = $siteurl . '/wp-content/plugins/sendit/';
$pro_plugins = array(
        		
        		array('Sendit Pro Scheduler',
        			  'sendit-scheduler/sendit-cron.php',
        			  'http://sendit.wordpressplanet.org/plugin-shop/sendit-pro-auto-css-inliner/?panel_from_domain='.$siteurl,
        			  'Essential add plugin for mailing list with more than 500/1000 email recipients to avoid spam and help
        			  newsletter delivery scheduling the Job and Tracking newsletter',
        			  $file_dir.'images/scheduler-90x90.jpg',
        			  '20'
        			  ),


    			array('Sendit Pro Template Manager',
        			  'sendit-pro-template-manager/sendit-pro-template-manager.php',
        			  'http://sendit.wordpressplanet.org/plugin-shop/sendit-pro-template-manager/?panel_from_domain='.$siteurl,
        			  'Want more for your Email Design? Would you like to preview your newsletter? Try now the new Template Manager for email templates and download the 3 scaffolding sample templates',
        			  $file_dir.'images/template-90x90.png',
        			  '10'
        			  ),
        			  
        			  
        		array('Sendit Pro Css Inliner',
        			  'sendit-css-inliner/sendit-pro-css-inliner.php',
        			  'http://sendit.wordpressplanet.org/plugin-shop/sendit-pro-auto-css-inliner/?panel_from_domain='.$siteurl,
        			  'Let your reader see the same email! No more timeless Inline css coding, let Sendit Pro Css inliner do for you',
        			  $file_dir.'images/css_inliner-90x90.png',
        			  '5'
        			  ),
        			  
        		array('Sendit Pro More Fields',
        			  'sendit-morefields/sendit-morefields.php',
        			  'http://sendit.wordpressplanet.org/plugin-shop/sendit-pro-more-fields/?panel_from_domain='.$siteurl,
        			  'Add Informations field to your widget form simply drag and drop fields (name,city etc)',
        			  $file_dir.'images/morefields-90x90.jpg',
        			  '5'
        			  ),


        		array('Sendit Pro Export to CsV',
        			  'sendit-export/sendit-export.php',
        			  'http://sendit.wordpressplanet.org/plugin-shop/sendit-pro-csv-list-exporter/?panel_from_domain='.$siteurl,
        			  'Need to export your mailing list for personal purpose or to change plugin? :( Feel free to do it with this tool!',
        			  $file_dir.'images/senditcsv-90x90.jpg',
        			  '5'
        			  ),




        		array('Sendit Pro Analytics Campaign tracker',
        			  'sendit-pro-analytics-campaign/sendit-pro-analytics-campaign.php',
        			  'http://sendit.wordpressplanet.org/plugin-shop/sendit-pro-auto-css-inliner/?panel_from_domain='.$siteurl,
        			  'Track your newsletter campaign by tracking visitors from newsletter with Google Analytics Integration',
        			  $file_dir.'images/scheduler-90x90.jpg',
        			  '5'
        			  ),

        		array('Sendit Premium All in One',
        			  'sendit-premium/sendit-premium-activator.php',
        			  'http://sendit.wordpressplanet.org/plugin-shop/sendit-premium/?panel_from_domain='.$siteurl,
        			  'This is the full Sendit Premium Package at special price of &euro; 35 (save up to 15 &euro;)',
        			  $file_dir.'images/allinone-90x90.png',
        			  '35'
        			  )


        			  
        		);
						 


	return $pro_plugins;						 


}




function options_array()
{

/*	
The final Hack to check my plugins!
*/

					

	$options = array (
 
array( "name" => $themename." Options",
	"type" => "title"),
 
//main settings wrapper
array( "name" => "Main Settings","type" => "section"),
array( "type" => "open"),
 
array( "name" => "Sendit subscribe button text",
	"desc" => "Select the text to display in your subscription button",
	//"id" => $shortname."_color_scheme",
	"id" => 'sendit_subscribe_button_text',
	"type" => "text"),
	
array( "name" => "Sendit Response Mode",
	"desc" => "Enter the response mode if you want alert or a jquery append style response",
	"id" => 'sendit_response_mode',
	"type" => "select",
	"options" => array("Ajax", "alert"),

	"std" => "Ajax"),


	
array( "name" => "Show gravatar on subscribers list",
	"desc" => "If enabled avatar will be showed for each subscriber",
	"id" => 'sendit_gravatar',
	"type" => "select",
	"options" => array("yes", "no"),

	"std" => "yes"),


	
	
array( "name" => "Subscription widget markup",
	"desc" => "Want to add any custom CSS code? Put in here, and the rest is taken care of. This overrides any other stylesheets. eg: a.button{color:green}",
	"id" => 'sendit_markup',
	"type" => "textarea",
	"std" => ""),	





array( "name" => "Custom CSS",
	"desc" => "Want to add any custom CSS code or customize this one? Put in here, and the rest is taken care of. This overrides any other subscription form widget stylesheets. eg: a.button{color:green}.",
	"id" => 'sendit_css',
	"class" => 'black_field',
	"type" => "textarea",
	"std" => ""),		
	
array( "type" => "close"),


//smtp panel

/*     
        //new from 1.5.0!!!
        update_option('sendit_sleep_time',$_POST['sendit_sleep_time']);
        update_option('sendit_sleep_each',$_POST['sendit_sleep_each']);
*/
array( "name" => "SMTP Settings",
	"type" => "section"),
array( "type" => "open"),

array( "name" => "Smtp host",
	"desc" => "Enter your smtp host",
	"id" => 'sendit_smtp_host',
	"type" => "text",
	"std" => ""),
	
array( "name" => "Smtp Port",
	"desc" => "Enter your smtp port (es 465)",
	"id" => 'sendit_smtp_port',
	"type" => "text",
	"std" => ""),


array( "name" => "Smtp Username",
	"id" => 'sendit_smtp_username',
	"type" => "text",
	"desc" => "Enter your smtp username Required only if your SMTP provider requires authentication",
	"std" => ""),


array( "name" => "Smtp Password",
	"desc" => "",
	"id" => 'sendit_smtp_password',
	"type" => "password",
	"desc" => "Enter your smtp password, required only if your SMTP provider requires authentication",
	"std" => ""),


array( "name" => "Smtp SLL/TLS",
	"id" => 'sendit_smtp_ssl',
	"type" => "select",
	"desc" => 'If SMTP requires a secure connection is required please select one. Are you on panic for large mailing lists, bad delivery (spam etc)?<br><strong>Relax!</strong>Let SendGrid handle your email delivery used with Sendit. Get 25% off any plan by clicking my link.<br><a href="http://sendgrid.tellapal.com/a/clk/3Rv3Ng">http://sendgrid.tellapal.com/a/clk/3Rv3Ng</a><br>SendGrid helps you reach more users instead of spam folders. Click this link to get your 25% discount on your first month membership. Believe me you will be addicted!<br><a href="http://sendgrid.tellapal.com/a/clk/3Rv3Ng">http://sendgrid.tellapal.com/a/clk/3Rv3Ng</a>',
	"options"=>array('yes','no',''),
	"std" => ""),

array( "type" => "close"),


array( "name" => "Design Elements",
	"type" => "section"),
array( "type" => "open"),
	
array( "name" => "Footer copyright text",
	"desc" => "Enter text used in the right side of the footer. html code allowed",
	"id" => 'sendit_footer_text',
	"type" => "text",
	"std" => ""),
	
array( "name" => "Privacy text",
	"desc" => "You can paste hereyour privacy text that will be displayed in footer.",
	"id" => 'sendit_privacy_text',
	"type" => "textarea",
	"std" => ""),	
	
 
array( "type" => "close"),

//scheduler panel

array( "name" => "Scheduler Settings",
	"type" => "section"),
array( "type" => "open"),
	
array( "name" => "",
	"desc" => "Enter text used in the right side of the footer. html code allowed",
	"id" => 'scheduler_panel',
	"type" => "scheduler_panel",
	"std" => ""),
	

 
array( "type" => "close"),

//premium panel


array( "name" => "Sendit Plugins Premium",
	"type" => "section"),
array( "type" => "open"),
	
array( "name" => "Sendit Panel Text",
	"desc" => "Enter text used in the right side of the footer. It can be HTML",
	"id" => $shortname."_footer_text",
	"type" => "plugin_check_list",
	"std" => ""),

 
array( "type" => "close")


 
);

return $options;
}


function senditpanel_add_admin() {
$options=options_array();
 
global $themename, $shortname;
 
if ( $_GET['page'] == 'sendit_general_settings' ) {
 
	if ( 'save' == $_REQUEST['action'] ) {
 		
		foreach ($options as $value) {
		update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
 
foreach ($options as $value) {
	if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], stripslashes( $_REQUEST[ $value['id'] ])); } else { delete_option( $value['id'] ); } }
 
	header("Location: admin.php?page=sendit_general_settings&saved=true");
die;
 
} 
else if( 'reset' == $_REQUEST['action'] ) {
 
	foreach ($options as $value) {
		delete_option( $value['id'] ); }
 
	header("Location: admin.php?page=sendit_general_settings&reset=true");
die;
 
}
}
 
}

function senditpanel_add_init() {

$siteurl = get_option('siteurl');
$file_dir = $siteurl . '/wp-content/plugins/sendit/';
wp_enqueue_style("functions", $file_dir."functions.css", false, "1.0", "all");
wp_enqueue_script("sendit_app", $file_dir."sendit_app.js", false, "1.0");

}


function senditpanel_admin() {
 
global $themename, $shortname, $options;
$i=0;

$siteurl = get_option('siteurl');
$file_dir = $siteurl . '/wp-content/plugins/sendit/';

 
if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
 
?>
<div class="wrap rm_wrap">

<h2>Sendit Control Panel</h2>


<div class="" id="sendit-banner">
	<span class="main">Welcome to the new <?php echo $themename; ?> <?php echo SENDIT_VERSION; ?> control panel</span>
	<span>Enhance your newsletter plugin by adding pro plugins! Learn more. <a target="_blank" title="Get Sendit" href="http://sendit.wordpressplanet.org/" class="button-primary">take a tour</a></span>
	
	
</div>


 
<div class="rm_opts">
<form method="post">
<?php 
$options=options_array();

foreach ($options as $value) {
switch ( $value['type'] ) {
 
case "open":
?>
 
<?php break;
 
case "close":
?>
 
</div>
</div>
<br />

 
<?php break;
 
case "title":
?>
<p>To easily use the <?php echo $themename;?> theme, you can use the menu below.</p>

 
<?php break;
 
case 'text':
?>

<div class="rm_input rm_text">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
 	<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id']); } else { echo $value['std']; } ?>" />
 <small><?php echo $value['desc']; ?></small>
 <div class="clearfix"></div>
 
 </div>
<?php
break;

case 'password':
?>

<div class="rm_input rm_text">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
 	<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id'])  ); } else { echo $value['std']; } ?>" />
 <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
 
 </div>
<?php
break;


case 'plugin_check_list':


	


?>

<div class="rm_input rm_text">
<h2>Sendit Control Panel</h2>


<p><i>This is your checkpoint where you can activate and buy additional pro plugins to your Sendit Free Installation</i></p>
	<table>
		<thead>	
			<tr>
				<th>Plugin</th>
				<th>Name</th>
				<th>Description</th>
				<th>Price</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
	<?php 
			
	$pro_plugins=list_sendit_plugins();
	foreach($pro_plugins as $plugin)
	 {

	 if (is_plugin_active($plugin[1])) {
	     echo '<tr><td><img class="pluginthumb" width="60" src="'.$plugin[4].'" alt="'.$plugin[0].'"/></td>
	     			<td><a href="'.$plugin[2].'">'.$plugin[0].'</a></td>
	     			<td class="plugin_on"><strong>&euro; '.$plugin[5].'</strong></td>
	     			<td class="plugin_on"><small>'.$plugin[3].'</small></td>
	     			<td class="plugin_on"> <strong>ACTIVE</strong> </td></tr>';
	   }
	  else {
	  	 echo '<tr><td><img class="pluginthumb" width="60" src="'.$plugin[4].'" alt="'.$plugin[0].'"/></td>
	  	 			<td><a href="'.$plugin[2].'">'.$plugin[0].'</a></td>
	     			<td class="plugin_on"><strong>&euro; '.$plugin[5].'</strong></td>
	  	 			<td><small>'.$plugin[3].'</small></td>
	  	 			<td class="plugin_off"><a class="button-primary" href="'.$plugin[2].'">buy now ' .$plugin[0].'</a></td></tr>';
	    }
	 }

	?>
	</tbody>
	</table>
 
 <div class="clearfix"></div>
 
 </div>
<?php
break;


case 'scheduler_panel':
?>

<div class="rm_input rm_textarea">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
	<?php 
	if (is_plugin_active('sendit-scheduler/sendit-cron.php')) {
		echo cron_settings_panel(); 
	} else {
		echo buy_plugin($plugin);	
	}
	?>
 
 </div>
  
<?php
break;

 
case 'textarea':
?>

<div class="rm_input rm_textarea">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
 	<textarea class="<?php echo $value['class']; ?>" name="<?php echo $value['id']; ?>"><?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id']) ); } else { echo $value['std']; } ?></textarea>
 <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
 
 </div>
  
<?php
break;
 
case 'select':
?>

<div class="rm_input rm_select">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
	
<select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
<?php foreach ($value['options'] as $option) { ?>
		<option <?php if (get_settings( $value['id'] ) == $option) { echo 'selected="selected"'; } ?>><?php echo $option; ?></option><?php } ?>
</select>

	<small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
</div>
<?php
break;
 
case "checkbox":
?>

<div class="rm_input rm_checkbox">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
	
<?php if(get_option($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />


	<small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
 </div>
<?php break; 
case "section":

$i++;

?>

<div class="rm_section">
<div class="rm_title"><h3><img src="<?php echo $file_dir; ?>images/trans.gif" class="inactive" alt="""><?php echo $value['name']; ?></h3><span class="submit"><input class="button-primary" name="save<?php echo $i; ?>" type="submit" value="Save changes" />
</span><div class="clearfix"></div></div>
<div class="rm_options">

 
<?php break;
 
}
}
?>
 
<input type="hidden" name="action" value="save" />
</form>
<form method="post">
<p class="submit">
<input name="reset" class="button-primary" type="submit" value="Reset" />
<input type="hidden" name="action" value="reset" />
</p>
</form>

</div> 
 

<?php
}
?>
<?php
add_action('admin_init', 'senditpanel_add_init');
add_action('admin_menu', 'senditpanel_add_admin');

function buy_plugin()
{ ?>
<div id="premium-panel">
	<span class="main">You don't have Sendit Pro Scheduler installed</span>
<span>Scheduler split delivery process for you using cron jobs <a class="button-primary" href="http://sendit.wordpressplanet.org">Buy Now</a></span>
</div>
<? }


function buy_plugin_page()
{ ?>
<div class="wrap">
<h2>Sendit Pro Scheduler...</h2>
	<div id="premium-panel">
		<span class="main">Ops! You don't have Sendit Pro Scheduler installed or maybe you forgot to activate!</span>
	<span>Scheduler split delivery process for you using cron jobs <a class="button-primary" href="http://sendit.wordpressplanet.org">Buy Now</a></span>
	</div>
</div>
<? }
?>