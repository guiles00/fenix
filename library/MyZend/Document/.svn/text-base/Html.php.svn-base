<?php

require_once 'HTMLPurifier.standalone.php';
define("DOMPDF_ENABLE_REMOTE", true);
require_once 'DomPdf/dompdf_config.inc.php';
require_once 'SimpleHtmlDom/simple_html_dom.php';
require_once 'tcpdf/tcpdf.php';

Class MyZend_Document_Html{
	private $html;	
	private $pdf_exporter;
    function __construct($html='')
    {
       $this->html=$html;
       $this->pdf_exporter=new MyZend_Document_Html_PdfExporter_DomPdf();
    } 
       
    public static function getInstance($html='')
    {
    	return new MyZend_Document_Html($html);
    }
    
    function setPdfExporter($pdf_exporter)
    {
    	if ($pdf_exporter instanceof MyZend_Document_Html_PdfExporter_Interface)
    	{
    		$this->pdf_exporter=$pdf_exporter;
    	}
    	return $this;
    }
    
    function purify()
    {
    	$purifier = new HTMLPurifier ();
		$this->html = $purifier->purify ( $this->html );
    	return $this;	
    }
    
    function getPlainText()
    {    
    	$htmldom=new simple_html_dom();
		$htmldom->load($this->html);
		return $htmldom->plaintext;
    }
    
    function streamAsPdf($name='noname.pdf')
    {
    	$this->pdf_exporter->streamAsPdf($this->html, $name);
    }
    
    function getHtml()
    {
    	return $this->html;
    }
    
}