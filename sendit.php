<?php
/*
Plugin Name: Sendit!
Plugin URI: http://www.giuseppesurace.com/sendit-wp-newsletter-mailing-list/
Description: For professional use is strongly recommended to buy http://sendit.wordpressplanet.org With Sendit you can Send your post to your subscribers with Sendit, an italian plugin that allows you to send newsletter and manage mailing list in 2 click. New version also include an SMTP configuration and import functions from comments and author emails. It can be used with a template tag in your post or page content or subscribtion widget on your Sidebar. Now you can set interval and emails block (Polish language added in 1.5.1). Version 1.5.9 fixes the Tinymce editor. 
Version: 1.6.2
Author: Giuseppe Surace
Author URI: http://www.giuseppesurace.com
*/

register_activation_hook(__FILE__,'Sendit_install');
add_action('wp_head', 'Pushsack');
add_action('admin_menu', 'gestisci_menu');
add_action('plugins_loaded','DisplayForm');
add_action('admin_head', 'Pusheditor');


load_plugin_textdomain('sendit', false, basename(dirname(__FILE__)) . '/languages'); //thanks to Davide http://www.jqueryitalia.org


$sendit_directory   = "sendit/";
$sendit_root = get_option('siteurl') . '/wp-content/plugins/'.$sendit_directory;

/*filtro x frontend*/
add_filter('the_content', 'GeneraForm');


function Pushsack() // Spingo ajax su header
{
  // uso JavaScript SACK library per Ajax
  wp_print_scripts( array('jquery' ));

  // Define custom JavaScript function
?>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#sendit_subscribe_button').click(function(){
		jQuery.ajax({
		beforeSend: function() { jQuery('#sendit_wait').show(); jQuery('#sendit_subscribe_button').hide();},
        complete: function() { jQuery('#sendit_wait').hide(); jQuery('#sendit_subscribe_button').show(); },
		type: "POST",
      	data: ({email_add : jQuery('#email_add').val(),lista : jQuery('#lista').val()}),  		
      	url: '<?php bloginfo( 'wpurl' ); ?>/wp-content/plugins/sendit/submit.php',
  		success: function(data) {
    	/*jQuery('#dati').html(data);*/
   		alert(data);
   		
  }
});
	});
});

</script>

<script type="text/javascript">
function clearText(thefield){
if (thefield.defaultValue==thefield.value)
thefield.value = ""
}
</script>

<?php
} // fine PHP function PushSack + javascript clear field

function SenditSubscribe($id)
{


        $form_aggiunta="<form name=\"theform\">
                        <p>
                            <input id=\"email_add\" type=\"text\" value=\"\" name=\"email_add\"/>
                            <input type=\"hidden\" name=\"lista\" id=\"lista\" value=\"".$id."\">
                            <input class=\"button\" type=\"button\" id=\"sendit_subscribe_button\" name=\"agg_email\" value=\"".__('Subscribe', 'sendit')."\"/>
                </p>
                    <small>Sendit <a href=\"http://www.giuseppesurace.com\">Wordpress newsletter</a></small>
            </form>
            
            <div id=\"dati\"></div>";

        echo $form_aggiunta;
}



/*form di iscrizione su the:content */    

function GeneraForm($text)
{

if (stristr($text, '[newsletter' ))
{

    $search = "@(?:<p>)*\s*\[newsletter\s*=\s*(\w+|^\+)\]\s*(?:</p>)*@i";
        if (preg_match_all($search, $text, $matches, PREG_SET_ORDER)) {
            foreach($matches as $match)
            {
    
                $form_aggiunta="<form name=\"theform\">
                <p>
                <input id=\"email_add\" type=\"text\" value=\"\" name=\"email_add\"/>
                    <input type=\"hidden\" name=\"lista\" id=\"lista\" value=\"".$match[1]."\">
                <input class=\"button\" type=\"button\" id=\"sendit_subscribe_button\" name=\"agg_email\" value=\"".__('Subscribe', 'sendit')."\"/>
                </p>
                    <small>Sendit <a href=\"http://www.giuseppesurace.com\" title=\"Wordpress newsletter plugin\">Wordpress newsletter</a></small>
            </form>
            <div id=\"sendit_wait\" style=\"display:none;\"></div>
            <div id=\"dati\"></div>";
    
    $text = str_replace($match[0], $form_aggiunta, $text);

    
            }
    
       }
    } else { $text=$text; }
        return $text;
}

/* WIDGET PER SIDEBAR con valore 1 (valore di default) */


function WidgetForm($args) {
    global $dcl_global;
    extract($args);
    //before_widget,before_title,after_title,after_widget

    $form_aggiunta=$before_widget."
             ".$before_title.get_option('titolo').$after_title."
            <div class=\"sendit\">
            <form name=\"theform\">
            <input id=\"email_add\" type=\"text\" value=\"email\" name=\"email_add\" onFocus=\"clearText(this)\"/>
                <input type=\"hidden\" name=\"lista\" id=\"lista\" value=\"".get_option('id_lista')."\">
            <input class=\"button\" type=\"button\" onclick=\"javascript:Ajax(this.form.email_add.value, this.form.lista.value,'dati');\" name=\"agg_email\" value=\"".__('Subscribe', 'sendit')."\"/>
            </p>";
            if (!$dcl_global) $form_aggiunta.="<p><small>Sendit <a href=\"http://www.giuseppesurace.com\">Wordpress newsletter</a></small></p>";
            $form_aggiunta.="
        </form><div id=\"dati\"></div></div>".$after_widget;
    
    echo $form_aggiunta;
}

