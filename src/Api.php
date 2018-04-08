<?php

namespace DeSalvatierra\MyArena\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Api
{
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
    public function hasErrors()
    {
        return (bool)$this->errors;
    }

    /**
     * Получение информации от сервера
     * @return Server Возвращает ложь при ошибке, или массив с данными от сервера
     * @throws ApiException
     */
    public function status()
    {
        // Отправка команды status на АПИ
        $data =  $this->request('status');

        // Если ошибка при обработке команды, возвращаем ложь
        if (!$data) {
            throw new ApiException('Can\'t get status');
        }

        $players = [];
        // Информация об игроках
        if(!empty($data->data->p)) {
            foreach($data->data->p as $p) {
                $players[] = new Player(trim($p->name), $p->score ?? null, $p->time ?? null);
            }
        }
        $dateBlock = intval($data->server_dateblock);
        $server = new Server(
            intval($data->online),
            trim($data->data->s->game),
            trim($data->data->b->type),
            trim($data->data->s->name),
            trim($data->data->s->map),
            trim($data->data->b->ip),
            intval($data->data->b->c_port),
            intval($data->data->s->players),
            intval($data->data->s->playersmax),
            $players,
            new HostInfo(
                intval($data->server_id),
                trim($data->server_name),
                trim($data->server_address),
                intval($data->server_maxslots),
                trim($data->server_location),
                trim($data->server_type),
                intval($data->server_daystoblock),
                $dateBlock ? (new \DateTime())->setTimestamp($dateBlock) : null
            )
        );

        return $server;
    }

    /**
    * Запуск сервера
    * @return boolean
    */
    public function start()
    {
        return (bool)$this->request('start');
    }

    /**
    * Остановка сервера
    * @return boolean
    */
    public function stop()
    {
        return (bool)$this->request('stop');
    }

    /**
    * Перезапуск сервера
    * @return boolean
    */
    public function restart()
    {
        return (bool)$this->request('restart');
    }

    /**
    * Смена карты
    * @return boolean Если карты нет на сервере, вернет ложь
    */
    public function changeMap($map)
    {
        if (!in_array($map, $this->mapList())) {
            return false;
        }
        return (bool)$this->request('changelevel', array('map' => $map));
    }

    /**
    * Список карт
    * @return array
    */
    public function mapList()
    {
        $data = $this->request('getmaps');
        if (!isset($data->maps)) {
            return array();
        }
        sort($data->maps);
        return $data->maps;
    }

    /**
    * Кастом команда
    * @return boolean
    */
    public function command($command)
    {
        $command = str_replace(' ', '%20', $command);
        return (bool)$this->request('consolecmd', array('cmd' => $command));
    }

    /**
    * Получение ресурсов
    * @return array
    */
    public function resources()
    {
        $data = $this->request('getresources');
        $info = array();
        foreach($data as $key => $val) {
            if ($key == 'status') {
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
     *
     * @param mixed $default Задает что вернуть при неуспешном ответе
     *
     * @return mixed
     */
    protected function request($query, Array $extra = array(), $default = null)
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
        $responseData = json_decode($response->getBody());
        if(json_last_error() !== JSON_ERROR_NONE) {
            return $default;
        }
        if (strtolower($responseData->status) !== 'ok') {
            if(isset($responseData->message) && $responseData->message) {
                $this->errors[] = $responseData->message;
            }
            return $default;
        }
        return $responseData;
    }
}
