<?php 

require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Grab.php');

class Database {
	
	// DB tables
	protected $nouveautes_table;
	
	protected $categories_table;
	
	protected $subcategories_table;
	
	protected $keywords_table;
	
	protected $extracategory_table;
	
	// Include classes
	protected $grab;
	
	// urls	
	protected $urlRoot;
	
	protected $urlArret;

	function __construct( $test = null) {
				
		/*
			0 date publication
			1 date decision
			2 lien
			3 numero
			4 categorie
			5 subcategorie
			6 is publication
			7 categorie id
			8 language

		*/
		
		// Set tables
		
		$this->nouveautes_table    = ( $test ? 'wp_nouveautes_test' : 'wp_nouveautes' );

		$this->categories_table    = ( $test ? 'wp_custom_categories_test' : 'wp_custom_categories' );

		$this->subcategories_table = ( $test ? 'wp_subcategories_test' : 'wp_subcategories' );
		
		$this->keywords_table      = 'wp_keyword_extra';
		
		$this->extracategory_table = 'extracategories';
		
		// Set classes
		
		$this->grab = new Grab;
		
		// urls
		
		$this->urlRoot  = 'http://relevancy.bger.ch';
	
		$this->urlArret = 'http://relevancy.bger.ch/php/aza/http/index.php?lang=fr&zoom=&type=show_document&highlight_docid=aza%3A%2F%2F';

	}
		
	/* ===============================================
		For tests only
	 =============================================== */	
	 
	public function deleteTable(){
	 
		global $wpdb;

		$wpdb->query('TRUNCATE TABLE '.$this->nouveautes_table.'');

		$wpdb->query('DELETE FROM '.$this->categories_table.' WHERE term_id > 247');
				 
		$wpdb->query('TRUNCATE TABLE '.$this->subcategories_table.'');
		 		 
	}		

	/* ===============================================
		Insert data into database
	 =============================================== */
	 
	public function insertNewArrets($arrets){
		 
		 global $wpdb;
		 
		 $inserted = array();
		 
		 if(!empty($arrets)){
			 
			 foreach($arrets as $arret){
				 
				 // organised arret and subcategory
				 $elements   = $this->organiserArret($arret);
				 
				 $newarret   = $elements['arret'];
				 $newsubcat  = $elements['subcategorie'];
				 
				 // Insert DB arret
				 $id = $this->insertArret($newarret);
				 				 
				 $inserted[$id] = $newarret;
				 
				 // Insert DB subcategory
				 if($id)
				 {
					 $this->insertSubcategory($newsubcat , $id); 
				 }
			 }
		 }
		 
		 return $inserted;
	}	
		 
	public function insertArret($arret){
	 	
	 	global $wpdb;
	 			 
		if( $wpdb->insert( $this->nouveautes_table , $arret , array( '%s')) !== FALSE )
		{
			// return new arret id for subcategorie
			return $wpdb->insert_id;
		}
		
		return false;		
	}

	public function insertCategory($category){
	
		global $wpdb;
			
		$name_fr = $category;
		$name_de = $category.'-allemand';
		$name_it = $category.'-italien';
		
		$data = array( 'name' => $name_fr,  'name_de' => $name_de,  'name_it' => $name_it );  
		
		if( $wpdb->insert( $this->categories_table , $data , array( '%s') ) === FALSE )
		{	
			return false;
		}
				
		return true;  
	}
	 
