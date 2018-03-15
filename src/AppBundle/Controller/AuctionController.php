<?php
/**
 * Created by PhpStorm.
 * User: tomasz
 * Date: 2018-03-15
 * Time: 21:00
 */

namespace AppBundle\Controller;

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
        $auctions = [
            [
                'id' => 1,
                'title' => 'table',
                'description'=> 'description 1',
                'price' => '1000 $',
            ],
            [
                'id' => 2,
                'title' => 'phone',
                'description'=> 'description 2',
                'price' => '350 $',
            ],
            [
                'id' => 3,
                'title' => 'computer',
                'description'=> 'description 3',
                'price' => '500 $',
            ],

        ];

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

