<?php

require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Grab.php');
require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Arrange.php');
require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Database.php');

class Update{

	protected $grab;
	
	protected $arrange;
	
	protected $database;

	protected $updated_table;
	
	// urls	
	protected $urlList;
	
	function __construct() {

		// Set tables
				
		$this->updated_table = 'wp_updated';
		
		// Get classes
		
		$this->grab     = new Grab;
		
		$this->arrange  = new Arrange;
		
		$this->database = new Database(true);
		
		// urls
		
		$this->urlList = 'http://relevancy.bger.ch/AZA/liste/fr/';
						
	}
	
	public function init(){
		
		global $wpdb;
		
		$arrets      = array();
		$newInserted = array();
		
		// Get list of dates from TF
		
		$dates    = $this->grab->getLastDates($this->urlList);

		// Test if there's today's date in the list and if not in the database already
				
		$toUpdate = $this->database->datesToUpdate($dates);
		
		// Grab list of arrets from the date and clean the arrets
		
		if(!empty($toUpdate))
		{
			foreach($toUpdate as $list)
			{						
				$arrets = $this->grab->getListDecisions($this->urlList, $list);	
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
					foreach($arranged as $arrange)
					{
						// arrange arret eand subcategorie
						$newArrets[] = $this->database->organiserArret($arrange);						
					}
				}
								
				echo '<pre>';
				print_r($newArrets);
				echo '</pre>';	
				
				// Insert arrets in DB
/*
								
				if(!empty($newArrets))
				{											
					$newInserted[] = $this->database->insertNewArrets($newArrets);
				}
*/

		
			}
		}
	
		// Update text for arrets
		// Get extra categories if we have them in the textes
		// Pass arrets to updated
		
		// Cron test if all arrets are updated if not do it
		
		//return $newInserted;
			
	}

		
	
}
