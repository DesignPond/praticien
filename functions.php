<?php
/*================================
	Functions misc
==================================*/

function imap_utf8_fix($string) {
  return iconv_mime_decode($string,0,"UTF-8");
} 

function decode_qprint($str) {
    $str = quoted_printable_decode($str);
    $str = iconv('ISO-8859-2', 'UTF-8', $str);
    return $str;
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

function curl_grab_page($url){

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
    curl_setopt($ch, CURLOPT_TIMEOUT, 40);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_REFERER, $url);

    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_exec($ch);

    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);

    ob_start();
    $data = curl_exec($ch);
    ob_end_clean();

    curl_close($ch);
    return $data;

}


	function getArret($url , $host)
	{   
		$allHtml = '';
		$da = curl_grab_page($url);
 
	    $html = str_get_html($da);
	    $main = $html->find('div[class=content]',0); 
		$title = $html->find('title',0); 
		$title = strip_tags($title); 
	    
	    $article = utf8_encode( $main );
		$article = str_replace('href="', 'href="'.$host, $article);
		
		$allHtml .= '<h1>'.$title.'</h1>';
		$allHtml .= $article;
		
		return $allHtml;
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