<?php

declare(strict_types=1);

namespace DeSalvatierra\MyArena\Api;

use DateTime;

/**
 * Class HostInfo
 * @package DeSalvatierra\MyArena\Api
 */
class HostInfo
{
    /**
     * @var int ID сервера
     */
    protected $id;

    /**
     * @var string Название игры
     */
    protected $gameName;

    /**
     * @var string Полный адрес сервера с портом
     */
    protected $address;

    /**
     * @var int количество слотов сервера по тарифу
     */
    protected $slots;

    /**
     * @var string Название локации, на которой расположен сервер
     */
    protected $location;

    /**
     * @var string Название тарифа
     */
    protected $tariff;

    /**
     * @var DateTime|null Дата блокировки сервера. Если null - значит бесплатный
     */
    protected $blockDate;

    /**
     * @var int Количество дней, оставшееся до конца аренды сервера
     */
    protected $days;

    /**
     * @return int ID сервера на хостинге
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string Полное название игры
     */
    public function getGameName(): string
    {
        return $this->gameName;
    }

    /**
     * @return string Полный адрес с портом
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return int Количество слотов по тарифу
     */
    public function getSlots(): int
    {
        return $this->slots;
    }

    /**
     * @return string Имя локации
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @return string Название тарифа
     */
    public function getTariff(): string
    {
        return $this->tariff;
    }

    /**
     * @return DateTime|null Дата блокировки. Если null - значит сервер бесплатный
     */
    public function getBlockDate()
    {
        return $this->blockDate;
    }

    /**
     * @return int Остаток дней аренды. Если 0 - значитлибо истекает сегодня, либо бесплатный
     */
    public function getDays(): int
    {
        return $this->days;
    }

    /**
     * @param int $id
     * @return HostInfo
     */
    public function setId(int $id): HostInfo
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $gameName
     * @return HostInfo
     */
    public function setGameName(?string $gameName): HostInfo
    {
        $this->gameName = $gameName;
        return $this;
    }

    /**
     * @param string $address
     * @return HostInfo
     */
    public function setAddress(?string $address): HostInfo
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @param int $slots
     * @return HostInfo
     */
    public function setSlots(int $slots): HostInfo
    {
        $this->slots = $slots;
        return $this;
    }

    /**
     * @param string $location
     * @return HostInfo
     */
    public function setLocation(?string $location): HostInfo
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @param string $tariff
     * @return HostInfo
     */
    public function setTariff(?string $tariff): HostInfo
    {
        $this->tariff = $tariff;
        return $this;
    }

    /**
     * @param DateTime|null $blockDate
     * @return HostInfo
     */
    public function setBlockDate(?DateTime $blockDate): HostInfo
    {
        $this->blockDate = $blockDate;
        return $this;
    }

    /**
     * @param int $days
     * @return HostInfo
     */
    public function setDays(int $days): HostInfo
    {
        $this->days = $days;
        return $this;
    }
}