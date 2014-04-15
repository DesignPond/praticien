<?php 

require_once( plugin_dir_path( __FILE__ ) . '../simple_html_dom.php');

class Grab {

	protected $html;
	
	protected $user_agent;

	function __construct() {
	
		$this->html = new simple_html_dom();
		
		$this->user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
		
	}	

	public function curl_grab_page($url){
	
	    $ch = curl_init();
	
	    curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 40);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_REFERER, $url);
	
	    curl_setopt($ch, CURLOPT_HEADER, TRUE);
	    curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
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

	/* ========================================
	   GRAB ARRET FROM TF WEBSITE
	==========================================*/
		
	public function getPage($url){
		
		// Curl the url and retrive html
		$conten = $this->curl_grab_page($url);
		
		// Parse html 
		return str_get_html($conten);
		
	}

	public function getTitle($html)
	{   
		// Get the title of page
		$title  = $html->find('title',0)->innertext; 
		
		return  strip_tags($title); 
	}
	
	public function getContent($html)
	{   
		// Get the content of page		
		$content = $html->find('div[class=content]',0)->innertext; 
		
		return utf8_encode($content);
	}

	public function getArticle($url , $host){
		
		$allHtml = '';
		
		$html    = $this->getPage($url);		
		$title   = $this->getTitle($html);		
		$content = $this->getContent($html);
		
		// Prepare title and text
		$article  = str_replace('href="', 'href="'.$host, $content);
		
		$allHtml .= '<h1>'.$title.'</h1>';
		$allHtml .= $article;
		
		return $allHtml;
			
	}
	
	/* ========================================
	   GRAB LIST FROM TF WEBSITE
	==========================================*/
	
	public function getLastDates($url){
		
		$url  = 'http://relevancy.bger.ch/AZA/liste/fr/';
		
		$html = $this->getPage($url);		
		
		$hrefs      = '';
		$paragraphs = array();
		$values     = array();
		
		foreach($html->find('p') as $p)
		{
			$paragraphs[] = utf8_encode($p->innertext);
		}
      
		$list = $paragraphs[1];		
		$list = explode("<br>", $list);
		
		$list = $this->removeBlank($list);
		
		
		foreach($list as $link)
		{
			$newString = '';
			
			$htmlLinks = str_get_html($link);
			
			// get the link (the date of arret)
			foreach($htmlLinks->find('a') as $a) 
			{
			    $hrefs = $a->href;
			}
			
			if(!empty($hrefs))
			{
				$newString .= $hrefs;
			}
			
			$newString = $this->removeWhitespace($newString);
			
			// remove page extension
			$string    = str_replace('.htm', '', $newString);
			$values[]  = $string;
		}

		return $values;

	}
	
 	public function getListDecisions($url, $date){
		
		$urlPage = $url.$date.'.htm';
		
	    $html    = $this->getPage($urlPage);		
	    $content = $html->find('body ',0); 
	    
		 // initialize empty array to store the data array from each row
		$theData = array();
		
		// loop over rows
		foreach($content->find('TR') as $row) 
		{
		    // initialize array to store the cell data from each row
		    $rowData = array();
		    
			    foreach($row->find('TD') as $cell) 
			    {
			        // push the cell's text to the array
			        $rowData[] = $this->removeWhitespace($cell->innertext);
			    }
			    
			// push the row's data array to the 'big' array
		    $theData[] = $rowData;
		}
		
		return $theData;
	}
	

	/* ================================================
	   Utils, remove blanks from an array or string
	==================================================*/
	
	public function removeBlank($array){
		
		$notempty = array();
		
		foreach($array as $inner)
		{
			$inner = trim($inner);
			
			if($inner != '')
			{
				$notempty[] = $inner;
			}
		}	
		
		return $notempty;	
	}

    public function removeWhitespace($string) {
     
	    $string = preg_replace('/\s+/', ' ', $string);
	    $string = trim($string);
	    return $string;     
    }
	
}