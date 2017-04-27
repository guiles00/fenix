<?php
class My_View_Helper_ObjetoToSelect extends Zend_View_Helper_Abstract {
    public $view;

	public function objetoToSelect($idSelect,$valueSelect, $objetoSource, 		$propertyKey, $propertyValue,  $firstOption=null){
		$arr=array();
		if ($firstOption!==null) $arr[0]=$firstOption;
		foreach($objetoSource as $item){
			$arr[$item->$propertyKey]=$item->$propertyValue;
		}
		return $this->view->formSelect($idSelect,  $valueSelect , null, $arr) ;
	}		

	
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
	
}