# FIAS API Installation

1. Клинируйте проект с репозитория FIAS API
    ```
    git clone https://stash.telecontact.ru/scm/telecontact/fias-api.git
    ```
2. Скопируйте файл `secretsTemplate.json` в корень проекта с новым именем `secrets.json`
    ```
    cp ./secretsTemplate.json ./secrets.json
    ```
3. Заполните все параметры подключения `MYSQL_*`
    
    Рекомендуется настроить подключение к тестовой БД на `tc-msk-fias0.telecontact.ru`,
    т.к. для работы базы необходимо не менее 70Гб свободного места на диске.
    
    Пример (ваши параметры могут отличаться):
    ```json5
    {
        "data": {
            "MYSQL_DB_NAME": "fias",
            "MYSQL_DATABASE": "fias",
            "MYSQL_DB_HOST": "mysql",
            "MYSQL_DB_PORT": "3306",
            "MYSQL_DB_USER": "root",
            "MYSQL_USER": "root",
            "MYSQL_DB_PASS": "root",
            "MYSQL_PASSWORD": "root"
        }
    }
    ```
4. Скопируйте файл `.env.example` в корень проекта с новым именем `.env`
    ```
    cp ./.env.example ./.env
    ```
5. Запустите сборку образов и контейнеров docker с дальнейшим запуском
    ```
    make build run
    ```
6. Далее необходимо сгенерировать `APP_KEY` и записать его в файл `secrets.json`
    
    6.1 Зайдите в контейнер `fias_web`
    ```
    ./scripts/bash fias_web
    ```
    6.2 Сгенерируйте ключ
    ```
    ./artisan key:generate --show
    ```
    6.3 Скопируйте сгенерированный ключ вместе с `base64:` и заполните `APP_KEY` в `secrets.json`
    
    Пример:
    ```
    {
        "data": {
            "APP_KEY": "base64:obr833EQeWe89iXe6F5NMTB5E/G7AIBwV3QcBni3S78="
        }
    }
    ```

## Использование локальной БД
При использовании локальной БД, необходимо выполнить 
несколько init миграций в контейнере `fias_web`
и запустить парсер загруженной выгрузки ФИАС:

**ВНИМАНИЕ!** Для последующих действий необходимо минимум
100Гб свободного места на диске. 

1. Раскоментируйте сервис `mysql` в `./docker-compose.dev.yml`
2. Запустите сервис командой
    ```
    docker-compose up mysql
    ```
3. Зайдите в контейнер `fias_mysql` и создайте новую БД `fias`.

    Выполните следующие команды по очереди:
    ```
    ./scripts/bash fias_mysql
    mysql -h localhost -uroot -proot
    CREATE DATABASE fias CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
    exit;
    exit
    ```
4. Зайдите в контейнер `fias_web` и выполните init-миграцию.

    Выполните следующие команды по очереди:
    ```
    ./scripts/bash fias_web
    ./artisan migrate
    ```
5. Загрузите полную выгрузку в формате DBF с портала ФИАС
    ```
    ./artisan fias:downloadData --fileType=dbf --dataType=complete
    ```
6. Запустите парсер
    ```
    ./artisan fias:parseData --fileType=dbf --dataType=complete
    ```
