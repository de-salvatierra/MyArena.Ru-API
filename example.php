<?php
/**
 * Рабочий пример использования класса управления 
 * серверами хостинга MyArena.Ru через API
 * @author Александр Урих <urichalex@mail.ru>
 * @license Проприетарное программное обеспечение
 */

/**
 * Токен и пользователей сменить на свои, 
 * остальное исправлять в меру Вашей 
 * осведомленности в этом вопросе =)
 */

// Токен
$token        = '123';

// Пользователи
$users = array(
    'admin' => '132465',
    'admin2' => 'qwertyuiop'
);

// Сессия
session_start();

$scriptName = filter_input(INPUT_SERVER, 'PHP_SELF');

// Авторизован ли юзер
$auth         = !empty($_SESSION['user']);

// Класс
include './MyArenaAPI.php';

// Экземпляр
$server       = new MyArenaAPI($token);

// Получение инфы
$info         = $server->status();

// Игроки
$players      = $info['playersInfo'];

/** Запросы **/
// Логин и пароль, авторизация
$login        = filter_input(INPUT_POST, 'login');
$password     = filter_input(INPUT_POST, 'password');

// Получение инфы
$getInfo      = filter_input(INPUT_POST, 'getInfo',      FILTER_VALIDATE_BOOLEAN);

// Получение ресурсов
$getResources = filter_input(INPUT_POST, 'getResources', FILTER_VALIDATE_BOOLEAN);

/** Обработчики **/
// Авторизация
if($login && $password) {
    if(isset($users[$login]) && $users[$login] == $password && !$auth) {
        $_SESSION['user'] = true;
        $auth = true;
    }
    header('Location: ' . $scriptName);
}

// Получение инфы
if($getInfo) {
    $exit = array(
        'hostname' => htmlspecialchars($info['name'], ENT_QUOTES),
        'address' => $info['hostInfo']['address'],
        'game' => $info['hostInfo']['game'],
        'status' => $info['status'],
        'map' => $info['map'],
        'maxPlayers' => $info['maxPlayers'],
        'curPlayers' => $info['curPlayers'],
        'online' => $info['online']
    );
    $exit['players'] = '';
    foreach($players as $player) {
        $exit['players'] .= '<tr>';
        $exit['players'] .= '<td>'.htmlspecialchars($player['name'], ENT_QUOTES).'</td>';
        if(isset($player['score'])) {
            $exit['players'] .= '<td class="text-center">'.intval($player['score']).'</td>';
        }
        if(isset($player['time'])) {
            $exit['players'] .= '<td class="text-center">'.$player['time'].'</td>';
        }
    }
    $exit['errors'] = $server->getErrors();
    exit(json_encode($exit));
}

// Получение ресурсов
if($getResources) {
    $resources = $server->resources();
    unset($resources['players']);
    exit(json_encode($resources));
}

