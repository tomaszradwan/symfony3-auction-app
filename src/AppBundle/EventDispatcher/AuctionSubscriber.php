<?php
/**
 * Created by PhpStorm.
 * User: tomasz
 * Date: 2018-06-01
 * Time: 19:57
 */

namespace AppBundle\EventDispatcher;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AuctionSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    public $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::AUCTION_ADD => "log",
            Events::AUCTION_EXPIRE => "logExpire"
        ];
    }

    /**
     * @param AuctionEvent $event
     */
    public function log(AuctionEvent $event)
    {
        $auction = $event->getAuction();

        $this->logger->info("Auction {$auction->getId()} added.");
    }

    /**
     * @param AuctionEvent $event
     */
    public function logExpire(AuctionEvent $event)
    {
        $auction = $event->getAuction();

        $this->logger->info("Auction {$auction->getId()} expired.");
    }
}
