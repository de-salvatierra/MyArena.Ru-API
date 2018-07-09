<?php

declare(strict_types=1);

namespace DeSalvatierra\MyArena\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Class Api
 * @package DeSalvatierra\MyArena\Api
 */
class Api
{
    private const COMMAND_STOP = 'stop';
    private const COMMAND_START = 'start';
    private const COMMAND_STATUS = 'status';
    private const COMMAND_RESTART = 'restart';
    private const COMMAND_GET_MAPS = 'getmaps';
    private const COMMAND_CHANGELEVEL = 'changelevel';
    private const COMMAND_GET_RESPORCES = 'getresources';
    private const COMMAND_CONSOLE_COMMAND = 'consolecmd';

    /**
    * @var string Токен управления сервером
    */
    protected $token;

    /**
     * Ошибки
     * @var array
     */
    private $errors = [];

    private $url = 'https://www.myarena.ru';

    /**
    * Конструктор класса
    * @param string $token Токен управления сервером
    */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Ошибки
     * @param boolean $string Возвращать массивом или строкой
     * @param string $separator Если отдавать строкой, то чем разделять массив
     * @return mixed ошибки в массиве или строкой
     */
    public function getErrors($string = false, $separator = PHP_EOL)
    {
        return $string ? implode($separator, $this->errors) : $this->errors;
    }

    /**
     * Проверяет, есть ли ошибки
     * @return boolean
     */
    public function hasErrors(): bool
    {
        return (bool)$this->errors;
    }

    /**
     * Получение информации от сервера
     * @return Server
     * @throws ApiException
     */
    public function status(): Server
    {
        // Отправка команды status на АПИ
        $data =  $this->request(self::COMMAND_STATUS);

        // Если ошибка при обработке команды, возвращаем ложь
        if (!$data) {
            throw new ApiException('Can\'t get status');
        }

        $players = [];
        // Информация об игроках
        if(!empty($data->data->p) && is_array($data->data->p)) {
            foreach($data->data->p as $p) {
                $player = new Player();
                $player->setName($p->name)
                    ->setTime($p->time ?: null);
                if(is_numeric($p->score)) {
                    $player->setScore($p->score);
                }
                $players[] = $player;
            }
        }

        $hostInfo = new HostInfo();
        $hostInfo->setId((int)$data->server_id)
            ->setGameName($data->server_name)
            ->setAddress($data->server_address)
            ->setSlots((int)$data->server_maxslots)
            ->setLocation($data->server_location)
            ->setTariff($data->server_type)
            ->setDays((int)$data->server_daystoblock);

        if($data->server_dateblock) {
            $hostInfo->setBlockDate((new \DateTime())->setTimestamp((int)$data->server_dateblock));
        }

        $server = new Server();
        $server->setOnline((int)$data->online)
            ->setGame($data->data->s->game)
            ->setEngine($data->data->b->type)
            ->setName($data->data->s->name)
            ->setMap($data->data->s->map)
            ->setIp($data->data->b->ip)
            ->setPort((int)$data->data->b->c_port)
            ->setCurrentPlayers((int)$data->data->s->players)
            ->setMaxPlayers((int)$data->data->s->playersmax)
            ->setPlayers($players)
            ->setHostInfo($hostInfo);
        return $server;
    }

    /**
    * Запуск сервера
    * @return boolean
    */
    public function start(): bool
    {
        return (bool)$this->request(self::COMMAND_START);
    }

    /**
    * Остановка сервера
    * @return boolean
    */
    public function stop(): bool
    {
        return (bool)$this->request(self::COMMAND_STOP);
    }

    /**
    * Перезапуск сервера
    * @return boolean
    */
    public function restart(): bool
    {
        return (bool)$this->request(self::COMMAND_RESTART);
    }

    /**
     * Смена карты
     * @param string $map
     * @return boolean Если карты нет на сервере, вернет ложь
     */
    public function changeMap(string $map): bool
    {
        if (!\in_array($map, $this->mapList(), true)) {
            return false;
        }
        return (bool)$this->request(self::COMMAND_CHANGELEVEL, array('map' => $map));
    }

    /**
    * Список карт
    * @return array
    */
    public function mapList(): array
    {
        $data = $this->request(self::COMMAND_GET_MAPS);
        if (!isset($data->maps)) {
            return array();
        }
        sort($data->maps);
        return $data->maps;
    }

    /**
     * Кастом команда
     * @param string $command
     * @return boolean
     */
    public function command(string $command): bool
    {
        $command = str_replace(' ', '%20', $command);
        return (bool)$this->request(self::COMMAND_CONSOLE_COMMAND, array('cmd' => $command));
    }

    /**
    * Получение ресурсов
    * @return array
    */
    public function resources(): array
    {
        /** @var array $data */
        $data = $this->request(self::COMMAND_GET_RESPORCES, [], []);
        $info = array();
        foreach($data as $key => $val) {
            if ($key === 'status') {
                continue;
            }
            $info[$key] = $val;
        }
        return $info;
    }

    /**
     * Формировка и отправка запроса
     *
     * @param string $query
     * @param array $extra Дополнительные параметры запроса
     * @param mixed $default Задает что вернуть при неуспешном ответе
     *
     * @return mixed
     */
    protected function request(string $query, Array $extra = [], $default = null)
    {
        $params = [
            'token' => $this->token,
            'query' => $query
        ];

        if(!empty($extra)) {
            $params = array_merge($params, $extra);
        }

        try {
            $client = new Client([
                'base_uri' => $this->url
            ]);
            $response = $client->get('/api.php', [
                'query' => $params
            ]);
        } catch(RequestException $e) {
            return $default;
        }
        $responseData = json_decode((string)$response->getBody());
        if(json_last_error() !== JSON_ERROR_NONE) {
            return $default;
        }
        if (strtolower($responseData->status) !== 'ok') {
            if(!empty($responseData->message)) {
                $this->errors[] = $responseData->message;
            }
            return $default;
        }
        return $responseData;
    }
}
