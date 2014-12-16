<?php
require_once dirname(__FILE__) . '/PHPExcel.php';

function export_csv($data = '', $filename = '',$sheet = false) {
    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
	// Set document properties
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
								 ->setLastModifiedBy("Maarten Balliauw")
								 ->setTitle("Office 2007 XLSX Test Document")
								 ->setSubject("Office 2007 XLSX Test Document")
								 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Test result file");					 
	$filename = empty ( $filename ) ? date ( 'YmdHis' ) : $filename ;
	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename='.$filename.'.xls');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');
	
	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0
	$Line = array(
	'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'
	);
	if(!$sheet){
		foreach ( $data as $k=>$v ) {
		    $u=$k+1;
		    $s = count($v);
		    for($i=0;$i<$s;$i++){
		    	    $n = $Line[$i].$u;
		    	    $va = array_values($v);
		    		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue($n,$va[$i]);
		    }  
	  } 

		/*// Miscellaneous glyphs, UTF-8
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A4', 'Miscellaneous glyphs')
		            ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');
		*/
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Simple');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
	}else {
	  	$f=0;
	  	foreach ( $data as $t=>$u )
	  	 {
	  		foreach ( $u as $k=>$v )
	  		{
			    $u=$k+1;
			    $s = count($v);
			    for($i=0;$i<$s;$i++){
			    	    $n = $Line[$i].$u;
			    	    $va = array_values($v);
			    		$objPHPExcel->setActiveSheetIndex($f)
			            ->setCellValue($n,$va[$i]);
			            if($data[$t][$k][1]!=$data[$t][$k-1][1]&&$k!=0){
						$objPHPExcel->getActiveSheet()->getStyle($n)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
						$objPHPExcel->getActiveSheet()->getStyle($n)->getFill()->getStartColor()->setARGB('FFFF00');
			            }
			    } 
	  		}
	  		
	  		
		/*// Miscellaneous glyphs, UTF-8
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A4', 'Miscellaneous glyphs')
		            ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');
		*/
		// Rename worksheet
		$objPHPExcel->createSheet();$objPHPExcel->getSheet($f)->setTitle($t);
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex($f);
	  	$f++;
	  	}
	  	$f=0;
	  }
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
}


