<?php

class Common{

	function limit_words($string, $word_limit)
	{
		$words = explode(" ",$string);
		$new   = implode(" ",array_splice($words,0,$word_limit));
		
		if( !empty($new) ){
			$new = $new.'...';
		}
		
		return $new;
	}
}