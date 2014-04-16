<?php

require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Grab.php');
require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Dates.php');
require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Arrange.php');
require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Database.php');

class Insert{

	protected $grab;

	protected $dates;
	
	protected $arrange;
	
	protected $database;

	// Tables
	protected $keywords_table;
	
	protected $extracategory_table;
		
	// Urls	
	protected $urlList;
	
	function __construct() {

		// Get classes		
		$this->grab     = new Grab;
		
		$this->dates    = new Dates;
				
		$this->arrange  = new Arrange;
		
		$this->database = new Database(true);

		// Tables		
		$this->keywords_table      = 'wp_keyword_extra';
		
		$this->extracategory_table = 'extracategories';
				
		// Urls		
		$this->urlList = 'http://relevancy.bger.ch/AZA/liste/fr/';
						
	}
	
	public function insert(){
		
		global $wpdb;
		
		$arrets      = array();
		$newInserted = array();
		
		// Get list of dates from TF
		
		$dates = $this->grab->getLastDates($this->urlList);

		// Test if there's today's date in the list and if not in the database already
				
		$toUpdate = $this->dates->datesToUpdate($dates);
		
		if(!empty($toUpdate))
		{
			foreach($toUpdate as $list)
			{	
				// Grab list of arrets for the date in list	
														
				$arrets = $this->grab->getListDecisions($this->urlList, $list);	
				
				// Clean all arrets for each date
				
				$result = $this->arrange->cleanFormat($arrets , $list);
				
				// Update category list in DB				

				if(!empty($result['allCategories']))
				{
					$this->database->existCategorie($result['allCategories']);
				}

				// Prepare arrets
				if(!empty($result['allArrets']))
				{
					$arranged = $this->database->arrangeArret($result['allArrets']);			
				}	

				if(!empty($arranged))
				{
														
					echo '<pre>';
					print_r($arranged);
					echo '</pre>';	
					// arrange arret eand subcategorie
					// $this->database->insertNewArrets($arranged);						
				}
		
			}
		}	
			
	}
		
	
}
