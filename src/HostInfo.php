<?php

/**
 * Информация о сервере на основании данных хостинга
 */

namespace DeSalvatierra\MyArena\Api;

use DateTime;

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
     * HostInfo constructor.
     *
     * @param int $id ID сервера
     * @param string $gameName Название игры
     * @param string $address Полный адрес сервера с портом
     * @param int $slots количество слотов сервера по тарифу
     * @param string $location Название локации, на которой расположен сервер
     * @param string $tariff Название тарифа
     * @param int $days Количество дней, оставшееся до конца аренды сервера
     * @param DateTime|null $blockDate Дата блокировки сервера. Если null - значит бесплатный
     */
    public function __construct(
        int $id,
        string $gameName,
        string $address,
        int $slots,
        string $location,
        string $tariff,
        int $days,
        $blockDate
    )
    {
        $this->id = $id;
        $this->gameName = $gameName;
        $this->address = $address;
        $this->slots = $slots;
        $this->location = $location;
        $this->tariff = $tariff;
        $this->days = $days;
        $this->blockDate = $blockDate;
    }

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
}