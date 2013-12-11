MyArena.Ru-API
==============

Класс для работы с API игрового хостинга MyArena.ru

Подключение:
```php
$token = 'qwertyuiopp'; // Токен управления сервером
$api = new MyArenaAPI($token);
```
Доступные методы:
```php
$api->start();                        // Запуск сервера
$api->stop();                         // Останов сервера
$api->restart();                      // Перезапуск сервера
$api->changeMap('de_dust2');          // Смена карты
$api->mapList();                      // Список карт
$api->command('amx_reloadadmins');    // Отправка команды
$api->resources();                    // Получение занимаемых ресурсов
```
