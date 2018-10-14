<?php

namespace aminkt\ticket\components;

use yii\base\Event;

/**
 * Class TicketEvent
 * @author Mohammad Parvaneh <mohammad.pvn1375@gmail.com>
 * @package aminkt\ticket\components
 */
class TicketEvent extends Event
{
    public $status;
    public $userName;
    public $userMobile;
    public $userEmail;
    public $ticketSubject;

    public function init()
    {
        parent::init();
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param mixed $userName
     */
    public function setUserName($userName): void
    {
        $this->userName = $userName;
    }

    /**
     * @return mixed
     */
    public function getUserMobile()
    {
        return $this->userMobile;
    }

    /**
     * @param mixed $userMobile
     */
    public function setUserMobile($userMobile): void
    {
        $this->userMobile = $userMobile;
    }

    /**
     * @return mixed
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    /**
     * @param mixed $userEmail
     */
    public function setUserEmail($userEmail): void
    {
        $this->userEmail = $userEmail;
    }

    /**
     * @return mixed
     */
    public function getTicketSubject()
    {
        return $this->ticketSubject;
    }

    /**
     * @param mixed $ticketSubject
     */
    public function setTicketSubject($ticketSubject): void
    {
        $this->ticketSubject = $ticketSubject;
    }


}