<?php 

class Search {
	
	// DB tables
	protected $nouveautes_table;

	function __construct( ) {
		
		$this->nouveautes_table = 'wp_nouveautes_test';

	}
	 	 		 
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
	 	
	// search in databse
	public function searchDatabaseKeywords($search , $id) {
						
		global $wpdb;
	
		$terms = $this->prepareSearch($search);
						
		// contruction de la requete
		$query = 'SELECT * FROM '.$this->nouveautes_table.' WHERE id_nouveaute = "'.$id.'" AND ';			

		$i = 1;
		
		$nbr = count($terms);
		
		if(!empty($terms))
		{
			foreach($terms as $term)
			{			
				$query .= ''.$this->nouveautes_table.'.texte_nouveaute REGEXP   "[[:<:]]'.$term.'[[:>:]]"  ';

				$query .= ( $i < $nbr ? ' AND ' : '');
				
				$i++;
			}
		}

		$wpdb->get_results( $query );
		
		$rows = $wpdb->num_rows;
		
		return $rows;  
	}				
}