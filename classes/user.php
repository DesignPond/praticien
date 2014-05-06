<?php 

require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Utils.php');
require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Search.php');
require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Log.php');

class User {

	// DB tables
	protected $user_meta;
	
	protected $abo_user;
	
	protected $abo_pub_table;
	
	// Include classes
	
	protected $utils;
	
	protected $search;
		
	protected $log;
		
	// propreties		
	protected $special;

	function __construct( $test = null ) {
				
		// Set tables		
		$this->user_meta     = ( $test ? 'wp_usermeta' : 'wp_usermeta' );

		$this->abo_user      = ( $test ? 'wp_user_abo_test' : 'wp_user_abo' );

		$this->abo_pub_table = ( $test ? 'wp_user_abo_pub_test' : 'wp_user_abo_pub' );
		
		// Set classes		
		$this->utils   = new Utils;

		$this->search  = new Search();
		
		$this->log     = new Log;
		
		// special categories
		$this->special = array('LLCA','BGFA');

	}
	
	public function getUserAbos($when){
					
		global $wpdb;
		
		$userAbos = array();

		$userAbosWithPub  = $this->getAllUserAbosPublicationCategory();
	
		$userKeywordsAbos = $this->getAllUserAbosRythme($when);
				 							  	 								  		
		if(!empty($userKeywordsAbos))
		{	
			foreach($userKeywordsAbos as $user)
			{		
				// Test if user is valid				
				if( $this->validUserAbo($user->refUser) )
				{	
				
					if(!empty($user->keywords))
					{
						$userAbos[$user->refUser][$user->refCategorie]['keywords'][] = $user->keywords;
					}
					else
					{
						$userAbos[$user->refUser][$user->refCategorie]['keywords'] = '';
					}
					
					$userAbos[$user->refUser][$user->refCategorie]['ispub'] = (isset($userAbosWithPub[$user->refUser][$user->refCategorie])?$userAbosWithPub[$user->refUser][$user->refCategorie]: 0 );
				}
			}
		}
		
		return $userAbos;
	}
	
	// find out if the user has a valid abo
	public function validUserAbo($user_id){
		
		global $wpdb;
						
		$date  = date('Y-m-d');
		
		$valid = get_user_meta($user_id, 'date_abo_active', true);
		
		if($valid > $date)
		{
			return true;
		}
		
		return false;
	}
	
	public function getAllUserAbosPublicationCategory(){
	
		global $wpdb;
				
		$userAbosWithPub = array();
		
		// Get user categories and pub
		$abosPub  = $wpdb->get_results('SELECT * FROM '.$this->abo_pub_table.'');			
		
		if( !empty($abosPub) )
		{
		    foreach($abosPub as $userpub)
		    {
		  		$userAbosWithPub[$userpub->refUser][$userpub->refCategorie] = $userpub->ispub;
		    }
		}
		
		return $userAbosWithPub;		
	}
	
	public function getAllUserAbosRythme($when){
	
		global $wpdb;
			
		$listUserAbos = $wpdb->get_results('SELECT    refUser , refCategorie , keywords , user_email
		 									FROM      '.$this->abo_user.' 
		 									LEFT JOIN '.$this->user_meta.' on '.$this->user_meta.'.user_id = '.$this->abo_user.'.refUser 
											LEFT JOIN wp_users on wp_users.ID = '.$this->abo_user.'.refUser 
				 							WHERE     '.$this->user_meta.'.meta_key = "rythme_abo" AND '.$this->user_meta.'.meta_value = "'.$when.'" ');	
		
		return $listUserAbos;		
	}


		
}
	
?>