# API: методы

Название каждого метода является ключом в JSON объекте.
JSON должен быть передан в теле HTTP POST запроса.

### query

```json5
{"query":  "{QUERY_STRING}", "count":  {COUNT}}
```

`{QUERY_STRING}` - строка с перечисленными названиями адресных 
элементов

`{COUNT}` - количество элементов в ответе

##### Пример:
```json5
{"query":  "красногор кир 26", "count":  2}
```

##### Пример на PHP:
```php
$body = [
    'query' => 'красногор кир 26',
    'count' => 2
];
$host = 'https://tc-fias-test.telecontact.ru/api';
$headers = [
    'Accept: application/json',
    'Cache-Control: no-cache',
    'Content-Type: application/json',
    'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjY0NzFjODQ5Y2ZkNzg5MWQ1MTM5ZGE4OTMxMTI4NDI3NzAyN2E3NDAwY2I2NzgzNTBiZDczZGM2ODUxNDhlYTM5Njc4YWZjNjljNzNjN2MyIn0.eyJhdWQiOiIxIiwianRpIjoiNjQ3MWM4NDljZmQ3ODkxZDUxMzlkYTg5MzExMjg0Mjc3MDI3YTc0MDBjYjY3ODM1MGJkNzNkYzY4NTE0OGVhMzk2NzhhZmM2OWM3M2M3YzIiLCJpYXQiOjE1ODAzMDg0OTMsIm5iZiI6MTU4MDMwODQ5MywiZXhwIjoxNjExOTMwODkzLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.DSAM3oMq1gtCibMBPR5s29qffp0KumqOxSoZhbI2ofxPnzGnuHjbxm5bf2R1oK9209oE9vXxg_O60Xwp-a3tzHezUZX0-CnrnLY__n-Lz6oLOK0UvACITet7_-5FImgK0N3bwC_L2lNoe-bkjjq6p2J3lMmCvsS69MAlyMMaCyFPE45y5O_rXxu9782RuOlHLjWV-zAwUzaSHkxu7HOhRY4LI3OuAOHwfJ6x9mySzGWhTOKNebqk2Audg5dzK1h680rhp1KLi79RDRinE9CoctRzXWztm1B3HOV6C2gwvp90-6Z2q5YJXzF0lZvi6azFcrOpsJrpZ2ZSn7ZI03FkJ5negLAdxP200uqMc6NddkOcOeeHvB6Q73vSRRrRmp0-dpdM56jRJa_HKD4--0p-FJW3Sl7zspj3qrlchJpsA2nR0tt22I6tGbXX5iEHN8J3Ub4ggkTkHgr80y7U0obU_AXwiJuLQxUMbzXiUtGOI8FgeNH33r-Utiu7YPtWNOl7gD8LbIAaznvQrP1AxD-qHxf_tOZeGz-79IN5WdM_hBK14GWlu8_3etheJVoF5_EJM0kTjUBFyH8WtTfBLIwA_EI-ZbuTFocusWIQ3ebIsoC05pUv39mKZlba-58HUdghbNEOLC0_OypiQWTgS9UxQMwjLLGHEccijqS3GmPZn18',
];

$ch = curl_init($host);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result = json_decode(curl_exec($ch));
curl_close($ch);

return $result;
```
