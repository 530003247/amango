<?php
/**
 +------------------------------------------------------------------------------
 * 功能升级类
 +------------------------------------------------------------------------------
 */
class Update { // 类定义开始
               
	// 下载到本地的目录
	public $downloadPath;
	public $addon_name = '';
	
	/**
	 * +----------------------------------------------------------
	 * 架构函数
	 * +----------------------------------------------------------
	 *
	 * @access public
	 *         +----------------------------------------------------------
	 */
	public function __construct($downloadPath = '') {
		if (! empty ( $downloadPath )) {
			$this->downloadPath = $downloadPath;
		} else {
			$this->downloadPath = DATA_PATH . 'download/';
		}
	}
	function downloadFile($packageURL) {
		if (! is_dir ( $this->downloadPath ))
			$res = mkdir ( $this->downloadPath, 0777, true );
		
		$file = fopen ( $packageURL, "rb" );
		if (! $file) {
			return 0;
		}
		
		$newfname = $this->downloadPath . basename ( $packageURL );
		$newf = fopen ( $newfname, "wb" );
		if (! $newf) {
			return - 1;
		}
		
		while ( ! feof ( $file ) ) {
			$data = fread ( $file, 1024 * 8 ); // 默认获取8K
			fwrite ( $newf, $data, 1024 * 8 );
			ob_flush ();
			flush ();
		}
		fclose ( $file );
		fclose ( $newf );
		
		return 1;
	}
	function unzipPackage($packageName, $targetDir = '', $cleanFile = true) {
		if (empty ( $targetDir )) {
			$targetDir = $this->downloadPath . 'unzip';
		}
		if ($cleanFile) {
			$this->rmdirr ( $targetDir );
		}
		$package = $this->downloadPath . $packageName;
		
		Vendor ( 'pclzip' );
		$archive = new PclZip ( $package );
		$res = $archive->extract ( PCLZIP_OPT_PATH, $targetDir, PCLZIP_OPT_REPLACE_NEWER );
		
		if ($res) {
			return 1;
		} else {
			return $archive->errorInfo ( true );
		}
	}
	/**
	 * 压缩文件成zip格式
	 * package string|array 需要压缩的文件路径，多个则放到数组里
	 * zipFile string 压缩文件的存在路径
	 * zipName string 压缩文件名，不需要带后缀.zip
	 *
	 * @return void
	 */
	//
	function zipPackage($package, $zipDir, $zipName, $removePath = '') {
		if (! is_dir ( $zipDir ))
			@mkdir ( $zipDir, 0777, true );
		
		$zipName = $zipName . '.zip';
		if (empty ( $removePath )) {
			$removePath = SITE_PATH;
		}
		
		Vendor ( 'pclzip' );
		$archive = new PclZip ( $zipDir . '/' . $zipName );
		
		$res = $archive->create ( $package, PCLZIP_OPT_REMOVE_PATH, $removePath );
		if ($res) {
			return true;
		} else {
			return $archive->errorInfo ( true );
		}
	}
	
	// 递归覆盖代码文件
	function overWrittenFile($destination = '', $source = '', $res = array()) {
		if (empty ( $source ))
			$source = $this->downloadPath . 'unzip';
		if (empty ( $destination ))
			$destination = SITE_PATH;
		
		if (! is_dir ( $destination )) {
			@mkdir ( $destination, 0777, true );
		}
		
		$handle = dir ( $source );
		while ( $entry = $handle->read () ) {
			if (($entry != ".") && ($entry != "..")) {
				$file = $source . "/" . $entry;
				if (is_dir ( $file )) {
					empty ( $this->addon_name ) && $this->addon_name = $entry;
					$res = $this->overWrittenFile ( $destination . "/" . $entry, $file, $res );
				} else {
					$result = copy ( $file, $destination . "/" . $entry );
					if (! $result) {
						$res ['error'] [] = $file;
					}
				}
			}
		}
		
		return $res;
	}
	
	// 删除多层目录
	function rmdirr($dirname) {
		if (! file_exists ( $dirname )) {
			return false;
		}
		if (is_file ( $dirname ) || is_link ( $dirname )) {
			return unlink ( $dirname );
		}
		$dir = dir ( $dirname );
		if ($dir) {
			while ( false !== $entry = $dir->read () ) {
				if ($entry == '.' || $entry == '..') {
					continue;
				}
				$this->rmdirr ( $dirname . DIRECTORY_SEPARATOR . $entry );
			}
		}
		$dir->close ();
		return rmdir ( $dirname );
	}
} // 类定义结束
?>