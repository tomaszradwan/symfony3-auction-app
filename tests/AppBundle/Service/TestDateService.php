<?php
/**
 * Created by PhpStorm.
 * User: tomasz
 * Date: 2018-06-04
 * Time: 19:40
 */

namespace Tests\AppBundle\Service;

use AppBundle\Service\DateService;

class TestDateService extends \PHPUnit_Framework_TestCase
{
    /**
     * @inheritdoc
     */
    public function testGetDay()
    {
        $dateService = new DateService();

        $this->assertEquals(
            19,
            $dateService->getDate(new \DateTime("2018-06-19"),
                "It should be day = 19.")
        );

        $this->assertEquals(
            1,
            $dateService->getDate(new \DateTime("2018-01-01"))
        );
    }
}