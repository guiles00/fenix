<?php

require_once 'xlsstream/excel.php';

Class MyZend_Document_Xls{
	private $assoc;	

	function __construct($assoc=array())
    {
       $this->assoc=$assoc;
    } 
       
    public static function getInstance($assoc=array())
    {
    	return new MyZend_Document_Xls($assoc);
    }
    
    function streamAsXls($name='noname.xls')
    {
    	$tmpfname = tempnam(sys_get_temp_dir(), "xlsstream");
		$export_file = "xlsfile:/".$tmpfname;
		$fp = fopen($export_file, "wb");
		if (!is_resource($fp))
		{
		    die("Cannot open $export_file");
		}
		
		// typically this will be generated/read from a database table
		$assoc = $this->assoc;
		/*array(
		    array("Sales Person" => "Sam Jackson", "Q1" => "$3255", "Q2" => "$3167", "Q3" => 3245, "Q4" => 3943),
		    array("Sales Person" => "Jim Brown", "Q1" => "$2580", "Q2" => "$2677", "Q3" => 3225, "Q4" => 3410),
		    array("Sales Person" => "John Hancock", "Q1" => "$9367", "Q2" => "$9875", "Q3" => 9544, "Q4" => 10255),
		);*/
		
		fwrite($fp, serialize($assoc));
		fclose($fp);
		
		header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
		header ("Cache-Control: no-cache, must-revalidate");
		header ("Pragma: no-cache");
		header ("Content-type: application/x-msexcel");
		header ("Content-Disposition: attachment; filename=\"" . $name . "\"" );
		header ("Content-Description: PHP/INTERBASE Generated Data" );
		readfile($export_file);
		unlink($fp);
    }
    
    function getAssoc()
    {
    	return $this->assoc;
    }
    
}