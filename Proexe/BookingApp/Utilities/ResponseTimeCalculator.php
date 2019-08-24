<?php
/**
 * Date: 09/08/2018
 * Time: 00:16
 * @author Artur Bartczak <artur.bartczak@code4.pl>
 */

namespace Proexe\BookingApp\Utilities;


use Proexe\BookingApp\Offices\Interfaces\ResponseTimeCalculatorInterface;

class ResponseTimeCalculator implements ResponseTimeCalculatorInterface
{
    public function calculate($bookingDateTime, $responseDateTime, $officeHours)
    {
        
    }
}