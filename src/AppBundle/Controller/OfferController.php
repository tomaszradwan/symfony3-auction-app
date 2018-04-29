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
use AppBundle\Form\BidType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
            ->setType(Offer::TYPE_BUY)
            ->setPrice($auction->getPrice());

        $auction
            ->setStatus(Auction::STATUS_FINISHED)
            ->setExpiresAt(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($auction);
        $em->persist($offer);
        $em->flush();

        $this->addFlash(
            "success",
            "You bought the item {$auction->getTitle()} for the amount $ {$auction->getPrice()}"
        );

        return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
    }

    /**
     * @Route("/auction/bid/{id}", name="offer_bid", methods={"POST"})
     * @param Request $request
     * @param Auction $auction
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function bidAction(Request $request, Auction $auction)
    {
        $offer = new Offer();
        $bidForm = $this->createForm(BidType::class, $offer);

        $bidForm->handleRequest($request);

        $offer
            ->setType(Offer::TYPE_BID)
            ->setAuction($auction);

        $em = $this->getDoctrine()->getManager();
        $em->persist($offer);
        $em->flush();

        $this->addFlash(
            "success",
            "You bid the item {$auction->getTitle()} for the amount $ {$auction->getPrice()}"
        );

        return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
    }
}