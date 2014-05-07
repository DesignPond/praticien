<?php 

require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Utils.php');
require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Search.php');
require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Log.php');

class Nouveautes {

	// DB tables
	protected $nouveautes_table;
	
	protected $categories_table;
	
	protected $subcategories_table;
	
	// Include classes
	
	protected $utils;
	
	protected $search;
		
	protected $log;
		
	// propreties		
	protected $special;

	function __construct( $test = null ) {
				
		/* 0 date publication , 1 date decision, 2 lien, 3 numero, 4 categorie, 5 subcategorie, 6 is publication, 7 categorie id, 8 language */
		
		// Set tables		
		$this->nouveautes_table    = ( $test ? 'wp_nouveautes_test' : 'wp_nouveautes' );

		$this->categories_table    = ( $test ? 'wp_custom_categories_test' : 'wp_custom_categories' );

		$this->subcategories_table = ( $test ? 'wp_subcategories_test' : 'wp_subcategories' );
		
		// Set classes		
		$this->utils   = new Utils;

		$this->search  = new Search();
		
		$this->log     = new Log;
		
		// special categories
		$this->special = array('LLCA','BGFA');

	}

	
	/*============================================
	  Main functions
	============================================*/
	
	/*
	 * Get all arrets for date
	 * @return array
	*/
	public function getArretsAndCategoriesForDates($date){
	
		global $wpdb;
		
		$categories = array();
		$arrets     = array();
		$new        = array();
		
		// Find if we passed a single date or a range	
		$when = ( is_array($date) ? ' BETWEEN "'.$date[0].'" AND "'.$date[1].'"' : ' = "'.$date.'"' );
		
		$listArrets = $wpdb->get_results('SELECT '.$this->nouveautes_table.'.id_nouveaute , 
												 '.$this->nouveautes_table.'.datep_nouveaute , 
												 '.$this->nouveautes_table.'.dated_nouveaute , 
												 '.$this->nouveautes_table.'.categorie_nouveaute , 
												 '.$this->nouveautes_table.'.link_nouveaute , 
												 '.$this->nouveautes_table.'.numero_nouveaute , 
												 '.$this->nouveautes_table.'.publication_nouveaute , 
												 '.$this->categories_table.'.name as nameCat , 
												 '.$this->subcategories_table.'.name as nameSub 
										  FROM '.$this->nouveautes_table.' 
										  JOIN '.$this->categories_table.'  on '.$this->categories_table.'.term_id  = '.$this->nouveautes_table.'.categorie_nouveaute 
										  LEFT JOIN '.$this->subcategories_table.' on '.$this->subcategories_table.'.refNouveaute = '.$this->nouveautes_table.'.id_nouveaute 
										  WHERE '.$this->nouveautes_table.'.datep_nouveaute '.$when.'');	

		if(!empty($listArrets))
		{
			foreach ($listArrets as $arret) 
			{
				$new['id_nouveaute']          = $arret->id_nouveaute;
				$new['datep_nouveaute']       = $arret->datep_nouveaute;
				$new['dated_nouveaute']       = $arret->dated_nouveaute;
				$new['categorie_nouveaute']   = $arret->categorie_nouveaute;
				$new['nameCat']               = $arret->nameCat;
				$new['nameSub']               = $arret->nameSub;
				$new['link_nouveaute']        = $arret->link_nouveaute;
				$new['numero_nouveaute']      = $arret->numero_nouveaute;
				$new['publication_nouveaute'] = $arret->publication_nouveaute;
				
				$arrets[$arret->categorie_nouveaute][$arret->id_nouveaute] = $new;	
				
				// categories list
				$categories[$arret->categorie_nouveaute][$arret->id_nouveaute]['ispub'] = $arret->publication_nouveaute; 
			}
		}
		
		return array( 'arrets' => $arrets , 'categories' => $categories );										  
	}
	
	// Get 5 last week days
	public function getWeekDays(){
	
		global $wpdb;
		
		$week  = array();
		
		$dates = $wpdb->get_results(' SELECT datep_nouveaute FROM '.$this->nouveautes_table.' GROUP BY datep_nouveaute ORDER BY datep_nouveaute DESC LIMIT 0,5 ');
		
		if( !empty($dates) )
		{
		   foreach($dates as $date)
		   {
		   	   $week[] = $date->datep_nouveaute;
		   }
		}
		
		$range[] = array_pop($week);
		$range[] = array_shift($week);					
		
		return $range;
	}
	
	public function assignArretsUsers($users, $arrets){
		
		$userArrets = array();
				
		foreach($users as $user => $cat)
		{
			foreach($cat as $idCat => $listes)
			{
							
				$keywords = (!empty($listes['keywords']) ? $listes['keywords'] : NULL );
				
				$isPub    = (!empty($listes['ispub']) ? $listes['ispub'] : NULL );
					
				// Categorie general 		
				if($idCat == 247)
				{										
					$list = $this->utils->groupArray($arrets);
					
					// keywords are not optional here!!
					if($keywords)
					{
						// search in all arrets but only arrets and not the category key => flatten the array
						$listArrets = $this->dispatchArretWithKeyword($list, $keywords , $isPub);
											
						if(!empty($listArrets))
						{
							$userArrets[$user][] = $listArrets;
						}
					}
				}
				else
				{
					// if it's the current categorie exist
					if( isset($arrets[$idCat]) )
					{
						// Get list of arrets for current category
						$list = $arrets[$idCat];
					
						// search in arrets
						$listArrets = $this->dispatchArretWithKeyword($list, $keywords , $isPub);
							
						if(!empty($listArrets))
						{
							$userArrets[$user][] = $listArrets;
						}		
					}
				}
				
			}			
		}
		
		return $userArrets;
	}	
	
	/*============================================
	  Inside functions
	============================================*/

	public function dispatchArretWithKeyword($arrets , $keywords = NULL, $isPub = NULL){
		
		$listIds = array();
		
		if(!empty($arrets))
		{		
			foreach($arrets as $arret)
			{				
				// Test if is pub or/and keywords found				
				if( ($isPub && $this->isPub($arret)) || !$isPub )
				{					
					if($keywords)
					{
						$result = $this->arretsInSearch($keywords,$arret['id_nouveaute']);
						
						if(!empty($result))
						{
							$listIds[$arret['id_nouveaute']] = $result;
						}
					}
					else
					{
						$listIds[$arret['id_nouveaute']] = NULL;
					}		
				}
			}
		}
		
		return $listIds;
	}	
	
	/**
	 * Utils
	*/
	
	public function isPub($arret){
	
		$isPub = ( $arret['publication_nouveaute'] ? true : false );
		
		return $isPub;
	}

	public function listIdArretsCategorie($categorie){
		
		$ids = array();
		
		if( !empty($categorie) )
		{
		   foreach($categorie as $arret)
		   {
		  	  $ids[] = $arret['id_nouveaute'];
 		   }
		}
		
		return $ids;
	}
	
		
	public function cleanEachUser($users){
	
		$cleaned = array();
		
		if(!empty($users))
		{			
			foreach($users as $user_id => $user)
			{
				foreach($user as $arret)
				{
					foreach($arret as $id => $keyword)
					{
						// already keywords
						
						$word = ( !empty($cleaned[$user_id][$id]) ? ';'.$cleaned[$user_id][$id] : '' );
						
						$cleaned[$user_id][$id] = $keyword.$word;
					}
				}
			}
		}
		
		return $cleaned;
		
	}
	
	// keywords is array of strings 
	public function arretsInSearch($keywords ,$arret){
		
		$result = '';
		$found  = array();
		
		if( !empty($keywords) )
		{
		    foreach($keywords as $keyword)
		    {
		  	    $words = $this->formatKeywords($keyword);
			    
			    $keyExist = $this->search->searchDatabaseKeywords($words , $arret);
				
			    if( $keyExist == 1 )
			    {	
				   $found[] = $words;
			    }
		    }
		    
		    if(!empty($found))
		    {
			    $result = implode(',', $found);
		    }
		    // No else because if we want keywords we dont want arrets without them
		}

		return $result;
	}
	
	public function formatKeywords($keywords){
		
		$words = explode(',' , $keywords );
		$words = array_filter(array_map('trim', $words));
		$words = implode(' ', $words);
		
		return $words;									    
	}

	/*
	 * Arrange list of arrets to categories
	 * @return array
	*/	
	public function arrange_categorie($list){
				
		$categories  = array();
		
		// Keywords for droit de l'avocat	
		$droitavocat = array('LLCA','BGFA');
		
		if($list)
		{
			foreach ($list as $arret) 
			{
				$categories[$arret['categorie_nouveaute']][] = $arret;	
				
				// Droit de l'avocat
				foreach($droitavocat as $droit)
				{
					$keyExist = $this->search_text_nouveautes( $droit , $arret['id_nouveaute'] );
								 
					// Si le mot est trouvé on indique le mot trouvé										 								 									
					if ( $keyExist === 1 ) 
					{ 
					    $categories[244][] = $arret;
					}
				}
				// fin droit avocat
			}
		}
		
		return $categories;	
	}

	/*
	 * Search if the arret is for publication
	 * @return boolean
	*/		
	function isPublication($id)
	{
		global $wpdb;
		
		$query = 'SELECT * FROM wp_nouveautes WHERE id_nouveaute = "'.$id.'"';
	
		$result    = mysql_query($query);
		
		if($result)
		{
			$rowarrets = mysql_fetch_assoc($result); 
			return $rowarrets['publication_nouveaute'];
		}
			
		return 0;	
	}
	
	
} // END CLASS 