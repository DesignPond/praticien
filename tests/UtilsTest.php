<?php

class UtilsTest extends PHPUnit_Framework_TestCase
{	
	protected $utils;
			
	public function __construct()
	{
		
		$this->utils    = new Utils;
		
	}
	
	protected function setUp()
    {
		
    }

	public function testCleanStringSimple(){
	
		$string = 'Unterrichtswesen und Berufsausbildung *';
		
		$expect = 'Unterrichtswesen und Berufsausbildung';
				
		$actual = $this->utils->cleanString($string);
		
		$this->assertEquals($actual,$expect);
	
	}

	public function testCleanStringDatabase(){
	
		$string = 'Unterrichtswesen und Berufsausbildung (en general) *';
		
		$expect = 'Unterrichtswesen und Berufsausbildung (general)';
				
		$actual = $this->utils->cleanString($string,true);
		
		$this->assertEquals($actual,$expect);
	
	}

	public function testPercentageSimilarString(){
	
		$string  = 'Régime allocations et pertes de gain';
		
		$similar = 'Régime allocations et pertes de gains';
				
		$actual = $this->utils->percent($similar,$string);
		
		$this->assertTrue($actual);
	
	}

	public function testFlattenArray(){

		$subcategories = array(
			array('Strafrecht' , 'Droits politique'),
			array('Droit pénal administratif' , 'Registre'),
			array('Droits réels')
		); 
		
		$expect = array('Strafrecht' , 'Droits politique' , 'Droit pénal administratif' , 'Registre' , 'Droits réels');

		$actual = $this->utils->flattenArray($subcategories);
		
		$this->assertEquals($expect,$actual);	
				
	}
			
}