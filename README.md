MyArena.Ru-API
==============

Библиотека для работы с API игрового хостинга MyArena.ru

Установка
---------

```bash
composer require desalvatierra/myarenaapi
```
или
```bash
php composer.phar require desalvatierra/myarenaapi
```
или пропишите в ваш composer.json в секцию require:
```bash
"desalvatierra/myarenaapi": "*"
```

Использование
-------------
```php
<?php

use DeSalvatierra\MyArena\Api\Api;

include './vendor/autoload.php';
$token = 'qwertyuiopp';
$api = new Api($token);

// Доступные методы

$api->start();              // Запуск сервера
$api->stop();               // Останов сервера
$api->restart();            // Перезапуск сервера
$api->status();             // Информация  о сервере
$api->mapList();            // Список карт
$api->resources();          // Получение занимаемых ресурсов
$api->getConsole();         // Получение консоли

// Примеры использования

$api->changeMap('de_dust2');  		// Сменить карту на de_dust2
$api->command('amx_reloadadmins');	// Отправить на сервер команду amx_reloadadmins

// Получение информации о сервере
$info = $api->status();

// Информация от хостинга:
$hostInfo = $info->getHostInfo();

$hostInfo->getAddress(); // Полный адрес с портом
$hostInfo->getBlockDate(); // Дата блокировки. Если null - значит сервер бесплатный
$hostInfo->getDays(); // Остаток дней аренды. Если 0 - значит либо истекает сегодня, либо бесплатный
$hostInfo->getGameName(); // Полное название игры
$hostInfo->getId(); // ID сервера на хостинге
$hostInfo->getLocation(); // Имя локации
$hostInfo->getSlots(); // Количество слотов по тарифу
$hostInfo->getTariff(); // Название тарифа

$info->getOnline();	        // Числовое значение статуса сервера (0 - Выключен, 1 - Работает, 2 - Запускается или завис)

echo $info->getStatus();	// Строковое представление статуса (Выключен, Работает, Запускается/Завис)
echo $info->getGame();		// Игра (cstrike, tf2, czero...)
echo $info->getEngine();	// Движок сервера (halflife, source, samp...)
echo $info->getIp();	    // IP сервера
echo $info->getPort();		// Порт сервера
echo $info->getName();      // Имя сервера
echo $info->getMap();       // Текущая карта
echo $info->getCurrentPlayers();   // Игроков на сервере
echo $info->getMaxPlayers();   // Кол-во слотов

// Информация об игроках
$players = $info->getPlayers();
?>

<!-- Получение информации об игроках -->
<table>
    <thead>
        <tr>
            <th>
                <b>Ник</b>
            </th>
            <th>
                <b>Счет</b>
            </th>
            <th>
                <b>Время</b>
            </th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($players as $player):?>
        <tr>
            <td>
                <?php echo $player->getName()?>
            </td>
            <td>
                <?php echo $player->getScore()?>
            </td>
            <td>
                <?php echo $player->getTime()->format('H:I:S')?>
            </td>
        </tr>
	<?php endforeach;?>
    </tbody>
</table>
```