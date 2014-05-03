<?php

class UserTest extends PHPUnit_Framework_TestCase
{	
	protected $user;
			
	public function __construct()
	{
		$this->user = new User(true);		
	}	
	
	public function testGetAllUserAbosPublicationCategory(){
		
		$expect = array(
			1 => array(204 => 1),
			3 => array(199 => 1)
		);
				
		$actual = $this->user->getAllUserAbosPublicationCategory();
		
		$this->assertEquals($expect,$actual);			
	}	
	
	public function testGetAllUserAbos(){
		
		$expect = array( 
			1 => array(
				204 => array( 'keywords' => array('Tribunal Fédéral'),'ispub' => 1),
				180 => array( 'keywords' => '', 'ispub' => 0),
				192 => array( 'keywords' => '', 'ispub' => 0),
				222 => array( 'keywords' => '', 'ispub' => 0),
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
				
		$actual = $this->user->getUserAbos('all');
		
		$this->assertEquals($expect,$actual);			
	}
				
}