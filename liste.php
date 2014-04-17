<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="css/demo.css" rel="stylesheet" type="text/css" />
<title>Retrive email with imap/php</title>

</head>

<body>
<div id="container">

<?php

include_once('simple_html_dom.php');
include_once('functions.php');
include_once('config.php');

/*================================
	Variables
==================================*/
$url = 'http://relevancy.bger.ch/AZA/liste/fr/';

$hosted = 'http://jumpcgi.bger.ch/cgi-bin/JumpCGI?id=';

 /*================================
	Get the data of list
==================================*/
 
    $da = curl_grab_page($url);
  
    $new = preg_replace('/&lt;(\/?a(?:|\s[\S\s]+?))&gt;/i', '<$1>', $da);
    $html = str_get_html($da);
	
	$hrefs = array();
	
	foreach($html->find('p') as $e)
	{
		$inner = $e->innertext;
		$in[] = $inner;
	}
	
	unset($in[0]);
	$string = $in[1];
	
	$pieces = explode("<br>", $string);
	
	foreach($pieces as $inner){
		$inner = trim($inner);
		if($inner != ''){
		$pre[] = $inner;
		}
	}

	foreach($pre as $in){
			$newString = '';
			$html = str_get_html($in);
			foreach($html->find('a') as $a) 
			{
			    $link = $a->href;
				$hrefs = $link;
			}
			if(!empty($hrefs)){
				$newString  .= $hrefs;
			}
			$newString = remove_whitespace($newString);
			$values[] = $newString;
	 }

	 // echo '<pre>'; print_r($value); echo '</pre>';
	
/*================================
	Get the data
==================================*/
	
	$datep = '121024.htm';
	 
   // debut test avec tout les dates	
	
	$datap[] = getData($datep);

	$url = 'http://relevancy.bger.ch/AZA/liste/fr/'.$datep;
 
    $da = curl_grab_page($url);
  
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
		        $rowData[] = remove_whitespace($cell->innertext);
		    }
		// push the row's data array to the 'big' array
	    $theData[] = $rowData;
	}

 /************************************
     Arrange and format  the data 
 *************************************/
 
 	// Delete first p with text  
	unset($theData[0]);
	
	$sliced_array = array();
	$big = array();
	// Arrange all same infos in one array
	$Table = arrangeArray($theData);
	
	// format infos and dates
	foreach($Table as $key=>$tab)
	{	
			$datad = array();
			$t = array();
			$catTrim = array();
			$linkTrim = array();
			$noArret = array();
			$tab2 = array();
			$tab4 = array();

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
			$catTrim[] = trim($ct);
			// reformat date
			$pieces = explode('.',$tab1);
			$datad[] = $pieces[2].'-'.$pieces[1].'-'.$pieces[0];
			
			// recompose array
			$t = array_merge($datap, $datad , $linkTrim, $noArret, $catTrim, $tab4);
			// get all categories in array
			$sliced_array[] = $ct;
			// put each recomposed array in big array
			$big[] = $t;
	  }

	  // Get all categories from infos in one array	
	  $b =  flattenArray($sliced_array);
	  $b = array_unique($b);
	
	// echo '<pre>'; print_r($big); echo '</pre>';
	
	 /************************************
	 		    BDD update/select
	 *************************************/

	$arrayIn = array();
	$arrayNo = array();
	$arrayTout = array();
	
	// SQL
	foreach($b as $cat)
	{
		mysql_query("SET NAMES UTF8"); 
		$cat = str_replace('*', '', $cat);
		$cat = trim($cat);
		$query = 'SELECT * FROM wp_custom_categories
				  WHERE name = "'.mysql_real_escape_string($cat).'" 
				  OR name_de = "'.mysql_real_escape_string($cat).'" 
				  OR name_it = "'.mysql_real_escape_string($cat).'"  ';
		$result = mysql_query($query);
		    //  number of lines
			$row_cnt = mysql_num_rows($result);
			$row = mysql_fetch_assoc($result);
			if ($row_cnt == 0)
			{
				$arrayNo[] = $cat;
			}
			else
			{
				$arrayIn[$row['term_id']] = $row['name'];	
			}
	}
		
	// echo 'Non <pre>'; print_r($arrayNo); echo '</pre>';
	// echo 'Oui <pre>'; print_r($arrayIn); echo '</pre>';
	 
