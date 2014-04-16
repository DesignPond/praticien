<?php 
/*
	Plugin Name: Nouveaut&eacute;s arr&ecirc;ts
	Plugin URI: http://www.designpond.ch
	Description: Plugin pour nouveautés
	Author: C.Leschaud
	Version: 1.0
	Author URI: http://www.designpond.ch
*/	

	/*******************************************
		Cron schedule event
	********************************************/

	register_activation_hook (__FILE__, 'wps_new_options_page_activation');
	register_deactivation_hook( __FILE__ , 'wps_new_options_page_deactivation');
	 
	add_filter( 'cron_schedules', 'filter_cron_schedules' );
    // add custom time to cron
    function filter_cron_schedules( $param ) {
      return array( 'once_half_hour' => array(
	      'interval' => 86400, // seconds
	      'display' => __( 'Once every day' )
      ) );
    }
	 
	function wps_new_options_page_activation () {
		// If our cron hook doesn't yet exist, create it.
		if (!wp_next_scheduled('send_task_hook')) {
			wp_schedule_event( time(), 'once_half_hour', 'send_task_hook' );
		}
	}
	 
	function wps_new_options_page_deactivation () {
		// If our cron hook exists. remove it.
		if (wp_next_scheduled('send_task_hook')) {
			wp_clear_scheduled_hook('send_task_hook');
		}
	}
	
	add_action( 'send_task_hook', 'task_send_email' ); 
	
