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
				
		$actual = $this->nouveaute->getArretsAndCategoriesForDates($date);
		
		$this->assertEquals($expect,$actual['arrets']);	
		
	}

	public function testDispatchArretsForUser(){
		
		$arrets = array(
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
		
		$users  = array( 
			1 => array(
				204 => array( 'keywords' => array('Tribunal Fédéral'),'ispub' => 1),
				180 => array( 'keywords' => '', 'ispub' => 0),
				192 => array( 'keywords' => '', 'ispub' => 0),
				174 => array( 'keywords' => '', 'ispub' => 0),
				247 => array( 'keywords' => array('Bohnet') , 'ispub' => 0)				
			),
			3 => array(
				198 => array( 'keywords' => '', 'ispub' => 0),
				199 => array( 'keywords' => '', 'ispub' => 1),
				203 => array( 'keywords' => '', 'ispub' => 0),
				195 => array( 'keywords' => '', 'ispub' => 0),
				247 => array( 'keywords' => array('miete'),'ispub' => 0)
			)			
		);
		
		$expect = array( 1 => 
			array( 0 => array( 1 => '' ) ) 
		);
				
		$actual = $this->nouveaute->assignArretsUsers($users, $arrets);		
		
		$this->assertEquals($expect,$actual);	
		
	}

	public function testGetCategoriesList(){

		$date   = '2014-04-13';
				
		$expect  = array(
						174 => array(
							1 => array('ispub' => 1)
						)
					);
		
		$actual = $this->nouveaute->getArretsAndCategoriesForDates($date);
		
		$this->assertEquals($expect,$actual['categories']);	
		
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
			1 => array('id_nouveaute' => 1 , 'publication_nouveaute' => 1),
			2 => array('id_nouveaute' => 2 ,'publication_nouveaute' => 0)  
		);
		
		$keywords = array('"quam volutpat molestie"' , 'volutpat');
		
		$isPub  = 1;
		
		$expect = array( 1 => '"quam volutpat molestie",volutpat' );
				
		$actual = $this->nouveaute->dispatchArretWithKeyword($arrets , $keywords , $isPub);
		
		$this->assertEquals($expect,$actual);
		
	}
	
	public function testDispatchIsPubArretByKeywordButNotFound(){
			
		$arrets = array(
			1 => array('id_nouveaute' => 1 ,'publication_nouveaute' => 0),
			2 => array('id_nouveaute' => 2 ,'publication_nouveaute' => 1)  
		);
		
		$keywords = array('"quam volutpat molestie"' , 'volutpat');
		
		$isPub  = 1;
		
		$expect = array();
				
		$actual = $this->nouveaute->dispatchArretWithKeyword($arrets , $keywords , $isPub);
		
		$this->assertEquals($expect,$actual);
		
	}
		
	public function testDispatchIsPubArretByKeywordButNotAllFound(){

		$arrets = array(
			1 => array('id_nouveaute' => 1 ,'publication_nouveaute' => 0),
			2 => array('id_nouveaute' => 2 ,'publication_nouveaute' => 0)  
		);
		
		$keywords = array('"quam volutpat molestie"' , 'volutpat');
		
		$isPub  = 0;
		
		$expect = array( 1 => '"quam volutpat molestie",volutpat');
				
		$actual = $this->nouveaute->dispatchArretWithKeyword($arrets , $keywords , $isPub);
		
		$this->assertEquals($expect,$actual);
	}
	
	public function testDispatchArretIsPubNoKeywords(){

		$arrets = array(
			1 => array('id_nouveaute' => 1 ,'publication_nouveaute' => 1),
			2 => array('id_nouveaute' => 2 ,'publication_nouveaute' => 0)  
		);
		
		$isPub  = 1;
		
		$expect = array( 1 => '' );
				
		$actual = $this->nouveaute->dispatchArretWithKeyword($arrets , NULL , $isPub);
		
		$this->assertEquals($expect,$actual);
	}
	
	public function testDispatchhArretNoKeywords(){

		$arrets = array(
			1 => array('id_nouveaute' => 1 ,'publication_nouveaute' => 0),
			2 => array('id_nouveaute' => 2 ,'publication_nouveaute' => 0)  
		);
		
		$isPub  = 0;
		
		$expect = array(1 => '' , 2 => '');
				
		$actual = $this->nouveaute->dispatchArretWithKeyword($arrets , NULL , $isPub);
				
		$this->assertEquals($expect,$actual);
	}
		
	public function testCleanUserArray(){
	
		$users = array(
			1 => array(
	            0 => array(3 => '' , 4 => ''),
	            1 => array(1 => '' , 2 => ''),
	            2 => array(1 => 'Lorem')		
		    )
		);
		
		$expect = array(
			1 => array(
				1 => 'Lorem', 
				2 => '', 
				3 => '', 
				4 => ''
			)
		);
		
		$actual = $this->nouveaute->cleanEachUser($users);
		
		$this->assertEquals($expect,$actual);		
		
	}
		
				
}