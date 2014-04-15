<?php
	
	require_once( plugin_dir_path( __FILE__ ) . '/simple_html_dom.php');
	require_once( plugin_dir_path( __FILE__ ) . '/function_misc.php');
	
	/*===============================
		Functions for both
	================================*/
	
	
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
	
	/*================================
		Get the data of list
	==================================*/

	function getListDate(){
		
		$urlList = 'http://relevancy.bger.ch/AZA/liste/fr/';
		$da = curl_grab_page($urlList);
	    $dataList = str_get_html($da);
	    $encoded = utf8_encode( $dataList );
		$html = str_get_html($encoded);
		
		$hrefs = array();
		
			foreach($html->find('p') as $e)
			{
				$inner = $e->innertext;
				$in[] = $inner;
			}
			
        unset($in[0]);
		$string = $in[1];
		
		$pieces = explode("<br>", $string);
		
			foreach($pieces as $inner)
			{
				$inner = trim($inner);
				if($inner != ''){
					$pre[] = $inner;
				}
			}
			
			foreach($pre as $in)
			{
					$newString = '';
					$htmlData = str_get_html($in);
					foreach($htmlData->find('a') as $a) 
					{
					    $link = $a->href;
						$hrefs = $link;
					}
					if(!empty($hrefs)){
						$newString  .= $hrefs;
					}
					$newString = remove_whitespace($newString);
					$string = str_replace('.htm', '', $newString);
					$values[] = $string;
			 }
			 
			return $values;
	
	}
	
	/*================================
		Get the data
	==================================*/	
 
 	function grabData($url, $date){
		
		$urlPage = $url.$date.'.htm';
	    $da = curl_grab_page($urlPage);
	  
	    $html = str_get_html($da);
	    $main = $html->find('body ',0); 
		 // initialize empty array to store the data array from each row
		$theData = array();
		// loop over rows
		foreach($main->find('TR') as $row) {
		    // initialize array to store the cell data from each row
		    $rowData = array();
			    foreach($row->find('TD') as $cell) {
			        // push the cell's text to the array
			        $new = remove_whitespace($cell->innertext);
					$rowData[] = $new;
			    }
			// push the row's data array to the 'big' array
		    $theData[] = $rowData;
		}
		return $theData;
	}
	
	/************************************
    	 Arrange and format  the data 
    *************************************/
 	
 	function cleanFormat($theData, $datap)
	{
		
		 	// Delete first p with text  
			unset($theData[0]);
			
			$sliced_array = array();
			$big = array();
			// Arrange all same infos in one array
			$Table = arrangeArray($theData);
			
			// format infos and dates
			foreach($Table as $key=>$tab)
			{	
					//$datap = array();
					$datad = array();
					$alltabs = array();
					$catTrim = array();
					$linkTrim = array();
					$noArret = array();
					$tab2 = array();
					$tab4 = array();
					$publication = array();
		
					// date parution
					$tab1 = array_shift($tab);
					// lien link
					$tab2 = array_shift($tab);
					// category
					$tab3 = array_shift($tab);
					// remarque
					$tab4[] = array_shift($tab);
					
					// reformat link
					$a = trim(strip_tags($tab2));
					$noArret[] = $a;
					$html = str_get_html($tab2);
					
						foreach($html->find('a') as $element)
			      		{ 
							$linkTrim[] = $element->href;
						}
					// reformat category
					$ct = strip_tags($tab3);
					// Test des arrets pour publication
					if (strpos( $ct , '*') !== false) { 
			    		$publication[] = '1';
					}
					else{
						$publication[] = '0';
					}
					//	
					$catTrim[] = trim($ct);
					// reformat date
					$pieces = explode('.',$tab1);
					$datad[] = $pieces[2].'-'.$pieces[1].'-'.$pieces[0];
					
					// recompose array
					$alltabs = array_merge($datap, $datad , $linkTrim, $noArret, $catTrim, $tab4 , $publication);
					// get all categories in array
					$sliced_array[] = $ct;
					// put each recomposed array in big array
					// test if noarret already in array
					if(multi_in_array($a, $big) == false)
					{
						$big[] = $alltabs;
					}
			  }
		
			  // Get all categories from infos in one array	
			  $b =  flattenArray($sliced_array);
			  $b = array_unique($b);
			  
			return array('dataArray'=>$big, 'catArray'=>$b);
	}
	
	function cleanFormat2($theData, $datap)
	{
			
			// Delete first p with text  
			unset($theData[0]);
			
			$sliced_array = array();
			$big = array();
			// Arrange all same infos in one array
			$Table = arrangeArray($theData);
			
			// format infos and dates
			foreach($Table as $key=>$tab)
			{	
					//$datap = array();
					$datad = array();
					$alltabs = array();
					$catTrim = array();
					$linkTrim = array();
					$noArret = array();
					$tab2 = array();
					$tab4 = array();
					$publication = array();

					// date parution
					$tab1 = array_shift($tab);
					// lien link
					$tab2 = array_shift($tab);
					// category
					$tab3 = array_shift($tab);
					// remarque
					$tab4[] = array_shift($tab);
					
					// reformat link
					$a = trim(strip_tags($tab2));
					$noArret[] = $a;
					$html = str_get_html($tab2);
					
						foreach($html->find('a') as $element)
			      		{ 
							$linkTrim[] = $element->href;
						}
					// reformat category
					$ct = strip_tags($tab3);
					// Test des arrets pour publication
					if (strpos( $ct , '*') !== false) { 
			    		$publication[] = '1';
					}
					else{
						$publication[] = '0';
					}
					//	
					$catTrim[] = trim($ct);
					// reformat date
					$pieces = explode('.',$tab1);
					$datad[] = $pieces[2].'-'.$pieces[1].'-'.$pieces[0];
					
					// recompose array
					$alltabs = array_merge($datap, $datad , $linkTrim, $noArret, $catTrim, $tab4 , $publication);
					// get all categories in array
					$sliced_array[] = $ct;
					// put each recomposed array in big array
					// test if noarret already in array
					if(multi_in_array($a, $big) == false)
					{
						$big[] = $alltabs;
					}
			  }
		
			  // Get all categories from infos in one array	
			  $b =  flattenArray($sliced_array);
			  $b = array_unique($b);
			  
			return array('dataArray'=>$big, 'catArray'=>$b);
	}
	
	
	function existCategorie($catArray){
		
		global $wpdb;
		$cat_list = $wpdb->get_results('SELECT * FROM wp_custom_categories');

		 foreach($catArray as $cat)
		 { 
				$cat = str_replace('*', '', $cat);
				$cat = trim($cat);
				
				$find = ' (en ';
				$pos  = strpos($cat, $find);
						
						$q = 'SELECT * FROM  wp_custom_categories WHERE  soundex(name)=soundex("'.mysql_real_escape_string($cat).'")';
						
						if($pos){
							$catw = str_replace($find, ' (', $cat);
							$q  .= ' OR   soundex(name)=soundex("'.mysql_real_escape_string($catw).'")';
						}
						
						$q .= ' OR   soundex(name_de)=soundex("'.mysql_real_escape_string($cat).'")
							    OR   soundex(name_it)=soundex("'.mysql_real_escape_string($cat).'")';		   
									   
				$query = $wpdb->get_results($q);
			    //  number of lines
				$row_cnt = $wpdb->num_rows; 
				
				if ($row_cnt != 0) { $arrayIn[$query[0]->term_id] = $query[0]->name;  }
				else  { $arrayNo[] = $cat;	 }
		  }
		  
		  return array('arrayIn'=>$arrayIn, 'arrayNo'=>$arrayNo);
	}
	
	
	function addCategory($arrayNo){
		global $wpdb;
		foreach($arrayNo as $k => $theCat)
		{
				$name = $theCat;
				$name_de = $theCat.'-allemand';
				$name_it = $theCat.'-italien';
				
				$data = array( 'name' => $name,  'name_de' => $name_de,  'name_it' => $name_it );  
				
				if( $wpdb->insert( 'wp_custom_categories', $data , array( '%s') ) === FALSE )
				{	
					return 'Probleme avec la création d\'une nouvelle sous-catégorie';
				}
				else 
				{  
					$last_id = $wpdb->insert_id;	
				    $newCat[$k][] = $last_id;
				    $newCat[$k][] = $name;
					$newCat[$k][] = $name_de;
					$newCat[$k][] = $name_it;
				}
		}
		return $newCat;  
	}
	
	function arrangeStack($allInfos){
		
	// geta all categories
		$arrayTout = array();
		
	    global $wpdb;
		$cat_list = $wpdb->get_results('SELECT * FROM wp_custom_categories');
		
	    foreach($cat_list as $cat)
		{ 
		    $arrayTout[$cat->term_id][] = $cat->name;
			$arrayTout[$cat->term_id][] = $cat->name_de;
			$arrayTout[$cat->term_id][] = $cat->name_it;	
		}
	// arrange array
	
			$in = array();
			foreach($allInfos as $stack )
			{
				$output = array();
				
				$temp = $stack;
				
				$output = array_slice($stack, -3, 1); 				
				$o = str_replace('*', '', $output[0]);
				$o = str_replace('(en', '(', $o);
				$o = trim($o);
				
				$put = array_slice($temp, -2, 1); 				
				$p = $put[0];
				
					foreach($arrayTout as $ky => $value)
					{
						$out2 = array();
						$out2[] = $ky;
						
						foreach($value as $k => $v)
						{
							$out3 = array();
							$out3[] = $k;
							
							similar_text($o, $v, $percent);
							
							if( $percent >= 90)
							{
								 $in[] = array_merge($stack, $out2, $out3); 
								 $sub[$ky][] = $p;
							}
						}
					}
			}
			
		 return array('preparedArray'=>$in, 'subArray'=>$sub);
	}
	
