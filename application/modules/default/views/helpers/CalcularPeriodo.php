<?php
class My_View_Helper_CalcularPeriodo extends Zend_View_Helper_Abstract {
	

	public function calcularPeriodo($fecha_desde,$periodo){

		if(empty($fecha_desde)) return false;
		$date = new DateTime($fecha_desde);
		
		switch ($periodo) {
			case '1':
				
			$date->add(new DateInterval('P1M'));
			$fecha_hasta =  $date->format('Y-m-d') . "\n";
			break;
			case '7':
				
			$date->add(new DateInterval('P2M'));
			$fecha_hasta =  $date->format('Y-m-d') . "\n";
			break;
			case '2':
				
			$date->add(new DateInterval('P3M'));
			$fecha_hasta =  $date->format('Y-m-d') . "\n";
			break;
			
			case '3':
				
			$date->add(new DateInterval('P4M'));
			$fecha_hasta =  $date->format('Y-m-d') . "\n";
			break;
			
			case '4':
				
			$date->add(new DateInterval('P6M'));
			$fecha_hasta =  $date->format('Y-m-d') . "\n";
			break;
			
			case '5':
				
			$date->add(new DateInterval('P1Y'));
			$fecha_hasta =  $date->format('Y-m-d') . "\n";
			break;
			
			
			case '6':
				
			$date->add(new DateInterval('P2Y'));
			//$fecha_hasta =  $date->format('Y-m-d') . "\n";
			$fecha_hasta =  $date->format('Y-m-d') . "\n";
			break;
			default:
			$fecha_hasta = null	;
			break;
		}
		

		return $fecha_hasta;
	}


}


