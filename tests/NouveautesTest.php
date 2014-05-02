<?php

class NouveautesTest extends PHPUnit_Framework_TestCase
{	
	protected $nouveaute;
			
	public function __construct()
	{
		$this->nouveaute = new Nouveautes(true);		
	}	
	
	public function testGetArretList(){
		
		$date   = '2014-04-13';
		
		$expect = array(
			174 => array(
				1 => array(
					'id_nouveaute'          => '1',
					'datep_nouveaute'       => '2014-04-13',
					'dated_nouveaute'       => '2014-04-01',
					'categorie_nouveaute'   => '174',
					'nameCat'               => 'Droit fondamental',
					'nameSub'               => 'test',
					'link_nouveaute'        => 'http://www.google.ch',
					'numero_nouveaute'      => '12_345/2014',
					'publication_nouveaute' => '1'
				)
			)
		);
				
		$actual = $this->nouveaute->getArretsForDates($date);
		
		$this->assertEquals($expect,$actual);	
		
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
	
	public function testDispatchIsPubArretByKeywordFound(){
			
		$arrets = array(
			1 => array('publication_nouveaute' => 1),
			2 => array('publication_nouveaute' => 0)  
		);
		
		$keywords = array('"quam volutpat molestie"' , 'volutpat');
		
		$isPub  = 1;
		
		$expect = array( 1 => '"quam volutpat molestie",volutpat');
				
		$actual = $this->nouveaute->dispatchArretWithKeyword($arrets , $keywords , $isPub);
		
		$this->assertEquals($expect,$actual);
		
	}
	
	public function testDispatchIsPubArretByKeywordButNotFound(){
			
		$arrets = array(
			1 => array('publication_nouveaute' => 0),
			2 => array('publication_nouveaute' => 1)  
		);
		
		$keywords = array('"quam volutpat molestie"' , 'volutpat');
		
		$isPub  = 1;
		
		$expect = array(2 => '');
				
		$actual = $this->nouveaute->dispatchArretWithKeyword($arrets , $keywords , $isPub);
		
		$this->assertEquals($expect,$actual);
		
	}
		
	public function testDispatchIsPubArretByKeywordButNotAllFound(){

		$arrets = array(
			1 => array('publication_nouveaute' => 0),
			2 => array('publication_nouveaute' => 0)  
		);
		
		$keywords = array('"quam volutpat molestie"' , 'volutpat');
		
		$isPub  = 0;
		
		$expect = array( 1 => '"quam volutpat molestie",volutpat' , 2 => '');
				
		$actual = $this->nouveaute->dispatchArretWithKeyword($arrets , $keywords , $isPub);
		
		$this->assertEquals($expect,$actual);
	}
	
	public function testDispatchArretIsPubNoKeywords(){

		$arrets = array(
			1 => array('publication_nouveaute' => 1),
			2 => array('publication_nouveaute' => 0)  
		);
		
		$isPub  = 1;
		
		$expect = array( 1 => '' );
				
		$actual = $this->nouveaute->dispatchArretWithKeyword($arrets , NULL , $isPub);
		
		$this->assertEquals($expect,$actual);
	}
	
	public function testDispatchhArretNoKeywords(){

		$arrets = array(
			1 => array('publication_nouveaute' => 0),
			2 => array('publication_nouveaute' => 0)  
		);
		
		$isPub  = 0;
		
		$expect = array(1 => '' , 2 => '');
				
		$actual = $this->nouveaute->dispatchArretWithKeyword($arrets , NULL , $isPub);
				
		$this->assertEquals($expect,$actual);
	}
				
}