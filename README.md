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
$api->status();                       // Информация  о сервере
$api->changeMap('de_dust2');          // Смена карты
$api->mapList();                      // Список карт
$api->command('amx_reloadadmins');    // Отправка команды
$api->resources();                    // Получение занимаемых ресурсов
```

Пример использования
--------------------

```php
<?php
$token = 'qwertyuiopp'; // Токен управления сервером
$api = new MyArenaAPI($token);

$info = $api->status();

$info['online'];	    // Boolean значение статуса сервера (TRUE - работает, FALSE - не работает)

echo $info['status'];	    // Вывод статуса сервера (Выключен, Работает, Запускается)
echo $info['name'];         // Имя сервера
echo $info['map'];          // Текущая карта
echo $info['curPlayers'];   // Игроков на сервере
echo $info['maxPlayers'];   // Кол-во слотов

$players = $info['playersInfo']; // Информация об игроках
?>
```

```htm
<!-- Получение информации об игроках -->
<table>
	<thead>
		<tr>
			<th><b>Ник</b></th>
```
```php
			<?php if(isset($players[0]['score'])):?>
```
```htm
			<td><b>Счет</b></td>
```
```php
			<?php endif;?>
			<?php if(isset($players[0]['time'])):?>
```
```htm
				<td><b>Время</b></td>
```
```php
			<?php endif;?>
```
```htm
		</tr>
	</thead>
	<tbody>
```
```php
	<?php
	foreach($players as $player):?>
```
```htm
		<tr>
			<td>
				<?php echo $player['name']?>
			</td>
```
```php
			<?php if(isset($player['score'])):?>
```
```htm
			<td>
```
```php
				<?php echo $player['score']?>
```
```htm
			</td>
```
```php
			<?php endif;?>
			<?php if(isset($player['time'])):?>
```
```htm
			<td>
```
```php
				<?php echo $player['time']?>
```
```htm
			</td>
```
```php
			<?php endif;?>
```
```htm
		</tr>
```
```php
	<?php endforeach;?>
```
```htm
	</tbody>
</table>
```
