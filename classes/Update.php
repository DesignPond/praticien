<?php

require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Grab.php');
require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Database.php');

class Update{

	protected $grab;
	
	protected $database;

	protected $updated_table;
	
	function __construct() {

		// Set tables
				
		$this->updated_table = 'wp_updated';
		
		// Get classes
		
		$this->grab     = new Grab;
		
		$this->database = new Database(true);
						
	}
	
	public function update(){
		
		global $wpdb;
				
	
		// Update text for arrets
		// Get extra categories if we have them in the textes
		// Pass arrets to updated
		
		// Cron test if all arrets are updated if not do it
		
		//return $newInserted;
			
	}
	
	 
	public function updateTextArret($listLinks){

	 	global $wpdb;
		
		if(!empty($listLinks))
		{	 	
			foreach($listLinks as $id => $link)
			{								 	
	 			$urlArret = '';
				
				$urlArret = $this->formatArretUrl($link);				

				$text     = $this->grab->getArticle($urlArret , $this->urlRoot);
				
				if( !empty($text) )
				{		
					$data = array( 'texte_nouveaute' => $text , 'updated' => 1 ); 
						
					$wpdb->update( $this->nouveautes_table , $data , array( 'id_nouveaute' => $id), array( '%s' , '%d' ), array( '%d' ));
					
					$this->addExtraKeywords($id);
				}				
			}
		}	
		
		return 'ok';	
	}
	
	public function addExtraKeywords($id){

	 	global $wpdb;
		
		$needles = $this->getExtraKeywords();
			
		if( $needles )
		{				
			foreach($needles as $needle_id => $needle)
			{
				foreach($needle as $word)
				{
					if( $this->searchDatabaseKeywords($word , $id) !== 0 )
					{
						$data = array( 
							'parent_extra'    => $needle_id,  
							'nouveaute_extra' => $id
						); 
								
						$wpdb->insert( $this->extracategory_table , $data , array( '%d' , '%d'));
						
						// break the foreaches 
						return true;							
					} 
				}
			} 			
		} 
		
		return true;
				
	}
	
	// Get all subcategories 
	public function getExtraKeywords(){
	
		global $wpdb;
		
		$needles = array();
		
		$extraKeywords = $wpdb->get_results('SELECT * FROM '.$this->keywords_table.'');
		
		if($extraKeywords)
		{
			foreach($extraKeywords as $extra)
			{
				$needles[$extra->parent_keywords][] = $extra->extra_keywords ;
			}
		}
		
		return $needles;
			
	}
	 
	public function formatArretUrl($arret){
		
		$urlArret = '';
		
		$date   = new DateTime($arret['dated_nouveaute']);
		$dated  = $date->format('d-m-Y');
		$numero = str_replace("/","-",$arret['numero_nouveaute']);
		
		$urlArret  = $this->urlArret;				
		$urlArret .= $dated.'-'.$numero;
		
		return $urlArret;
		
	}
	
		
	/* ===============================================
		Utils function, clean and test
	 =============================================== */
	 
	 public function prepareSearch($search){
	
		$search =  htmlspecialchars_decode($search);
		
	    preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $search, $matches);
		
		$recherche = $matches[0];
		
		$find  = array();
		
		foreach($recherche as $rech)
		{
			// there is quotes "
			if (preg_match('/\"([^\"]*?)\"/', $rech, $m)) 
			{
			   $string = $m[1];
			   $string = str_replace('"', '', $string);
			   $item   = str_replace('"', '', $string);
			   $string = trim($string);
			   
		 	   $find[] = $item;   
			}
			else // no quotes
			{
			   $string = str_replace(',', '', $rech);
			   $string = trim($string); 
			   
			   if( $string != '')
			   {
				   $find[] = $string;   
			   }			   
			}			
		}

		return $find;
		
	}
	 	
	// search in databse
	public function searchDatabaseKeywords($search , $id) {
						
		global $wpdb;
	
		$terms = $this->prepareSearch($search);
						
		// contruction de la requete
		$query = 'SELECT * FROM wp_nouveautes WHERE id_nouveaute = "'.$id.'" AND ';			

		$i = 1;
		
		$nbr = count($terms);
		
		if(!empty($terms))
		{
			foreach($terms as $term)
			{			
				$query .= 'wp_nouveautes.texte_nouveaute REGEXP   "[[:<:]]'.$term.'[[:>:]]"  ';

				$query .= ( $i < $nbr ? ' AND ' : '');
				
				$i++;
			}
		}

		$wpdb->get_results( $query );
		
		$rows = $wpdb->num_rows;
		
		return $rows;  
	}

		
	
}