// wp_nouveautes
	
function insertDb($infosArray){
		
	 /************************************
	 	BDD insert infos retrive id
	 *************************************/
 	 global $wpdb;
	 
	   $allIn = $infosArray;

		foreach($allIn as $inKey => $inValue)
		{
			
			$datep_nouveaute        =  $inValue[0];	
			$dated_nouveaute        =  $inValue[1];	
			$link_nouveaute         =  $inValue[2];		
			$categorie_nouveaute    =  $inValue[7];	
			$langue_nouveaute       =  $inValue[8];		
			$remarque_nouveaute     =  $inValue[5];		
			$numero_nouveaute       =  $inValue[3];
			$publication_nouveaute  =  $inValue[6];
			
			$data = array( 
				'datep_nouveaute'       => $datep_nouveaute,  
				'dated_nouveaute'       => $dated_nouveaute, 
				'categorie_nouveaute'   => $categorie_nouveaute,  
				'link_nouveaute'        => $link_nouveaute,
				'numero_nouveaute'      => $numero_nouveaute,
				'texte_nouveaute'       => $texte_nouveaute,
				'langue_nouveaute'      => $langue_nouveaute,
				'publication_nouveaute' => $publication_nouveaute	  
			); 
			
				if( $wpdb->insert( 'wp_nouveautes', $data , array( '%s')) === FALSE )
				{	
					return 'Erreur durant la mise a jour : <pre>'.$data.'</pre>';
				}
				else 
				{ 
						$last_id = $wpdb->insert_id;
					    $newList[$inKey][] = $last_id;
					    $newList[$inKey][] = $link_nouveaute;
						$subCategories[$inKey][] = $last_id;
						$subCategories[$inKey][] = $categorie_nouveaute;
					    $subCategories[$inKey][] = $remarque_nouveaute;
				}
		}

		foreach( $subCategories as $data ){
			
			$pie = array();
			$refNouveaute = $data[0]; 
			$refCategorie = $data[1]; 
			$pie          = $data[2]; 
			$stri = '';
			
			$string = $pie;
			$regex = '#\((([^()]+|(?R))*)\)#';
			
			if (preg_match_all($regex, $string ,$matches)) 
			{
				 $stri = implode(' ', $matches[1]);
			} 
			else  
			{ $stri = $string; }
	
	
			$query = $wpdb->get_results('SELECT * FROM wp_subcategories  WHERE name = "'.$stri.'"  AND refCategorie = "'.$refCategorie.'" ');
			//  number of lines
			$row_cnt = $wpdb->num_rows; 
					
				if ($row_cnt == 0)
				{			
					$data = array( 'name' => $stri,  'refCategorie' => $refCategorie,  'refNouveaute' => $refNouveaute );  
								
						if( $wpdb->insert( 'wp_subcategories', $data , array( '%s')) === FALSE )
						{	
							return false;
						}
				} 
		}
		
		/**************************************
			Get extra keywords
		***************************************/
		
		$extraKeywords = $wpdb->get_results('SELECT * FROM wp_keyword_extra');
		
		$extraArray = array();
		
		if($extraKeywords)
		{
			foreach($extraKeywords as $extra){
				$needles[$extra->parent_keywords][] = $extra->extra_keywords ;
			}
		}
		
		/***************************************
			Get text for each new arrets
		***************************************/

		$listeLiens = $newList;
	
		foreach($listeLiens as $keyArret => $arret)
		{
			$host = 'http://jumpcgi.bger.ch';
			$id_nouveaute =	$arret[0];	
			$url =	$arret[1];	
			
			$texte_nouveaute = getArret($url, $host);

			$data = array( 
				'texte_nouveaute' => $texte_nouveaute  
			); 
			
			$wpdb->update( 'wp_nouveautes', $data , array( 'id_nouveaute' => $id_nouveaute), array( '%s'), array( '%d' ));
			
			if( $needles ){
				
				foreach($needles as $needle_id => $needle){
					foreach($needle as $word){
						if( search_database_keywords($word , $id_nouveaute ) !== 0 )
						{
								$data = array( 
									'parent_extra' => $needle_id,  
									'nouveaute_extra' => $id_nouveaute
								); 
										
								$wpdb->insert( 'wp_extracategories', $data , array( '%d' , '%d'));
								// break the foreaches 
								break 2;	
						}
					}
				}
				
			}
		
		}
		
	/// end foreach	
		
	return  true; 
 }