/** Для авторизованного **/
if($auth) {
    // Запросы
    $action       = filter_input(INPUT_POST, 'action');
    $map          = filter_input(INPUT_POST, 'map');
    $command      = filter_input(INPUT_POST, 'command');
    $logout       = filter_input(INPUT_GET, 'logout');

    /** Обработчики **/
    // Выход
    if($logout) {
        if($auth) {
            session_destroy();
            $auth = false;
        }
        header('Location: ' . $scriptName);
    }

    // Действие над сервером (вкл, выкл, рестарт)
    if($action) {
        $server->$action();
        exit();
    }

    // Смена карты
    if($map) {
        $server->changeMap($map);
        exit();
    }

    // Выполнение команды
    if($command) {
        $server->command($command);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MyArenaApi Example</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>
                        Управление сервером MyArena через API
                        <?php if($auth):?>
                        <a href="?logout=true" class="pull-right">Выйти</a>
                        <?php endif;?>
                    </h3>
                </div>
                <div class="panel-body">
                    <?php if($server->hasErrors()):?>
                    <div class="alert alert-danger">
                        <strong>Ошибка!</strong> Не удалось получить информацию с сервера. 
                        Проверьте правильность указания API токена
                    </div>
                    <?php else:?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="panel panel-info">
                                <div class="panel-heading text-center">
                                    <strong>Информация</strong>
                                </div>
                                <div class="panel-body text-center">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Название:</th>
                                            <td id="hostname"><?php echo htmlspecialchars($info['name'], ENT_QUOTES)?></td>
                                        </tr>
                                        <tr>
                                            <th>Адрес:</th>
                                            <td id="address"><?php echo $info['hostInfo']['address']?></td>
                                        </tr>
                                        <tr>
                                            <th>Статус:</th>
                                            <td id="status"><?php echo $info['status']?></td>
                                        </tr>
                                        <tr>
                                            <th>Карта:</th>
                                            <td>
                                                <?php if($auth && $maps = $server->mapList()):?>
                                                <div class="btn-group">
                                                    <button 
                                                        type="button" 
                                                        class="btn btn-default btn-xs dropdown-toggle" 
                                                        data-toggle="dropdown" 
                                                        aria-expanded="false">
                                                        <span id="map"><?php echo $info['map']?></span> <span class="caret"></span>
                                                    </button>
                                                    <ul 
                                                        class="dropdown-menu" 
                                                        role="menu" 
                                                        id="maplist" 
                                                        style="max-height: 300px; overflow: auto">
                                                        <?php foreach($maps as $map):?>
                                                        <li<?php if($map == $info['map']) echo ' class="active disabled"';?> data-map="<?php echo $map;?>">
                                                            <a href="#" class="changeMap" data-map="<?php echo $map;?>"><?php echo $map;?></a>
                                                        </li>
                                                        <?php endforeach;?>
                                                    </ul>
                                                </div>
                                                <?php else:?>
                                                <span id="map"><?php echo $info['map']?></span>
                                                <?php endif;?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Игроков:</th>
                                            <td>
                                                <span id="curPlayers">
                                                    <?php echo intval($info['curPlayers'])?>
                                                </span>
                                                /
                                                <span id="maxPlayers">
                                                    <?php echo intval($info['maxPlayers'])?>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel panel-info">
                                <div class="panel-heading text-center">
                                    <strong>Ресурсы</strong>
                                </div>
                                <div class="panel-body text-center">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 130px">Диск:</th>
                                            <td>
                                                <div class="progress" style="margin-bottom: 0">
                                                    <div 
                                                        class="progress-bar progress-bar-striped active" 
                                                        role="progressbar"
                                                        id="disk_proc"
                                                        aria-valuenow="0" 
                                                        aria-valuemin="0" 
                                                        aria-valuemax="100" 
                                                        style="width: 0%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 130px">Память:</th>
                                            <td>
                                                <div class="progress" style="margin-bottom: 0">
                                                    <div 
                                                        class="progress-bar progress-bar-striped active" 
                                                        role="progressbar"
                                                        id="mem_proc"
                                                        aria-valuenow="0" 
                                                        aria-valuemin="0" 
                                                        aria-valuemax="100" 
                                                        style="width: 0%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 130px">Процессор:</th>
                                            <td id="cpu">
                                                <div class="progress" style="margin-bottom: 0">
                                                    <div 
                                                        class="progress-bar progress-bar-striped active" 
                                                        role="progressbar"
                                                        id="cpu_proc"
                                                        aria-valuenow="0" 
                                                        aria-valuemin="0" 
                                                        aria-valuemax="100" 
                                                        style="width: 0%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php if($auth):?>
                                        <tr>
                                            <th>Управление:</th>
                                            <td>
                                                    <button 
                                                        class="btn btn-xs btn-success serverAction" 
                                                        id="start" 
                                                        disabled>
                                                        Включить
                                                    </button>
                                                    <button 
                                                        class="btn btn-xs btn-danger serverAction" 
                                                        id="stop" 
                                                        disabled>
                                                        Выключить
                                                    </button>
                                                    <button 
                                                        class="btn btn-xs btn-warning serverAction" 
                                                        id="restart" 
                                                        disabled>
                                                        Перезапустить
                                                    </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <form action="" method="post" id="sendCommand">
                                                    <div class="input-group input-group-sm col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                        <input 
                                                            type="text" 
                                                            class="form-control" 
                                                            id="command"
                                                            name="command"
                                                            placeholder="Введите команду">
                                                        <span class="input-group-btn">
                                                            <button 
                                                                type="submit"
                                                                class="btn btn-default">
                                                                <span 
                                                                    class="glyphicon glyphicon-send" 
                                                                    aria-hidden="true"></span>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php else:?>
                                        <tr>
                                            <td colspan="2">
                                                <form class="form-inline" action="" method="post">
                                                    <div class="form-group">
                                                        <input 
                                                            type="text" 
                                                            class="form-control" 
                                                            id="login" 
                                                            name="login" 
                                                            placeholder="Логин">
                                                    </div>
                                                    <div class="form-group">
                                                        <input 
                                                            type="password" 
                                                            class="form-control" 
                                                            id="password" 
                                                            name="password" 
                                                            placeholder="Пароль">
                                                    </div>
                                                    <button 
                                                        type="submit" 
                                                        class="btn btn-default">
                                                        Войти
                                                    </button>
                                                  </form>
                                            </td>
                                        </tr>
                                        <?php endif;?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-success">
                        <div class="panel-heading text-center">
                            <strong>Игроки</strong>
                        </div>
                        <div class="panel-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <th>Ник</th>
                                    <?php if(isset($players[0]['score'])):?>
                                    <th class="text-center" style="width: 100px;">Счет</th>
                                    <?php endif;?>
                                    <?php if(isset($players[0]['time'])):?>
                                    <th class="text-center" style="width: 100px;">Время</th>
                                    <?php endif;?>
                                </thead>
                                <tbody id="players">
                                    <?php foreach($players as $player):?>
                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars($player['name'], ENT_QUOTES)?>
                                        </td>
                                        <?php if(isset($player['score'])):?>
                                        <td class="text-center">
                                            <?php echo intval($player['score'])?>
                                        </td>
                                        <?php endif;?>
                                        <?php if(isset($player['time'])):?>
                                        <td class="text-center">
                                            <?php echo $player['time']?>
                                        </td>
                                        <?php endif;?>
                                    </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <script>
                        $(function(){
                            <?php if($auth):?>
                            $(document).on("click", ".serverAction", function(){
                                var action = $(this).attr("id");
                                if(!confirm("Уверены?")) {
                                    return false;
                                }
                                $(".serverAction").attr("disabled", true);
                                $.post("",{"action": action},function() {getInfo();});
                                return false;
                            });
                            $(document).on("click", ".changeMap", function(){
                                var map = $(this).data("map");
                                $(this).closest("div.btn-group").removeClass("open");
                                $.post("",{"map": map},function() {getInfo();});
                                return false;
                            });
                            $("#sendCommand").submit(function(){
                                var command = $("#command");
                                if(command.val()) {
                                    $.post(
                                        "",
                                        {"command": command.val()},
                                        function() {
                                            command.val("");
                                        }
                                    );
                                }
                                return false;
                            });
                            <?php endif;?>
                            function getResources() {
                                $.post(
                                    "",
                                    {
                                        "getResources": true
                                    },
                                    function(data) {
                                        if(data) {
                                            data = $.parseJSON(data);
                                            $(".progress-bar").removeClass("progress-bar-danger progress-bar-warningprogress-bar-info");
                                            $.each(data, function(key, val) {
                                                val = parseInt(val);
                                                var color = '';
                                                if(val >= 85) {
                                                    color = 'progress-bar-danger';
                                                } else if(val >= 65) {
                                                    color = 'progress-bar-warning';
                                                } else if(val >= 20) {
                                                    color = 'progress-bar-info';
                                                }
                                                $("#" + key)
                                                        .attr("valuenow", val)
                                                        .css("width", val + "%")
                                                        .text(val + "%")
                                                        .addClass(color);
                                            });
                                        }
                                    }
                                );
                                return false;
                            }
                            function getInfo() {
                                $.post(
                                    "",
                                    {
                                        "getInfo": true
                                    },
                                    function(data) {
                                        if(data) {
                                            data = $.parseJSON(data);
                                            $.each(data, function(key, val) {
                                                $("#"+key).html(val);
                                            });
                                            if(data.online == 0) {
                                                $("#start").removeAttr("disabled");
                                                $("#stop").attr("disabled", true);
                                                $("#restart").attr("disabled", true);
                                            } else {
                                                $("#start").attr("disabled", true);
                                                $("#stop").removeAttr("disabled");
                                                $("#restart").removeAttr("disabled");
                                            }
                                            $("ul#maplist li").removeClass("active disabled");
                                            $("li[data-map="+data.map+"]").addClass("active disabled");
                                        }
                                    }
                                );
                                return false;
                            }
                            getInfo();
                            getResources();
                            setInterval(getInfo, 7000);
                            setInterval(getResources, 10000);
                        });
                    </script>
                    <?php endif;?>
                </div>
            </div>
        </div>
        
    </body>
</html>