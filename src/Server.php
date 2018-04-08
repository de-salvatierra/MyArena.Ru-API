<?php

declare(strict_types=1);

namespace DeSalvatierra\MyArena\Api;

/**
 * Class Server
 * @package DeSalvatierra\MyArena\Api
 */
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

    /**
     * @param int $online
     * @return Server
     */
    public function setOnline(int $online): Server
    {
        $this->online = $online;
        return $this;
    }

    /**
     * @param string $game
     * @return Server
     */
    public function setGame(?string $game): Server
    {
        $this->game = $game;
        return $this;
    }

    /**
     * @param string $engine
     * @return Server
     */
    public function setEngine(?string $engine): Server
    {
        $this->engine = $engine;
        return $this;
    }

    /**
     * @param string $name
     * @return Server
     */
    public function setName(?string $name): Server
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $map
     * @return Server
     */
    public function setMap(?string $map): Server
    {
        $this->map = $map;
        return $this;
    }

    /**
     * @param string $ip
     * @return Server
     */
    public function setIp(?string $ip): Server
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @param int $port
     * @return Server
     */
    public function setPort(int $port): Server
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @param int $currentPlayers
     * @return Server
     */
    public function setCurrentPlayers(int $currentPlayers): Server
    {
        $this->currentPlayers = $currentPlayers;
        return $this;
    }

    /**
     * @param int $maxPlayers
     * @return Server
     */
    public function setMaxPlayers(int $maxPlayers): Server
    {
        $this->maxPlayers = $maxPlayers;
        return $this;
    }

    /**
     * @param HostInfo $hostInfo
     * @return Server
     */
    public function setHostInfo(HostInfo $hostInfo): Server
    {
        $this->hostInfo = $hostInfo;
        return $this;
    }

    /**
     * @param Player[] $players
     * @return Server
     */
    public function setPlayers(array $players): Server
    {
        $this->players = $players;
        return $this;
    }
}