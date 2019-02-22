<?php
/**
 * Created by PhpStorm.
 * User: sni10
 * Date: 22.02.19
 * Time: 16:27
 */

namespace Helper\Export;


class Controller
{

	public function actionExportFile() : void {

		$filter = \Input::post('Filter', []);

		if (isset($filter['fromDate'], $filter['toDate'])) {

			$dateFrom = \DateTime::createFromFormat('Y-m-d', $filter['fromDate']);
			$dateTo = \DateTime::createFromFormat('Y-m-d', $filter['toDate']);

			// создаем сервис
			$storage = new ExportReturnArticlesFileHelper('export/return_articles', 'return_articles_');
			// очищаем сторейдж от файлов предыдущей выборки
			$storage->clearStorage();
			// непосредственное выполнение запроса и нарезка на файлы пока не иссякнут данные
			$storage->prepareExportReturnArticles( $dateFrom, $dateTo );
			// упаковка всего что нагенерилось в архив ( если файлов больше одного и кол-во ограничено лишь системными ресурсами )
			$storage->makeArchive( $dateFrom, $dateTo );
			// выдача файла в клиент
			$storage->downloadFile();
		}

	}

}