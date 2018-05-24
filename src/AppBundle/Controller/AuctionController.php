<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: tomasz radwan
 * Date: 2018-03-15
 * Time: 21:00
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Auction;
use AppBundle\Form\BidType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AuctionController extends Controller
{
    /**
     * @Route("/", name="auction_index")
     * @Template("Auction/index.html.twig")
     * @return array
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $auctions = $em
            ->getRepository(Auction::class)
            ->findActiveOrdered();

        return ["auctions" => $auctions];
    }

    /**
     * @Route("auction/details/{id}", name="auction_details")
     * @Template("Auction/details.html.twig")
     * @param Auction $auction
     * @return array
     */
    public function detailsAction(Auction $auction)
    {
        if ($auction->getStatus() === Auction::STATUS_FINISHED) {
            return $this->render("Auction/finishedAuctions.html.twig", ["auction" => $auction]);
        }

        $buyForm = $this->createFormBuilder()
            ->setAction($this->generateUrl("offer_buy", ["id" => $auction->getId()]))
            ->setMethod(Request::METHOD_POST)
            ->add("submit", SubmitType::class, ["label" => "Buy"])
            ->getForm();

        $bidForm = $this->createForm(
            BidType::class,
            null,
            ["action" => $this->generateUrl("offer_bid", ["id" => $auction->getId()])]
        );

        return [
            "auction" => $auction,
            "buyForm" => $buyForm->createView(),
            "bidForm" => $bidForm->createView(),
        ];
    }

    /**
     * @param Auction $auction
     */
    private function isUserLoggedAndOwner(Auction $auction)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        if ($this->getUser() !== $auction->getOwner()) {
            throw new AccessDeniedException();
        }
    }
}