function JqueryForm($args) {
    global $dcl_global;
    extract($args);
    //before_widget,before_title,after_title,after_widget

    $form_aggiunta=$before_widget."
             ".$before_title.get_option('titolo').$after_title."
            <div class=\"sendit\">
            <form name=\"theform\">
            <input id=\"email_add\" type=\"text\" value=\"email\" name=\"email_add\" onFocus=\"clearText(this)\"/>
                <input type=\"hidden\" name=\"lista\" id=\"lista\" value=\"".get_option('id_lista')."\">
            <input class=\"button\" type=\"button\" id=\"sendit_subscribe_button\" name=\"agg_email\" value=\"".__('Subscribe', 'sendit')."\"/>
       
           	<div id=\"sendit_wait\" style=\"display:none;\"></div>

            
            ";
            if (!$dcl_global) $form_aggiunta.="<p><small>Sendit <a href=\"http://www.giuseppesurace.com\">Wordpress  newsletter</a></small></p>";
            $form_aggiunta.="
        </form>
			</div>".$after_widget;
    
    echo $form_aggiunta;
}


/*
* qui setto l ID della lista dal widget!!! generalmente  1
*/
function Sendit_widget_options() {
        if ($_POST['id_lista']) {
            $id_lista=$_POST['id_lista'];
            $titolo=$_POST['titolo'];
            update_option('id_lista',$id_lista);
            update_option('titolo',$_POST['titolo']);
        }
        $id_lista = get_option('id_lista');
        $titolo = get_option('titolo');
        //titolo
        echo '<p><label for="titolo">'.__('Newsletter title: ', 'sendit').': <input id="titolo" name="titolo"  type="text" value="'.$titolo.'" /></label></p>';
        //id della mailing list
        echo '<p><label for="id_lista">'.__('Mailing list ID: ', 'sendit').'<input id="id_lista" name="id_lista" type="text" value="'.$id_lista.'"  style="width: 25px; text-align: center;" /></label></p>';
        
            
    }




/*
 * DIVENTA UN WIDGET x SIDEBAR!!!
 */


function DisplayForm()
{
    if ( !function_exists(
        'register_sidebar_widget') )
    {
        return;
    }

    register_sidebar_widget('Sendit Widget','JqueryForm');
    register_widget_control('Sendit Widget','Sendit_widget_options', 200, 200);
    
}






function Pusheditor() {
	wp_enqueue_script('editor');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('media-upload');

		wp_tiny_mce( true , // true makes the editor "teeny"
			array(
				"editor_selector" => "messaggio"
			)
		);

}

/*
*Installazione
*/

function Sendit_install() {
        
        global $wpdb;
        //creo tabella email
        
          $table_name_email = $wpdb->prefix . "nl_email";
           
           
           if ($wpdb->get_var("show tables like '$table_name_email'") != $table_name_email)
           {
               add_option("nl_db_version", "1.2");             
               
              $sql = "CREATE TABLE " . $table_name_email . " (
              id_email int(11) NOT NULL AUTO_INCREMENT,
              id_lista  int(11) default '1',
              `contactname` varchar(250) default NULL,
              `email` varchar(250) default NULL,
              `magic_string` varchar(250) default NULL,
              `accepted` varchar(1) default 'n',
              `post_id` mediumint(9) NULL,
              `ipaddress` VARCHAR(255)   NULL,
            
               PRIMARY KEY  (`id_email`),
                           KEY `id_lista` (`id_lista`)
                           
                        
            );";

                
                               
             require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
            dbDelta($sql);
                  $welcome_nome  = "Test Name";
                    $welcome_email = "firstsubscriber@example.com";

      $insert = "INSERT INTO " . $table_name_email ." (id_lista, contactname, email) " . "VALUES (1, '" . $wpdb->escape($welcome_name) . "','" . $wpdb->escape($welcome_email) . "')";

      //$results = $wpdb->query( $insert );

            
            
            
           } else {
           
           //la tabella esiste verifico la versione e aggiorno i campi aggiungendo post_id e ip           
           $sql="ALTER TABLE " . $table_name_email . "
            ADD COLUMN post_id INT(9) NULL,
            ADD COLUMN ip_address VARCHAR(55) NULL ;";
           
           require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
        $wpdb->query($sql);           
           
           }
           
           //creo tabella liste
           $table_name_liste = $wpdb->prefix . "nl_liste";
           

        $header_default='<h1>'.get_option('blogname').'</h1>';
        $header_default.='<h3>newsletter</h1>';
        $footer_default='<p>'.__('Newsletter sent by Sendit Wordpress plugin').'</p>';
        


           if($wpdb->get_var("show tables like '$table_name_liste'") != $table_name_liste)
           {
               $sql_liste = "CREATE TABLE ".$table_name_liste." (
                    `id_lista` int(11) NOT NULL auto_increment,                  
                  `nomelista` varchar(250) default NULL,
                  `email_lista` varchar(250) default NULL,
                  `header` mediumtext NULL,
                  `footer` mediumtext NULL,
                   PRIMARY KEY  (`id_lista`));";
               
             require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
            dbDelta($sql_liste);
      
      $admin_email=bloginfo('admin_email');
      
      $insert = "INSERT INTO " . $table_name_liste . " (id_lista, nomelista, email_lista,header,footer) " . "VALUES (1, 'Mailing List 1','$admin_email','$header_default','$footer_default')";    
      $results = $wpdb->query( $insert );        
           }

               
        
                                  
 
 }
    
    /*gestione pagine wp-admin*/
    
