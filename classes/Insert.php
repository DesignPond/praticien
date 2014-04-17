<?php

require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Grab.php');
require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Dates.php');
require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Arrange.php');
require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Database.php');
require_once(plugin_dir_path(  dirname(__FILE__)  ) . 'classes/Log.php');

class Insert{

	protected $grab;

	protected $dates;
	
	protected $arrange;
	
	protected $database;
	
	protected $log;
		
	// Urls	
	protected $urlList;
	
	function __construct( $test = NULL ) {

		// Get classes		
		$this->grab     = new Grab;
		
		$this->dates    = new Dates;
				
		$this->arrange  = new Arrange;
		
		$this->log      = new Log;
		
		$this->database = new Database($test);
				
		// Urls		
		$this->urlList = 'http://relevancy.bger.ch/AZA/liste/fr/';
						
	}
	
	public function insert(){
		
		$arrets   = array();
		$arranged = array();
		
		// Get list of dates from TF		
		$dates = $this->grab->getLastDates($this->urlList);

		// Test if there's today's date in the list and if not in the database already				
		$toInsert = $this->dates->datesToUpdate($dates);
		
		if(!empty($toInsert))
		{
			foreach($toInsert as $list)
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
					// Insert new arrets
					if( $this->database->insertNewArrets($arranged) === false)
					{
						return false;
					}	
					
					// LOGGING
					$this->log->write('All arret inserted for date '.$list.'');
				 	// END LOGGIN					
				}		
			}
			
			return true;
		}
		
		// LOGGING
		$this->log->write('Nothing to insert');
		// END LOGGIN	

		return false;	
	}	
	
}
