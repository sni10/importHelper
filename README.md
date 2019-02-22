# importHelper

Вся работа с файлом инкапсулирована в одном обьекте. 

В констуктор необходимо отправить часть пути для конкатенации к пути вашего хранилища.

А так же (опционально) часть имени файла с которого будет начинаться имя архива.


1) Создаем и конфигурируем хелпер
2) ```clearStorage()``` - Очищаем хранилище от файлов прошлой выборки 
3) ```prepareExportReturnArticles( $dateFrom, $dateTo )``` - Иниируем получение данных и нарезка их на файлы используя чанки 
4) ```makeArchive( $dateFrom, $dateTo )``` - Упаковка результата в архив ( use default http://php.net/manual/en/book.zip.php )
5) ```downloadFile()``` - Возврат содержимого архива сразу в клиент, без генерации ссылки для скачивания

Делал без ссылки из секьюрных соображений. Нечего юзеру вообще пути видеть.


```php
    $storage = new ExportReturnArticlesFileHelper('export/return_articles', 'return_articles_');
    $storage->clearStorage();
    $storage->prepareExportReturnArticles( $dateFrom, $dateTo );
    $storage->makeArchive( $dateFrom, $dateTo );
    $storage->downloadFile();
```