<?php
/*================================
	Functions misc
==================================*/
	function search_database_keywords($s , $id) {
	
			$s =  htmlspecialchars_decode($s);
		    preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $s, $matches);
			
			$recherche = $matches[0];
			
			$rechercheArray['quote'] = array();
			$rechercheArray['normal'] = array();
			
			foreach($recherche as $rech){
	
				if (preg_match('/\"([^\"]*?)\"/', $rech, $m)) {
				   $string = $m[1];
				   $string = str_replace('"', '', $string);
				   $item = str_replace('"', '', $string);
			 	   $rechercheArray['quote'][] = $item;   
				}
				else{
				   $string = str_replace('"', '', $rech);
				   $item = str_replace('"', '', $string);
				   $rechercheArray['normal'][] = $string;   
				}
				
			}
			
			$quotes = array();
			$normal = array();
							
			// contruction de la requete
			$query = 'SELECT * FROM wp_nouveautes WHERE id_nouveaute = "'.$id.'" AND ';
			
			$quotes = $rechercheArray['quote'];
			$normal = $rechercheArray['normal'];
			
			$searchArray =  array();
					  
			$nbrItemQuote = count($quotes);
			$nbrItemNormal = count($normal);

			$i = 1;
			if($quotes){
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
			if($normal){
				foreach($normal as $n){	
				
					if( $nbrItemQuote > 0 ){
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
			
			global $wpdb;
			$wpdb->get_results( $query );
			$row_cnt = $wpdb->num_rows;
		
		return $row_cnt;  
	}


    function remove_whitespace($string) {
     
	    $string = preg_replace('/\s+/', ' ', $string);
	    $string = trim($string);
	    return $string;
     
    }
	
	function deleteBlank($ar){
		foreach($ar as $a)
		{
			$string = utf8_encode($a);
			$new[] = trim($string);
		}
		return $new;
	}

	function getData($date)
	{
		$string = str_replace(".htm", "", $date);
		$pieces = str_split($string, 2);
		$date_p = '20'.$pieces[0].'-'.$pieces[1].'-'.$pieces[2];
		return $date_p;
	}
	
	function getDataPoint($date)
	{
		$pieces = explode('.',$string);
		$date_p = $pieces[2].'-'.$pieces[1].'-'.$pieces[0];
		return $date_p;
	}
    

	function arrangeArray($stackAll) {
	
		$count = count($stackAll);
		$nbr = $count/2;
		
		for ($i= 0 ; $i<$nbr; $i++) {
			$first = array_shift($stackAll);
		    $Second = array_shift($stackAll);
		    $first = array_filter(deleteBlank($first));
		    $Second = array_filter(deleteBlank($Second));
		    
		    $newArray[] = array_merge($first , $Second); 
		}
		return $newArray;
	}
	
	function addMyDate($array)
	{
		$big = array();
		foreach($array as $tab)
		{	
			$add = array();
			$t = array();
			
			$tab1 = array_shift($tab);
			$pieces = explode('.',$tab1);
			$add[] = $pieces[2].'-'.$pieces[1].'-'.$pieces[0];
			$t =  array_merge($datap, $add ,$tab);
			$big[] = $t ;
		}	
		return $big;
	}
	
	function array_push_assoc($array, $key, $value){
		$array[$key] = $value;
		return $array;
	}

	function flattenArray(array $array){
		  $ret_array = array();
		  foreach(new RecursiveIteratorIterator(new RecursiveArrayIterator($array)) as $value)
		     {
		     $ret_array[] = $value;
		     }
		  return $ret_array;
	}
	  
	function in_array_r($needle, $haystack, $strict = true) {
	    foreach ($haystack as $item) {
	        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
	            return true;
	        }
	    }
	    return false;
	}
 
	
	  function multi_in_array($value, $array)
	{
	    foreach ($array AS $item)
	    {
	        if (!is_array($item))
	        {
	            if ($item == $value)
	            {
	                return true;
	            }
	            continue;
	        }
	
	        if (in_array($value, $item))
	        {
	            return true;
	        }
	        else if (multi_in_array($value, $item))
	        {
	            return true;
	        }
	    }
	    return false;
	} 
	  	
	function wd_remove_accents($str, $charset='utf-8')
	{
		$str = strtolower(trim($str));
		$str= str_replace('"', '', $str);
		$str= str_replace("'", "-", $str);
		$str= str_replace('/', '-', $str);
		$pieces = explode(" ", $str);
		$str = join('-', $pieces);
	    $str = htmlentities($str, ENT_NOQUOTES, $charset);
	    $str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
	    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
	    $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
	    
	    return $str;
	}

/*================================
	Functions grab information
	----------------------------
	$auth_processing_url .. is the posted 'action' url in login form like <form method=post action='http://www.abc.com/login.asp'> 
	So it should be like: "http://www.abc.com/login.asp"
    $url_to_go_after_login .. is the url you want to go (to be redireced) after login
    $login_post_values .. are the form input names what Login Form is asking. E.g on form: <input name="username" /><input name="password" />. 
	So it should be: "username=4lvin&password=mypasswd"
	
==================================*/

function curl_exec_follow($ch, &$maxredirect = null) {
  
  // we emulate a browser here since some websites detect
  // us as a bot and don't let us do our job
  $user_agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5)".
                " Gecko/20041107 Firefox/1.0";
  curl_setopt($ch, CURLOPT_USERAGENT, $user_agent );

  $mr = $maxredirect === null ? 5 : intval($maxredirect);

  if (ini_get('open_basedir') == '' && ini_get('safe_mode') == 'Off') {

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0);
    curl_setopt($ch, CURLOPT_MAXREDIRS, $mr);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

  } else {
    
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

    if ($mr > 0)
    {
      $original_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
      $newurl = $original_url;
      
      $rch = curl_copy_handle($ch);
      
      curl_setopt($rch, CURLOPT_HEADER, true);
      curl_setopt($rch, CURLOPT_NOBODY, true);
      curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
      do
      {
        curl_setopt($rch, CURLOPT_URL, $newurl);
        $header = curl_exec($rch);
        if (curl_errno($rch)) {
          $code = 0;
        } else {
          $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
          if ($code == 301 || $code == 302) {
            preg_match('/Location:(.*?)\n/', $header, $matches);
            $newurl = trim(array_pop($matches));
            
            // if no scheme is present then the new url is a
            // relative path and thus needs some extra care
            if(!preg_match("/^https?:/i", $newurl)){
              $newurl = $original_url . $newurl;
            }   
          } else {
            $code = 0;
          }
        }
      } while ($code && --$mr);
      
      curl_close($rch);
      
      if (!$mr)
      {
        if ($maxredirect === null)
        trigger_error('Too many redirects.', E_USER_WARNING);
        else
        $maxredirect = 0;
        
        return false;
      }
      curl_setopt($ch, CURLOPT_URL, $newurl);
    }
  }
  return curl_exec($ch);
}

