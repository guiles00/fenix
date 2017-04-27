<?php
class My_View_Helper_ObjetoToArray extends Zend_View_Helper_Abstract {
    public $view;

	public function objetoToArray($objetoSource, $metodoKey, $metodoValue){
		$arr=array();
		foreach($objetoSource as $item){
			$arr[$item->$metodoKey()]=$item->$metodoValue();
		}
		//return $arr ;
		echo "STRING";
		return "STRING";
	}		

	public function objetoToArray2($objetoSource, $metodoKey, $metodoValue){
		$arr=array();
		foreach($objetoSource as $item){
			$arr[$item->$metodoKey] = $item->$metodoValue;
		}
		return $arr ;
	}
		
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
	
}