function gestisci_menu() {
global $wpdb;
   
    add_menu_page(__('Send', 'sendit'), __('Newsletter', 'sendit'), 8, __FILE__, 'invianewsletter');

    add_submenu_page(__FILE__, __('Manage subscribers', 'sendit'), __('Manage subscribers', 'sendit'), 8, 'lista-iscritti', 'Iscritti');

    add_submenu_page(__FILE__, __('SMTP settings', 'sendit'), __('SMTP settings', 'sendit'), 8, 'Smtp', 'Smtp');
    
    add_submenu_page(__FILE__, __('Options', 'sendit'), __('Lists management', 'sendit'), 8, 'opzioni-newsletter', 'opzioni');
    
    add_submenu_page(__FILE__, __('email import', 'sendit'), __('Import emails from comments', 'sendit'), 8, 'mass-import', 'Importazioni');


    add_submenu_page(__FILE__, __('email import', 'sendit'), __('Import emails from WP Users', 'sendit'), 8, 'import', 'ImportWpUsers');
 
 
if ($wpdb->get_var("show tables like 'bb_press'") != '') :
 add_submenu_page(__FILE__, __('email import', 'sendit'), __('Import emails from BBpress', 'sendit'), 8, 'import-bb-users', 'ImportBbPress');
endif;

if (function_exists('sendit_check')) {
    add_submenu_page(__FILE__, __('Cron Settings', 'sendit'), __('cron settings', 'sendit'), 8, 'cron-settings', 'cron_settings');
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

    
    

}


function Smtp()
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
        <td><input name="sendit_smtp_password" id="sendit_smtp_password" type="text" value="'.get_option('sendit_smtp_password').'" class="regular-text code" /></td>
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
	<tr>
        <th colspan="2">
        <h3>'.__('These settings are useful if you want to send email blocks and stop for some seconds','sendit').'</h3>
        </th>
    </tr> 
        <tr>
        <th><label for="sendit_sleep_time">Usleep interval (seconds)</label></th>
        <td><select name="sendit_sleep_time" id="sendit_sleep_time">
        		<option value="'.get_option('sendit_sleep_time').'" selected="selected" />'.substr(get_option('sendit_sleep_time'),0,2).'</option>
        		<option value="0">0</option>
        		<option value="10000000">10</option>
        		<option value="20000000">20</option>
        		<option value="30000000">30</option>
        		<option value="40000000">40</option>
		</select>
        		
        </td>
    </tr>


        <tr>
        <th><label for="sendit_sleep_each">each (email sent)</label></th>
        <td>
        <select name="sendit_sleep_each" id="sendit_sleep_each">
        		<option value="'.get_option('sendit_sleep_each').'" selected="selected" />'.get_option('sendit_sleep_each').'</option>
        		<option value="0">0</option>
        		<option value="10">10</option>
        		<option value="20">20</option>
        		<option value="30">30</option>
        		<option value="40">40</option>
        		<option value="50">50</option>
        		<option value="60">60</option>
        		<option value="70">70</option>
        		<option value="80">80</option>
        		<option value="90">90</option>
        		<option value="100">100</option>
		</select>
        		
        </td>
    </tr>

</table>


<p class="submit">
    <input type="submit" name="submit" class="button-primary" value="'.__('Save settings', 'sendit').'" />
</p>
  </form>';

    $markup.='</div>';

    echo $markup;

}






