<?php
/**
 * Created by PhpStorm.
 * User: tomasz
 * Date: 2018-05-26
 * Time: 15:59
 */

namespace AppBundle\Command;

use AppBundle\Entity\Auction;
use AppBundle\EventDispatcher\AuctionEvent;
use AppBundle\EventDispatcher\Events;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ExpireAuctionCommand extends Command
{
    /**
     * @var
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * (@inheritdoc)
     */
    protected function configure()
    {
        $this
            ->setName("app:expire_auction")
            ->setDescription("Command to close the auction");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $auctions = $this->entityManager
            ->getRepository(Auction::class)
            ->findActiveExpired();

        $output->writeln(
            sprintf("Found <info>%d</info> auction(s) to closed",
                count($auctions))
        );

        foreach ($auctions as $auction) {
            $auction->setStatus(Auction::STATUS_FINISHED);
            $this->entityManager->persist($auction);

            $this->eventDispatcher->dispatch(Events::AUCTION_EXPIRE, new AuctionEvent($auction));
        }

        $this->entityManager->flush();

        $output->writeln("Auctions updated!");
    }
}
