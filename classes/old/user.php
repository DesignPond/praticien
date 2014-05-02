<?php 

require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/arret.php');
require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/data.php');

class User {

	protected $arret;
	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/

	function __construct() {
		
		$this->arret = new Arret();
	}
	
	/*
	 * Get current user abos list
	*/
	
	public function get_user_abo($when){
		
		global $wpdb;
		
		$pub_user_abos = array();
		
		// Get user categories ans pub
		$user_abos_pub  = $wpdb->get_results('SELECT * FROM wp_user_abo_pub');	
		
		if( !empty($user_abos_pub) ){
		    foreach($user_abos_pub as $userpub){
		  		$pub_user_abos[$userpub->refUser][$userpub->refCategorie] = $userpub->ispub;
		    }
		}
		
		$email_user_abos = array();
	
		$list_user_abos  = $wpdb->get_results('SELECT    refUser,refCategorie,keywords ,keywords , user_email
		 									  FROM       wp_user_abo 
		 									  LEFT JOIN  wp_usermeta on wp_usermeta.user_id = wp_user_abo.refUser 
											  LEFT JOIN  wp_users on wp_users.ID = wp_user_abo.refUser 
				 							  WHERE      wp_usermeta.meta_key = "rythme_abo" AND wp_usermeta.meta_value = "'.$when.'" ');	
				 							  	 								  		
		if($list_user_abos)
		{	
			foreach($list_user_abos as $user)
			{		
				if(!empty($user->keywords))
				{
					$email_user_abos[$user->refUser][$user->refCategorie]['keywords'][] = $user->keywords;
				}
				else
				{
					$email_user_abos[$user->refUser][$user->refCategorie]['keywords'] = '';
				}
				
				if( isset($pub_user_abos[$user->refUser][$user->refCategorie]) ){
					$email_user_abos[$user->refUser][$user->refCategorie]['ispub'] = $pub_user_abos[$user->refUser][$user->refCategorie];
				}
				else{
					$email_user_abos[$user->refUser][$user->refCategorie]['ispub'] = 0;
				}
			}
		}
		
		return $email_user_abos;
	}

	/*
	 * Get test current user abos list
	*/
	
	public function get_user_abo_active($when){
		
		global $wpdb;
		
		$pub_user_abos = array();
		
		$currentdate  = date('Y-m-d');
		
		// Get user categories ans pub
		$user_abos_pub  = $wpdb->get_results('SELECT * FROM wp_user_abo_pub');	
		
		if( !empty($user_abos_pub) ){
		    foreach($user_abos_pub as $userpub){
		  		$pub_user_abos[$userpub->refUser][$userpub->refCategorie] = $userpub->ispub;
		    }
		}
		
		$email_user_abos = array();
	
		$list_user_abos  = $wpdb->get_results('SELECT    refUser,refCategorie,keywords ,keywords , user_email
		 									  FROM       wp_user_abo 
		 									  LEFT JOIN  wp_usermeta on wp_usermeta.user_id = wp_user_abo.refUser 
											  LEFT JOIN  wp_users on wp_users.ID = wp_user_abo.refUser 
				 							  WHERE      wp_usermeta.meta_key = "rythme_abo" AND wp_usermeta.meta_value = "'.$when.'" 
				 							  ');	
				 							  	 								  		
		if($list_user_abos)
		{	
			foreach($list_user_abos as $user)
			{	
				// Test if user is valid
				$valid = get_user_meta($user->refUser, 'date_abo_active', true);
				
				if($valid > $currentdate)
				{					
					if(!empty($user->keywords))
					{
						$email_user_abos[$user->refUser][$user->refCategorie]['keywords'][] = $user->keywords;
					}
					else
					{
						$email_user_abos[$user->refUser][$user->refCategorie]['keywords'] = '';
					}
					
					if( isset($pub_user_abos[$user->refUser][$user->refCategorie]) ){
						$email_user_abos[$user->refUser][$user->refCategorie]['ispub'] = $pub_user_abos[$user->refUser][$user->refCategorie];
					}
					else{
						$email_user_abos[$user->refUser][$user->refCategorie]['ispub'] = 0;
					}
				}
			}
		}
		
		return $email_user_abos;
	}

	/*
	 * Get arrets for users
	*/
	
	public function assignArretsUsers($users , $list , $categories){
		
		$userArrets = array();
				
		foreach($users as $user => $cat)
		{
			foreach($cat as $key => $listes )
			{
				$words = NULL;
				$isPub = NULL;
				
				if( !empty($listes['keywords']) )
				{
					$words = $listes['keywords'];
				}

				if( isset($listes['ispub']))
				{
					$isPub = $listes['ispub'];
				}
				
				if($key == 247)
				{
					$allArrets  = array_keys($list);
					$dispArrets = $this->arret->dispatch_arret_keyword($allArrets, $list, $words , $isPub);
						
					if(!empty($dispArrets))
					{
						foreach($dispArrets as $id => $dispa)
						{
							if( isset($userArrets[$user][$id]) )
							{
								$dispa .= ' '.$userArrets[$user][$id];
							}
							
							$dispa = trim($dispa);
							
							$userArrets[$user][$id] = $dispa;
						}
					}
				}
				else
				{
					if( isset($categories[$key]) )
					{
						$listArrets = $categories[$key];
						$allArrets  = $this->arret->listIdArretsCategorie($listArrets);	
						$dispArrets = $this->arret->dispatch_arret_keyword($allArrets, $list, $words , $isPub);
						
						if(!empty($dispArrets))
						{
							foreach($dispArrets as $id => $dispa)
							{
								if( isset($userArrets[$user][$id]) )
								{	
									$dispa .= ' '.$userArrets[$user][$id];
								}
								
								$dispa = trim($dispa);
								
								$userArrets[$user][$id] = $dispa;
							}
						}		
					}
				}
			}			
		}
		
		return $userArrets;
	}
	

	public function setEmailHtml($user, $list){
		
		 global $wpdb;
		 
		 $html = ''; 
		 	
		 $urlRoot   = home_url('/');
		 $pageRoot  = 1143;
		 $userInfos = get_user_meta($user); 

		 $nom    = ''; 
		 $prenom = '';	
		 
		 if( isset($userInfos['last_name'][0]) )
		 {
			 $nom = $userInfos['last_name'][0]; 
		 }
		 if( isset($userInfos['first_name'][0]) )
		 {
			 $prenom = $userInfos['first_name'][0];
		 }

		 // Wrapper 
		 $html .= '<table align="center" style="border:1px solid #dddddd;background:#ffffff;font-family:arial,sans serif; padding:5px; margin:0; width:720px; display:block;">';
		 $html .= '<tr>'; 
		 $html .= '<td>';
		 
		 $html .= '<table width="100%" style="border:none; text-align:left; background:#b2c9d7; font-family:arial,sans serif;height:75px;">';
		 $html .= '<tr valign="middle"><td style="height:50px; display:block;">'; 
		 $html .= '<h1 style="display:block; padding:0 5px; color:#fff; font-size:25px;"><span style="color:#0f4060;">Droit</span> pour le Praticien</h1>';  
		 $html .= '</td></tr>'; 
		 $html .= '</table>'; 
		 
		 $html .= '<p style="color:#000; font-size:15px; margin-bottom:20px;font-family:arial,sans serif; line-height:20px; ">Bonjour';
		 $html .= '<strong> '.$prenom. ' ' .$nom.'</strong>';
		 $html .= ',<br/>Voici les derniers arr&ecirc;ts correspondant &agrave; vos abonnements</p>';
		 
		 // Debut du mail
		 $html .= '<table style="border:none; text-align:left; font-family:arial,sans serif; " width="100%">';
		 $html .= '<tr style="background:#0f4060; text-align:left; color:#ffffff; font-weight:bold;">
		 		   <th width="75" style="padding:5px;font-size:12px; color:#ffffff;">Date de publication</th>
				   <th width="75" style="padding:5px;font-size:12px; color:#ffffff;">Date de d&eacute;cision</th>
				   <th width="150" style="padding:5px;font-size:12px; color:#ffffff;">Cat&eacute;gorie</th>
				   <th width="185" style="padding:5px;font-size:12px;word-wrap: keep-all ; color:#ffffff;">Sous-cat&eacute;gorie</th>
				   <th width="60" style="padding:5px; color:#ffffff;font-size:12px;">R&eacute;f&eacute;rence</th>
				   <th width="175" style="padding:5px; color:#ffffff;font-size:12px;">Mots cl&eacute;s</th>
				   </tr>';
				   
		 // Loop through array of ids
		 $nouveautes = '';
		 
		 foreach($list as $ids => $words)
		 {
			 $infosNouveaute = $wpdb->get_results('SELECT wp_nouveautes.* , wp_custom_categories.name as nameCat , wp_custom_categories.*, 
													      wp_subcategories.name as nameSub , wp_subcategories.*
														  FROM wp_nouveautes 
														  JOIN wp_custom_categories on wp_custom_categories.term_id = wp_nouveautes.categorie_nouveaute 
														  LEFT JOIN wp_subcategories on wp_subcategories.refNouveaute = wp_nouveautes.id_nouveaute 
														  WHERE id_nouveaute = "'.$ids.'" ');	
														 
			 $nouveautes .= '<tr style="background:#f5f5f5; border:1px solid 3ebebeb; text-align:left;">';	
			 $nouveautes .= '<td style="padding:5px;font-size:13px; color:#343434;text-align:left;">'.$infosNouveaute[0]->datep_nouveaute.'</td>';										  
			 $nouveautes .= '<td style="padding:5px;font-size:13px; color:#343434;text-align:left;">'.$infosNouveaute[0]->dated_nouveaute.'</td>';
			 $nouveautes .= '<td style="padding:5px;font-size:13px; color:#343434;text-align:left;">'.$infosNouveaute[0]->nameCat.'</td>';
			 $nouveautes .= '<td style="padding:5px;font-size:13px; color:#343434;text-align:left;word-break:break-word;">'.limit_words($infosNouveaute[0]->nameSub,8).'</td>';
			 $nouveautes .= '<td style="padding:5px;font-size:13px; color:#343434;text-align:left;">';
			 $nouveautes .= '<a style="color:#343434;font-size:13px;" href="'.$urlRoot.'?page_id='.$pageRoot.'&arret='.$infosNouveaute[0]->numero_nouveaute.'"><strong>';
			 $nouveautes .= $infosNouveaute[0]->numero_nouveaute;
			 $nouveautes .= '</strong></a></td>';
			 $nouveautes .= '<td style="padding:5px;font-size:12px; word-break:break-all;color:#343434; text-align:left;">'.$words.'</td>';
			 $nouveautes .= '</tr>';											 
		 }
		 
		 $html .= $nouveautes;
		 $html .= '</table>'; 
		 
		 // end wrapper
		 $html .= '</td>'; 
		 $html .= '</tr></table>'; 
		 
		 return $html;
		 
	}
	
} // END CLASS