<?php
declare(strict_types=1);

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
        $this->denyAccessUnlessGranted("ROLE_USER");

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
        $this->denyAccessUnlessGranted("ROLE_USER");

        $offer = new Offer();
        $bidForm = $this->createForm(BidType::class, $offer);

        $bidForm->handleRequest($request);

        if ($bidForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $lastOffer = $em
                ->getRepository(Offer::class)
                ->findOneBy(["auction" => $auction], ["createdAt" => "DESC"]);

            if (!isset($lastOffer)
                && ($offer->getPrice() <= $auction->getStartPrice())) {
                $this->addFlash(
                    "error",
                    "Offer price is lower than starting price.");

                return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
            }
            elseif (isset($lastOffer)
                && ($offer->getPrice() <= $lastOffer->getPrice())) {
                $this->addFlash(
                    "error",
                    "Your offer cannot be lower than {$lastOffer->getPrice()}");

                return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
            }

            $offer
                ->setType(Offer::TYPE_BID)
                ->setAuction($auction);

            $em->persist($offer);
            $em->flush();

            $this->addFlash(
                "success",
                "You bid the item {$auction->getTitle()} for the amount $ {$offer->getPrice()}"
            );
        } else {
            $this->addFlash("error", "You cannot bid object {$auction->getTitle()}");
        }

        return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
    }
}