/* opzioni mailing list */
function opzioni() {
    global $_POST;
    global $wpdb;
    
    //nome tabella LISTE
    $table_liste = $wpdb->prefix . "nl_liste";
    
    if($_POST['newsletteremail']!="" AND $_POST['com']!="EDIT"):   
        
        $liste_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_liste where email_lista ='$_POST[newsletteremail]';");
        
        
                
                
            
        
                $wpdb->query("INSERT INTO $table_liste (email_lista, nomelista) VALUES ('$_POST[newsletteremail]', '$_POST[newslettername]')");
                 echo '<div id="message" class="updated fade"><p><strong>'.__('Mailing list created succesfully!', 'sendit').'</strong></p></div>';   
         
    
    endif;
    
    
    //qui arrivo x modificare/cancellare o aggiunge con id_lista in $_GET o il $com in post
    
    
    if($_POST['com']=="EDIT") :
    
    $header = $_POST['header'];
    $footer = $_POST['footer'];
        
        $aggiorno= $wpdb->query("UPDATE $table_liste set email_lista = '$_POST[newsletteremail]', nomelista = '$_POST[newslettername]', header='$header', footer='$footer' where id_lista = '$_POST[id_lista]'");
        //messagio di OK
        //$wpdb->debug();
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
                <form action=\"admin.php?page=opzioni-newsletter\" method=\"post\" name=\"delml\">
                    <input type=\"hidden\" name=\"id_lista\" value = \"".$_GET['id_lista']."\">
                    <input type=\"submit\" name=\"com\" value = \"DEL\">
                </form>
                </div>";
    endif;
            
    
            //global $_POST;
           // echo '<div id="message" class="updated fade"><p><strong>Settings saved.</strong></p></div>';
            echo "<div class=\"wrap\"class=\"wrap\"><h2>".__('Options', 'sendit')." ".$listacorrente->nomelista."</h2>";
              
            
            
                    
            //esco il messaggio
                    echo $msg;        
            
                        
            
                    
                    $table_liste = $wpdb->prefix . "nl_liste";
                    $liste= $wpdb->get_results("select * from $table_liste");
                    
                    
                    echo "<form action='$_SERVER[REQUEST_URI]' method='post' name='addml'><table class=\"widefat post fixed\">
                            <tbody>
                            <tr>
                            <th colspan=\"2\">".__('Available lists', 'sendit')."
                            </th>
                            <th align=\"right\">
                            <label for='com'>
                            <input type='hidden' name='com' value='ADD'>
                            </label>
                            <input type='submit' class='button-primary' name='go' value='".__('Create new list', 'sendit')."'>
                        </form></th>
                            </tr>
                            ";
                    foreach ($liste as $lista) {
                        
                        echo "<tr>
                            <td>".__('Mailing list', 'sendit')." ". $lista->id_lista." - ". $lista->email_lista. " - "  .$lista->nomelista."</td>
                            <td><a href=\"admin.php?page=opzioni-newsletter&update=1&id_lista=".$lista->id_lista."\">".__('Edit', 'sendit')."</a></td>
                        <td><a href=\"admin.php?page=opzioni-newsletter&delete=1&id_lista=".$lista->id_lista."\">".__('Delete', 'sendit')."</a></td></tr>";
                        
                        }
                        
                        echo "</tbody></table>";
                        
            if($_GET['id_lista'] and !$_GET['delete']) :
                        
            echo "<form action='$_SERVER[REQUEST_URI]' method='post' >
            <p>".__('Newsletter options', 'sendit')."</p>
            <table>
                    
                    <tr>
                        <th scope=\"row\" width=\"200\"><label for=\"newsletteremail\">".__('from email', 'sendit')."</label><th>
                        <td><input type=\"text\" name=\"newsletteremail\" value=\"".$listacorrente->email_lista."\" ></td></tr>
                    
                    <tr><th scope=\"row\" ><label for=\"newslettername\">".__('Newsletter name', 'sendit')."</label><th>
                    <td><input type=\"text\" name=\"newslettername\"  value=\"".$listacorrente->nomelista."\"><input type=\"hidden\" name=\"com\" value=\"".$com."\">
                    <input type=\"hidden\" name=\"id_lista\" value=\"".$_GET[id_lista]."\">
                    </td></tr>
                    <tr>
                        <th colspan=\"2\"><h2>".__('Template', 'sendit')."</h2>
                        <p>".__('Header and Footer (XHTML code)', 'sendit')."</p>
                        </th>
                    </tr>
                    
                    
                    
                        <tr><th scope=\"row\" ><label for=\"header\">".__('Header', 'sendit')."</label><th>
                    <td><textarea name=\"header\" rows=\"5\" cols=\"50\">".$listacorrente->header."</textarea></td></tr>
                    
                        <tr><th scope=\"row\" ><label for=\"footer\">".__('Footer', 'sendit')."</label><th>
                    <td><textarea name=\"footer\" rows=\"5\" cols=\"50\">".$listacorrente->footer."</textarea></td></tr>
                    
                    
                    <tr><th scope=\"row\" ><th>
                    <td><p class=\"submit\"><input type=\"submit\" class=\"button-primary\" name=\"salva\" value=\"".__('Save', 'sendit')."\"></p></td></tr>
                    
                    </table>
                    </form>";
                endif;        
                echo "</div>";
                    
                    
    
    
}


/*************************
MASS IMPORT da wp_comments
**************************/
function Importazioni() {

    global $_POST;
    global $wpdb;
    
    //disegno i div
    echo "<div class=\"wrap\"class=\"wrap\"><h2>".__('Import email from comments (wp_comments)', 'sendit')."</h2>";

    echo"<form action='$_SERVER[REQUEST_URI]' method='post' name='importform' id='importform'>
        <table>
            <tr><th scope=\"row\" width=\"600\" align=\"left\">".__('Click on Import button to start. All comments email will be added to your mailing list ID 1', 'sendit')."<small><br />".__('(email address already presents will not be added)', 'sendit')."</small></label><th>";
               echo "<td><input type=\"submit\" name=\"start\" value=\"".__('Import', 'sendit')."\" ></td></tr>
            
            </table></form>";
            
            echo '
            <p>'.__("Do you think Sendit it\'s useful? Please send a donation to support our development and i really appreciate!", "sendit").'
            <form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="giuseppe@streetlab.it">
<input type="hidden" name="item_name" value="Sendit Wordpress plugin">
<input type="hidden" name="currency_code" value="EUR">
<input type="hidden" name="amount" value="10.00">
<input type="image" src="http://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="Make payments with PayPal - it\'s fast, free and secure!">
</form>';

    
    //nome tabella commenti = wp_comments
    $table_comments = $wpdb->prefix . "comments";
    //tabella email
    $table_email = $wpdb->prefix . "nl_email";
 
   if($_POST['start']) :   
        
        $comment_emails = $wpdb->get_results("SELECT distinct comment_author_email FROM $table_comments WHERE comment_approved=1");
        
        foreach ($comment_emails as $comment_email)
        {
            //verifico che gia non ci siano
            $user_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_email where email ='$comment_email->comment_author_email' and id_lista = '1';");
                        
                if($user_count>0) :
                    echo "<div class=\"error\">".sprintf(__('email %s already present', 'sendit'), $comment_email->comment_author_email)."</div>";
                else :
                //genero stringa univoca x conferme e cancellazioni sicure
                    $code = md5(uniqid(rand(), true));
                    $wpdb->query("INSERT INTO $table_email (email,id_lista, magic_string, accepted) VALUES ('$comment_email->comment_author_email', '1', '$code', 'y')");
                     echo '<div class="updated fade"><p><strong>'.sprintf(__('email %s succesfully added', 'sendit'), $comment_email->comment_author_email).'</strong></p></div>';   
                 endif;    
         
         
        
        //echo $comment_email->comment_author_email."<br /></br >";    
            
        }
        
    endif;    
            
        
}