function insertDatabase($infosArray){
		
	  /************************************
	 	BDD insert infos retrive id
	  *************************************/
 	  global $wpdb;
	 
	  $allIn = $infosArray;

	  foreach($allIn as $inKey => $inValue)
	  {
			
			$datep_nouveaute        =  $inValue[0];	
			$dated_nouveaute        =  $inValue[1];	
			$link_nouveaute         =  $inValue[2];		
			$categorie_nouveaute    =  $inValue[7];	
			$langue_nouveaute       =  $inValue[8];		
			$remarque_nouveaute     =  $inValue[5];		
			$numero_nouveaute       =  $inValue[3];
			$publication_nouveaute  =  $inValue[6];
			
			$data = array( 
				'datep_nouveaute'       => $datep_nouveaute,  
				'dated_nouveaute'       => $dated_nouveaute, 
				'categorie_nouveaute'   => $categorie_nouveaute,  
				'link_nouveaute'        => $link_nouveaute,
				'numero_nouveaute'      => $numero_nouveaute,
				'texte_nouveaute'       => $texte_nouveaute,
				'langue_nouveaute'      => $langue_nouveaute,
				'publication_nouveaute' => $publication_nouveaute	  
			); 
			
				if( $wpdb->insert( 'wp_nouveautes', $data , array( '%s')) === FALSE )
				{	
					return 'Erreur durant la mise a jour : <pre>'.$data.'</pre>';
				}
				else 
				{ 
						$last_id                 = $wpdb->insert_id;
					    $newList[$inKey][]       = $last_id;
					    $newList[$inKey][]       = $link_nouveaute;
					    $newList[$inKey][]       = $numero_nouveaute;
					    $newList[$inKey][]       = $dated_nouveaute;
						$subCategories[$inKey][] = $last_id;
						$subCategories[$inKey][] = $categorie_nouveaute;
					    $subCategories[$inKey][] = $remarque_nouveaute;
				}
		}

		foreach( $subCategories as $data )
		{
			
			$pie  = array();
			$stri = '';
			
			$refNouveaute = $data[0]; 
			$refCategorie = $data[1]; 
			$pie          = $data[2]; 
			
			$string = $pie;
			$regex  = '#\((([^()]+|(?R))*)\)#';
			
			if (preg_match_all($regex, $string ,$matches)) 
			{
				 $stri = implode(' ', $matches[1]);
			} 
			else  
			{ 
				$stri = $string; 
			}
		
			$query = $wpdb->get_results('SELECT * FROM wp_subcategories  WHERE name = "'.$stri.'"  AND refCategorie = "'.$refCategorie.'" ');
			//  number of lines
			$row_cnt = $wpdb->num_rows; 
					
			if ($row_cnt == 0)
			{			
				$data = array( 'name' => $stri,  'refCategorie' => $refCategorie,  'refNouveaute' => $refNouveaute );  
						
				if( $wpdb->insert( 'wp_subcategories', $data , array( '%s')) === FALSE )
				{	
					return false;
				}
			} 
			
		}
		
		return  $newList; 
 }


	
