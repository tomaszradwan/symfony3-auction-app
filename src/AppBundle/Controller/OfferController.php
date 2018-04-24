<?php
/**
 * Created by PhpStorm.
 * User: tomasz
 * Date: 2018-04-24
 * Time: 09:34
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Auction;
use AppBundle\Entity\Offer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class OfferController extends Controller
{
    /**
     * @Route("/auction/buy/{id}", name="offer_buy", methods={"POST"})
     * @param Auction $auction
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function buyAction(Auction $auction)
    {
        $offer = new Offer($auction);
        $offer
            ->setAuction($auction)
            ->setType(Offer::TYPE_BUE)
            ->setPrice($auction->getPrice());

        $auction
            ->setStatus(Auction::STATUS_FINISHED)
            ->setExpiresAt(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($auction);
        $em->persist($offer);
        $em->flush();

        return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
    }
}