/*
 *IMPORTAZIONE DA WP-COMMENTS
 */
 
 /*************************
MASS IMPORT da wp_comments
**************************/
function ImportWpUsers() {

    global $_POST;
    global $wpdb;
    
     $table_liste =  $wpdb->prefix . "nl_liste";   
     $liste = $wpdb->get_results("SELECT id_lista, nomelista FROM $table_liste ");

    
    
    //disegno i div
    echo "<div class=\"wrap\"class=\"wrap\"><h2>".__('Import email from Authors (wp_users)', 'sendit')."</h2>";

    echo"<form action='$_SERVER[REQUEST_URI]' method='post' name='importform' id='importform'>
        <table>
            <tr><th scope=\"row\" width=\"600\" align=\"left\">".__('Click on Import button to start. All Authors email will be added to your mailing list ID 1', 'sendit')."<small><br />".__('(email address already presents will not be added)', 'sendit')."</small></label><th>";
               echo "<td><input type=\"submit\" name=\"start\" value=\"".__('Import', 'sendit')."\" ></td></tr>
            <tr>
                <td>".__('Select list', 'sendit')."
                <select name='list_id'>";
                
                foreach ($liste as $lista) {
                    
                    echo "<option value=".$lista->id_lista.">".$lista->nomelista."</option>";
                    
                }
                    
                echo "</select>
                </td>
            </tr>
            
            </table></form>";
            
            echo '
            <p>'.__("Do you think Sendit it\'s useful? Please send a donation to support our development and i really appreciate!", "sendit").'
            <form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="giuseppe@streetlab.it">
<input type="hidden" name="item_name" value="Sendit Wordpress plugin">
<input type="hidden" name="currency_code" value="EUR">
<input type="hidden" name="amount" value="10.00">
<input type="image" src="http://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="Make payments with PayPal - it\'s fast, free and secure!">
</form>';

    
    //nome tabella commenti = wp_comments
    $table_users = $wpdb->prefix . "users";
    //tabella email
    $table_email = $wpdb->prefix . "nl_email";
 
   if($_POST['start']) :   
        
        $users_emails = $wpdb->get_results("SELECT distinct user_email FROM $table_users");
        
        foreach ($users_emails as $user_email)
        {
            //verifico che gia non ci siano
            $user_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_email where email ='$user_email->user_email' and id_lista = '$_POST[list_id]';");
                        
                if($user_count>0) :
                    echo "<div class=\"error\">".sprintf(__('email %s already present', 'sendit'), $user_email->user_email)."</div>";
                else :
                //genero stringa univoca x conferme e cancellazioni sicure
                    $code = md5(uniqid(rand(), true));
                    $wpdb->query("INSERT INTO $table_email (email,id_lista, magic_string, accepted) VALUES ('$user_email->user_email', '$_POST[list_id]', '$code', 'y')");
                     echo '<div class="updated fade"><p><strong>'.sprintf(__('email %s succesfully added', 'sendit'), $user_email->user_email).'</strong></p></div>';   
                 endif;    
         
         
        
        //echo $comment_email->comment_author_email."<br /></br >";    
            
        }
        
    endif;    
            
        
}


/*
 *IMPORTAZIONE DA WP-COMMENTS
 */
 
 /*************************
MASS IMPORT da wp_comments
**************************/
function ImportBbPress() {

    global $_POST;
    global $wpdb;
    
     $table_liste =  $wpdb->prefix . "nl_liste";   
     $liste = $wpdb->get_results("SELECT id_lista, nomelista FROM $table_liste ");

    
    
    //disegno i div
    echo "<div class=\"wrap\"class=\"wrap\"><h2>".__('Import email from BBpress Users (bb_users)', 'sendit')."</h2>";

    echo"<form action='$_SERVER[REQUEST_URI]' method='post' name='importform' id='importform'>
        <table>
            <tr><th scope=\"row\" width=\"600\" align=\"left\">".__('Click on Import button to start. All Authors email will be added to your mailing list ID 1', 'sendit')."<small><br />".__('(email address already presents will not be added)', 'sendit')."</small></label><th>";
               echo "<td><input type=\"submit\" name=\"start\" value=\"".__('Import', 'sendit')."\" ></td></tr>
            <tr>
                <td>".__('Select list', 'sendit')."
                <select name='list_id'>";
                
                foreach ($liste as $lista) {
                    
                    echo "<option value=".$lista->id_lista.">".$lista->nomelista."</option>";
                    
                }
                    
                echo "</select>
                </td>
            </tr>
            
            </table></form>";
            
            echo '
            <p>'.__("Do you think Sendit it\'s useful? Please send a donation to support our development and i really appreciate!", "sendit").'
            <form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="giuseppe@streetlab.it">
<input type="hidden" name="item_name" value="Sendit Wordpress plugin">
<input type="hidden" name="currency_code" value="EUR">
<input type="hidden" name="amount" value="10.00">
<input type="image" src="http://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="Make payments with PayPal - it\'s fast, free and secure!">
</form>';

    
    //nome tabella commenti = wp_comments
    
    $table_users =  "bb_users";
    
    //tabella email
    $table_email = $wpdb->prefix . "nl_email";
 
   if($_POST['start']) :   
        
        $users_emails = $wpdb->get_results("SELECT distinct user_email FROM $table_users");
        
        foreach ($users_emails as $user_email)
        {
            //verifico che gia non ci siano
            $user_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_email where email ='$user_email->user_email' and id_lista = '$_POST[list_id]';");
                        
                if($user_count>0) :
                    echo "<div class=\"error\">".sprintf(__('email %s already present', 'sendit'), $user_email->user_email)."</div>";
                else :
                //genero stringa univoca x conferme e cancellazioni sicure
                    $code = md5(uniqid(rand(), true));
                    $wpdb->query("INSERT INTO $table_email (email,id_lista, magic_string, accepted) VALUES ('$user_email->user_email', '$_POST[list_id]', '$code', 'y')");
                     echo '<div class="updated fade"><p><strong>'.sprintf(__('email %s succesfully added', 'sendit'), $user_email->user_email).'</strong></p></div>';   
                 endif;    
         
         
        
        //echo $comment_email->comment_author_email."<br /></br >";    
            
        }
        
    endif;    
            
        
}




