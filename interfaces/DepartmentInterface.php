<?php

namespace aminkt\ticket\interfaces;

use yii\db\ActiveQuery;

/**
 * Interface DepartmentInterface
 *
 * @package aminkt\ticket\interfaces
 *
 * @author Amin Keshavarz <ak_1596@yahoo.com>
 */
interface DepartmentInterface
{
    const STATUS_ACTIVE = 1;
    const STATUS_DEACTIVE = 2;

    /**
     * Return department id.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Return tickets as active query.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTickets(): ActiveQuery;

    /**
     * Returns status label
     *
     * @return string
     *
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public function getStatusLabel(): string;

    /**
     * Assign an user to current department.
     *
     * @param BaseTicketUserInterface $user User object.
     *
     * @return boolean  User assigned or not.
     */
    public function assign($user): bool;

    /**
     * Remove user from current department.
     *
     * @param BaseTicketUserInterface $user User object.
     *
     * @return boolean  User removed or not.
     */
    public function unAssign($user): bool;
}