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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/my/auction/details/{id}", name="my_auction_details")
     * @Template("MyAuction/details.html.twig")
     * @param Auction $auction
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function detailsAction(Auction $auction)
    {
        if ($auction->getStatus() === Auction::STATUS_FINISHED) {
            return $this->render("Auction/finishedAuctions.html.twig", ["auction" => $auction]);
        }

        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl("auction_delete", ["id" => $auction->getId()]))
            ->setMethod(Request::METHOD_DELETE)
            ->add("submit", SubmitType::class, ["label" => "Delete"])
            ->getForm();

        $finishForm = $this->createFormBuilder()
            ->setAction($this->generateUrl("auction_finish", ["id" => $auction->getId()]))
            ->setMethod(Request::METHOD_POST)
            ->add("submit", SubmitType::class, ["label" => "Finish auction"])
            ->getForm();

        return [
            "auction"=> $auction,
            "deleteForm" => $deleteForm->createView(),
            "finishForm" => $finishForm->createView(),
        ];
    }
}
