<?php
/**
 * Created by PhpStorm.
 * User: tomasz
 * Date: 2018-05-31
 * Time: 20:51
 */

namespace AppBundle\EventDispatcher;

use AppBundle\Entity\Auction;
use Symfony\Component\EventDispatcher\Event;

class AuctionEvent extends Event
{
    /**
     * @var Auction
     */
    private $auction;

    /**
     * AuctionEvent constructor.
     * @param Auction $auction
     */
    public function __construct(Auction $auction)
    {
        $this->auction = $auction;
    }

    /**
     * @return Auction
     */
    public function getAuction()
    {
        return $this->auction;
    }
}
