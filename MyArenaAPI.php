<?php
/**
 * Класс для работы с API игрового хостинга MyArena.Ru
 * @author Александр Урих <urichalex@mail.ru>
 * @license Проприетарное программное обеспечение
 * @version 1.0
 */

class MyArenaAPI {
	/**
	 * @var string Токен управления сервером
	 */
	protected $token;
	
	/**
	 * @var string Сформированный URL API
	 */
	protected $url;
    
    /**
     * Ошибки
     * @var type 
     */
    private $errors = array();
	
	/**
	 * Конструктор класса
	 * @param string $token Токен управления сервером
	 */
	public function __construct($token)
	{
		$this->token = $token;
		$this->url = 'https://www.myarena.ru/api.php?token='.$this->token.'&query=';
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
     * 
     * @return boolean
     */
    public function hasErrors() {
        return empty($this->errors);
    }
    
	/**
	 * Получение информации от сервера
	 * @return boolean|array Возвращает ложь при ошибке, или массив с данными от сервера
	 */
	public function status()
	{
		// Отправка команды status на АПИ
		$data =  $this->cmd('status');
		
		// Если ошибка при обработке команды, возвращаем ложь
		if (!$data) {
            return false;
        }

        // Формируем массив
		$info = array(
            'online'        => $data->online !== 0,
            'game'          => $data->data->s->game,
            'engine'        => $data->data->b->type,
            'name'          => $data->data->s->name,
            'map'           => $data->data->s->map,
            'ip'            => $data->data->b->ip,
            'port'          => $data->data->b->c_port,
            'curPlayers'    => intval($data->data->s->players),
            'maxPlayers'    => intval($data->data->s->playersmax),
            'playersInfo'   => array(),
            'hostInfo'      => array(
                'serverId'      => $data->server_id,
                'game'          => $data->server_name,
                'address'       => $data->server_address,
                'slots'         => $data->server_maxslots,
                'location'      => $data->server_location,
                'tariff'        => $data->server_type,
                'blockDate'     => $data->server_dateblock,
                'days'          => $data->server_daystoblock,
            )
        );
		switch($data->online) {
			case 0:
				$info['status'] = 'Выключен';
				break;
			case 1:
				$info['status'] = 'Работает';
				break;
			case 2:
				$info['status'] = 'Запускается/Висит';
				break;
			default:
				$info['status'] = 'Состояние неизвестно';
		}
        
		if(isset($data->data->e) && !empty($data->data->e)) {
			foreach($data->data->e as $key => $val) {
				$info[$key] = $val;
			}
		}
		
		// Информация об игроках
		if(!empty($data->data->p)) {
			foreach($data->data->p as $p) {
                $player = array();
				$player['name'] = $p->name;
				if (isset($p->score)) {
                    $player['score'] = $p->score;
                }
                if (isset($p->score)) {
                    $player['time'] = $p->time;
                }
                $info['playersInfo'][] = $player;
            }
		}
		
		return $info;
	}
	
	/**
	 * Запуск сервера
	 * @return boolean
	 */
	public function start()
	{
		return (bool)$this->cmd('start');
	}
	
	/**
	 * Остановка сервера
	 * @return boolean
	 */
	public function stop()
	{
		return (bool)$this->cmd('stop');
	}
	
	/**
	 * Перезапуск сервера
	 * @return boolean
	 */
	public function restart()
	{
		return (bool)$this->cmd('restart');
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
        return (bool)$this->cmd('changelevel', array('map' => $map));
	}
	
	/**
	 * Список карт
	 * @return array
	 */
	public function mapList()
	{
		$data = $this->cmd('getmaps');
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
		return (bool)$this->cmd('consolecmd', array('cmd' => $command));
	}
	
	/**
	 * Получение ресурсов
	 * @return boolean
	 */
	public function resources() 
	{
		$data = $this->cmd('getresources');
		$info = array();
		foreach($data as $key => $val) {
			if($key === 'status') continue;
			$info[$key] = $val;
		}
		return $info;
	}

	/**
	 * Формировка и отправка запроса
	 * @return boolean
	 */
	protected function cmd($cmd, Array $extra = array())
    {
		if(!empty($extra)) {
			$e = array();
			foreach($extra as $key => $val) {
				$e[] = $key . '=' . $val;
			}
		}
		$url = $this->url . $cmd;
        if(!empty($e)) {
            $url .= implode('&', $e);
        }
		$get = file_get_contents($url);
		$json = json_decode($get);
		if (strtolower($json->status) !== 'ok') {
            $this->errors[] = !empty($json->message) ? $json->message : '';
            return false;
        }
        return $json;
	}
}
