<?php
/**
 * Created by PhpStorm.
 * User: tomasz
 * Date: 2018-05-19
 * Time: 09:07
 */

namespace AppBundle\Twig;

class DateExtension extends \Twig_Extension
{
    /**
     * @return array|\Twig_SimpleFilter[]
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter("expireDate", [$this, "expireDate"])
        ];
    }

    /**
     * @return array|\Twig_SimpleFunction[]
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction("auctionStyle", [$this, "auctionStyle"])
        ];
    }

    /**
     * @param \DateTime $expiresAt
     * @return string
     */
    public function expireDate(\DateTime $expiresAt)
    {
        $currentDate = new \DateTime();

        if (($expiresAt < $currentDate)
            || ($expiresAt < new \DateTime('-7 days'))) {
            return $expiresAt->format("Y-m-d H:i");
        }

        if ($expiresAt->diff($currentDate)->format('%d') > 0) {
            return " in "
                . $expiresAt->diff($currentDate)->format('%d')
                . " day(s)";
        }

        return "in " . $currentDate->diff($expiresAt)->format('%h hour(s) %i minutes');
    }

    /**
     * @param \DateTime $expiresAt
     * @return string
     */
    public function auctionStyle(\DateTime $expiresAt)
    {
        if ($this->dateIsInBetween(new \DateTime(), new \DateTime('+1 day'), $expiresAt)) {
            return "panel-danger";
        }

        return "panel-default";
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param \DateTime $userDate
     * @return bool
     */
    public function dateIsInBetween(\DateTime $from, \DateTime $to, \DateTime $userDate)
    {
        return ($userDate->getTimestamp() > $from->getTimestamp())
                && ($userDate->getTimestamp() < $to->getTimestamp()) ? true : false;
    }
}
