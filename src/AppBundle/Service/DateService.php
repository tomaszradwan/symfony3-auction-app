<?php
/**
 * Created by PhpStorm.
 * User: tomasz
 * Date: 2018-05-27
 * Time: 14:29
 */

namespace AppBundle\Service;


class DateService
{
    /**
     * @param \DateTime $date
     * @return string
     */
    public function getDate(\DateTime $date)
    {
        return $date->format("d");
    }
}