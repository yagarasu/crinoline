<?php

	class HomePresenter extends Presenter {
		
		public function main($args) {
			plg('CRLaces')->loadAndRender('templates/home.ltp');
		}

		public function about($args) {
			plg('CRLaces')->loadAndRender('templates/about.ltp');
		}

		public function build($args){
			print_r($args);
			$cid = $this->newId($args['appData']['className']);
			$dir = 'tmp/'.$cid;
			$zipfile = 'tmp/'.$cid.'.zip';
			mkdir($dir, 0644);
			mkdir($dir.'/recursive', 0644);
			file_put_contents($dir . '/lorem1.txt', "LEEEEEEEL");
			file_put_contents($dir . '/recursive/loremA.txt', "LAAAAAAAL");
			file_put_contents($dir . '/lorem2.txt', "LEEsdfgsdfgsfdgsdfgEEEEEL");
			$zip = new ZipArchive();
			$zip->open($zipfile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
			$files = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($dir),
				RecursiveIteratorIterator::LEAVES_ONLY
			);
			foreach ($files as $name=>$file) {
				if(!$file->isDir()) {
					$path = $file->getPathName();
					$rpath = substr($path, strlen($dir)+1);
					$zip->addFile($path, $rpath);
				}
			}
			$zip->close();
			$this->deleteDir($dir);
			echo json_encode(array(
				'status'	=> 'SUCCESS',
				'data'		=> array(
					'downloadLink'	=> 'download/?cid='.$cid
				)
			));
		}

		public function download($args) {
			$cid = $args['cid'];
			$zipfile = 'tmp/'.$cid.'.zip';
			header("Content-disposition: attachment; filename=".$args['appData']['className'].".zip");
			header("Content-type: application/zip");
			header('Content-Length: ' . filesize($zipfile));
			readfile($zipfile);
			unlink($zipfile);
			
		}

		private function newId($name) {
			$now = time();
			$cat = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$str = '';
			for ($i = 0; $i < 10; $i++) {
				$str .= $cat[rand(0, strlen($cat))];
			}
			return $name . '_' . $now . '_' . $str;
		}

		private function deleteDir($dir) {
			if(is_dir($dir)){
				$files = glob( $dir . '*', GLOB_MARK );
				foreach( $files as $file ) {
					$this->deleteDir( $file );      
				}
				rmdir( $dir );
			} elseif(is_file($dir)) {
				unlink( $dir );  
			}
		}
		
	}

?>