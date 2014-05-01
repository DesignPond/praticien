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
	
	/*
	 * Get all arrets for date
	 * @return array
	*/
	public function getArretsForDates($date){
	
		global $wpdb;
		
		$list   = array();
		$arrets = array();
		
		// Find if we passed a single date or a range	
		$when = ( is_array($date) ? ' BETWEEN "'.$date[0].'" AND "'.$date[1].'"' : ' = "'.$date.'"' );
		
		$listArrets = $wpdb->get_results('SELECT '.$this->nouveautes_table.'.* , 
												 '.$this->categories_table.'.name as nameCat , 
												 '.$this->categories_table.'.*, 
												 '.$this->subcategories_table.'.name as nameSub , 
												 '.$this->subcategories_table.'.*
										  FROM '.$this->nouveautes_table.' 
										  JOIN '.$this->categories_table.'  on '.$this->categories_table.'.term_id  = '.$this->nouveautes_table.'.categorie_nouveaute 
										  LEFT JOIN '.$this->subcategories_table.' on '.$this->subcategories_table.'.refNouveaute = '.$this->nouveautes_table.'.id_nouveaute 
										  WHERE '.$this->nouveautes_table.'.datep_nouveaute '.$when.'');	

		if(!empty($listArrets))
		{
			foreach ($listArrets as $arret) 
			{
				$arrets['id_nouveaute']          = $arret->id_nouveaute;
				$arrets['datep_nouveaute']       = $arret->datep_nouveaute;
				$arrets['dated_nouveaute']       = $arret->dated_nouveaute;
				$arrets['categorie_nouveaute']   = $arret->categorie_nouveaute;
				$arrets['nameCat']               = $arret->nameCat;
				$arrets['nameSub']               = $arret->nameSub;
				$arrets['link_nouveaute']        = $arret->link_nouveaute;
				$arrets['numero_nouveaute']      = $arret->numero_nouveaute;
				$arrets['publication_nouveaute'] = $arret->publication_nouveaute;
				
				$list[$arret->id_nouveaute] = $arrets;	
			}
		}
		
		return $list;										  
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

	// Categorie 247 (General) get array_keys($list)
	// All others get list of id_nouveaute from categorie array
	public function dispatchArretByKeyword($arrets , $keywords = NULL, $isPub = NULL){
		
		$listIds = array();
		
		if(!empty($arrets))
		{		
			foreach($arrets as $id => $arret)
			{				
				// Test if is pub
				if( $isPub )
				{
					if( $this->isPub($arret) )
					{		
						$result = $this->arretsInSearch($keywords,$id);
						
						if(!empty($result) && $keywords)
						{
							$listIds[$id] = $result;
						}
						else
						{
							$listIds[$id] = '';
						}
																																				
					}			
				}
				else
				{		
					$result = $this->arretsInSearch($keywords,$id);
						
					if(!empty($result) && $keywords)
					{
						$listIds[$id] = $result;
					}
					else
					{
						$listIds[$id] = '';
					}																													
				}

			}
		}
		
		return $listIds;
	}
	
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