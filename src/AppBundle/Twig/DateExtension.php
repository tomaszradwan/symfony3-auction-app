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
     * @param \DateTime $expiresAt
     * @return string
     */
    public function expireDate(\DateTime $expiresAt)
    {
        $currentDate = new \DateTime();

        if ($expiresAt < new \DateTime('- 7 days')) {
            return $expiresAt->format("Y-m-d H:i");
        }

        if ($expiresAt->diff($currentDate)->format('%d') > 0) {
            return " in "
                . ($expiresAt->format('d') - $currentDate->format('d'))
                . " days";
        }

        return "in " . $expiresAt->diff($currentDate)->format('%h hours %i minutes');
    }
}
