<?php

require_once( '/Applications/MAMP/htdocs/praticien/wp-content/plugins/new-arret-plugin/simple_html_dom.php');

class FunctionTest extends PHPUnit_Framework_TestCase
{
	protected $grab;
	
	protected $arrange;
	
	protected $database;
	
	// urls	
	protected $urlRoot;
	
	protected $urlArret;
			
	public function __construct()
	{
		$this->grab     = new Grab;
		
		$this->arrange  = new Arrange;
		
		$this->database = new Database(true);	
		
		// urls
		
		$this->urlRoot  = 'http://relevancy.bger.ch';
	
		$this->urlArret = 'http://relevancy.bger.ch/php/aza/http/index.php?lang=fr&zoom=&type=show_document&highlight_docid=aza%3A%2F%2F';	
	}
	
	protected function setUp()
    {
    		
		// delete test table before test
		$this->database->deleteTable();
		
    }
	
    public function testGetTitleOFPage()
    {
    	
    	$url    = 'http://relevancy.bger.ch/php/aza/http/index.php?lang=fr&zoom=&type=show_document&highlight_docid=aza%3A%2F%2F12-02-2014-5A_816-2013';
    	
    	$html   = $this->grab->getPage($url);
    	$actual = $this->grab->getTitle($html);
    	
    	$expect = '5A_816/2013 (12.02.2014)';
    	
    	$this->assertEquals($actual,$expect);
    }
    
	public function testArrangeArrayFromTable(){
		
		$array = array(
			array(123 , 'new' , '', 'hey6'),
			array('' , '' , '', 'other2'),
			array(345 , 'new' , '', 'hey6'),
			array('' , '' , '', 'other3')
		);
		
		$expect = array(
			array(123,'new','hey6','other2'),
			array(345,'new','hey6','other3')
		);
		
		$actual = $this->arrange->arrangeArray($array);
		
		$this->assertEquals($actual,$expect);
		
	}
	
	public function testIsArretAPublication(){
		
		$arret  = 'Unterrichtswesen und Berufsausbildung *';
		
		$actual = $this->arrange->isPublication($arret);
		
		$this->assertEquals($actual,'1');		
		
	}

	public function testIsNotArretAPublication(){
		
		$arret  = 'Unterrichtswesen und Berufsausbildung';
		
		$actual = $this->arrange->isPublication($arret);
		
		$this->assertEquals($actual,'0');		
		
	}
	
	public function testFormatDateFromString(){
				
		$arret  = '140407';
		
		$actual = $this->arrange->formatDateFromString($arret);
		
		$expect = '2014-04-07';
		
		$this->assertEquals($actual , $expect);

	}
	
	public function testUniqueArray(){
	
		$array  = array( 'Strafprozess' , 'Strafprozess' , 'Straftaten' , 'Straftaten');
		
		$expect = array( 'Strafprozess' , 'Straftaten' );
		
		$actual = $this->arrange->uniqueArray($array);
		
		$this->assertEquals($actual,$expect);
		
	}

	public function testCleanStringSimple(){
	
		$string = 'Unterrichtswesen und Berufsausbildung *';
		
		$expect = 'Unterrichtswesen und Berufsausbildung';
				
		$actual = $this->database->cleanString($string);
		
		$this->assertEquals($actual,$expect);
	
	}

	public function testCleanStringDatabase(){
	
		$string = 'Unterrichtswesen und Berufsausbildung (en general) *';
		
		$expect = 'Unterrichtswesen und Berufsausbildung (general)';
				
		$actual = $this->database->cleanString($string,true);
		
		$this->assertEquals($actual,$expect);
	
	}

	public function testPercentageSimilarString(){
	
		$string  = 'Régime allocations et pertes de gain';
		
		$similar = 'Régime allocations et pertes de gains';
				
		$actual = $this->database->percent($similar,$string);
		
		$this->assertTrue($actual);
	
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
				
		$result = $this->database->arrangeArret($arret);
		
		$actual = $result['arrets'];
		
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
	
	public function testFlattenArray(){

		$subcategories = array(
			array('Strafrecht' , 'Droits politique'),
			array('Droit pénal administratif' , 'Registre'),
			array('Droits réels')
		); 
		
		$expect = array('Strafrecht' , 'Droits politique' , 'Droit pénal administratif' , 'Registre' , 'Droits réels');

		$actual = $this->database->flattenArray($subcategories);
		
		$this->assertEquals($expect,$actual);	
				
	}
	
	public function testUpdateTextArret(){
		
		$links  = array( 'dated_nouveaute'  => '2014-03-24', 'numero_nouveaute' => '2C_692/2013' );
		
		$expect = $this->urlArret.'24-03-2014-2C_692-2013'; 
		
		$actual = $this->database->formatArretUrl($links);
		
		$this->assertEquals($expect,$actual);	
		
	}
	
	public function testPrepareSearchForQuery(){
		
		$search = 'sicherheit, "autre mot ici", "tribunal"';
		
		$expect = array('sicherheit','autre mot ici','tribunal');

	  	$actual = $this->database->prepareSearch($search);
	  	
		$this->assertEquals($expect,$actual);		
	
	}
	
	public function testLastDayInDbIsToday(){
	
		$result = $this->database->isToday(date('Y-m-d'));
		
		$this->assertTrue($result);
		
	}

		
}