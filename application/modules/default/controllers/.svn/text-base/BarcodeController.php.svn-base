<?php
class BarcodeController extends Zend_Controller_Action
{
    public function init()
    {
	/* Initialize action controller here */
	//$this->view->headLink()->setStylesheet('/css/layout.css');
	/*
	if ($this->getRequest()->getParam('controller')!='login') 
		$this->view->headScript()->appendFile('/js/com.iskitz.ajile.js?mvcoff');
	*/
	if($this->_request->isXmlHttpRequest() || ($this->getRequest()->getParam('ajax')==1)){
	    $this->_helper->layout->disableLayout();
	}  
    }

    public function draw39Action()
    {
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender();
    	$params=$this->_getAllParams();
    	$barcode=new MyZend_Barcode_Code39();
    	$img=$barcode->draw($params['code']);
    	header('Content-type: image/png');
        imagepng($img);
        imagedestroy($img);
    }
}