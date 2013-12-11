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
	 * Конструктор класса
	 * @param string $token Токен управления сервером
	 */
	public function __construct($token)
	{
		$this->token = $token;
		$this->url = 'https://www.myarena.ru/api.php?token='.$this->token.'&query=';
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
		if(!$data)
			return FALSE;
		
		// Формируем массив
		$info = array();
		$info['online']		= $data->status == 1;
		$info['game']		= $data->data->s->game;
		$info['name']		= $this->safe($data->data->s->name);
		$info['map']		= $data->data->s->map;
		$info['curPlayers']	= intval($data->data->s->players);
		$info['maxPlayers']	= intval($data->data->s->playersmax);
		$info['playersInfo']= array();
		
		// Информация об игроках
		if(isset($data->data->p) && !empty($data->data->p))
		{
			foreach($data->data->p as $p)
			{
				$info['playersInfo'][]['name'] = $this->safe($p->name);
				if(isset($p->score))
					$info['playersInfo'][]['score'] = $this->safe($p->score);
				if(isset($p->score))
					$info['playersInfo'][]['time'] = $this->safe($p->time);
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
		if(!in_array($map, $this->mapList()))
			return FALSE;
		return (bool)$this->cmd('changelevel', array('map' => $map));
	}
	
	/**
	 * Список карт
	 * @return array
	 */
	public function mapList()
	{
		$data = $this->cmd('getmaps');
		if(!isset($data->maps))
			return array();
		return $data->maps;
	}
	
	/**
	 * Кастом команда
	 * @return boolean
	 */
	public function command($command) {
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
		foreach($data as $key => $val)
		{
			if($key === 'status') continue;
			$info[$key] = $val;
		}
		return $info;
	}

	/**
	 * Вырезка лишнего
	 * @param string $text
	 * @return string
	 */
	private function safe($text)
	{
		return htmlspecialchars($text, ENT_QUOTES);
	}

	/**
	 * Формировка и отправка запроса
	 * @return boolean
	 */
	private function cmd($cmd, $extra = FALSE) {
		if($extra && is_array($extra))
		{
			$e = array();
			foreach($extra as $key => $val)
			{
				$e[] = $key . '=' . $val;
			}
		}
		$url = $this->url . $cmd . (isset($e) && !empty($e) ? '&'.implode('&', $e) : '');
		$get = file_get_contents($url);
		$json = json_decode($get);

		if($json->status !== 'OK')
			return FALSE;
		return $json;
	}
}
