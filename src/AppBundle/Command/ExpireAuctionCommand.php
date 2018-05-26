<?php
/**
 * Created by PhpStorm.
 * User: tomasz
 * Date: 2018-05-26
 * Time: 15:59
 */

namespace AppBundle\Command;

use AppBundle\Entity\Auction;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExpireAuctionCommand extends ContainerAwareCommand
{
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
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this
            ->getContainer()
            ->get("doctrine.orm.entity_manager");

        $auctions = $em
            ->getRepository(Auction::class)
            ->findActiveExpired();

        $output->writeln(sprintf("Found <info>%d</info> auction(s) to closed", count($auctions)));

        foreach ($auctions as $auction) {
            $auction->setStatus(Auction::STATUS_FINISHED);
            $em->persist($auction);
        }

        $em->flush();

        $output->writeln("Auctions updated!");

    }
}