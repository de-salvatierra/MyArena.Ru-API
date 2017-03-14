<?php

namespace DeSalvatierra\MyArena\Api;

class Server
{
    /**
     * @var int Код статуса сервера. 0 - сервер выключен, 1 - Работает, 2 - запускается или завис
     */
    protected $online;

    /**
     * @var string Код игры (напр. cstrike или csgo). Может использоваться, например, для формирования пути к картинке игры или карт
     */
    protected $game;

    /**
     * @var string Код движка игрового сервера (напр. halflife или halflife2, etc)
     */
    protected $engine;

    /**
     * @var string Текущее название сервера
     */
    protected $name;

    /**
     * @var string Название текущей карты
     */
    protected $map;

    /**
     * @var string IP адрес
     */
    protected $ip;

    /**
     * @var int Порт
     */
    protected $port;

    /**
     * @var int Количество игроков сейчас на сервере
     */
    protected $currentPlayers;

    /**
     * @var int Максимально возможное количество игроков на сервере
     */
    protected $maxPlayers;

    /**
     * @var HostInfo Информация от хостинга
     */
    protected $hostInfo;

    /**
     * @var Player[]
     */
    protected $players;

    public function __construct(
        int $online,
        string $game,
        string $engine,
        string $name,
        string $map,
        string $ip,
        int $port,
        int $currentPlayers,
        int $maxPlayers,
        array $players,
        HostInfo $hostInfo
    )
    {
        $this->online = $online;
        $this->game = $game;
        $this->engine = $engine;
        $this->name = $name;
        $this->map = $map;
        $this->ip = $ip;
        $this->port = $port;
        $this->currentPlayers = $currentPlayers;
        $this->maxPlayers = $maxPlayers;
        $this->players = $players;
        $this->hostInfo = $hostInfo;
    }

    /**
     * @return int
     */
    public function getOnline(): int
    {
        return $this->online;
    }

    /**
     * @return string
     */
    public function getGame(): string
    {
        return $this->game;
    }

    /**
     * @return string
     */
    public function getEngine(): string
    {
        return $this->engine;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getMap(): string
    {
        return $this->map;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return int
     */
    public function getCurrentPlayers(): int
    {
        return $this->currentPlayers;
    }

    /**
     * @return int
     */
    public function getMaxPlayers(): int
    {
        return $this->maxPlayers;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        switch($this->online) {
            case 0:
                return 'Выключен';
            case 1:
                return 'Работает';
            case 2:
                return 'Запускается/Висит';
            default:
                return 'Состояние неизвестно';
        }
    }

    /**
     * @return HostInfo
     */
    public function getHostInfo(): HostInfo
    {
        return $this->hostInfo;
    }

    /**
     * Список игроков
     * @return Player[]
     */
    public function getPlayers(): array
    {
        return $this->players;
    }
}