<?php 

class Arret {

	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/

	function __construct() {

	}
	
	/*
	 * Get all arrets for date
	 * @return array
	*/
	public function get_all_day($date){
	
		global $wpdb;
		
		$droitavocat = array('LLCA','BGFA');
		
		// Find if we passed a single date or a range
		if( is_array($date) )
		{
			$when = ' BETWEEN "'.$date[0].'" AND "'.$date[1].'"';
		}
		else
		{
			$when = ' = "'.$date.'"';
		}
		
		$all  = array();
		$list = array();
		
		$list_arret = $wpdb->get_results('SELECT wp_nouveautes.* , wp_custom_categories.name as nameCat , wp_custom_categories.*, 
										  wp_subcategories.name as nameSub , wp_subcategories.*
										  FROM wp_nouveautes 
										  JOIN wp_custom_categories  on wp_custom_categories.term_id  = wp_nouveautes.categorie_nouveaute 
										  LEFT JOIN wp_subcategories on wp_subcategories.refNouveaute = wp_nouveautes.id_nouveaute 
										  WHERE wp_nouveautes.datep_nouveaute '.$when.'');	
		if($list_arret)
		{
			foreach ($list_arret as $arret) 
			{
				$all['id_nouveaute']          = $arret->id_nouveaute;
				$all['datep_nouveaute']       = $arret->datep_nouveaute;
				$all['dated_nouveaute']       = $arret->dated_nouveaute;
				$all['categorie_nouveaute']   = $arret->categorie_nouveaute;
				$all['nameCat']               = $arret->nameCat;
				$all['nameSub']               = $arret->nameSub;
				$all['link_nouveaute']        = $arret->link_nouveaute;
				$all['numero_nouveaute']      = $arret->numero_nouveaute;
				$all['publication_nouveaute'] = $arret->publication_nouveaute;
				
				$list[$arret->id_nouveaute] = $all;	
			}
		}
		
		return $list;										  
	}

	// Categorie 247 get array_keys($list)
	// All others get list of id_nouveaute from categorie array
	public function dispatch_arret_keyword($list,$allArrets, $keywords = NULL, $isPub = NULL){
		
		$allids = array();
		
		if(!empty($list))
		{		
			foreach($list as $arret)
			{
				$arretIsPub = NULL;
				$result     = array();
				
				if( isset($allArrets[$arret]['publication_nouveaute']) )
				{
					$arretIsPub = $allArrets[$arret]['publication_nouveaute'];
				}

				// Check if we only want ispub arrets
				if($isPub)
				{
					if( $arretIsPub == 1 )
					{
						$res = $this->arretsInSearch($keywords,$arret);
						
						if( !empty($res) )
						{
							$id = $res['id'];
							$k  = $res['keywords'];

							$allids[$id] = $k;
						}
					} 
				}
				else
				{
					$res = $this->arretsInSearch($keywords,$arret);
					
					if( !empty($res) )
					{
						$id = $res['id'];
						$k  = $res['keywords'];
							
						$allids[$id] = $k;
					}
				}
			}
		}
		
		return $allids;
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
	
	public function arretsInSearch($keywords ,$arret){
		
		$result = array();
		$found  = array();
		
		if( !empty($keywords) )
		{
		    foreach($keywords as $keyword)
		    {
		  	    $words = $this->formatKeywords($keyword);
		  	    
			    // search for keywords
			    $keyExist = $this->search_text_nouveautes( $words , $arret );
				
			    if( $keyExist == 1 )
			    {	
				   $found[] = $words ;
			    }
		    }
		    
		    if(!empty($found))
		    {
			    $allwords           = implode(',', $found);
			    $result['id']       = $arret;
			    $result['keywords'] = $allwords;
		    }
		    // No else because if we want keaywords we dont want arrets without them
		}
		else
		{
			$result['id']       = $arret;
			$result['keywords'] = '';
		}

		return $result;
	}
	
	public function formatKeywords($keywords){
		
		$words = explode(',' , $keywords );
		$words = array_filter(array_map('trim', $words));
		$words = implode(" ", $words);
		
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
	
	/*
	 * Search if the keyword is in the arret
	 * @return boolean
	*/		
	function search_text_nouveautes($s, $id) {
		
			// search comes in form of => hey ho "i am a teapot" 
			// original was => hey, ho, "i am a teapot" 
			// One space beteween words and "string in double quotes"
			
			$s =  htmlspecialchars_decode($s);
		    preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $s, $matches);
			
			$recherche = $matches[0];
			
			$rechercheArray['quote'] = array();
			$rechercheArray['normal'] = array();
			
			foreach($recherche as $rech){
	
				if (preg_match('/\"([^\"]*?)\"/', $rech, $m)) 
				{
				   $string = $m[1];
				   $string = str_replace('"', '', $string);
				   $item = str_replace('"', '', $string);
			 	   $rechercheArray['quote'][] = $item;   
				}
				else
				{
				   $string = str_replace('"', '', $rech);
				   $item = str_replace('"', '', $string);
				   $rechercheArray['normal'][] = $string;   
				}
			}
			
			$quotes = array();
			$normal = array();
							
			// contruction de la requete
			$query = 'SELECT * FROM wp_nouveautes WHERE id_nouveaute = "'.$id.'" AND (';
			
			$quotes = $rechercheArray['quote'];
			$normal = $rechercheArray['normal'];
			
			$searchArray =  array();
					  
			$nbrItemQuote = count($quotes);
			$nbrItemNormal = count($normal);

			$i = 1;
			if($quotes)
			{
				foreach($quotes as $q){	
				
					$query .= 'wp_nouveautes.texte_nouveaute REGEXP   "[[:<:]]'.$q.'[[:>:]]"  ';
					$searchArray[] = $q;
					if($i < $nbrItemQuote ){
						$query .= ' AND ';
					}
					
					$i++;
				}
			}
			$j = 1;
			if($normal)
			{
				foreach($normal as $n){	
				
					if( ($nbrItemQuote > 0) && ($j == 1 ) ){
						$query .= ' AND ';
					}   
					
					$query .= 'wp_nouveautes.texte_nouveaute LIKE  "%'.$n.'%"  ';
					$searchArray[] = $n;
					
					if($j < $nbrItemNormal ){
						$query .= ' AND ';
					}
					
					$j++;
				}
			}
			
			$query .= ' ) ';
			
			global $wpdb;
			
			$wpdb->get_results( $query );
			$row_cnt = $wpdb->num_rows;
		
		return $row_cnt;  
	}	
	
} // END CLASS 