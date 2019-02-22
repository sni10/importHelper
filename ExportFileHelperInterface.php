<?php
/**
 * Created by PhpStorm.
 * User: sni10
 * Date: 22.02.19
 * Time: 16:20
 */

namespace Helper\Export;

interface ExportFileHelperInterface
{
	/**
	 * Read works files list will be packed to archive
	 * @return array
	 */
	public function getFiles();

	/**
	 * Set works files list will be packed to archive
	 * @param null $file
	 * @return $this Object
	 */
	public function setFiles($file = null);

	/**
	 * Set file content to $fileName
	 * @param $fileName
	 * @param $data
	 * @return bool|int
	 */
	public function set($fileName, $data);

	/**
	 * Get file content from $fileName
	 * @param $fileName
	 * @return bool|string
	 */
	public function get($fileName);

	/**
	 * Physically delete file from storage and list
	 * @param $fileName
	 * @return bool
	 */
	public function delFile($fileName);

	/**
	 * Physically empty`s work directory
	 * @param null $filePath
	 */
	public function clearStorage($filePath = null );

	/**
	 * @param $fileName
	 * @return string
	 */
	public function getFilePath($fileName);

	/**
	 * @param $dir
	 * @param array $results
	 * @return array
	 */
	public function getDirContents($dir, &$results = []);

	/**
	 * @return bool|string
	 */
	public function getWorkDir();

	/**
	 * @param $fullPath
	 * @return bool|string
	 */
	public function checkDir($fullPath);

	/**
	 * @param $fullPath
	 */
	public function createDir($fullPath);

	/**
	 * @param $data
	 * @param $fileName
	 * @param string $mode
	 * @return $this
	 */
	public function makeCsv($data , $fileName, $mode = 'w+' );

	/**
	 * @param \DateTime $dateFrom
	 * @param \DateTime $dateTo
	 * @return $this
	 * @throws \Exception
	 */
	public function makeArchive(\DateTime $dateFrom, \DateTime $dateTo );

	/**
	 * @param null $filename
	 * @throws \Exception
	 * @return void
	 */
	public function downloadFile($filename = null);

}