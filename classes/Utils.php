<?php

class Utils{
	
	/* ===============================================
		Utils function, clean and test
	 =============================================== */

	public function flattenArray(array $array){
	
		$ret_array = array();
		  
		foreach(new RecursiveIteratorIterator(new RecursiveArrayIterator($array)) as $value)
		{
		   $ret_array[] = $value;
		}
		  
	    return $ret_array;
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