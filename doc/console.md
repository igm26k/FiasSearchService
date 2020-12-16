# Console

Описание консольных команд контейнера web

#### fias:downloadData
Загрузка данных с портала ФИАС.

`fias:downloadData --fileType=xml|dbf --dataType=delta|complete`

- Опции:

        --fileType - формат выгрузки (может быть `xml` или `dbf`)
        --dataType - тип выгрузки
                     delta - обновления
                     complete - полная
- Пример:
        
    ```shell script
    ./artisan fias:downloadData --fileType=dbf --dataType=delta
    ```
   В данном примере будут загружены обновления в формате DBF

Архив будет загружен в директорию `/var/www/html/storage/app/fias/data/{fileType}_{dataType}/` и сразу же разархивирован 
в ту же самую директорию.

#### fias:parseData
Парсер данных, загруженных с портала ФИАС.

`fias:parseData --fileType=xml|dbf --dataType=delta|complete`

- Опции:

        --fileType - формат выгрузки (может быть `xml` или `dbf`)
        --dataType - тип выгрузки
                     delta - обновления
                     complete - полная
- Пример:
        
    ```shell script
    ./artisan fias:downloadData --fileType=dbf --dataType=delta
    ```
   В данном примере будут прочитаны в алфавитном порядке файлы DBF, загруженные в примере fias:downloadData и вставлены 
   записи из них в соответствующие таблицы базы данных FIAS API.

#### fias:update
Обновление базы данных из дельта-выгрузки ФИАС. Приоритетный формат - DBF.
По факту является последовательным запуском команд `fias:downloadData` 
и `fias:parseData`.

- Пример:
        
    ```shell script
    ./artisan fias:update
    ```
   Будет скачана последняя дельта выгрузка с портала ФИАС 
   и разархивирована. Затем будет запущен парсер с обновлением 
   базы данных.
 
### Логирование
  
Каждый этап выполнения консольных команд логируется и пишется в файл `/var/www/htmlstorage/logs/laravel-{YYYY-MM-DD}.log`, 
где YYYY - год в 4-х значном формате, MM - номер месяца в 2-х значном формате, DD - день месяца в 2-х значном формате.
