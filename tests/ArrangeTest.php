<?php

class ArrangeTest extends PHPUnit_Framework_TestCase
{	
	protected $arrange;
			
	public function __construct()
	{		
		$this->arrange  = new Arrange;
	}
	
	protected function setUp()
    {		
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
		
}