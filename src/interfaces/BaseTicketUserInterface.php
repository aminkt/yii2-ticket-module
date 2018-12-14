<?php

namespace aminkt\ticket\interfaces;

/**
 * Interface BaseTicketUserInterface
 * 
 * This interface will show ability of ticket system users.
 * 
 * @package aminkt\ticket
 * 
 * @author Amin Keshavarz <amin@keshavarz.pro>
 * @author Mohammad Parvane
 */
interface BaseTicketUserInterface
{
    const TYPE_CUSTOMER_CARE = 'customer_care';
    const TYPE_CUSTOMER = 'customer';

    /**
     * Return User Id.
     * 
     * @return integer
     */
    function getId();

    /**
     * Return user full name.
     * 
     * @return string
     */
    function getName();

    /**
     * Return user email.
     * @return string|null
     */
    function getEmail();

    /**
     * Return user mobile.
     * 
     * @return string|null
     */
    function getMobile();

    /**
     * Return base type of user.
     * can be customer or customer_care.
     *
     * @return string
     */
    function getType();
}