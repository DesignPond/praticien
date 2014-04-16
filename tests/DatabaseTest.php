<?php

class DatabaseTest extends PHPUnit_Framework_TestCase
{
	
	protected $database;
	
			
	public function __construct()
	{
		
		$this->database = new Database(true);

	}
	
	protected function setUp()
    {
    		
		// delete test table before test
		$this->database->deleteTable();
		
    }
    
	public function testSoundexSimilarCategory(){
	
		$string = 'Procédure civile';
				
		$actual = $this->database->findCategory($string);
		
		$this->assertEquals(1,$actual);
	
	}
			
	public function testArrangeArretWithInfos(){
	
		$arret = array(
			array(
				0 => '2014-04-07',
	            1 => '2014-04-02',
	            2 => '/cgi-bin/JumpCGI?id=02.04.2014_1B_88/2014&lang=fr',
	            3 => '1B_88/2014',
	            4 => 'Strafprozess',
	            5 => 'Sicherheitshaft',
	            6 => '0'
			)
		);
		
		$expect = array(
			array(
				0 => '2014-04-07',
	            1 => '2014-04-02',
	            2 => '/cgi-bin/JumpCGI?id=02.04.2014_1B_88/2014&lang=fr',
	            3 => '1B_88/2014',
	            4 => 'Strafprozess',
	            5 => 'Sicherheitshaft',
	            6 => '0',
	            7 => '208',     
	            8 => '1'   
			)                   
		);
				
		$actual = $this->database->arrangeArret($arret);
		
		$this->assertEquals($expect,$actual);
	
	}
		
	public function testArrangeArretForInsert(){

		$arret = array(
			0 => '2014-04-07',
            1 => '2014-04-02',
            2 => '/cgi-bin/JumpCGI?id=02.04.2014_1B_88/2014&lang=fr',
            3 => '1B_88/2014',
            4 => 'Strafprozess',
            5 => 'Sicherheitshaft',
            6 => '0',
            7 => '208',     
            8 => '1'                     
		);
		
		$expect = array( 
			'datep_nouveaute'       => '2014-04-07',  
			'dated_nouveaute'       => '2014-04-02', 
			'categorie_nouveaute'   => '208', 
			'link_nouveaute'        => '/cgi-bin/JumpCGI?id=02.04.2014_1B_88/2014&lang=fr',
			'numero_nouveaute'      => '1B_88/2014',
			'langue_nouveaute'      => '1',
			'publication_nouveaute' => '0',
			'updated'               => '0' 
		); 

		// $subcategorie = array( 'name' => $arret[5] , 'refCategorie' => $arret[7] ); 
				
		$result = $this->database->organiserArret($arret);
		
		$actual = $result['arret'];
		
		$this->assertEquals($expect,$actual);
	
	}
		
	public function testArrangeSubcategorieForInsert(){

		$arret = array(
			0 => '2014-04-07',
            1 => '2014-04-02',
            2 => '/cgi-bin/JumpCGI?id=02.04.2014_1B_88/2014&lang=fr',
            3 => '1B_88/2014',
            4 => 'Strafprozess',
            5 => 'Sicherheitshaft',
            6 => '0',
            7 => '208',     
            8 => '1'                     
		);

		$expect = array( 'name' => $arret[5] , 'refCategorie' => $arret[7] ); 
				
		$result = $this->database->organiserArret($arret);
		
		$actual = $result['subcategorie'];
		
		$this->assertEquals($expect,$actual);
	
	}
			
	public function testInsertNewArretInDatabase(){
						
		$arret = array( 
			'datep_nouveaute'       => '2014-04-07',  
			'dated_nouveaute'       => '2014-04-02', 
			'categorie_nouveaute'   => '208', 
			'link_nouveaute'        => '/cgi-bin/JumpCGI?id=02.04.2014_1B_88/2014&lang=fr',
			'numero_nouveaute'      => '1B_88/2014',
			'langue_nouveaute'      => '1',
			'publication_nouveaute' => '0'
		); 
		
		$actual = $this->database->insertArret($arret);
		
		// a new id
		$this->assertInternalType("int", $actual);
		
	}
			
	public function testInsertNewSubcategory(){

		$arret  = array( 'name' => 'Sicherheitshaft or else' , 'refCategorie' => '208' ); 
				
		$result = $this->database->insertSubcategory($arret,'11111');
		
		$this->assertTrue($result);
	
	}
	
	public function testCategoryDontExistInDb(){
	
		$categories = array( 'Strafrecht' , 'Droits politique' , 'Heyho' ); 

		$actual = $this->database->existCategorie($categories);
		
		$this->assertTrue($actual);
	}

	
	public function testCategoryExistInDb(){
	
		$categories = array( 'Strafrecht' , 'Droits politique' ); 

		$actual = $this->database->existCategorie($categories);
		
		$this->assertTrue($actual);
	}
	
	public function testInserNewCategoryInDb(){
		
		$category = 'I am not in here';

		$result   = $this->database->insertCategory($category);
		
		$this->assertTrue($result);
	}	

		
}