<?php

class NouveautesTest extends PHPUnit_Framework_TestCase
{	
	protected $nouveaute;
			
	public function __construct()
	{
		$this->nouveaute = new Nouveautes(true);		
	}	
	
	public function testIsPublication(){
		
		$arret = array('publication_nouveaute' => 1);
		
		$result = $this->nouveaute->isPub($arret);
		
		$this->assertTrue($result);
		
	}
	
	public function testIsNotPublication(){
		
		$arret = array('publication_nouveaute' => 0);
		
		$result = $this->nouveaute->isPub($arret);
		
		$this->assertFalse($result);
		
	}
	
	public function testFormatKeywordsForSearchInArret(){
			
		$keywords = '"quam volutpat molestie" , volutpat';	
		
		$expect   = '"quam volutpat molestie" volutpat' ;
						
		$actual   = $this->nouveaute->formatKeywords($keywords);
		
		$this->assertEquals($expect,$actual);	

	}
	
	public function testArretsInSearch(){
	
		$keywords = array('"quam volutpat molestie"' , 'volutpat') ;	
		
		$expect   = '"quam volutpat molestie",volutpat';
				
		$actual   = $this->nouveaute->arretsInSearch($keywords , 1);
		
		$this->assertEquals($expect,$actual);	
		
	}
	
	public function testDispatchArretByKeywordFound(){
			
		$arrets = array(
			1 => array('publication_nouveaute' => 1),
			2 => array('publication_nouveaute' => 0)  
		);
		
		$keywords = array('"quam volutpat molestie"' , 'volutpat') ;
		
		$isPub    = 1;
		
		$expect   = array( 1 => '"quam volutpat molestie",volutpat');
				
		$actual   = $this->nouveaute->dispatchArretByKeyword($arrets , $keywords , $isPub);
		
		$this->assertEquals($expect,$actual);
		
	}
	
	public function testDispatchArretByKeywordNotFound(){

		$arrets = array(
			1 => array('publication_nouveaute' => 0),
			2 => array('publication_nouveaute' => 0)  
		);
		
		$keywords = array('"quam volutpat molestie"' , 'volutpat') ;
		
		$isPub    = 0;
		
		$expect   = array( 1 => '"quam volutpat molestie",volutpat' , 2 => '');
				
		$actual   = $this->nouveaute->dispatchArretByKeyword($arrets , $keywords , $isPub);
		
		$this->assertEquals($expect,$actual);
	}
			
}