function updateTextArret($listeLiens){
		
	 /************************************
	 	BDD insert infos retrive id
	 *************************************/
 	 global $wpdb;
	 
	   	/**************************************
			Get extra keywords
		***************************************/
		
		$extraKeywords = $wpdb->get_results('SELECT * FROM wp_keyword_extra');
		
		$extraArray = array();
		
		if($extraKeywords)
		{
			foreach($extraKeywords as $extra){
				$needles[$extra->parent_keywords][] = $extra->extra_keywords ;
			}
		}
		
		/***************************************
			Get text for each new arrets
		***************************************/
	
		foreach($listeLiens as $keyArret => $arret)
		{
			$host            = 'http://relevancy.bger.ch';
			$id_nouveaute    = $arret[0];	
			$url             = $arret[1];	
			$numero          = $arret[3];	
			$dated           = $arret[4];	
			
			// new installement
			$date  = new DateTime($dated);
			$ndate = $date->format('d-m-Y');

			$numero = str_replace("/","-",$numero);

			// insert							
			$host = 'http://relevancy.bger.ch';
			$new  = 'http://relevancy.bger.ch/php/aza/http/index.php?lang=fr&zoom=&type=show_document&highlight_docid=aza%3A%2F%2F';
			$url  = $new.$ndate.'-'.$numero;
			
			$texte_nouveaute = getArret($url, $host);
			
			if( !empty($texte_nouveaute) )
			{		
				$data = array( 
					'texte_nouveaute' => $texte_nouveaute  
				); 
				
				$wpdb->update( 'wp_nouveautes', $data , array( 'id_nouveaute' => $id_nouveaute), array( '%s'), array( '%d' ));
				
				if( $needles )
				{				
					foreach($needles as $needle_id => $needle)
					{
						foreach($needle as $word)
						{
							if( search_database_keywords($word , $id_nouveaute ) !== 0 )
							{
								$data = array( 
									'parent_extra' => $needle_id,  
									'nouveaute_extra' => $id_nouveaute
								); 
										
								$wpdb->insert( 'wp_extracategories', $data , array( '%d' , '%d'));
								// break the foreaches 
								break 2;	
								
							} // end if search
						}
					} // end foreach 2)
					
				} // end if needles
				
			} // if text not empty
			else
			{
				return false;
			}
		
		} // end foreach 1)
		
	return  true; 
	
 }
