<?php
/**
 * Created by PhpStorm.
 * User: tomasz
 * Date: 2018-03-15
 * Time: 21:00
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Auction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuctionController extends Controller
{
    /**
     * @Route("/", name="auction_index")
     * @return Response
     */
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $auctions = $entityManager->getRepository(Auction::class)->findAll();

        return $this->render("Auction/index.html.twig", ['auctions' => $auctions]);
    }

    /**
     * @Route("/{id}", name="auction_details")
     */
    public function detailsAuction($id)
    {
        return $this->render("Auction/detail.html.twig");
    }
}

