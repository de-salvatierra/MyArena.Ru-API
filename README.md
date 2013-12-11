MyArena.Ru-API
==============

Класс для работы с API игрового хостинга MyArena.ru

Подключение:

$token = 'qwertyuiopp'; // Токен управления сервером
$api = new MyArenaAPI($token);

Доступные методы:
```php
$api->start();      // Запуск сервера
$api->stop();       // Останов сервера
$api->restart();    // Перезапуск сервера
$api->changeMap();  // Смена карты
$api->mapList();    // Список карт
$api->command();    // Отправка команды
$api->resources();  // Получение занимаемых ресурсов
```
