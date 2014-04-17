<?php

class Log{

	protected $file;
	
	public function __construct() {
	
		$today = date('Y-m-d');
		
		$filename = plugin_dir_path(  dirname(__FILE__)  ) . 'logs/log_'.$today.'.txt';
										
		$fh = fopen($filename, 'w');
				
		$this->file = $filename;
						
	}
	
	public function write($message){
	
		// Open the file to get existing content
		$current = file_get_contents($this->file);
		// Append a new person to the file
		$current .= $message."\n";
		// Write the contents back to the file
		file_put_contents($this->file, $current);

	}
}