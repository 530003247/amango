<?php
/**
*导出到CSV文件
*/
function export_csv($data = '', $filename = '') {
	$filename = empty ( $filename ) ? date ( 'YmdHis' ) . ".csv" : $filename . ".csv";
	header ( "Content-type:text/csv" );
	header ( "Content-Disposition:attachment;filename=" . $filename );
	header ( 'Cache-Control:must-revalidate,post-check=0,pre-check=0' );
	header ( 'Expires:0' );
	header ( 'Pragma:public' );
	echo array_to_string ( $data );
}
/**
 * *导出数据转换
 * 
 * @param
 *        	$result
 *        	
 */
function array_to_string($result) {
	$data = '';
	foreach ( $result as $v ) {
		$line = '';
		foreach ( $v as $vo ) {
			$line .= i ( $vo ) . ',';
		}
		$line = rtrim ( $line, ',' );
		
		$data .= $line . "\n";
	}
	
	return $data;
}

/**
 * *编码转换
 * 
 * @param <type> $strInput        	
 * @return <type>
 *
 */
function i($strInput) {
	if (is_string ( $strInput )) {
		// if(strstr($strInput, '"'))
		$strInput = str_replace ( ',', '，', $strInput );
		// else
		// $strInput = '"'.$strInput.'"';
	}
	return iconv ( 'utf-8', 'gbk//IGNORE', $strInput ); // 页面编码为utf-8时使用，否则导出的中文为乱码
}

?>
