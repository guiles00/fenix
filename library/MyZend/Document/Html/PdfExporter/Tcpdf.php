<?php
require_once 'MyZend/Document/Html/PdfExporter/Interface.php';

class MyZend_Document_Html_PdfExporter_Tcpdf implements MyZend_Document_Html_PdfExporter_Interface
{
	public function streamAsPdf($html,$name)
	{
			// @todo con libreria tcpdf	
	}
}