/*
	require_once( plugin_dir_path( __FILE__ ). '/data_functions.php');	
	require_once( plugin_dir_path( __FILE__ ). '/classes/arret.php');
	require_once( plugin_dir_path( __FILE__ ). '/classes/user.php');
*/
	
	function task_send_email() {
	
		$maj = false;
		
		$listRecuperer = array();
		
		global $wpdb;	
		/*===============================================
				Mise à jour
		=================================================*/
	/*

			// Get last date
			$dernier_date_arret = $wpdb->get_results('SELECT * FROM wp_nouveautes ORDER BY datep_nouveaute DESC LIMIT 0,1 ');	
		  	$dateArret          = $dernier_date_arret[0]->datep_nouveaute; 
			
			// has to be greater than yesterday
		    $beforetoday     = date("Y-m-d", strtotime("-1 day"));
			$timestampbefore = strtotime($beforetoday);
			$yesterday       = date("ymd", $timestampbefore);
			
			$timestamp       = strtotime($dateArret);
			$derniereDateBd  = date("ymd", $timestamp);
			
			// Get list of dates from TF website
			$datesListTF = getListDate();
					
			// boucle sur la liste du tf
			foreach($datesListTF as $testDate)
			{ 
				// test si cela resseble a une date
				if (preg_match('/^\d{6}$/' , $testDate)) 
				{					
					if( ($derniereDateBd != $testDate) && ($derniereDateBd < $testDate) && ($yesterday < $testDate)  )
					{
						$listRecuperer[] = $testDate;
					}
				}
			}		
		
			if($listRecuperer){
			
				asort($listRecuperer);		
				
				foreach($listRecuperer as $dateRecuperer)
				{
					// the date to get
					$date  = $dateRecuperer;
					// empty array
					$dates = array();
					// split date 2x2 digit => 14 03 10 so 3 mars 2014 from 140310
					$arrays         = str_split($date,2);
					// recompose the date in mysql format 2014-03-10
					$recomposedDate = '20'.$arrays[0].'-'.$arrays[1].'-'.$arrays[2];
					// put in array of dates to get
					$dates[] = $recomposedDate;
					
					// url to list of link with dates from TF website
					$url = 'http://relevancy.bger.ch/AZA/liste/fr/';
					
					// grab the data with curl from date
	 		    	$theData = grabData($url, $date);
	 		    	/*==========================================================
		 		    	The data comes in an awful format of 2 arrays 
		 				1) array: date, reference with link and categorie
		 				2) array: subcategorie
		 				we have to clean the data with cleanFormat2
		 				and rearrange all infos to:
		 					
		 					- date publication	
		 					- date décision
		 					- link 
		 					- référence
		 					- categorie
		 					- subcategorie
		 					- is for publication
		 				
		 				cleanFormat2 returns: 
		 				-  dataArray (array) with infos rearranded
		 				-  catArray (array) with categories to insert if new
	 		    	============================================================*/
	 				
	 			/*
					$dataCleaned = cleanFormat2($theData, $dates);
					
					// Get rearranded arrays				
					$dataTable    = $dataCleaned['dataArray'];
					$dataCategory = $dataCleaned['catArray'];
					
					// Insert categories if dosen't exist in db
					$verifyCategory = existCategorie($dataCategory);
					$notIn          = $verifyCategory['arrayNo'];
	
					if($notIn){ $newCats = addCategory($notIn); }
					
					// prepare the each arret array with number of categorie from db and language
					$prepared = arrangeStack($dataTable);
					
					// insert all arrets in database exept for text
					$links    = insertDatabase($prepared['preparedArray']);
					
					// update text for arret
					$response = updateTextArret($links);
					
					if($response)
					{
						wp_mail('cindy.leschaud@gmail.com', 'Résultat de la mise à jour', 'La mise à jour a été effectué :) ');
					}
					else
					{
						wp_mail('cindy.leschaud@gmail.com', 'Résultat de la mise à jour', 'Problème avec la mise à jour des textes :( ');	
					}
				}
			}	
			else
			{
				wp_mail('cindy.leschaud@gmail.com', 'Résultat de la mise à jour', 'Hello, il n\'y a pas eu de mise à jour :( ');
			}
			
			*/

			/*=========================================	
				 new envois
			==========================================*/
	
/*
			$arretClass = new Arret();
			$userClass  = new User();
		
			// Current day number
			$currentday = date("N");
			$daySend    = 5;
			$istoday    = date("Y-m-d"); 
			$semaine    = array();
			
			// Get last date of last arret
			$dernier_date = $wpdb->get_results(' SELECT datep_nouveaute FROM wp_nouveautes ORDER BY datep_nouveaute DESC LIMIT 0,1 ');	
			$dateArret    = $dernier_date[0]->datep_nouveaute; 

			// Test if maj is complete
			if($dateArret == $istoday) {
			
				// Test if we are day for one
				if( $currentday == $daySend )
				{
					$cinq_dernier_date = $wpdb->get_results(' SELECT datep_nouveaute FROM wp_nouveautes GROUP BY datep_nouveaute ORDER BY datep_nouveaute DESC LIMIT 0,5 ');
					
					foreach($cinq_dernier_date as $day)
					{
						$semaine[] = $day->datep_nouveaute;
					}
					
					$dateOne[] = array_pop($semaine);
					$dateOne[] = array_shift($semaine);
					
					// Get list of arrets 
					// Arrange each arret in his categorie
					$listOne       = $arretClass->get_all_day($dateOne);
					$categoriesOne = $arretClass->arrange_categorie($listOne);
					
					// Get users 
					// Users who want updates each day : all
					// Users who want updates only once a week : one
					$usersOne      = $userClass->get_user_abo_active('one');	
					$userArretsOne = $userClass->assignArretsUsers($usersOne, $listOne, $categoriesOne);
				
				}
				
				// Get list of arrets , Arrange each arret in his categorie
				$listAll       = $arretClass->get_all_day($dateArret);
				$categoriesAll = $arretClass->arrange_categorie($listAll);
	
				// Get users , Users who want updates each day : all
				$usersAll      = $userClass->get_user_abo_active('all');	
				$userArretsAll = $userClass->assignArretsUsers($usersAll, $listAll, $categoriesAll);
				
			    if( $currentday == $daySend )
				{
					$userEmails = $userArretsOne + $userArretsAll;
				}
				else
				{
					$userEmails = $userArretsAll;
				}		
				
				// Loop over all users=>arrets array, Prepare the newsletter html, Get email of user
				// Send the newsletter	
				foreach($userEmails as $theuser => $all)
				{	
					$html  = $userClass->setEmailHtml($theuser, $all);	
							 
					$us    = get_userdata( $theuser );
					$email = $us->user_email;
					
					add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
				    wp_mail($email, 'Nouveaux arrêts', $html );
					
					add_filter( 'wp_mail_content_type', create_function('', 'return "text/html"; '));
					wp_mail('archives@leschaud.ch', 'Nouveaux arrêts pour '.$email, $html);

				}
				
			} // end if date && maj ok
*/
			
	
			/*=========================================	
				END new envois
			==========================================*/
	
	}
	
	function wps_new_options_page_gestion(){
	////////////////////////////////////////////////////////////////////////
		
		global $wpdb;	
		/*===============================================
				Mise à jour
		=================================================*/
	
		// Get last date
		$dernier_date_arret = $wpdb->get_results('SELECT * FROM wp_nouveautes ORDER BY datep_nouveaute DESC LIMIT 0,1 ');	
	  	$dateArret          = $dernier_date_arret[0]->datep_nouveaute; 
		
		// has to be greater than yesterday
	    $beforetoday     = date("Y-m-d", strtotime("-1 day"));
		$timestampbefore = strtotime($beforetoday);
		$yesterday       = date("ymd", $timestampbefore);
		
		$timestamp       = strtotime($dateArret);
		$derniereDateBd  = date("ymd", $timestamp);
		
		// Get list of dates from TF website
		$datesListTF = getListDate();
				
		// boucle sur la liste du tf
		foreach($datesListTF as $testDate)
		{ 
			// test si cela resseble a une date
			if (preg_match('/^\d{6}$/' , $testDate)) {
				
				// test si la date est déjà recupéré 
				// For live , add condition if not date of yesterday
				/* 
				if( ($derniereDateBd != $testDate) && ($derniereDateBd < $testDate) && ($yesterday < $testDate) )
				{
					$listRecuperer[] = $testDate;
				}
				*/
				
				if( ($derniereDateBd != $testDate) && ($derniereDateBd < $testDate) && ($yesterday < $testDate)  )
				{
					$listRecuperer[] = $testDate;
				}
			}
		}		
	
		if($listRecuperer){
		
			asort($listRecuperer);		
			
			foreach($listRecuperer as $dateRecuperer)
			{
				// the date to get
				$date  = $dateRecuperer;
				// empty array
				$dates = array();
				// split date 2x2 digit => 14 03 10 so 3 mars 2014 from 140310
				$arrays         = str_split($date,2);
				// recompose the date in mysql format 2014-03-10
				$recomposedDate = '20'.$arrays[0].'-'.$arrays[1].'-'.$arrays[2];
				// put in array of dates to get
				$dates[] = $recomposedDate;
				
				// url to list of link with dates from TF website
				$url = 'http://relevancy.bger.ch/AZA/liste/fr/';
				
				// grab the data with curl from date
 		    	$theData = grabData($url, $date);
			
				$dataCleaned = cleanFormat2($theData, $dates);
				
				// Get rearranded arrays				
				$dataTable    = $dataCleaned['dataArray'];
				$dataCategory = $dataCleaned['catArray'];
				
				// Insert categories if dosen't exist in db
				$verifyCategory = existCategorie($dataCategory);
				$notIn          = $verifyCategory['arrayNo'];

				//if($notIn){ $newCats = addCategory($notIn); }
				
				// prepare the each arret array with number of categorie from db and language
				$prepared = arrangeStack($dataTable);
				
				echo '<pre>';
				print_r($prepared);
				echo '</pre>';
				
				
			}
		}
		else
		{
			//echo 'base à jour';
		}
		
		/*
		$dated  = '2014-02-25';
		$numero = '5A_641/2013';
		
		$date  = new DateTime($dated);
		$ndate = $date->format('d-m-Y');

		$numero = str_replace("/","-",$numero);

		// insert							
		$host = 'http://relevancy.bger.ch';
		$new  = 'http://relevancy.bger.ch/php/aza/http/index.php?lang=fr&zoom=&type=show_document&highlight_docid=aza%3A%2F%2F';
		$url  = $new.$ndate.'-'.$numero;
		
		$texte_nouveaute = getArret($url, $host);	
		
		echo $texte_nouveaute;
		*/

	}

	/*******************************************
		Constances variables and files
	********************************************/
	
	require_once( plugin_dir_path( __FILE__ ). '/pagination.class.php');
	

	function my_admin_init() {
		
		global $pagenow;
  		
		 if ( 'admin.php' == $pagenow )
    	 {
			$pluginfolder = get_bloginfo('url') . '/' . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__));
			
			//wp_enqueue_script('jquery-ui-core');
			
			wp_enqueue_script('jquery-ui-datepicker', $pluginfolder . '/jquery.ui.datepicker.min.js', array('jquery', 'jquery-ui-core') );
			wp_enqueue_style('jquery.ui.theme', $pluginfolder . '/css/smoothness/jquery-ui-1.8.24.custom.css');
		
		}
	}
	add_action('admin_init', 'my_admin_init');
	
	function my_admin_footer() {
		global $pagenow;
  		
		 if ( 'admin.php' == $pagenow )
    	 {	
		?>
			<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery('.mydatepicker').datepicker({
					dateFormat : 'yy-mm-dd'
				});
			});
			</script>
		<?php
		 }
	}
	add_action('admin_footer', 'my_admin_footer');

	/******************************************
	
		Liste nouveaux arrets 
		
	******************************************/
	
	function wps_new_options_page () {
		
	global $wpdb;
	
	if($_POST['submit']){
		
		    $id_nouveaute = $_POST['id_nouveaute'];
			$datep_nouveaute  = $_POST['datep_nouveaute'];
			$dated_nouveaute  = $_POST['dated_nouveaute'];
			$link_nouveaute   = $_POST['link_nouveaute'];
			$numero_nouveaute = $_POST['numero_nouveaute'];
			$texte_nouveaute  = $_POST['texte_nouveaute'];
			$langue_nouveaute = $_POST['langue_nouveaute'];
			$categorie_nouveaute = $_POST['categorie_nouveaute'];
			
			$data = array( 
				'datep_nouveaute' => $datep_nouveaute,  
				'dated_nouveaute' => $dated_nouveaute, 
				'categorie_nouveaute' => $categorie_nouveaute,  
				'link_nouveaute' => $link_nouveaute,
				'numero_nouveaute' => $numero_nouveaute,
				'texte_nouveaute' => $texte_nouveaute,
				'langue_nouveaute' => $langue_nouveaute	  
			); 
			
			if( $wpdb->update( 'wp_nouveautes', $data , array( 'id_nouveaute' => $id_nouveaute), array( '%s'), array( '%d' )) === FALSE )
			{	
				echo '<div id="message" class="error below-h2"><p>Erreur </p></div>';
			}
			else  
			{ 
				echo '<div id="message" class="updated below-h2"><p> Arret mis à jour </p></div>'; 
			}	
			
	}
		
		// Effacer une catégorie
		
	if($_GET['id'] && ( $_GET['action'] == "delete") )
	{
			$id_nouveaute = $_GET['id'];
			
			if( $wpdb->query(' DELETE FROM wp_nouveautes WHERE id_nouveaute = "'.$id_nouveaute.'" ') === FALSE )
			{	
				echo '<div id="message" class="error below-h2"><p>Erreur </p></div>';
			}
			else  
			{ 
				echo '<div id="message" class="updated below-h2"><p> Arret effacée </p></div>'; 
			}

	}
		
		// Editer et afficher
		
	if($_GET['id'] && ( $_GET['action'] == "edit") )
	{
		$theId = $_GET['id'];
	
		$cat_list = $wpdb->get_results('SELECT * FROM wp_custom_categories');
		$arret = $wpdb->get_results('SELECT * FROM wp_nouveautes WHERE id_nouveaute = "'.$theId.'" ');
		$catego = $arret[0]->categorie_nouveaute;
		
		?>
		
	    <div class="wrap">
		<div id="icon-options-general" class="icon32"><br></div>
	    <h2>&Eacute;diter arrêt</h2><br/>
	    
		<div id="wpsEditForm">	    
	    	<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		    <ul class="wps_form">
		    
		      <li><label for="datep_nouveaute">Date publication</label>
		      <input id="datep_nouveaute" size="10" class="mydatepicker" name="datep_nouveaute" value="<?php echo $arret[0]->datep_nouveaute; ?>" /></li>   
		             
		      <li><label for="dated_nouveaute">Date décision </label>
		      <input id="dated_nouveaute" size="10"  class="mydatepicker"  name="dated_nouveaute" value="<?php echo $arret[0]->dated_nouveaute; ?>" /></li>
	            	             
		      <li><label for="link_nouveaute">Lien </label>
		      <input id="link_nouveaute" size="20" name="link_nouveaute" value="<?php echo $arret[0]->link_nouveaute; ?>" /></li>
		        
		      <li><label for="numero_nouveaute">Numéro </label>
		      <input id="numero_nouveaute" size="20" name="numero_nouveaute" value="<?php echo $arret[0]->numero_nouveaute; ?>" /></li>
		        
		      <li style="heigth:400px;" ><label for="texte_nouveaute">Texte </label>
		      <textarea id="texte_nouveaute" style="width:400px;display:block; height:300px;" name="texte_nouveaute"><?php echo $arret[0]->texte_nouveaute; ?></textarea></li>
		        
		       <li><label for="categorie_nouveaute">Catégorie</label>
			        <select name="categorie_nouveaute" style="width:350px;">
				        <option value="">Choix</option>
				        <?php
				        
				        if($cat_list){
					        foreach ($cat_list as $cat) {
					         $thecat = $cat->term_id;
					        	echo '<option ';
					        	if( $thecat == $catego){echo 'selected="selected" ';}
					        	echo ' value="'.$cat->term_id.'">'.$cat->name.'</option>';
					        }
					     }
				        ?>
			        </select>
		       </li>
		        
		       <li><label for="langue_nouveaute">Langue </label>
			        <select name="langue_nouveaute">
				        <option <?php if($arret[0]->langue_nouveaute == 0 ){echo 'selected="selected"';} ?> value="0">Français</option>
				        <option <?php if($arret[0]->langue_nouveaute == 1 ){echo 'selected="selected"';} ?> value="1">Allemand</option>
				        <option <?php if($arret[0]->langue_nouveaute == 2 ){echo 'selected="selected"';} ?> value="2">Italien</option>
			        </select>
		       </li>
		        
	  		</ul>
	  		<input type="hidden" name="id_nouveaute" value="<?php echo $theId ?>" />
	            <p class="wps_button"><input class="button-primary" type="submit" value="&Eacute;diter" name="submit"></p>
		  
			</form>
	    </div>
	        
	    </div>
	<?php
	}
	 
	else if($_GET['datep'])
	{
	$theDate = $_GET['datep'];
	
	$new_list_arret = $wpdb->get_results('SELECT wp_nouveautes.* , wp_custom_categories.name as nameCat , wp_custom_categories.*, 
												 wp_subcategories.name as nameSub , wp_subcategories.*
										  FROM wp_nouveautes 
										  JOIN wp_custom_categories on wp_custom_categories.term_id = wp_nouveautes.categorie_nouveaute 
										  LEFT JOIN wp_subcategories on wp_subcategories.refNouveaute = wp_nouveautes.id_nouveaute 
										  WHERE wp_nouveautes.datep_nouveaute = "'.$theDate.'"  ');	
	?>

	<div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div>
    <h2>Nouveaux arrêts date <?php echo mysql2date('j M Y', $theDate); ?></h2>
    <br/>  
    <a class="button-secondary" href="admin.php?page=wps_new_admin" title="">Retour</a>
    <p class="clear"></p>
     
	 <table class="widefat">
        <thead>
		<tr>
			<th width="105">Action</th>
            <th>Date de décisions</th>
	        <th>Catégorie</th>
            <th>Sous-catégorie</th>
            <th>Lien</th>
            <th>Numéro</th>
            <th>Langue</th>
        </tr>
		</thead>
        <tfoot>
		<tr>
			<th width="105">Action</th>
            <th>Date de décisions</th>
	        <th>Catégorie</th>
            <th>Sous-catégorie</th>
            <th>Lien</th>
            <th>Numéro</th>
            <th>Langue</th>
        </tr>
		</tfoot>
		<tbody>
			<?php
			$langue = array('Fr','All','It');
			if($new_list_arret){
				foreach ($new_list_arret as $new) {
					
					$datep = mysql2date('j M Y', $new->datep_nouveaute);
					$dated = mysql2date('j M Y', $new->dated_nouveaute);
					// id_nouveaute

					echo '<tr>';
					echo '<td width="115"><a href="admin.php?page=wps_new_admin&id='.$new->id_nouveaute.'&action=edit">éditer</a>|<a href="admin.php?page=wps_new_admin&id='.$new->id_nouveaute.'&action=delete">supprimer</a></td>';
					echo '<td style="background:#fff;"><strong>'.$dated.'</strong></td>';
					echo '<td style="background:#fff;"><strong>'.$new->nameCat.'</strong></td>';
					echo '<td style="background:#fff;"><strong>'.$new->nameSub.'</strong></td>';
					echo '<td style="background:#fff;"><strong><a href="'.$new->link_nouveaute.'">'.$new->link_nouveaute.'</a></strong></td>';
					echo '<td style="background:#fff;"><strong>'.$new->numero_nouveaute.'</strong></td>';
					echo '<td style="background:#fff;"><strong>'.$langue[$new->langue_nouveaute].'</strong></td>';
					echo '</tr>';	
				}
			}
			else{ echo '<tr><td colspan="2">Rien trouvé</td></tr>'; }
			?>
		</tbody>
     </table>
     <br/>
     <a class="button-secondary" href="admin.php?page=wps_new_admin" title="">Retour</a>
	</div>
    <?php
	}
	else{	
										   									   
		$items = mysql_num_rows(mysql_query("SELECT * FROM wp_nouveautes GROUP BY datep_nouveaute")); // number of total rows in the database
		
		if($items > 0) {
			 $p = new pagination;
			 $p->items($items);
			 $p->limit(15); // Limit entries per page
			 $p->target("admin.php?page=wps_new_admin");
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
			 $limit = "LIMIT " . ($p->page - 1) * $p->limit . ", " . $p->limit;
			
		} 
		else  
		{  
			echo "No Record Found"; 
		}
											  
	$new_list = $wpdb->get_results('SELECT COUNT(*) as counted , datep_nouveaute 
										   FROM wp_nouveautes 
										   GROUP BY datep_nouveaute
										   ORDER BY datep_nouveaute DESC  '.$limit.' ');
	
  ?>

	<div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div>
    <h2>Admin nouveaux arrêts</h2>
    <br/>
    <div class="tablenav">
		 <div class='tablenav-pages'>
			 <?php echo $p->show(); // Echo out the list of paging. ?>
		 </div>
	</div>
    
	 <table class="widefat minitable">
        <thead>
		<tr>
            <th width="100">Décisions</th>
	        <th>Date publication</th>
        </tr>
		</thead>
        <tfoot>
		<tr>
            <th width="100">Décisions</th>
	        <th>Date publication</th>
        </tr>
		</tfoot>
		<tbody>
			<?php
			if($new_list){
				foreach ($new_list as $new) {
					
					$datep = mysql2date('j M Y', $new->datep_nouveaute);
					 
					echo '<tr>';
					echo '<td style="background:#fff;padding:5px 0 5px 15px;"><strong>'.$new->counted.'</strong></td>';
					echo '<td style="background:#fff;"><strong><a href="admin.php?page=wps_new_admin&datep='.$new->datep_nouveaute.'">'.$datep.'</a>
					</strong></td>';
					echo '</tr>';	
				}
			}
			else{ echo '<tr><td colspan="2">Rien trouvé</td></tr>'; }
			?>
		</tbody>
     </table>
        
	</div>

	<?php
		}
	}
	
	/******************************************
	
		Liste categories et edit
		
	******************************************/
		
	
	function wps_new_options_page_list(){
		global $wpdb;
		
		// Editer la catégorie
		
		if($_POST['submit']){
		
			$id = $_POST['catid'];
			$fname = $_POST['fname'];
			$aname = $_POST['aname'];
			$iname = $_POST['iname'];
			
			$data = array( 'name' => $fname,  'name_de' => $aname,  'name_it' => $iname );  
			
			if( $wpdb->update( 'wp_custom_categories', $data , array( 'term_id' => $id), array( '%s'), array( '%d' )) === FALSE )
			{	
				echo '<div id="message" class="error below-h2"><p>Erreur </p></div>';
			}
			else  
			{ 
				echo '<div id="message" class="updated below-h2"><p> Catégorie mise à jour </p></div>'; 
			}
		}
		
		// Effacer une catégorie
		
		if($_GET['id'] && ( $_GET['action'] == "delete") )
		{
			$theCategorie = $_GET['id'];
			
			if( $wpdb->query(' DELETE FROM wp_custom_categories WHERE term_id = "'.$theCategorie.'" ') === FALSE )
			{	
				echo '<div id="message" class="error below-h2"><p>Erreur </p></div>';
			}
			else  
			{ 
				echo '<div id="message" class="updated below-h2"><p> Catégorie effacée </p></div>'; 
			}

		}
		
		// Editer et afficher
		
		if($_GET['id'] && ( $_GET['action']== "edit") )
		{
		$theCategorie = $_GET['id'];
	
		$new_editer = $wpdb->get_results('SELECT * FROM wp_custom_categories  WHERE wp_custom_categories.term_id  = "'.$theCategorie.'"  ');
		
		?>
	    <div class="wrap">
		<div id="icon-options-general" class="icon32"><br></div>
	    <h2>&Eacute;diter <?php echo $new_editer[0]->name; ?></h2><br/>
	    
		<div id="wpsEditForm">	    
	    	<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		    <ul class="wps_form">
		        <li><label for="fname">Titre français: </label>
		        <input id="fname" size="20"  name="fname" value="<?php echo $new_editer[0]->name; ?>" /></li>   
		             
		        <li><label for="aname">Titre allemand: </label>
		        <input id="aname" size="20"  name="aname" value="<?php echo $new_editer[0]->name_de; ?>" /></li>
	            	             
		        <li><label for="iname">Titre italien: </label>
		        <input id="iname" size="20" name="iname" value="<?php echo $new_editer[0]->name_it; ?>" /></li>
	  		</ul>
	        <br/>
	        	<input type="hidden" name="catid" value="<?php echo $theCategorie ?>" />
	            <p class="wps_button"><input class="button-primary" type="submit" value="Mettre à jour" name="submit"></p>
		  
			</form>
	    </div>
	    
	        <a class="button-secondary" href="admin.php?page=wps_new_list" title="">Retour</a>
	        
	    </div>
	    <?php
		}
		else{									   									   
		$items = mysql_num_rows(mysql_query("SELECT * FROM wp_custom_categories")); // number of total rows in the database	
		if($items > 0) {
				 $p = new pagination;
				 $p->items($items);
				 $p->limit(15); // Limit entries per page
				 $p->target("admin.php?page=wps_new_list");
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
			 $limit = "LIMIT " . ($p->page - 1) * $p->limit . ", " . $p->limit;
		} else  {  echo "No Record Found"; }
		
		$cat_list = $wpdb->get_results('SELECT * FROM wp_custom_categories ORDER BY name ASC  '.$limit.' ');
											  
		?>
	
		<div class="wrap">
		<div id="icon-options-general" class="icon32"><br></div>
	    <h2>Liste catégories</h2><br/>
	    <div class="tablenav">
			 <div class='tablenav-pages'>
				 <?php echo $p->show(); // Echo out the list of paging. ?>
			 </div>
		</div>
		 <table class="widefat">
	        <thead>
			<tr>
	        	<th width="105">Action</th>
		        <th>Titre français</th>
	            <th>Titre allemand</th>
	            <th>Titre italien</th>
	        </tr>
			</thead>
	        <tfoot>
			<tr>
	        	<th width="105">Action</th>
		        <th>Titre français</th>
	            <th>Titre allemand</th>
	            <th>Titre italien</th>
	        </tr>
			</tfoot>
			<tbody>
				<?php
				if($cat_list){
					foreach ($cat_list as $cat) {
						echo '<tr>';
						echo '<td width="105"><a href="admin.php?page=wps_new_list&id='.$cat->term_id.'&action=edit">éditer</a> |
								  <a href="admin.php?page=wps_new_list&id='.$cat->term_id.'&action=delete">supprimer</a></td>';
						echo '<td style="background:#fff;"><strong>'.$cat->name.'</strong></td>';
						echo '<td style="background:#fff;"><strong>'.$cat->name_de.'</strong></td>';
						echo '<td style="background:#fff;"><strong>'.$cat->name_it.'</strong></td>';
						echo '</tr>';	
					}
				}
				else{ echo '<tr><td colspan="3">Rien trouvé</td></tr>'; }
				?>
			</tbody>
	     </table>
		</div>
	
	<?php
		}
	}
			
	/******************************************
	
		Add categorie
		
	******************************************/
		
	
	function wps_new_options_page_addcat(){
		global $wpdb;
				
		if($_POST['submit']){
		
			$fname = $_POST['fname'];
			$aname = $_POST['aname'];
			$iname = $_POST['iname'];
			
			$data = array( 'name' => $fname,  'name_de' => $aname,  'name_it' => $iname );  
			
			if( $wpdb->insert( 'wp_custom_categories', $data , array( '%s')) === FALSE )
			{	
				echo '<div id="message" class="error below-h2"><p>Erreur </p></div>';
			}
			else  
			{ 
				echo '<div id="message" class="updated below-h2"><p> Catégorie ajouté </p></div>'; 
			}
		}
		
		?>
	    <div class="wrap">
		<div id="icon-options-general" class="icon32"><br></div>
	    <h2>Ajouter catégorie</h2><br/>
	    
		<div id="wpsEditForm">	    
	    	<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		    <ul class="wps_form">
		        <li><label for="fname">Titre français: </label>
		        <input id="fname" size="20"  name="fname" value="" /></li>   
		             
		        <li><label for="aname">Titre allemand: </label>
		        <input id="aname" size="20"  name="aname" value="" /></li>
	            	             
		        <li><label for="iname">Titre italien: </label>
		        <input id="iname" size="20" name="iname" value="" /></li>
	  		</ul>
	        <br/>
	            <p class="wps_button"><input class="button-primary" type="submit" value="Ajouter" name="submit"></p>
			</form>
	    </div>
	        
	    </div>
	    <?php
	}
				
	/******************************************
	
		Add Arrets
		
	******************************************/	
	
	function wps_new_options_page_addarret(){
		global $wpdb;
		
		$cat_list = $wpdb->get_results('SELECT * FROM wp_custom_categories');
				
		if($_POST['submit']){
		
			$datep_nouveaute = $_POST['datep_nouveaute'];
			$dated_nouveaute = $_POST['dated_nouveaute'];
			$link_nouveaute = $_POST['link_nouveaute'];
			$numero_nouveaute = $_POST['numero_nouveaute'];
			$texte_nouveaute = $_POST['texte_nouveaute'];
			$langue_nouveaute = $_POST['langue_nouveaute'];
			$categorie_nouveaute = $_POST['categorie_nouveaute'];
			
			$data = array( 
				'datep_nouveaute' => $datep_nouveaute,  
				'dated_nouveaute' => $dated_nouveaute, 
				'categorie_nouveaute' => $categorie_nouveaute,  
				'link_nouveaute' => $link_nouveaute,
				'numero_nouveaute' => $numero_nouveaute,
				'texte_nouveaute' => $texte_nouveaute,
				'langue_nouveaute' => $langue_nouveaute	  
			); 
			
			if( $wpdb->insert( 'wp_nouveautes', $data , array( '%s')) === FALSE )
			{	
				echo '<div id="message" class="error below-h2"><p>Erreur </p></div>';
			}
			else 
			{ 
				 echo '<div id="message" class="updated below-h2"><p> Catégorie ajouté </p></div>'; 
			}
		}
		
		?>
	    <div class="wrap">
		<div id="icon-options-general" class="icon32"><br></div>
	    <h2>Ajouter arrêt</h2><br/>
	    
		<div id="wpsEditForm">	    
	    	<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		    <ul class="wps_form">
		    
		        <li><label for="datep_nouveaute">Date publication</label>
		        <input id="datep_nouveaute" size="10" class="mydatepicker"  name="datep_nouveaute" value="" /></li>   
		             
		        <li><label for="dated_nouveaute">Date décision </label>
		        <input id="dated_nouveaute" size="10"  class="mydatepicker"  name="dated_nouveaute" value="" /></li>
	            	             
		        <li><label for="link_nouveaute">Lien </label>
		        <input id="link_nouveaute" size="20" name="link_nouveaute" value="" /></li>
		        
		        <li><label for="numero_nouveaute">Numéro </label>
		        <input id="numero_nouveaute" size="20" name="numero_nouveaute" value="" /></li>
		        
		        <li style="heigth:400px;" ><label for="texte_nouveaute">Texte </label>
		        <textarea id="texte_nouveaute" style="width:400px;display:block; height:300px;" name="texte_nouveaute"></textarea></li>
		        
		        <li><label for="categorie_nouveaute">Catégorie</label>
			        <select name="categorie_nouveaute" style="width:350px;">
				        <option value="">Choix</option>
				        <?php
				        if($cat_list){
					        foreach ($cat_list as $cat) {
					        	echo '<option value="'.$cat->term_id.'">'.$cat->name.'</option>';
					        }
					     }
				        ?>
			        </select>
		        </li>
		        
		        <li><label for="langue_nouveaute">Langue </label>
			        <select name="langue_nouveaute">
				        <option value="0">Français</option>
				        <option value="1">Allemand</option>
				        <option value="2">Italien</option>
			        </select>
		        </li>
		        
	  		</ul>
	            <p class="wps_button"><input class="button-primary" type="submit" value="Ajouter" name="submit"></p>
		  
			</form>
	    </div>
	        
	    </div>
	    <?php
	  }

	/*******************************
		Custom meta for user
		Categories inscriptions
	********************************/
		
	/*define( 'MY_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
	define( 'MY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	
	require_once( MY_PLUGIN_PATH . '/data_functions.php');
	
	add_action( 'show_user_profile', 'my_show_extra_profile_fields' );
	add_action( 'edit_user_profile', 'my_show_extra_profile_fields' );
	
	function my_show_extra_profile_fields( $user ) {
		
		require_once( plugin_dir_path( __FILE__ ). '/user_functions.php');
		
		global $wpdb;	
		
		$useridget =  $_GET['user_id'];
		
		if( $useridget ){
			$user_id = $_GET['user_id'];
		}
		else{
			global $current_user;
			get_currentuserinfo();
			$user_id = $current_user->ID;
		}

		$userHasCat = array();
		
		$userCategories = $wpdb->get_results('SELECT * FROM wp_user_abo WHERE refUser = "'.$user_id.'" ');
		
		if($userCategories)
		{
			foreach($userCategories as $hasCat)
			{
				$userHasCat[] = $hasCat->refCategorie;
			}
		}
		
			$dernier_date_arret = $wpdb->get_results('SELECT * FROM wp_nouveautes ORDER BY datep_nouveaute DESC LIMIT 0,1 ');	
			$dateArret = $dernier_date_arret[0]->datep_nouveaute; 
			
		

	?>
	
		<h3>Catégories</h3>
		<table class="form-table">
			<tr>
				<th><label for="abo">Abonnements</label></th>
				<td>
					<fieldset>
							<ul class="checklist">
	                        <?php
							$cat_list = $wpdb->get_results('SELECT * FROM wp_custom_categories ');
							if($cat_list){
								$i = 0;
								foreach ($cat_list as $cat) { 
									echo '<li>';
									echo '<input id="choice_'.$i.'" name="selectCat[]" ';
									if($userHasCat){
										if ( in_array( $cat->term_id , $userHasCat)) {
										    echo 'checked="checked"';
										}
									}
									echo ' value="'.$cat->term_id.'" type="checkbox">';
									echo '<label for="choice_'.$i.'">'.$cat->name.'</label>';
									echo '<a class="checkbox-select" href="#">Select</a>';
									echo '<a class="checkbox-deselect" href="#">Annuler</a>';
									echo '</li>';
									
									$i++;
								}
							}
							?>
							</ul>
						</fieldset>
				</td>
			</tr>
		</table>
	<?php 
	}*/
/*
	add_action( 'personal_options_update', 'save_category_profile_fields' );
	add_action( 'edit_user_profile_update', 'save_category_profile_fields' );*/
	
	/*function save_category_profile_fields( $user_id ) {	
		global $wpdb;
		
		if ( !current_user_can( 'edit_user', $user_id ) )
		{return false;}
		
		$newCategories = $_POST['selectCat'];
		$listCurrent = array();
  
		    if( $_POST['selectCat'] )
		    {
			    $currentCategories = $wpdb->get_results('SELECT * FROM wp_user_abo WHERE refUser = "'.$user_id.'" ');
				
				if($currentCategories){
					foreach($currentCategories as $current)
					{
						$listCurrent[] = $current->refCategorie;
					}
				}
				$added = array_diff( $newCategories , $listCurrent );
				$deleted = array_diff( $listCurrent , $newCategories );

				 if( $added )
				 {	
					 foreach($added as $add)
					 {
					  if( $wpdb->query(' INSERT INTO wp_user_abo SET refUser = "'.$user_id.'" , refCategorie = "'.$add.'" ') === FALSE )
						 { return false;  }
					 }	
				 }
				 if( $deleted )
				 {		
					 foreach($deleted as $del)
					  {
						 if( $wpdb->query(' DELETE FROM wp_user_abo WHERE refCategorie = "'.$del.'" AND refUser = "'.$user_id.'" ') === FALSE )
						 { return false; }
					  }	
				 }
			}
			else
			{
				if( $wpdb->query(' DELETE FROM wp_user_abo WHERE refUser = "'.$user_id.'" ') === FALSE )
				{return false;}
			}
		return true;	
	}*/
		
	
	/******************************************
	
		Menu admin 
		
	******************************************/
	
	function wps_new_menu () {
	add_menu_page('Theme page title','Nouveaux arrêts','manage_options','wps_new_admin', 'wps_new_options_page');
	
	  add_submenu_page('wps_new_admin','Catégories nouveautés','Catégories nouveautés','manage_options', 'wps_new_list', 'wps_new_options_page_list');
	  add_submenu_page('wps_new_admin','Ajouter catégorie','Ajouter catégorie','manage_options','wps_new_addcat','wps_new_options_page_addcat');
	  add_submenu_page('wps_new_admin','Ajouter arrêt','Ajouter arrêt','manage_options','wps_new_addarret','wps_new_options_page_addarret');
	  add_submenu_page('wps_new_admin','Gestion','Gestion','manage_options','wps_new_gestion','wps_new_options_page_gestion');
	  
	}
	
	add_action('admin_menu','wps_new_menu');
	
	/******************************************
	
		Initialization
		
	******************************************/
/*	add_action('admin_init', 'user_category_init');
	
	function user_category_init() {
		
	   		wp_register_script( 'main_script', plugins_url( basename(dirname(__FILE__)) .'/js/main.js'));
	   		wp_enqueue_script( 'main_script' );	
	}*/
	
	function admin_register_headcss() {
	    $siteurl = get_option('siteurl');
	    $url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/css/style.css';
	    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
	}
	
	add_action('admin_head', 'admin_register_headcss');
	