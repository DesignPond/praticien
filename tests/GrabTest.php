<?php

require_once( '/Applications/MAMP/htdocs/praticien/wp-content/plugins/new-arret-plugin/simple_html_dom.php');

class GrabTest extends PHPUnit_Framework_TestCase
{
	protected $grab;
	
	// urls	
	
	protected $urlArret;
			
	public function __construct()
	{
		$this->grab     = new Grab;
		
		// urls
	
		$this->urlArret = 'http://relevancy.bger.ch/php/aza/http/index.php?lang=fr&zoom=&type=show_document&highlight_docid=aza%3A%2F%2F';	
	}
	
	protected function setUp()
    {		
    }
	
    public function testGetTitleOFPage()
    {
    	
    	$url    = $this->urlArret.'12-02-2014-5A_816-2013';
    	
    	$html   = $this->grab->getPage($url);
    	$actual = $this->grab->getTitle($html);
    	
    	$expect = '5A_816/2013 (12.02.2014)';
    	
    	$this->assertEquals($actual,$expect);
    }
		
}