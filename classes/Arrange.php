<?php 

require_once( plugin_dir_path( __FILE__ ) . '../simple_html_dom.php');

class Arrange {

	function __construct() {
		
	}
	
	public function imap_utf8_fix($string) {
		
		$string = trim($string);
		$string = strip_tags($string);
		$string = utf8_encode($string);
		
	    return $string;
	} 

	public function deleteBlank($ar){
		
		$new = array();
		
		foreach($ar as $string)
		{
			$string = utf8_encode($string);
			$new[]  = trim($string);
		}
		return $new;
	}
	
	public function arrangeArray($array){

		$count = count($array);
		$nbr   = $count/2;

		for ($i= 0 ; $i < $nbr; $i++) 
		{
			$first  = array_shift($array); // first table
		    $Second = array_shift($array); // second table
		    
		    $first  = array_filter($first);
		    $Second = array_filter($Second);
		    
		    $arranged[] = array_merge($first , $Second); 
		}
		
		return $arranged;
	}	

	public 	function cleanFormat($array, $date_pub)
	{			
		// Delete first p with text  
		unset($array[0]);
		
		$categories = array();
		$allArrates = array();
		
		// Arrange all same infos in one array
		$arranged = $this->arrangeArray($array);
		
		/* ==========================
		  Array content before
			0 => date of decision
			1 => arret number
			2 => category
			3 => subcatgory
		============================= */
		
		if(!empty($arranged))
		{		
			foreach($arranged as $decision)
			{
				$arret = array();

				$date_decison = ( isset($decision[0]) ? $decision[0] : '' );
				$arret_link   = ( isset($decision[1]) ? trim($decision[1]) : '' );
				$category     = ( isset($decision[2]) ? $this->imap_utf8_fix($decision[2]) : '' );
				$subcategory  = ( isset($decision[3]) ? $this->imap_utf8_fix($decision[3]) : '' );
				
				// get link from a tag
				$arret_number = trim(strip_tags($arret_link));

				$html = str_get_html($arret_link);

				foreach($html->find('a') as $link)
	      		{ 
					$arret_link = $link->href;
				}
				
				// is a publication ?				
				$publication  = $this->isPublication($category);
				
				// reformat date form Ymd to Y-m-d		
				$date_publication = $this->formatDateFromString($date_pub);	
							
				// reformat date form d.m.Y to Y-m-d
				$date         = new DateTime($date_decison, new DateTimeZone('Europe/Zurich'));
				$date_decison = $date->format('Y-m-d');
				
				$arret = array( $date_publication , $date_decison , $arret_link , $arret_number , $category , $subcategory , $publication);
				
				$allArrates[$arret_number] = $arret;
				
				$categories[] = preg_replace('/\s+/', ' ', $category);
				
			}			
		}
		
		$categories = $this->uniqueArray($categories);
		
		return array('allArrets' => $allArrates , 'allCategories' => $categories);
	}
	
	public function isPublication($arret){
	
		if (strpos( $arret , '*') !== false) 
		{ 
    		return '1';
		}
		
		return '0';
	}
	
	public function formatDateFromString($string_date){

		// format is 010414
		$date = str_split($string_date,2);
		
		// format is array(01,04,14)	
		array_reverse($date);
		
		// format is array(14,04,01)
		$date_publication = implode('-' , $date);
		
		// add century :(
		$date_publication = '20'.$date_publication;
		
		return $date_publication;
		
	}
	
	public function uniqueArray($array){
	
		$removed = array();
		  
		if(!empty($array))
		{
			foreach($array as $item)
			{
			  
				$removed[$item] = $item;
			}			  
		}
		  
		$removed = array_values($removed);
		  
	    return $removed;		  
	}

		
}