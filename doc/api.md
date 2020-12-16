# API

#### Описание

Интерфейс спроектирован по методологии JSON API. То есть каждый запрос
отправляется методом HTTP POST и имеет тело запроса в формате JSON. 
Форматом ответа также является JSON.

Доступ к интерфейсу осущесвляется с помощью токена по технологии OAuth.
То есть для доступа необходимо передать заголовок с секретным ключом.

#### Настройка подключения

Прежде всего необходимо получить специальный ключ для доступа. 
На данный момент его можно получить только через веб-интерфейс на
https://tc-fias-test.telecontact.ru. Срок жизни токена неограничен.

Доступ к веб-интерфейсу есть у главного программиста проекта 
[Ильи Голубева](mailto:i.golubev@telecontact.ru) и у Linux-инженеров 

После получения токена, его необходимо отправлять вместе с заголовком
каждого HTTP запроса.

Общий вид запроса:
```http request
POST {FIAS_HOST}/api
Accept: application/json
Content-Type: application/json
Authorization: Bearer {SECRET_KEY}
Cache-Control: no-cache

{JSON_QUERY}
```

Пример:
```http request
POST https://tc-fias-test.telecontact.ru/api
Accept: application/json
Content-Type: application/json
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjVhYzIwOTVlZDE0YmRjZmYwYTE2YzQyY2MyYjAzYzdiMmYwNjlmYTQ2YWY5MDViMTM0NTM0ZjgzOWQzMDllOTRjMGFhZGU4ZTUxYzZmZDIxIn0.eyJhdWQiOiIxIiwianRpIjoiNWFjMjA5NWVkMTRiZGNmZjBhMTZjNDJjYzJiMDNjN2IyZjA2OWZhNDZhZjkwNWIxMzQ1MzRmODM5ZDMwOWU5NGMwYWFkZThlNTFjNmZkMjEiLCJpYXQiOjE1NzI5NjY3NjAsIm5iZiI6MTU3Mjk2Njc2MCwiZXhwIjoxNjA0NTg5MTYwLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.jWz4IyhRIduT6KRdDFBU1Tc8nA_G2qN_QvmY_5YxPh9T4Zvma6jDzIuLR23p2nTv09hclml-bG8QMRauufG85TyhQ4s_0nlN9AETNaxlTdLd4qXPJdbtSVaGPSi4U7XXZopirBu5V7MNefUWtMilkPjJJ6D5NLJht95Pi8S-6nzRIByMY5S-_NE2gJZYSWNvH3jXUka7WGwoSXk5qexC_tltAh3dsng7wkxcCUKxsWhpGNKczcKAzpdax03G-nxFJLNxL3vw3l4Syi2QO1Om-k1qj9NwmY4wX65RfaMsB5D2YrL5wE2RJS9KsyzXlTecPLDRH8xEo5r-Rvr5t__i_tfZNdpWTuPR99VRnCSC6SW-T7WyRBxJ9TSfqAH5l2o8R5eUknV6WACe6BUpk1YolDf5YB5ns9TlKVpCi5YZXPD8FmL6TDSpVncXobSLI3KcVzQ1A71SZOpaTD3z3RXuL-qj6t2JkVtmYemg6Sj7meqRbj7i8wp10pavlY0HCjro3moSNT0deXEvl5uYY2QTmQ-a_KJnF9pDhY5Hsx0R1YZXI0Ax4q343VV2MkyxDPOzv_HliQGI0CQqtGKkHC1Je2sFvUGcrjmqM57QE_jlknxamxKsuvOBYRy0FeGF8RHRUsIM_ml2aOxgJLJL9Vi1USjRflYFJFP0g8LvLTUMg5s
Cache-Control: no-cache

{ "query":  "москва павловс 27" }
```
