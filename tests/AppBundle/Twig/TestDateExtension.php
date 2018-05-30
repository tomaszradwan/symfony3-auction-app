<?php
/**
 * Created by PhpStorm.
 * User: tomasz
 * Date: 2018-06-04
 * Time: 20:04
 */

namespace Tests\AppBundle\Twig;

use AppBundle\Twig\DateExtension;

class TestDateExtension extends \PHPUnit_Framework_TestCase
{
    /**
     * @inheritdoc
     */
    public function testGetStyle()
    {
        $dateExtension = new DateExtension();

        $this->assertEquals(
            "panel-default",
            $dateExtension->auctionStyle(new \DateTime("+2 days"))
        );

        $this->assertEquals(
            "panel-danger",
            $dateExtension->auctionStyle(new \DateTime("+20 minutes"))
        );
    }
}