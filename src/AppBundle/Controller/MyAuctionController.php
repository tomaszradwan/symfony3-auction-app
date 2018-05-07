<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: tomasz
 * Date: 2018-05-07
 * Time: 20:47
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Auction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class MyAuctionController extends Controller
{
    /**
     * @Route("/my", name="my_auction_index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $em = $this->getDoctrine()->getManager();

        $auctions = $em
            ->getRepository(Auction::class)
            ->findBy(["owner" => $this->getUser()]);

        return $this->render("MyAuction/index.html.twig", ["auctions" => $auctions]);
    }
}
