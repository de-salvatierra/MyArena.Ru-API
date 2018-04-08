<?php

declare(strict_types=1);

namespace DeSalvatierra\MyArena\Api;

/**
 * Class Player
 * @package DeSalvatierra\MyArena\Api
 */
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
     * @param string $name
     * @return Player
     */
    public function setName(?string $name): Player
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param int $score
     * @return Player
     */
    public function setScore(int $score): Player
    {
        $this->score = $score;
        return $this;
    }

    /**
     * @param string $time
     * @return Player
     */
    public function setTime(?string $time): Player
    {
        if(!$time) {
            $this->setDefaultTime();
            return $this;
        }
        [$hours, $minutes, $seconds] = explode(':', $time);
        if(intval($hours) < 0) {
            $hours = '00';
        }
        try {
            $this->time = new \DateInterval("PT{$hours}H{$minutes}M{$seconds}S");
        } catch(\Exception $e) {
            $this->setDefaultTime();
        }
        return $this;
    }

    private function setDefaultTime()
    {
        $this->time = new \DateInterval("PT00H00M00S");
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
}