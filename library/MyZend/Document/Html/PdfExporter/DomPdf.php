<?php
require_once 'MyZend/Document/Html/PdfExporter/Interface.php';

class MyZend_Document_Html_PdfExporter_DomPdf implements MyZend_Document_Html_PdfExporter_Interface
{
	public function streamAsPdf($html,$name)
	{
			$dompdf = new DOMPDF ();
			$dompdf->set_protocol ( 'http://' );
			$dompdf->set_host ( $_SERVER['HTTP_HOST'] );
			$dompdf->load_html ( $html );
			$dompdf->render ();			
			$dompdf->stream ( $name );		
	}
}