//
// test avec tout les dates	
 /************************************
 		 BDD insert categories
 *************************************/

  $newCat = array();
	foreach($arrayNo as $k => $theCat)
	{
		$name = $theCat;
		$name_de = $theCat.'-allemand';
		$name_it = $theCat.'-italien';
		$requete = 'INSERT INTO wp_custom_categories SET
		            name="'.mysql_real_escape_string($name).'",
					name_de="'.mysql_real_escape_string($name_de).'",	
					name_it="'.mysql_real_escape_string($name_it).'" ';	
			
		if(!mysql_query($requete))
		{
		    echo mysql_error();
		}
		else
		{
			$last_id = mysql_insert_id();	
		    $newCat[$k][] = $last_id;
		    $newCat[$k][] = $name;
			$newCat[$k][] = $name_de;
			$newCat[$k][] = $name_it;
		}
	}
	
	//echo '<pre>'; print_r($newCat); echo '</pre>';
	
	// see the categories in tables  wd_remove_accents
	$arrayTout = array();
	
	mysql_query("SET NAMES UTF8"); 
	$resultat = mysql_query('SELECT * FROM wp_custom_categories');
	while ($rows = mysql_fetch_assoc($resultat)) 
	{
	    $arrayTout[$rows['term_id']][] = $rows['name'];
		$arrayTout[$rows['term_id']][] = $rows['name_de'];
		$arrayTout[$rows['term_id']][] = $rows['name_it'];	
	}
	
		
	 // echo '<pre>'; print_r($arrayTout); echo '</pre>';
	 
	 
	
	/*****************************************
		 Add id categorie to infos array
	*****************************************/

	$in = array();
	foreach($big as $stack )
	{
		$output = array();
		
		$temp = $stack;
		
		$output = array_slice($stack, -2, 1); 				
		$o = str_replace('*', '', $output[0]);
		$o = str_replace('(en', '(', $o);
		$o = trim($o);
		
		$put = array_slice($temp, -1, 1); 				
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

	// echo '<pre>'; print_r($in); echo '</pre>';
	
	/*			
	mysql_query("SET NAMES UTF8"); 
	$resultatSub = mysql_query('SELECT * FROM wp_subcategories');
	
	while ($rowsSub = mysql_fetch_assoc($resultatSub)) 
	{
	    $arrayToutSub[$rowsSub['term_id']][] = $rowsSub['name'];

	}*/
		
	//echo '<pre>'; print_r($arrayToutSub); echo '</pre>';
	 

	
 /************************************
 	BDD insert infos retrive id
 *************************************/
	

/*

   $allIn = $in;
   $newList = array();
   $subCategories = array();
   
	foreach($allIn as $inKey => $inValue)
	{
		$datep_nouveaute     =	$inValue[0];	
		$dated_nouveaute     =	$inValue[1];	
		$link_nouveaute      =	$inValue[2];		
		$categorie_nouveaute = $inValue[6];	
		$langue_nouveaute    = $inValue[7];		
		$remarque_nouveaute  = $inValue[5];		
		$numero_nouveaute    = $inValue[3];
		
		$requete = 'INSERT INTO wp_nouveautes SET
					datep_nouveaute = "'.$datep_nouveaute.'",
					dated_nouveaute = "'.$dated_nouveaute.'",
					categorie_nouveaute = "'.$categorie_nouveaute.'",
					link_nouveaute = "'.$link_nouveaute.'",
		            numero_nouveaute="'.$numero_nouveaute.'" ,
					langue_nouveaute="'.$langue_nouveaute.'" ';	
			
		if(!mysql_query($requete))
		{
		    echo mysql_error();
		}
		else
		{
			$last_id = mysql_insert_id();
				
		    $newList[$inKey][] = $last_id;
		    $newList[$inKey][] = $link_nouveaute;
			
			$subCategories[$inKey][] = $last_id;
			$subCategories[$inKey][] = $categorie_nouveaute;
		    $subCategories[$inKey][] = $remarque_nouveaute;
		}
	}
	
	// echo '<pre>'; print_r($subCategories); echo '</pre>';

	
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
		{
			$stri = $string;
		}


		// Test if in BDD
			mysql_query("SET NAMES UTF8"); 
			
			$query = 'SELECT * FROM wp_subcategories
					  WHERE name = "'.mysql_real_escape_string($stri).'" 
					  AND refCategorie = "'.$refCategorie.'"  ';
			$result = mysql_query($query);
			
			    //  number of lines
				$row_cnt = mysql_num_rows($result);
				
				if ($row_cnt == 0)
				{
				    $r = 'INSERT INTO wp_subcategories SET
				          name = "'.mysql_real_escape_string(ucfirst($stri)).'",
				          refCategorie = "'.$refCategorie.'" ,
						  refNouveaute = "'.$refNouveaute.'"  ';	
							
					if(!mysql_query($r)) {  echo mysql_error(); }
				} 
		 
	}


*/


	// echo '<pre>'; print_r($nSub); echo '</pre>';
	
	/***************************************
		Get text for each new arrets
	***************************************/
	


/*

	$listeLiens = $newList;

	foreach($listeLiens as $keyArret => $arret)
	{
		$host = 'http://jumpcgi.bger.ch';
		$id_nouveaute =	$arret[0];	
		$url =	$arret[1];	
		
		$texte_nouveaute = getArret($url, $host);
		
		$requete = 'UPDATE wp_nouveautes SET
					texte_nouveaute = "'.mysql_real_escape_string($texte_nouveaute).'"
					WHERE id_nouveaute = "'.$id_nouveaute.'" ';	
			
		if(!mysql_query($requete))
		{
		    echo mysql_error();
		}
		else
		{
			echo 'ok pour : '.$keyArret.'<br/>';
		}
	}
	unset($listeLiens);

*/




?>
</div>

</body>
</html>
