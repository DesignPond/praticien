<?php

class UpdateTest extends PHPUnit_Framework_TestCase
{
	
	protected $update;
	
	// urls	
	
	protected $urlArret;
			
	public function __construct()
	{
		
		$this->update   = new Update(true);	
		
		// urls
	
		$this->urlArret = 'http://relevancy.bger.ch/php/aza/http/index.php?lang=fr&zoom=&type=show_document&highlight_docid=aza%3A%2F%2F';	
	}	

	public function testUpdateTextArret(){
		
		$links  = array( 'dated_nouveaute'  => '2014-03-24', 'numero_nouveaute' => '2C_692/2013' );
		
		$expect = $this->urlArret.'24-03-2014-2C_692-2013'; 
		
		$actual = $this->update->formatArretUrl($links);
		
		$this->assertEquals($expect,$actual);	
		
	}
		
}