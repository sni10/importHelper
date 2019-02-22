# importHelper

```php
    $storage = new ExportReturnArticlesFileHelper('export/return_articles', 'return_articles_');
    $storage->clearStorage();
    $storage->prepareExportReturnArticles( $dateFrom, $dateTo );
    $storage->makeArchive( $dateFrom, $dateTo );
    $storage->downloadFile();
```