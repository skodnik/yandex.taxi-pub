Парсит цены ЯндексТакси по указанному маршруту и экспортирует их в Googlesheets документ.

# Вариант с Docker
Установка docker: [get-docker](https://docs.docker.com/get-docker/)

## Установка
```
$ git clone https://github.com/skodnik/yandex.taxi-pub
$ cd yandex.taxi-pub
$ make start
```

## Настройка приложения в app/.env
> ВАЖНО! именно app/.env, а не .env

В текущей версии реализована возможность указания двух POI (например - долгота: 30.273645264, широта: 59.799730961):
- home - COORDINATE_LONGITUDE_1, COORDINATE_LATITUDE_1
- work - COORDINATE_LONGITUDE_2, COORDINATE_LATITUDE_2

Для сохранения данных в Google Spreadsheets необходимо указать:
- GOOGLE_SPREADSHEET_PUSH значение true (по умолчанию - false)
- GOOGLE_SPREADSHEET_ID (является частью ссылки на додкумент)
- наименования листов с указанием диапазонов ячеек для заполнения GOOGLE_SPREADSHEET_ID_TO_WORK_RANGE, GOOGLE_SPREADSHEET_ID_TO_HOME_RANGE (например - "Home_to_Work!A2")

Загрузка данных в Google Spreadsheets невозможна без получения `credentials.json` и размещения его в директории `storage/google-docs`. Для его получения необходимо включить Google Sheets API и создать приложение. Подробнее здесь - [developers.google.com/sheets/api/quickstart/](https://developers.google.com/sheets/api/quickstart/php#step_3_set_up_the_sample)

## Запуск по расписанию:
В контейнере:
```
# crontab -e
*/5 6-12 * * * php {{PATH_TO_SCRIPT}}/cli yandextaxi:get-data --direction=to_work --quiet >/dev/null 2>&1
*/5 17-21 * * * php {{PATH_TO_SCRIPT}}/cli yandextaxi:get-data --direction=to_home --quiet >/dev/null 2>&1
```

## Запуск из консоли:
На хосте:
```
$ make console c="app-php"
```
В контейнере:
```
# make help
# make to-work
# make to-home
```

## Пример результата работы
```
# make to-home
php cli yandextaxi:get-data --direction=to_home

Получает данные из Яндекс.Такси
===============================

Используется для получения актуальной стоимости поездки по выбранному направлению, по умолчанию - "дом -> работа"

----------------- ------------
Параметр          Значение
----------------- ------------
date              01.11.2020
time              16:24:46
duration          27
distance          21
econom            500
business          670
comfortplus       830
vip               1160
ultimate          2290
maybach           2520
child_tariff      590
minivan           850
premium_van       1910
personal_driver   2020
express           520
courier           370
cargo             860
----------------- ------------
```

## Тесты:
В контейнере:
```
# make run-tests
```

## Команды Composer:
На хосте (пример):
```
$ make composer c="update"
$ make composer c="dump-autoload"
```

## Остановка и очистка
На хосте:
```
$ make down
# rm -rf app/vendor app/composer.lock app/.phpunit.result.cache
```