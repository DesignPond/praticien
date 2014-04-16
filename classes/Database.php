<?php 

require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Utils.php');
require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Grab.php');

class Database{
	
	// DB tables
	protected $nouveautes_table;
	
	protected $categories_table;
	
	protected $subcategories_table;
	
	// Include classes
	protected $grab;
	
	protected $utils;
	
	// urls	
	protected $urlRoot;
	
	protected $urlArret;

	function __construct( $test = null ) {
				
		/* 0 date publication , 1 date decision, 2 lien, 3 numero, 4 categorie, 5 subcategorie, 6 is publication, 7 categorie id, 8 language */
		
		// Set tables		
		$this->nouveautes_table    = ( $test ? 'wp_nouveautes_test' : 'wp_nouveautes' );

		$this->categories_table    = ( $test ? 'wp_custom_categories_test' : 'wp_custom_categories' );

		$this->subcategories_table = ( $test ? 'wp_subcategories_test' : 'wp_subcategories' );
		
		// Set classes		
		$this->utils = new Utils;
		
		$this->grab  = new Grab;
		
		// urls		
		$this->urlRoot  = 'http://relevancy.bger.ch';
	
		$this->urlArret = 'http://relevancy.bger.ch/php/aza/http/index.php?lang=fr&zoom=&type=show_document&highlight_docid=aza%3A%2F%2F';

	}
		
	/* ===============================================
		For tests only
	 =============================================== */	
	 
	public function deleteTable(){
	 
		global $wpdb;

		$wpdb->query('TRUNCATE TABLE wp_nouveautes_test');

		$wpdb->query('DELETE FROM wp_custom_categories_test WHERE term_id > 247');
				 
		$wpdb->query('TRUNCATE TABLE wp_subcategories_test');
		
		$wpdb->query('TRUNCATE TABLE wp_extracategories_test');
		
		$wpdb->query('TRUNCATE TABLE wp_updated');
		 		 
	}		

	/* ===============================================
		Insert data into database
	 =============================================== */
	
	// Main insert function
	// Lopp over arrets, arrange them and insert
	// Insert subcategory
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
		 
		 // if we have inserted all arrets we are ok
		 $result = ( count($arrets) == count($inserted) ? true : false );
		 
		 return $result;
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
	 
	public function insertSubcategory($subcategory , $idNewArret){
	 	
	 	global $wpdb;
	 	
	 	$subcategory['refNouveaute'] = $idNewArret;
	 	
		if( $wpdb->insert( $this->subcategories_table , $subcategory , array( '%s')) !== FALSE )
		{
			return true;
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
	 	 	
	/* ===============================================
		Get data from database Categories
	 =============================================== */
	
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
		
	// See if the categorie already exist in database
	public function existCategorie($categories){
		
		global $wpdb;

		$ok = 0;
		
		if(!empty($categories))
		{
			foreach($categories as $cat)
			{ 
				$category = $this->utils->cleanString($cat);
				
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
		Arrange and organise for insert
	 =============================================== */
	
	// Arrange arret with infos, detect language   	
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
			$category = $this->utils->cleanString($category,true);
			
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
					
					if( $this->utils->percent($category,$titleCat) )
					{
						$preparedArret[] = array_merge($original, $id , $langueNumber); 
					}				
				}			
			 }
		  }
			
		  return $preparedArret;
	}
	
	// Organise array for insert in db
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
			
}