function curl_grab_page($url){

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    ob_start();
    $data = curl_exec_follow($ch);
    ob_end_clean();

    curl_close($ch);
    return $data;

}


function merge(){
    //check if there was at least one argument passed.
    if(func_num_args() > 0){
        //get all the arguments
        $args = func_get_args();
        //get the first argument
        $array = array_shift($args);
        //check if the first argument is not an array
        //and if not turn it into one.
        if(!is_array($array)) $array = array($array);
        //loop through the rest of the arguments.
        foreach($args as $array2){
            //check if the current argument from the loop
            //is an array.
            if(is_array($array2)){
                //if so then loop through each value.
                foreach($array2 as $k=>$v){
                    //check if that key already exists.
                    if(isset($array[$k])){
                        //check if that value is already an array.
                        if(is_array($array[$k])){
                            //if so then add the value to the end
                            //of the array.
                            $array[$k][] = $v;
                        } else {
                            //if not then make it one with the
                            //current value and the new value.
                            $array[$k] = array($array[$k], $v);
                        }
                    } else {
                        //if not exist then add it
                        $array[$k] = $v;
                    }
                }
            } else {
                //if not an array then just add that value to
                //the end of the array
                $array[] = $array2;
            }
        }
        //return our array.
        return($array);
    }
    //return false if no values passed.
    return(false);
}