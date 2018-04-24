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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        $auctions = $em
            ->getRepository(Auction::class)
            ->findBy(["status" => Auction::STATUS_ACTIVE]);

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

        $buyForm = $this->createFormBuilder()
            ->setAction($this->generateUrl("offer_buy", ["id" => $auction->getId()]))
            ->setMethod(Request::METHOD_POST)
            ->add("submit", SubmitType::class, ["label" => "Buy"])
            ->getForm();

        return [
            "auction"=> $auction,
            "deleteForm" => $deleteForm->createView(),
            "finishForm" => $finishForm->createView(),
            "buyForm" => $buyForm->createView(),
        ];
    }

    /**
     * @Route("/auction/add", name="auction_add")
     * @Template("Auction/add.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addAction(Request $request)
    {
        $auction = new Auction();

        $form = $this->createForm(AuctionType::class, $auction);

        if ($request->isMethod("POST")) {
            $form->handleRequest($request);

            $auction->setStatus(Auction::STATUS_ACTIVE);

            $em = $this->getDoctrine()->getManager();
            $em->persist($auction);
            $em->flush();

            return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
        }

        return ["form" => $form->createView()];
    }

    /**
     * @Route("/auction/edit/{id}", name="auction_edit")
     * @Template("Auction/edit.html.twig")
     * @param Request $request
     * @param Auction $auction
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAuction(Request $request, Auction $auction)
    {
        $form = $this->createForm(AuctionType::class, $auction);

        if ($request->isMethod("POST")) {
            $form->handleRequest($request);

            $em = $this->getDoctrine()->getManager();
            $em->persist($auction);
            $em->flush();

            return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
        }

        return ["form" => $form->createView()];
    }

    /**
     * @Route("/auction/delete/{id}", name="auction_delete", methods={"DELETE"})
     * @param Auction $auction
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Auction $auction)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($auction);
        $em->flush();

        return $this->redirectToRoute("auction_index");
    }

    /**
     * @Route("/auction/finish/{id}", name="auction_finish", methods={"POST"})
     * @param Auction $auction
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function finishAction(Auction $auction)
    {
        $auction
            ->setExpiresAt(new \DateTime())
            ->setStatus(Auction::STATUS_FINISHED);

        $em = $this->getDoctrine()->getManager();
        $em->persist($auction);
        $em->flush();

        return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
    }
}

