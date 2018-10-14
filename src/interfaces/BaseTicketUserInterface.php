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
}