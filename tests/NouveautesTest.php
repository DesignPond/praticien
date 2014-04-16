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
	
	public function testsearchKeywordInArret(){
	
		$arret = array(
			array(
				'id_nouveaute'          => '92',
				'datep_nouveaute'       => '2014-04-07',
	            'dated_nouveaute'       => '2014-04-02',
	            'categorie_nouveaute'   => '208',
	            'nameCat'               => 'Droit des poursuites et faillites',
	            'nameSub'               => 'Prestazione complementare',
	            'numero_nouveaute'      => '5A_676/2013',
	            'publication_nouveaute' => 0 
			)  
		);
		
		$expect = array(
	                
		);
				
		$actual = $this->database->arrangeArret($arret);
		
		$this->assertEquals($expect,$actual);
	}		
}