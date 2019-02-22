<?php
/**
 * Created by PhpStorm.
 * User: sni10
 * Date: 31.01.19
 * Time: 12:53
 */

namespace Helper\Export;

class ExportFileHelper implements ExportFileHelperInterface
{

	/*
	 * test, dev, prod
	 * */
	const SITE_ENV = 'test';

	public $chunkLimit = 500;
	public $archiveNameFirstPart = 'download_archive_';
	public $chunkCounter = 0;
	public $zip = null;
	public $files = [];
	public $path = '';
	public $storage = '';
	public $baseFolder = '';

	/**
	 * FileHelper constructor. Init required path`s
	 * @param $folder string required
	 * @param $archiveNameFirstPart
	 * @throws \Exception
	 */
	public function __construct( string $folder, string $archiveNameFirstPart = null)
	{
		if ( empty($folder) ) throw new \Exception('Path can not be empty');

		$this->path = '/var/www/your_project_storage_path';
		$this->storage = $folder;

		$folder = trim($folder, DS);

		if(SITE_ENV){
			$folder = $folder . DS . SITE_ENV;
		}

		$this->baseFolder = $folder;

		if ( $archiveNameFirstPart ) $this->archiveNameFirstPart = $archiveNameFirstPart;

	}

	/**
	 * @return array
	 */
	public function getFiles()
	{
		return $this->files;
	}

	/**
	 * @param null $file
	 * @return $this
	 */
	public function setFiles($file = null)
	{
		if ( $file ) {
			!in_array( $file, $this->files ) && array_push($this->files , $file );
		}

		return $this;
	}

	/**
	 * @param $fileName
	 * @param $data
	 * @return bool|int
	 */
	public function set($fileName, $data)
	{
		$dirName = $this->getWorkDir();
		if ($dirName) {
			$file = $dirName . DS . $fileName;
			return file_put_contents($file, $data);
		}

		return false;
	}

	/**
	 * @param $fileName
	 * @return bool|string
	 */
	public function get($fileName)
	{
		$dirName = $this->getWorkDir();
		$filePath = realpath($dirName . DS . $fileName);
		if ($filePath) {
			$data = file_get_contents($filePath);
			return $data;
		};

		return false;
	}

	/**
	 * @param $fileName
	 * @return bool
	 */
	public function delFile($fileName)
	{
		$dirName = $this->getWorkDir();
		$filePath = realpath($dirName . DS . $fileName);
		if ($filePath) {
			return unlink($filePath);
		};

		if ( in_array( $fileName, $this->files ) ) unset( $this->files[ array_search( $fileName, $this->files) ] );

		return false;
	}

	/**
	 * @param null $filePath
	 */
	public function clearStorage($filePath = null )
	{
		$filePath = $filePath === null ? $this->getWorkDir() : $filePath;
		foreach (  $this->getDirContents($filePath) as $item ) {
			if ( is_dir($item) ) {
				rmdir( $item );
			} elseif( is_file($item) ) {
				unlink($item);
			}
		}
	}

	/**
	 * @param $fileName
	 * @return string
	 */
	public function getFilePath($fileName){
		$dirPath = $this->getWorkDir();
		$filePath = $dirPath . DS . $fileName;

		return $filePath;
	}

	/**
	 * @param $dir
	 * @param array $results
	 * @return array
	 */
	public function getDirContents($dir, &$results = []){
		$files = scandir($dir);

		foreach($files as $key => $value){
			$path = realpath($dir.DS.$value);
			if(!is_dir($path)) {
				$results[] = $path;
			} else if($value != "." && $value != "..") {
				$this->getDirContents($path, $results);
				$results[] = $path;
			}
		}

		return $results;
	}

	/**
	 * @return bool|string
	 */
	public function getWorkDir()
	{
		$fullPath = $this->path . $this->baseFolder ;

		return $this->checkDir($fullPath);
	}

	/**
	 * @param $fullPath
	 * @return bool|string
	 */
	public function checkDir($fullPath)
	{
		if (!realpath($fullPath)) {
			$this->createDir($fullPath);
		}

		return realpath($fullPath);
	}

	/**
	 * @param $fullPath
	 * @return bool $done
	 */
	public function createDir($fullPath)
	{
		if (!is_dir($fullPath)) {
			return mkdir($fullPath, 0755, true);
		}

		return true;
	}

	/**
	 * @param $data
	 * @param $fileName
	 * @param string $mode
	 * @return $this
	 */
	public function makeCsv($data , $fileName, $mode = 'w+' )
	{
		$dirPath = $this->getWorkDir();
		$filePath = $dirPath . DS . $fileName;

		$fp = fopen($filePath, $mode);
		if ($data) {
			foreach ($data as $fields) {
				fputcsv($fp, $fields);
			}
		}
		fclose($fp);
		$this->setFiles($filePath);
		return $this;
	}

	/**
	 * @param \DateTime $dateFrom
	 * @param \DateTime $dateTo
	 * @return $this
	 * @throws \Exception
	 */
	public function makeArchive(\DateTime $dateFrom, \DateTime $dateTo )
	{
		$dirPath = $this->getWorkDir();
		$zipFileName = $this->archiveNameFirstPart . $dateFrom->format('Y-m-d_00:00:00') . '_' . $dateTo->format('Y-m-d_23:59:59') . '.zip';
		$zipFilePath = $dirPath . DS . $zipFileName ;
		$zip = new \ZipArchive;
		$readFiles = File::read_dir( $this->getWorkDir() );

		$createZip = $zip->open( $zipFilePath , \ZipArchive::CREATE );

		if ( $createZip ) {
			foreach ( $readFiles as $file ) {
				if ( is_file( $this->getFilePath( $file )  )) {
					$zip->addFile( $this->getFilePath( $file ), $file );
				} else {
					throw new \Exception(sprintf('Can not get access to file or file does not exist. File: "%s"', $file));
				}
			}

			$zip->close();
		}

		$this->zip = $zipFileName;

		return $this;
	}

	/**
	 *
	 * @param null $filename
	 * @throws \Exception
	 * @return void
	 */
	public function downloadFile($filename = null)
	{
		$filePath = $this->getWorkDir() . DS;
		$filename = $filename === null ? $this->zip : $filename;
		$fullFilePath = $filePath . $filename;
		if ( !is_file($fullFilePath) || !is_readable($fullFilePath) )
			throw new \Exception(sprintf('Can not get access to file or file does not exist. File: "%s"', $fullFilePath));

		$mime = mime_content_type($fullFilePath);

		header('Content-Type: '.$mime);
		header('Content-disposition: attachment; filename='.$filename);
		header('Content-Length: ' . filesize($fullFilePath));
		readfile($filePath . $filename );
		exit;
	}

}

