<?php

class SearchTest extends PHPUnit_Framework_TestCase
{
	
	protected $search;
			
	public function __construct()
	{
		
		$this->search   = new Search(true);	

	}	
	
	public function testPrepareSearchForQuery(){
		
		$search = 'sicherheit, "autre mot ici", "tribunal"';
		
		$expect = array('sicherheit','autre mot ici','tribunal');

	  	$actual = $this->search->prepareSearch($search);
	  	
		$this->assertEquals($expect,$actual);		
	
	}
		
}