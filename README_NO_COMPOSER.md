Для установки без composer просто скачайте архив на главной странице проекта: Clone or download -> Download zip

Распакуйте в удобное для вас место, затем подключите все классы:
```
<?php
include '/path/to/src/ApiException.php';
include '/path/to/src/Api.php';
include '/path/to/src/HostInfo.php';
include '/path/to/src/Player.php';
include '/path/to/src/Server.php';

```
А дальше уже так же, как в основной справке, кроме include './vendor/autoload.php';
