<?php
/**
 * Created by PhpStorm.
 * User: tomasz radwan
 * Date: 2018-03-15
 * Time: 21:00
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Auction;
use AppBundle\Form\AuctionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
        $auctions = $em->getRepository(Auction::class)->findAll();

        return ["auctions" => $auctions];
    }

    /**
     * @Route("/{id}", name="auction_details")
     * @Template("Auction/details.html.twig")
     * @param Auction $auction
     * @return array
     */
    public function detailsAuction(Auction $auction)
    {
        return ["auction"=> $auction];
    }

    /**
     * @Route("/auction/add", name="auction_add")
     * @Template("Auction/add.html.twig")
     * @param Request $request
     * @return array
     */
    public function addAction(Request $request)
    {
        $auction = new Auction();

        $form = $this->createForm(AuctionType::class, $auction);

        if ($request->isMethod("POST")) {
            $form->handleRequest($request);

            $auction
                ->setCreatedAt(new \DateTime())
                ->setUpdatedAt(new \DateTime())
                ->setStatus(Auction::STATUS_ACTIVE);

            $em = $this->getDoctrine()->getManager();
            $em->persist($auction);
            $em->flush();

            return $this->redirectToRoute("auction_index");
        }

        return ["form" => $form->createView()];
    }
}

