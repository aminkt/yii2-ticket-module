<?php

namespace aminkt\ticket\interfaces;

use yii\data\ActiveDataProvider;

/**
 * Interface TicketInterface
 *
 * @package aminkt\ticket\interfaces
 *
 * @author Amin Keshavarz <ak_1596@yahoo.com>
 */
interface TicketInterface
{

    const STATUS_NOT_REPLIED = 1;
    const STATUS_REPLIED = 2;
    const STATUS_CLOSED = 3;
    const STATUS_BLOCKED = 4;

    /**
     * Create a relation between message and ticket models.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTicketMessages(): \yii\db\ActiveQuery;

    /**
     * Create a relation between department and ticket models.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment(): \yii\db\ActiveQuery;

    /**
     * Change current ticket department.
     *
     * @param DepartmentInterface   $department
     *
     * @return void
     */
    public function setDepartment($department);

    /**
     * Get ticket subject.
     *
     * @return string
     */
    public function getSubject() : string;

    /**
     * Get name of user that created current ticket.
     *
     * @return string
     */
    public function getUserName() : string;

    /**
     * Return mobile number of user that created current ticket.
     *
     * @return string
     */
    function getUserMobile() : string;

    /**
     * Return email number of user that created current ticket.
     *
     * @return string
     */
    function getUserEmail() : string;

    /**
     * Return true if customer model not available and false if available.
     *
     * @return bool
     */
    function getIsGuestTicket() : bool;

    /**
     * Return model of user that created current ticket.
     *
     * @return CustomerInterface
     */
    function getCustomer() : CustomerInterface;

    /**
     * Create new ticket.
     *
     * @param string $subject
     * @param CustomerInterface $customer
     * @param Department $department
     *
     * @return Ticket
     *
     * @author Mohammad Parvaneh <mohammad.pvn1375@gmail.com>
     */
    public static function createNewTicket(string $subject, CustomerInterface $customer, Department $department): TicketInterface;

    /**
     * Send new message to current ticket.
     *
     * @param string $message
     * @param array $attachments
     * @param CustomerCareInterface|null $customerCare
     *
     * @throws \RuntimeException    When cant create ticket.
     *
     * @return TicketMessage
     *
     * @author Mohammad Parvaneh <mohammad.pvn1375@gmail.com>
     */
    public function sendNewMessage(string $message, array $attachments, CustomerCareInterface $customerCare = null): MessageInterface;

    /**
     * Close current ticket.
     *
     * @return $this
     */
    public function closeTicket(): TicketInterface;

    /**
     * Open current ticket.
     *
     * @return $this
     */
    public function openTicket(): TicketInterface;

    /**
     * Get customer care tickets by department
     *
     * @param $userId
     *
     * @return ActiveDataProvider
     *
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public static function getCustomerCareTickets($userId): ActiveDataProvider;

    /**
     * Returns status label
     *
     * @return string
     *
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public function getStatusLabel(): string;

    /**
     * Set ticket status
     *
     * @param $status
     *
     * @return TicketInterface
     *
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public function setStatus($status): TicketInterface;
}