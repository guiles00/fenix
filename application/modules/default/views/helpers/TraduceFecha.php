<?php
class My_View_Helper_TraduceFecha extends Zend_View_Helper_Abstract {


	public function traduceFecha($fecha){

		if(empty($fecha)) return false;
		$date = new DateTime($fecha);
		return $date->format('d/m/Y');

	}


}