	public function insertSubcategory($subcategory , $idNewArret){
	 	
	 	global $wpdb;
	 	
	 	$subcategory['refNouveaute'] = $idNewArret;
	 	
		if( $wpdb->insert( $this->subcategories_table , $subcategory , array( '%s')) !== FALSE )
		{
			return true;
		}
		
		return false;	 			
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
		Get data from database
	 =============================================== */
	 
	
	public function lastDayInDb(){
	
		global $wpdb;
				
		// Get last date
		$lastDate = $wpdb->get_row('SELECT datep_nouveaute FROM wp_nouveautes ORDER BY datep_nouveaute DESC LIMIT 0,1 ');	
		
		return $lastDate->datep_nouveaute;
		
	}
	
	public function isToday($date){
		
	    $yesterday = date("Y-m-d", strtotime("-1 day"));
		$yesterday = strtotime($yesterday);
		$yesterday = date("ymd", $yesterday);
		
		$date      = strtotime($date);
		$date      = date("ymd", $date);	
		
		$isToday = ( $date > $yesterday ? true : false);
		
		return $isToday;
	}
	
	public function datesToUpdate($dates){
	
		$toUpdate = array();
		
		$last = $this->lastDayInDb();
		$last = strtotime($last);
		$last = date("ymd", $last);	
		
		if(!empty($dates))
		{
			foreach($dates as $date)
			{			
				if( $this->isToday($date) && ($date > $last) )
				{
					$toUpdate[] = $date;
				}				
			}
		}
		
		return $toUpdate;		
	}
	
	// Get all categories and arrange them by language
	public function getCategories(){
	
		global $wpdb;
		
		$allcategories = array();
		    
		// geta all categories		
		$categories = $wpdb->get_results('SELECT * FROM '.$this->categories_table.'');
		
	    foreach($categories as $cat)
		{ 
		    $allcategories[$cat->term_id][] = $cat->name;
			$allcategories[$cat->term_id][] = $cat->name_de;
			$allcategories[$cat->term_id][] = $cat->name_it;	
		}
		
		return $allcategories;
			
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
		
	// See if the categorie already exist in database
	public function existCategorie($categories){
		
		global $wpdb;

		$ok = 0;
		
		if(!empty($categories))
		{
			foreach($categories as $cat)
			{ 
				$category = $this->cleanString($cat);
				
				$result   = $this->findCategory($category);
				
				if( !$result )
				{
					$this->insertCategory($category);
				}		
			}			
		}
		 
		return true;
	}
	
	public function findCategory($string){
		 
		global $wpdb;
		 		
		// if we have a variant like "(general)" or "(en general)" test it
		$find = ' (en ';
		$pos  = strpos($string, $find);
		
		// Select categorie where the string provided sounds the same		
		$query = 'SELECT * FROM '.$this->categories_table.' WHERE soundex(name)=soundex("'.mysql_real_escape_string($string).'")';
		
		if($pos)
		{
			$catw   = str_replace($find, ' (', $string);
			$query .= ' OR soundex(name)=soundex("'.mysql_real_escape_string($catw).'")';
		}
		
		$query .= ' OR soundex(name_de)=soundex("'.mysql_real_escape_string($string).'")
			        OR soundex(name_it)=soundex("'.mysql_real_escape_string($string).'")';		   
							   
		$wpdb->get_results($query);
		
	    //  number of lines
		$row = $wpdb->num_rows; 
		
		return $row;
		
	}
	
	/* ===============================================
		Arrange or search data
	 =============================================== */
	  	
	public function arrangeArret($arrets){

		$preparedArret = array();
		$subcategory   = array();
		
		// categories list
		$categories = $this->getCategories();		
	
		foreach($arrets as $original )
		{
			// temp array to retrive infos
			$temp = $original;
			
			// slice category out
			$category = array_slice($original, -3, 1); 
			$category = $category[0]; 
			
			// clean the category string
			$category = $this->cleanString($category,true);
			
			// slice subcategory out
			$subcategory = array_slice($temp, -2, 1);
			$subcategory = $subcategory[0]; 				
			
			// first loop to catch id
			foreach($categories as $idCat => $langue)
			{
				$id   = array();
				
				$id[] = $idCat;
				
				// second loop to go over the three languages (français, allemand,italien) and test in which language the category is
				foreach($langue as $number => $titleCat)
				{
					$langueNumber   = array();
					
					// put id of category in the array
					$langueNumber[] = $number;
					
					if( $this->percent($category,$titleCat) )
					{
						$preparedArret[] = array_merge($original, $id , $langueNumber); 
					}				
				}			
			 }
		  }
			
		  return $preparedArret;
	}
	
	public function organiserArret($arret){
			
		$data = array( 
			'datep_nouveaute'       => $arret[0],  
			'dated_nouveaute'       => $arret[1], 
			'categorie_nouveaute'   => $arret[7],  
			'link_nouveaute'        => $arret[2],
			'numero_nouveaute'      => $arret[3],
			'langue_nouveaute'      => $arret[8],
			'publication_nouveaute' => $arret[6],
			'updated'               => 0	  
		); 

		$subcategorie = array( 'name' => $arret[5] , 'refCategorie' => $arret[7] ); 
		
		return array( 'arret' => $data , 'subcategorie' => $subcategorie) ;
	}

	public function flattenArray(array $array){
	
		$ret_array = array();
		  
		foreach(new RecursiveIteratorIterator(new RecursiveArrayIterator($array)) as $value)
		{
		   $ret_array[] = $value;
		}
		  
	    return $ret_array;
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
	
	// Test if the string is the same
	public function percent($category,$string){
	
		similar_text($category, $string , $percent);
				
		if( $percent >= 90)
		{	
			return true;
		}		
		
		return false;
	}
	
	// clean almost identical category string 
	public function cleanString($string ,$db = NULL){
		
		// remove *
		$string = str_replace('*', '', $string);
		
		if($db)
		{
			$string = str_replace('(en ', '(', $string);
		}
		// trim string	
		$string = trim($string);

		return $string;
	}
			
}