<?php

namespace DeSalvatierra\MyArena\Api;

class Player
{
    /**
     * @var string Ник игрока
     */
    protected $name;

    /**
     * @var integer|null Счет
     */
    protected $score;

    /**
     * @var \DateInterval|null Время игрока на сервере
     */
    protected $time;

    /**
     * Player constructor.
     *
     * @param string $name Ник игрока
     * @param int|null $score Счет
     * @param null|string $time Время
     */
    public function __construct(string $name, ?int $score, ?string $time)
    {
        $this->name = $name;
        $this->score = $score;
        if($time) {
            $this->setTime($time);
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int|null
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @return \DateInterval|null
     */
    public function getTime()
    {
        return $this->time;
    }

    private function setTime(string $time): void
    {
        [$hours, $minutes, $seconds] = explode(':', $time);
        if(intval($hours) < 0) {
            $hours = '00';
        }
        $this->time = new \DateInterval("PT{$hours}H{$minutes}M{$seconds}S");
    }
}