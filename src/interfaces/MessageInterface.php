<?php

namespace aminkt\ticket\interfaces;

/**
 * Interface MessageInterface
 *
 * @package aminkt\ticket\interfaces
 *
 * @author Amin Keshavarz <ak_1596@yahoo.com>
 */
interface MessageInterface
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicket();

    /**
     * Return ticket message
     *
     * @return string
     */
    public function getMessage();

    /**
     * Return attachments models.
     *
     * @return array
     *
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public function getAttachments();

    /**
     * Return attachments url
     *
     * @return array
     *
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public function getAttachmentsUrl();

    /**
     * Return customer care user model.
     *
     * @return CustomerCareInterface|CustomerInterface
     *
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public function getUser();

    /**
     * Return true of message send bys customer care users.
     *
     * @return bool
     */
    public function getIsCustomerCareReply(): bool;

    /**
     * Send new message to current ticket.
     *
     * @param integer $id
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
    public static function sendNewMessage($id, string $message, array $attachments, CustomerCareInterface $customerCare = null): MessageInterface;
}