/*
 * INVIO NEWSLETTER######################################### rifatta rimuovendo phpmailer e utilizzando wp_mail
 */
function invianewsletter() {

    global $_POST;
    global $wpdb;

 echo "<div class=\"wrap\"><h2>".__('Send Newsletter', 'sendit')."</h2>";
	
	if(function_exists('sendit_check'))
	{
		 echo '<div style="color: #D8000C; background-color: #c9f5cc; margin:10px 0 20px 0; border:1px solid #75cc83; padding:5px;">
				<h2>'.__('Warning').'</h2>
				<p>'.__('You already buy Sendit Pro to send and schedule newsletter please use the <a href="'.get_bloginfo('url').'/wp-admin/edit.php?post_type=newsletter">new panel</a>').'</p>
				<p>'.__('Subscribe the mailing list on my new official Sendit Website and stay updated when Sendit PRO will be ready <a href="http://sendit.wordpressplanet.org/sendit-pro/">Sendit Pro</a>').'</p>

				</div>';

	} 
	else 
	{

		echo '<div style="color: #D8000C; background-color: #FFBABA; margin:10px 0 20px 0; border:1px solid #ffcc00; padding:5px;">
				<h2>'.__('Large mailing list? Be Careful for spam!').'</h2>
				<p>'.__('Be careful before to send newsletters to large list, check out the  Sendit Add Ons PRO to scheduler + newsletter tracker system! It will be available on September for a small fee (15/20 &euro;)').'</p>
				<p>'.__('Subscribe the mailing list on my new official Sendit Website and stay updated when Sendit PRO will be ready <a href="http://sendit.wordpressplanet.org/sendit-pro/">Sendit Pro</a>').'</p>

				</div>';


	}
			
	
	 echo '<div style="background:#f9f9f9; margin:10px 0 20px 0; border:1px solid #ffcc00; padding:5px;"><p>'.__('Sendit needs support! Make a donation NOW!<br />
	new version with new features is coming: cron-jobs / batch and queue emails / new user interface!','sendit').'
	            </p><form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<input name="cmd" value="_xclick" type="hidden">

	<input name="business" value="giuseppe@streetlab.it" type="hidden">
	<input name="item_name" value="Sendit Wordpress plugin 2010 support" type="hidden">
	<input name="currency_code" value="EUR" type="hidden">
	<input src="http://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" name="submit" alt="Make payments with PayPal" type="image" border="0">
	</form></div>';
    //estraggo le liste per l'invio
    $table_liste =  $wpdb->prefix . "nl_liste";   
    $liste = $wpdb->get_results("SELECT id_lista, nomelista FROM $table_liste ");
    
    //caso invio newsletter...
    if (isset($_POST['invianewsletter'])) :
     //interrogo il db solo con le email confermate
         $table_email = $wpdb->prefix . "nl_email";
         $emails = $wpdb->get_results("SELECT id_email, id_lista, magic_string, accepted, email FROM $table_email where id_lista='$_POST[lista]' and accepted ='y'");
        //echo $_POST['lista'];
        
        
        
        //carico il template e le info sulla lista
         $table_liste = $wpdb->prefix . "nl_liste";
         $templaterow=$wpdb->get_row("SELECT * from $table_liste where id_lista = '$_POST[lista]' ");
        //costruisco il messaggio come oggetto composto da $gheader $messagio $ footer

        /*COSTRUISCO VARIABILI x LINK CANCELLAZIONE*/
    
        $sendit_directory   = "sendit/";
        $sendit_root = get_option('siteurl') . '/wp-content/plugins/'.$sendit_directory;        
        
        $header= $templaterow->header;
        $messaggio= stripslashes($_POST['messaggio']);
        $plain_text = strip_tags($messaggio);
        //costruisco messaggio facendo un replace ../ con il bloginfo('wpurl')
        $content = $messaggio;
        $via = '../';
        $newpath = get_bloginfo('wpurl').'/';
        //aggiungo la url se no le immagini non si vedono..
        $messaggio = str_replace($via, $newpath, $content);
                
                
        $footer= $templaterow->footer;
        $mess=$header.$messaggio.$footer;

        $from=$templaterow->email_lista;
        $subject = $_POST['oggetto'];
        $AltBody= $plain_text." To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

        $headers= "MIME-Version: 1.0\n" .
		        "From: ".$from." <".$from.">\n" .
		        "Content-Type: text/html; charset=\"" .
		get_option('blog_charset') . "\"\n";                
            
        
        $i=0;
        foreach ($emails as $email)
        {
       	$i++;
        //aggiungo messaggio con il link di cancelazione che cicla il magic_string..
        $deletelink="<center>
        -------------------------------------------------------------------------------
        <p>".__('To unsubscribe, please click on the link below', 'sendit')."<br />
                    <a href=\"".$sendit_root."delete.php?action=delete&c=".$email->magic_string."\">".__('Unsubscribe now', 'sendit')."</a></p>
        </center>";
            
            $message=$mess.$deletelink;
            
            $recipient=$email->email;
            
            //if(1!=1) {
             if(!wp_mail($recipient, $subject ,$message, $headers, $attachments)) {
                echo '<div id="message" class="error"><p><strong>'.__($i." Error sending email!", "sendit").' => '. $mail->ErrorInfo.'</strong></p></div>';
            } else {
            echo '<div id="message" class="updated fade"><p><strong>'.__($i." Email sent to ".$email->email, "sendit").'</strong></p></div>';
                    }    
            

            
            //break di 30 secondi ogni 10 email provamoce...
			if(get_option('sendit_sleep_time')!='0')
			{
			
            	if (($i % get_option('sendit_sleep_each')) == 0 && $i!=0)
            	{
            	echo '<div id="message" class="error"><p><strong>'.sprintf(__('Sendit will restart to send newsletters in %s seconds', 'sendit'), get_option('sendit_sleep_time')).'</strong></p></div>';

            		usleep(get_option('sendit_sleep_time'));
             	}
            
            }
     	
            	
        } //endforeach
            
            //admin notify + report
/*           
 $mail->AddAddress($templaterow->email_lista);
            $mail->Subject = __('Newsletter report: '.time(),'sendit');
            $mail->MsgHTML(__('Mail sent to '.count($emails).' subscribers by <a href="http://www.giuseppesurace.com">Sendit</a>'));
            $mail->Send();
            $mail->ClearAddresses();
 
           $mail->SmtpClose();
*/
        else :
        

        /*chiamo i post dall id passato dalla form*/
        $my_id = $_POST['post'];
        $post_id = get_post($my_id);
        
        $title = $post_id->post_title;
        $content = $post_id->post_content;
        $via = '../';
        $newpath = get_bloginfo('wpurl').'/';
        //aggiungo la url se no le immagini non si vedono..
        $content_with_absolute_url = str_replace($via, $newpath, $content);
        echo "<!--".$content_with_absolute_url."-->";
                echo "
        
            <table class=\"widefat\">
                <form action='$_SERVER[REQUEST_URI]' method='post' name='sendpost' id='sendpost' >
                    <tr>
                        <th><label for=\"lista\">".__('Select a post (if you want to send it by email)', 'sendit')." </label><th>";
        
            echo "<td><select name=\"post\" style=\"width:150px\" >";
            
            /*estraggo la lista dei post*/
            //$lastposts = get_posts('numberposts=30');
            
            //provo a uscire il tutto
            $lastposts = $wpdb->get_results("select * from ".$wpdb->prefix."posts where post_type='post' or post_type='page' order by ID desc");
            foreach($lastposts as $post) {
                //setup_postdata($post);
                
                if($post->ID == $my_id) { $selected = " selected=\"selected\""; } else { $selected=""; }

                echo "<option value=\"".$post->ID."\"".$selected.">".$post->post_title." (ID:".$post->ID.")</option>";
            }
        
        echo"</select><input class=\"button-primary\" type=\"submit\" name=\"populate\" value=\"".__('Get content', 'sendit')."\">
             </td></tr></form>";
        
        
        
        echo "
    
            <form action='$_SERVER[REQUEST_URI]' method='post' name='sendform' id='sendform' >
            <tr><th scope=\"row\" width=\"250\"><label for=\"lista\">".__('Please select a mailing list', 'sendit')."<small><br /></small></label><th>";
               echo "<td><select name=\"lista\" style=\"width:150px\" >";
            
            foreach ($liste as $lista) {
                echo "<option value=\"".$lista->id_lista."\">".$lista->nomelista." (lista n ".$lista->id_lista.")</option>";
            }
        
        echo'</select></td></tr>
            <tr><th scope="row" width="250"><label for="oggetto">'.__("Subject", "sendit").'</label><th>
            <td><input type="text" name="oggetto" id="oggetto" style="width:250px" value="'.$title.'" ></td></tr>
            <tr><th scope="row" width="250"><label for="messaggio">Newsletter</label><th>
            <td><textarea class="messaggio" id="messaggio" name="messaggio" cols="70" rows="15">'.$content_with_absolute_url.'</textarea>
            
            </td></tr>
            <tr><th scope="row" width="250"><th>
            <td><p class="submit"><input type="submit" class="button-primary" name="invianewsletter" value="'.__('Send', 'sendit').'" /></p></td></tr>
            
            </table></form>';

            endif;
            echo '<p>'.__("Do you think Sendit it\'s useful? Please send a donation to support our development and i really appreciate!", "sendit").'
            <form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="giuseppe@streetlab.it">
<input type="hidden" name="item_name" value="Sendit Wordpress plugin">
<input type="hidden" name="currency_code" value="EUR">
<input type="image" src="http://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="Make payments with PayPal - it\'s fast, free and secure!">
</form>';
            echo "</div>";

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



//new version add loading gif to wp_head css 1.6.2 ->>>
add_action('wp_head', 'sendit_loading_image');


function sendit_loading_image() {
    $siteurl = get_option('siteurl');
    $img_url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/images/loading.gif';
    echo '<style type="text/css">#sendit_wait{background:url('.$img_url.') no-repeat; height:40px;margin:10px;display:block;}</style>';
    //echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
}



add_action('admin_head', 'sendit_register_head');
add_action('wp_head', 'sendit_register_head');

function sendit_register_head() {
    $siteurl = get_option('siteurl');
    $url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/sendit.css';
    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
}


/**********PAGINA LISTA ISCRITTI**********/
function Iscritti() {
	require('pagination.class.php');
    global $_POST;
    global $wpdb;
    
    $table_email = $wpdb->prefix . "nl_email";
    
    //cancellazione provamoce
    if($_POST['delete'] && $_POST['id_email']):   

          $delete=$wpdb->query("delete from $table_email where id_email = '$_POST[id_email]'");         
           
           echo '<div id="message" class="updated fade"><p><strong>'.__("Email deleted succesfully!", "sendit").'</strong></p></div>';   
           //print_r($_POST);
   
    endif;
    
    //modifica provamoce
    if($_POST['update']):   
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

   
    
    $emails = $wpdb->get_results("SELECT id_email, id_lista, email, magic_string, accepted FROM $table_email where id_lista= '$_GET[lista]' order by email $limit");
    //email confermat
    $emails_confirmed = $wpdb->get_results("SELECT id_email, id_lista, email, magic_string, accepted FROM $table_email where id_lista= '$_GET[lista]' and accepted='y'");

    echo "<div class=\"wrap\"><h2>".__('Mailing list management', 'sendit')."</h2>";
    
    
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
				</thead>";

            foreach ($liste as $lista) {

                if ($_GET['lista']==$lista->id_lista) : $selected=" class=\"updated fade\"";  else : $selected=""; endif;     

                echo "<tr >
                		<td>".$lista->id_lista."</td>
                		<td ".$selected."><a class=\"\" href=\"admin.php?page=lista-iscritti&lista=".$lista->id_lista."\">".$lista->nomelista."</a></td>
                		<td></td><tr>";
            }
        echo"</table>
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

					           <form id=\"add\" name=\"add\" method=\"post\" action=\"admin.php?page=lista-iscritti&lista=".$_GET[lista]."\">

                
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
			<table class=\"widefat post fixed\">
				<thead>
					<tr>
						<th>".__('email', 'sendit')."</th>
						<th>".__('status', 'sendit')."</th>
						<th>".__('actions', 'sendit')."</th>

					</tr>
				</thead>
    	
        ";
        
      
        foreach ($emails as $email) {
            
            //coloro le input per distinguere tra chi ha confermato e chi no
            if ($email->accepted=="y") { $style="style=\"background:#E4FFCF; border:1px solid #B6FF7F;\""; }
            elseif ($email->accepted=="n") { $style="style=\"background:#fffbcc; border:1px solid #e6db55;\""; }
            else { $style="style=\"background:#fd919b; border:1px solid #EF4A5C;\""; }
                    
                
        echo "<tr>	
        		<form action=\"#email_".$email->id_email."\" method=\"post\">

                <td id=\"email_".$email->id_email."\">
                   
                        <!--input type=\"checkbox\" name=\"email_handler[]\" value=\"".$email->id_email."\">-->
                        <input type=\"hidden\" name=\"id_email\" value=\"".$email->id_email."\">
                        <input type=\"hidden\" name=\"lista\" value=\"".$_POST['lista']."\">
                        <input type='hidden' name='code' value='".$email->magic_string."' />
                        <input type='text' name='email' value='".$email->email."' />
                        </td>
                        <td   ".$style." >
                        <select name=\"status\">
                            
                            <option value=\"y\"";
                            
                            if ($email->accepted=="y") { echo " selected=\"selected\""; }
                            
                            echo">".__('Confirmed', 'sendit')."</option>
                            <option value=\"n\"";
                            
                            if ($email->accepted=="n") { echo " selected=\"selected\""; }
                            echo">".__('Not confirmed', 'sendit')."</option>
                            <option value=\"d\"";
                            
                            if ($email->accepted=="d") { echo " selected=\"selected\""; }
                            echo">".__('Cancelled', 'sendit')."</option>
                            
                        </select>
                        </td>
                        <td>
	                        <input type=\"submit\" class=\"button\" name=\"update\" value=\"".__('Update', 'sendit')."\">
	                        <input type=\"submit\" class=\"button\" name=\"delete\" value=\"".__('Delete', 'sendit')."\">
						</td>
            </form>
            </tr>    ";
            
        
        }
    
    
    
    echo "				<thead>
					<tr>
						<th>".__('email', 'sendit')."</th>
						<th>".__('status', 'sendit')."</th>
						<th>".__('actions', 'sendit')."</th>

					</tr>
				</thead>
</table><br clear=\"all\" />";
    //ripeto la paginazione
    if($p):
			echo $p->show();
	endif;
    
    endif;    
    
    echo "</div>";
  
    
}



function export_subscribers_screen()
{ ?>
	<div class="wrap">

	<h2><?php echo __('To export Sendit mailing list you need to buy Sendit pro exporter','sendit');?></h2>
		<p><?php echo __('With Sendit pro export tool (available now for only 5 euros) you will be able to export and reimport as CSV files all your Sendit subscribers'); ?></p>
		<a class="button primary" href="http://sendit.wordpressplanet.org/plugin-shop/wordpress-plugin/sendit-pro-csv-list-exporter/"><?php echo __('Buy this plugin Now for 5 euros', 'Sendit'); ?></a>
	
	</div>
<? }


function export_subscribers() {
	global $wpdb;
	 $table_liste =  $wpdb->prefix . "nl_liste";   
     $liste = $wpdb->get_results("SELECT id_lista, nomelista FROM $table_liste ");
	 $export_url = get_option('siteurl') . '/wp-content/plugins/sendit/export.php';        

?>
<div class="wrap">

<h2><?php echo __('Export list subscribers','sendit');?></h2>
<p><?php echo __('This will generate a CSV file based on your export settings. The CSV file will be contain only email addresses. To import in other Sendit installation just paste the generated CSV into manage subscribers area'); ?></p>
<form method="post" action="<?php echo $export_url; ?>">
    <?php settings_fields( 'baw-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Select list</th>
        	<td><select name="list_id">
        		<option value="all"><?php echo __('All lists subscribers', 'sendit');?></option>
				<?php
	                foreach ($liste as $lista) {
                    
                    echo "<option value=".$lista->id_lista.">".$lista->nomelista."</option>";
                    
                } ?>
                 </select>
        	</td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Status</th>
        <td>
        	<select name="status">
        		<option value="y"><?php echo __('only Confirmed subscribers', 'sendit')?></option>
        		<option value="0"><?php echo __('All subscribers', 'sendit')?></option>
        	</select>
        </td>
        </tr>
        
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>
<?php }
?>