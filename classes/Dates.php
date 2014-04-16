<?php 

class Dates {
	
	// DB tables
	protected $nouveautes_table;
	
	// urls		
	protected $urlArret;

	function __construct( $test = null) {
		
		$this->nouveautes_table = 'wp_nouveautes_test';
			
		$this->urlArret         = 'http://relevancy.bger.ch/php/aza/http/index.php?lang=fr&zoom=&type=show_document&highlight_docid=aza%3A%2F%2F';
	}
	 	 	
	/* ===============================================
		Dates functions
	 =============================================== */	
	
	// Get last date from db
	public function lastDayInDb(){
	
		global $wpdb;
				
		// Get last date
		$lastDate = $wpdb->get_row('SELECT datep_nouveaute FROM '.$this->nouveautes_table.' ORDER BY datep_nouveaute DESC LIMIT 0,1 ');	
		
		$date = ( !empty($lastDate) ? $lastDate->datep_nouveaute : '');
		
		return $date;
	}
	
	// 
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
		
		if(!empty($last))
		{
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
		}

		return $toUpdate;		
	}
				
}