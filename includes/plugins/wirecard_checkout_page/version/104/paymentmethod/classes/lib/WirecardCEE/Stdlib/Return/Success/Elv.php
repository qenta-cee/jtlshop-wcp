<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

/**
 * @name WirecardCEE_Stdlib_Return_Success_Elv
 * @category WirecardCEE
 * @package WirecardCEE_Stdlib
 * @subpackage Return_Success
 * @version 3.0.0
 * @abstract
 */

/**
 * Container for returned ELV payment data.
 */
class WirecardCEE_Stdlib_Return_Success_Elv extends WirecardCEE_Stdlib_Return_Success
{
    /**
     * getter for the return parameter mandateId
     *
     * @return string
     */
    public function getMandateId()
    {
        return (string)$this->mandateId;
    }

    /**
     * getter for the return parameter mandateSignatureDate
     *
     * @return string
     */
    public function getMandateSignatureDate()
    {
        return (string)$this->mandateSignatureDate;
    }

    /**
     * getter for the return parameter creditorId
     *
     * @return string
     */
    public function getCreditorId()
    {
        return (string)$this->creditorId;
    }

    /**
     * getter for the return parameter dueDate
     *
     * @return string
     */
    public function getDueDate()
    {
        return (string)$this->dueDate;
    }

} 