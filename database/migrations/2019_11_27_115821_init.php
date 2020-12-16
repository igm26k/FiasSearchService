<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Class Init
 */
class Init extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $results = [];
        $results[] = DB::statement("create table if not exists fias_actual_status
            (
                id        bigint unsigned auto_increment comment 'ID' primary key,
                actstatid int                     null comment 'Идентификатор статуса (ключ)',
                name      varchar(100) default '' null comment 'Наименование
            0 – Не актуальный
            1 – Актуальный (последняя запись по адресному объекту)
            ',
                constraint fias_actual_status_uniqueKey unique (actstatid)
            ) ENGINE = InnoDB comment 'Статус актуальности ФИАС'
              collate = utf8mb4_unicode_ci");

        $results[] = DB::statement("create table if not exists fias_address_object_type
            (
                id       bigint unsigned auto_increment comment 'ID'
                    primary key,
                level    int                    null comment 'Уровень адресного объекта',
                scname   varchar(10) default '' null comment 'Краткое наименование типа объекта',
                socrname varchar(50) default '' null comment 'Полное наименование типа объекта',
                kod_t_st varchar(4)  default '' null comment 'Ключевое поле',
                constraint fias_address_object_type_uniqueKey
                    unique (kod_t_st)
            ) ENGINE = InnoDB comment 'Тип адресного объекта'
              collate = utf8mb4_unicode_ci");

        $results[] = DB::statement("create or replace index index_aotype_level_scname
                on fias_address_object_type (level, scname)");

        $results[] = DB::statement("create table if not exists fias_center_status
            (
                id         bigint unsigned auto_increment comment 'ID'
                    primary key,
                centerstid int                     null comment 'Идентификатор статуса',
                name       varchar(100) default '' null comment 'Наименование',
                constraint fias_center_status_uniqueKey
                    unique (centerstid)
            ) ENGINE = InnoDB comment 'Статус центра'
              collate = utf8mb4_unicode_ci");

        $results[] = DB::statement("create table if not exists fias_current_status
            (
                id         bigint unsigned auto_increment comment 'ID'
                    primary key,
                curentstid int                     null comment 'Идентификатор статуса (ключ)',
                name       varchar(100) default '' null comment 'Наименование (0 - актуальный, 1-50, 2-98 – исторический (кроме 51), 51 - переподчиненный, 99 - несуществующий)',
                constraint fias_current_status_uniqueKey
                    unique (curentstid)
            ) ENGINE = InnoDB comment 'Статус актуальности КЛАДР 4.0'
              collate = utf8mb4_unicode_ci");

        $results[] = DB::statement("create table if not exists fias_estate_status
            (
                id        bigint unsigned auto_increment comment 'ID'
                    primary key,
                eststatid int                    null comment 'Признак владения',
                name      varchar(20) default '' null comment 'Наименование',
                shortname varchar(20) default '' null comment 'Краткое наименование',
                constraint fias_estate_status_uniqueKey
                    unique (eststatid)
            ) ENGINE = InnoDB comment 'Признак владения'
              collate = utf8mb4_unicode_ci");

        $results[] = DB::statement("create table if not exists fias_flat_type
            (
                id        bigint unsigned auto_increment comment 'ID'
                    primary key,
                fltypeid  int                    null comment 'Тип помещения',
                name      varchar(20) default '' null comment 'Наименование',
                shortname varchar(20) default '' null comment 'Краткое наименование',
                constraint fias_flat_type_uniqueKey
                    unique (fltypeid)
            ) ENGINE = InnoDB comment 'Тип помещения'
              collate = utf8mb4_unicode_ci");

        $results[] = DB::statement("create table if not exists fias_house
            (
                id         bigint unsigned auto_increment comment 'ID'
                    primary key,
                postalcode varchar(6)   default '' null comment 'Почтовый индекс',
                regioncode varchar(2)   default '' null comment 'Код региона',
                ifnsfl     varchar(4)   default '' null comment 'Код ИФНС ФЛ',
                terrifnsfl varchar(4)   default '' null comment 'Код территориального участка ИФНС ФЛ',
                ifnsul     varchar(4)   default '' null comment 'Код ИФНС ЮЛ',
                terrifnsul varchar(4)   default '' null comment 'Код территориального участка ИФНС ЮЛ',
                okato      varchar(11)  default '' null comment 'OKATO',
                oktmo      varchar(11)  default '' null comment 'OKTMO',
                updatedate date                    null comment 'Дата время внесения записи',
                housenum   varchar(20)  default '' null comment 'Номер дома',
                eststatus  int                     null comment 'Признак владения',
                buildnum   varchar(10)  default '' null comment 'Номер корпуса',
                strucnum   varchar(10)  default '' null comment 'Номер строения',
                strstatus  int                     null comment 'Признак строения',
                houseid    varchar(36)  default '' null comment 'Уникальный идентификатор записи дома',
                houseguid  varchar(36)  default '' null comment 'Глобальный уникальный идентификатор дома',
                aoguid     varchar(36)  default '' null comment 'Guid записи родительского объекта (улицы, города, населенного пункта и т.п.)',
                startdate  date                    null comment 'Начало действия записи',
                enddate    date                    null comment 'Окончание действия записи',
                statstatus int                     null comment 'Состояние дома',
                normdoc    varchar(36)  default '' null comment 'Внешний ключ на нормативный документ',
                counter    int                     null comment 'Счетчик записей домов для КЛАДР 4',
                cadnum     varchar(100) default '' null comment 'Кадастровый номер',
                divtype    int                     null comment 'Тип адресации:
            0 - не определено
            1 - муниципальный;
            2 - административно-территориальный',
                constraint fias_house_uniqueKey
                    unique (houseid)
            ) ENGINE = InnoDB comment 'Сведения по номерам домов улиц городов и населенных пунктов'
              collate = utf8mb4_unicode_ci");

        $results[] = DB::statement("create table if not exists fias_house_interval
            (
                id         bigint unsigned auto_increment comment 'ID'
                    primary key,
                postalcode varchar(6)  default '' null comment 'Почтовый индекс',
                ifnsfl     varchar(4)  default '' null comment 'Код ИФНС ФЛ',
                terrifnsfl varchar(4)  default '' null comment 'Код территориального участка ИФНС ФЛ',
                ifnsul     varchar(4)  default '' null comment 'Код ИФНС ЮЛ',
                terrifnsul varchar(4)  default '' null comment 'Код территориального участка ИФНС ЮЛ',
                okato      varchar(11) default '' null comment 'OKATO',
                oktmo      varchar(11) default '' null comment 'OKTMO',
                updatedate date                   null comment 'Дата  внесения записи',
                intstart   int                    null comment 'Значение начала интервала',
                intend     int                    null comment 'Значение окончания интервала',
                houseintid varchar(36) default '' null comment 'Идентификатор записи интервала домов',
                intguid    varchar(36) default '' null comment 'Глобальный уникальный идентификатор интервала домов',
                aoguid     varchar(36) default '' null comment 'Идентификатор объекта родительского объекта (улицы, города, населенного пункта и т.п.)',
                startdate  date                   null comment 'Начало действия записи',
                enddate    date                   null comment 'Окончание действия записи',
                intstatus  int                    null comment 'Статус интервала (обычный, четный, нечетный)',
                normdoc    varchar(36) default '' null comment 'Внешний ключ на нормативный документ',
                counter    int                    null comment 'Счетчик записей домов для КЛАДР 4',
                constraint fias_house_interval_uniqueKey
                    unique (houseintid)
            ) ENGINE = InnoDB comment 'Интервалы домов'
              collate = utf8mb4_unicode_ci");

        $results[] = DB::statement("create table if not exists fias_house_state_status
            (
                id        bigint unsigned auto_increment comment 'ID'
                    primary key,
                housestid int                    null comment 'Идентификатор статуса',
                name      varchar(60) default '' null comment 'Наименование',
                constraint fias_house_state_status_uniqueKey
                    unique (housestid)
            ) ENGINE = InnoDB comment 'Статус состояния домов '
              collate = utf8mb4_unicode_ci");

        $results[] = DB::statement("create table if not exists fias_interval_status
            (
                id         bigint unsigned auto_increment comment 'ID'
                    primary key,
                intvstatid int                    null comment 'Идентификатор статуса (обычный, четный, нечетный)',
                name       varchar(60) default '' null comment 'Наименование',
                constraint fias_interval_status_uniqueKey
                    unique (intvstatid)
            ) ENGINE = InnoDB comment 'Статус интервала домов'
              collate = utf8mb4_unicode_ci");

        $results[] = DB::statement("create table if not exists fias_landmark
            (
                id         bigint unsigned auto_increment comment 'ID'
                    primary key,
                location   varchar(500) default '' null comment 'Месторасположение ориентира',
                postalcode varchar(6)   default '' null comment 'Почтовый индекс',
                ifnsfl     varchar(4)   default '' null comment 'Код ИФНС ФЛ',
                terrifnsfl varchar(4)   default '' null comment 'Код территориального участка ИФНС ФЛ',
                ifnsul     varchar(4)   default '' null comment 'Код ИФНС ЮЛ',
                terrifnsul varchar(4)   default '' null comment 'Код территориального участка ИФНС ЮЛ',
                okato      varchar(11)  default '' null comment 'OKATO',
                oktmo      varchar(11)  default '' null comment 'OKTMO',
                updatedate date                    null comment 'Дата  внесения записи',
                landid     varchar(36)  default '' null comment 'Уникальный идентификатор записи ориентира',
                landguid   varchar(36)  default '' null comment 'Глобальный уникальный идентификатор ориентира',
                aoguid     varchar(36)  default '' null comment 'Уникальный идентификатор родительского объекта (улицы, города, населенного пункта и т.п.)',
                startdate  date                    null comment 'Начало действия записи',
                enddate    date                    null comment 'Окончание действия записи',
                normdoc    varchar(36)  default '' null comment 'Внешний ключ на нормативный документ',
                constraint fias_landmark_uniqueKey
                    unique (landid)
            ) ENGINE = InnoDB comment 'Описание мест расположения  имущественных объектов'
              collate = utf8mb4_unicode_ci");

        $results[] = DB::statement("create table if not exists fias_normative_document
            (
                id        bigint unsigned auto_increment comment 'ID'
                    primary key,
                normdocid varchar(36) default '' null comment 'Идентификатор нормативного документа',
                docname   text                   null comment 'Наименование документа',
                docdate   date                   null comment 'Дата документа',
                docnum    varchar(20) default '' null comment 'Номер документа',
                doctype   int                    null comment 'Тип документа',
                docimgid  varchar(36) default '' null comment 'Идентификатор образа (внешний ключ)',
                constraint fias_normative_document_uniqueKey
                    unique (normdocid)
            )
                ENGINE = InnoDB comment 'Сведения по нормативному документу, являющемуся основанием присвоения адресному элементу наименования'
                collate = utf8mb4_unicode_ci");

        $results[] = DB::statement("create table if not exists fias_normative_document_type
            (
                id       bigint unsigned auto_increment comment 'ID'
                    primary key,
                ndtypeid int                     null comment 'Идентификатор записи (ключ)',
                name     varchar(250) default '' null comment 'Наименование типа нормативного документа',
                constraint fias_normative_document_type_uniqueKey
                    unique (ndtypeid)
            ) ENGINE = InnoDB comment 'Тип нормативного документа'
              collate = utf8mb4_unicode_ci");

        $results[] = DB::statement("create table if not exists fias_object
            (
                id         bigint unsigned auto_increment comment 'ID'
                    primary key,
                aoguid     varchar(36)  default '' null comment 'Глобальный уникальный идентификатор адресного объекта ',
                formalname varchar(120) default '' null comment 'Формализованное наименование',
                regioncode varchar(2)   default '' null comment 'Код региона',
                autocode   varchar(1)   default '' null comment 'Код автономии',
                areacode   varchar(3)   default '' null comment 'Код района',
                citycode   varchar(3)   default '' null comment 'Код города',
                ctarcode   varchar(3)   default '' null comment 'Код внутригородского района',
                placecode  varchar(3)   default '' null comment 'Код населенного пункта',
                streetcode varchar(4)   default '' null comment 'Код улицы',
                extrcode   varchar(4)   default '' null comment 'Код дополнительного адресообразующего элемента',
                plancode   varchar(4)   default '' null comment 'Код элемента планировочной структуры',
                sextcode   varchar(3)   default '' null comment 'Код подчиненного дополнительного адресообразующего элемента',
                offname    varchar(120) default '' null comment 'Официальное наименование',
                postalcode varchar(6)   default '' null comment 'Почтовый индекс',
                ifnsfl     varchar(4)   default '' null comment 'Код ИФНС ФЛ',
                terrifnsfl varchar(4)   default '' null comment 'Код территориального участка ИФНС ФЛ',
                ifnsul     varchar(4)   default '' null comment 'Код ИФНС ЮЛ',
                terrifnsul varchar(4)   default '' null comment 'Код территориального участка ИФНС ЮЛ',
                okato      varchar(11)  default '' null comment 'OKATO',
                oktmo      varchar(11)  default '' null comment 'OKTMO',
                updatedate date                    null comment 'Дата  внесения записи',
                shortname  varchar(10)  default '' null comment 'Краткое наименование типа объекта',
                aolevel    int                     null comment 'Уровень адресного объекта ',
                parentguid varchar(36)  default '' null comment 'Идентификатор объекта родительского объекта',
                aoid       varchar(36)  default '' null comment 'Уникальный идентификатор записи. Ключевое поле.',
                previd     varchar(36)  default '' null comment 'Идентификатор записи связывания с предыдушей исторической записью',
                nextid     varchar(36)  default '' null comment 'Идентификатор записи  связывания с последующей исторической записью',
                code       varchar(17)  default '' null comment 'Код адресного объекта одной строкой с признаком актуальности из КЛАДР 4.0. ',
                plaincode  varchar(15)  default '' null comment 'Код адресного объекта из КЛАДР 4.0 одной строкой без признака актуальности (последних двух цифр)',
                actstatus  int                     null comment 'Статус актуальности адресного объекта ФИАС. Актуальный адрес на текущую дату. Обычно последняя запись об адресном объекте.
            0 – Не актуальный
            1 - Актуальный
            ',
                centstatus int                     null comment 'Статус центра',
                operstatus int                     null comment 'Статус действия над записью – причина появления записи (см. описание таблицы OperationStatus):
            01 – Инициация;
            10 – Добавление;
            20 – Изменение;
            21 – Групповое изменение;
            30 – Удаление;
            31 - Удаление вследствие удаления вышестоящего объекта;
            40 – Присоединение адресного объекта (слияние);
            41 – Переподчинение вследствие слияния вышестоящего объекта;
            42 - Прекращение существования вследствие присоединения к другому адресному объекту;
            43 - Создание нового адресного объекта в результате слияния адресных объектов;
            50 – Переподчинение;
            51 – Переподчинение вследствие переподчинения вышестоящего объекта;
            60 – Прекращение существования вследствие дробления;
            61 – Создание нового адресного объекта в результате дробления
            ',
                currstatus int                     null comment 'Статус актуальности КЛАДР 4 (последние две цифры в коде)',
                startdate  date                    null comment 'Начало действия записи',
                enddate    date                    null comment 'Окончание действия записи',
                normdoc    varchar(36)  default '' null comment 'Внешний ключ на нормативный документ',
                livestatus tinyint(1)              null comment 'Признак действующего адресного объекта',
                divtype    int                     null comment '
                              Тип адресации:
                              0 - не определено
                              1 - муниципальный;
                              2 - административно-территориальный
                            ',
                cadnum     varchar(100)            null,
                constraint fias_object_uniqueKey
                    unique (aoid)
            ) ENGINE = InnoDB comment 'Классификатор адресообразующих элементов'
              collate = utf8mb4_unicode_ci");

        $results[] = DB::statement("create table if not exists fias_operation_status
            (
                id         bigint unsigned auto_increment comment 'ID'
                    primary key,
                operstatid int                     null comment 'Идентификатор статуса (ключ)',
                name       varchar(100) default '' null comment 'Наименование
            01 – Инициация;
            10 – Добавление;
            20 – Изменение;
            21 – Групповое изменение;
            30 – Удаление;
            31 - Удаление вследствие удаления вышестоящего объекта;
            40 – Присоединение адресного объекта (слияние);
            41 – Переподчинение вследствие слияния вышестоящего объекта;
            42 - Прекращение существования вследствие присоединения к другому адресному объекту;
            43 - Создание нового адресного объекта в результате слияния адресных объектов;
            50 – Переподчинение;
            51 – Переподчинение вследствие переподчинения вышестоящего объекта;
            60 – Прекращение существования вследствие дробления;
            61 – Создание нового адресного объекта в результате дробления;
            70 – Восстановление объекта прекратившего существование',
                constraint fias_operation_status_uniqueKey
                    unique (operstatid)
            ) ENGINE = InnoDB comment 'Статус действия'
              collate = utf8mb4_unicode_ci");

        $results[] = DB::statement("create table if not exists fias_room
            (
                id         bigint unsigned auto_increment comment 'ID'
                    primary key,
                roomguid   varchar(36)  default '' null comment 'Глобальный уникальный идентификатор адресного объекта (помещения)',
                flatnumber varchar(50)  default '' null comment 'Номер помещения или офиса',
                flattype   int                     null comment 'Тип помещения',
                roomnumber varchar(50)  default '' null comment 'Номер комнаты',
                roomtype   int                     null comment 'Тип комнаты',
                regioncode varchar(2)   default '' null comment 'Код региона',
                postalcode varchar(6)   default '' null comment 'Почтовый индекс',
                updatedate date                    null comment 'Дата  внесения записи',
                houseguid  varchar(36)  default '' null comment 'Идентификатор родительского объекта (дома)',
                roomid     varchar(36)  default '' null comment 'Уникальный идентификатор записи. Ключевое поле.',
                previd     varchar(36)  default '' null comment 'Идентификатор записи связывания с предыдушей исторической записью',
                nextid     varchar(36)  default '' null comment 'Идентификатор записи  связывания с последующей исторической записью',
                startdate  date                    null comment 'Начало действия записи',
                enddate    date                    null comment 'Окончание действия записи',
                livestatus tinyint(1)              null comment 'Признак действующего адресного объекта',
                normdoc    varchar(36)  default '' null comment 'Внешний ключ на нормативный документ',
                operstatus int                     null comment 'Статус действия над записью – причина появления записи (см. описание таблицы OperationStatus):
            01 – Инициация;
            10 – Добавление;
            20 – Изменение;
            21 – Групповое изменение;
            30 – Удаление;
            31 - Удаление вследствие удаления вышестоящего объекта;
            40 – Присоединение адресного объекта (слияние);
            41 – Переподчинение вследствие слияния вышестоящего объекта;
            42 - Прекращение существования вследствие присоединения к другому адресному объекту;
            43 - Создание нового адресного объекта в результате слияния адресных объектов;
            50 – Переподчинение;
            51 – Переподчинение вследствие переподчинения вышестоящего объекта;
            60 – Прекращение существования вследствие дробления;
            61 – Создание нового адресного объекта в результате дробления
            ',
                cadnum     varchar(100) default '' null comment 'Кадастровый номер помещения',
                roomcadnum varchar(100) default '' null comment 'Кадастровый номер комнаты в помещении',
                constraint fias_room_uniqueKey
                    unique (roomid)
            ) ENGINE = InnoDB comment 'Классификатор помещениях'
              collate = utf8mb4_unicode_ci");

        $results[] = DB::statement("create table if not exists fias_room_type
            (
                id        bigint unsigned auto_increment comment 'ID'
                    primary key,
                rmtypeid  int                    null comment 'Тип комнаты',
                name      varchar(20) default '' null comment 'Наименование',
                shortname varchar(20) default '' null comment 'Краткое наименование',
                constraint fias_room_type_uniqueKey
                    unique (rmtypeid)
            ) ENGINE = InnoDB comment 'Тип комнаты'
              collate = utf8mb4_unicode_ci");

        $results[] = DB::statement("create table if not exists fias_stead
            (
                id         bigint unsigned auto_increment comment 'ID'
                    primary key,
                steadguid  varchar(36)  default '' null comment 'Глобальный уникальный идентификатор адресного объекта (земельного участка)',
                number     varchar(120) default '' null comment 'Номер земельного участка',
                regioncode varchar(2)   default '' null comment 'Код региона',
                postalcode varchar(6)   default '' null comment 'Почтовый индекс',
                ifnsfl     varchar(4)   default '' null comment 'Код ИФНС ФЛ',
                terrifnsfl varchar(4)   default '' null comment 'Код территориального участка ИФНС ФЛ',
                ifnsul     varchar(4)   default '' null comment 'Код ИФНС ЮЛ',
                terrifnsul varchar(4)   default '' null comment 'Код территориального участка ИФНС ЮЛ',
                okato      varchar(11)  default '' null comment 'OKATO',
                oktmo      varchar(11)  default '' null comment 'OKTMO',
                updatedate date                    null comment 'Дата  внесения записи',
                parentguid varchar(36)  default '' null comment 'Идентификатор объекта родительского объекта',
                steadid    varchar(36)  default '' null comment 'Уникальный идентификатор записи. Ключевое поле.',
                previd     varchar(36)  default '' null comment 'Идентификатор записи связывания с предыдушей исторической записью',
                nextid     varchar(36)  default '' null comment 'Идентификатор записи  связывания с последующей исторической записью',
                operstatus int                     null comment 'Статус действия над записью – причина появления записи (см. описание таблицы OperationStatus):
            01 – Инициация;
            10 – Добавление;
            20 – Изменение;
            21 – Групповое изменение;
            30 – Удаление;
            31 - Удаление вследствие удаления вышестоящего объекта;
            40 – Присоединение адресного объекта (слияние);
            41 – Переподчинение вследствие слияния вышестоящего объекта;
            42 - Прекращение существования вследствие присоединения к другому адресному объекту;
            43 - Создание нового адресного объекта в результате слияния адресных объектов;
            50 – Переподчинение;
            51 – Переподчинение вследствие переподчинения вышестоящего объекта;
            60 – Прекращение существования вследствие дробления;
            61 – Создание нового адресного объекта в результате дробления
            ',
                startdate  date                    null comment 'Начало действия записи',
                enddate    date                    null comment 'Окончание действия записи',
                normdoc    varchar(36)  default '' null comment 'Внешний ключ на нормативный документ',
                livestatus tinyint(1)              null comment 'Признак действующего адресного объекта',
                cadnum     varchar(100) default '' null comment 'Кадастровый номер',
                divtype    int                     null comment 'Тип адресации:
            0 - не определено
            1 - муниципальный;
            2 - административно-территориальный',
                counter    int                     null,
                constraint fias_stead_uniqueKey
                    unique (steadid)
            ) ENGINE = InnoDB comment 'Классификатор земельных участков'
              collate = utf8mb4_unicode_ci");

        $results[] = DB::statement("create table if not exists fias_structure_status
            (
                id        bigint unsigned auto_increment comment 'ID'
                    primary key,
                strstatid int                    null comment 'Признак строения',
                name      varchar(20) default '' null comment 'Наименование',
                shortname varchar(20) default '' null comment 'Краткое наименование',
                constraint fias_structure_status_uniqueKey
                    unique (strstatid)
            ) ENGINE = InnoDB comment 'Признак строения'
              collate = utf8mb4_unicode_ci");

        $results[] = DB::statement("create table if not exists update_settings
            (
                id    bigint unsigned auto_increment comment 'ID'
                    primary key,
                `key` varchar(255) not null comment 'Ключ',
                value varchar(500) not null comment 'Значение',
                constraint update_settings_key_unique
                    unique (`key`)
            ) ENGINE = InnoDB
              collate = utf8mb4_unicode_ci");

        $results[] = DB::statement("create table if not exists users
            (
                id             bigint unsigned auto_increment
                    primary key,
                name           varchar(255)            not null,
                email          varchar(255)            not null,
                password       varchar(60)             not null,
                remember_token varchar(100) default '' not null,
                created_at     timestamp               null,
                updated_at     timestamp               null
            ) ENGINE = InnoDB
              collate = utf8mb4_unicode_ci");

        $dateTime = date('Y-m-d H:i:s');
        $results[] = DB::insert(
            "INSERT INTO db191027_fias_test.users
                     (id, name, email, password, remember_token, created_at, updated_at)
                   VALUES (?,?,?,?,?,?,?);",
            [
                1,
                'admin',
                'admin@igm26k.ru',
                '$2y$10$XKjXJwwmGSTEO9GphI39d.w.KyZ4JRikFJx4Hr.oWP27CznAIwcDO',
                '',
                $dateTime,
                $dateTime,
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
