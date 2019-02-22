<?php
/**
 * Created by PhpStorm.
 * User: sni10
 * Date: 31.01.19
 * Time: 15:20
 */

namespace Helper\Export;


class ExportReturnArticlesFileHelper extends ExportFileHelper
{

	/**
	 * @param \DateTimeInterface $dateFrom
	 * @param \DateTimeInterface $dateTo
	 * @param null $offset
	 * @return $this
	 * @throws \FuelException
	 */
	public function prepareExportReturnArticles(\DateTimeInterface $dateFrom, \DateTimeInterface $dateTo, $offset = null)
	{
		set_time_limit(0);
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');

		$limit = 500000;


		// get data by chunks
		$result = self::exportReturnArticles($dateFrom, $dateTo, $limit, $offset);

		if (empty($result)) {
			return $this;
		}

		$headers = [
			"Column Name 1",
			"Column Name 2",
			"можно набить руками а можно взять из ключей raw"
		];

		$this->chunkCounter = 0;

		$fileName =  intval($offset) . '_' . (intval($offset) + $limit) .'_' . $dateFrom->format('Y-m-d_00:00:00') .'_' . $dateTo->format('Y-m-d_23:59:59') . '.csv';


		// накидываем результат в файл, с периодическими вставками хедеров каждые 500 строк файла. Для удобство чтения "из коробки" если в файле млн строк
 		while ( ( $this->chunkCounter * $this->chunkLimit ) <= count( $result ) ):

			$resultChunk = array_slice( $result,$this->chunkCounter * $this->chunkLimit , $this->chunkLimit );

			array_unshift( $resultChunk, $headers );


			// никто не мешает заменить генерацию csv на xlsx используя любимую библиотеку
			$this->makeCsv( $resultChunk , $fileName, 'a+' );

			$this->chunkCounter++;

		endwhile;

		unset($result);

		// обновляем лимит/офсет и рекурсим
		$this->prepareExportReturnArticles($dateFrom, $dateTo, $limit+$offset);

		return $this;

	}


	/**
	 * @param \DateTime $dateFrom
	 * @param \DateTime $dateTo
	 * @param int|null $offset
	 * @return \Database_MySQLi_Result
	 */
	public static function exportReturnArticles(\DateTimeInterface $dateFrom, \DateTimeInterface $dateTo, int $limit = 1000, int $offset = null)
	{

		$sql = "SELECT *
                    FROM db.table as t
                    where o.Created BETWEEN '{$dateFrom->format('Y-m-d 00:00:00')}' and '{$dateTo->format('Y-m-d 23:59:59')}'
                LIMIT {$limit} {$offset}
            ";

		$result = \DB::query($sql)->execute()->as_array();


		return $result;
	}

}