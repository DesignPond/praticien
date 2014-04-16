<?php

class DateTest extends PHPUnit_Framework_TestCase
{
	
	protected $dates;

			
	public function __construct()
	{

		$this->dates = new Dates;
		
	}	

	public function testLastDayInDbIsToday(){
	
		$result = $this->dates->isToday(date('Y-m-d'));
		
		$this->assertTrue($result);
		
	}
		
}