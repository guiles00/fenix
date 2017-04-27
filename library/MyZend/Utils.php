<?php
class MyZend_Utils {
	
	// esta funcion desprecia el valor de los Ms del string con formato doctrine timestamp
	// 	'Oct 12 2010 12:01:00:050AM' se convierte en 'Oct 12 2010 12:01:00', luego se le aplica time.   
		 
	public static function doctrinetimestamptotime($time)
	{
		if ((substr($time,-1)=='M') && (substr($time,-6,1)==':'))
		{
			$time = substr($time,0,-6).substr($time,-2);
		}
		$time = strtotime($time);
		return $time;
	}
	public static function ifnull($v,$t)
	{
		if (!isset($v) || ($v==null))
		{
			return $t;
		}else{
			return $v;
		}
	}
}
