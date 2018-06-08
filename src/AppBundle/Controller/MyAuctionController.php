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
use AppBundle\EventDispatcher\AuctionEvent;
use AppBundle\EventDispatcher\Events;
use AppBundle\Form\AuctionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
            ->findMyOrdered($this->getUser());

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
        $this->denyAccessUnlessGranted("ROLE_USER");

        if ($auction->getStatus() === Auction::STATUS_FINISHED) {
            return $this->render("MyAuction/finishedAuctions.html.twig", ["auction" => $auction]);
        }

        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl("my_auction_delete", ["id" => $auction->getId()]))
            ->setMethod(Request::METHOD_DELETE)
            ->add("submit", SubmitType::class, ["label" => "Delete"])
            ->getForm();

        $finishForm = $this->createFormBuilder()
            ->setAction($this->generateUrl("my_auction_finish", ["id" => $auction->getId()]))
            ->setMethod(Request::METHOD_POST)
            ->add("submit", SubmitType::class, ["label" => "Finish auction"])
            ->getForm();

        return [
            "auction" => $auction,
            "deleteForm" => $deleteForm->createView(),
            "finishForm" => $finishForm->createView(),
        ];
    }

    /**
     * @Route("my/auction/add", name="my_auction_add")
     * @Template("MyAuction/add.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addAction(Request $request)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $auction = new Auction();

        $form = $this->createForm(AuctionType::class, $auction);

        if ($request->isMethod("POST")) {
            $form->handleRequest($request);

            if ($auction->getStartPrice() >= $auction->getPrice()) {
                $form
                    ->get("startPrice")
                    ->addError(new FormError("Starting price cannot be greater than sales price"));
            }

            if ($form->isValid()) {
                $auction
                    ->setStatus(Auction::STATUS_ACTIVE)
                    ->setOwner($this->getUser());

                $em = $this->getDoctrine()->getManager();
                $em->persist($auction);
                $em->flush();

                $this->get("event_dispatcher")->dispatch(Events::AUCTION_ADD, new AuctionEvent($auction));

                $this->addFlash("success", "Auction {$auction->getTitle()} add successfully.");

                return $this->redirectToRoute("my_auction_details", ["id" => $auction->getId()]);
            }
            $this->addFlash("error", "You cannot add auction!");
        }

        return ["form" => $form->createView()];
    }

    /**
     * @Route("my/auction/edit/{id}", name="my_auction_edit")
     * @Template("MyAuction/edit.html.twig")
     * @param Request $request
     * @param Auction $auction
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAuction(Request $request, Auction $auction)
    {
        $this->isUserLoggedAndOwner($auction);

        $form = $this->createForm(AuctionType::class, $auction);

        if ($request->isMethod("POST")) {
            $form->handleRequest($request);

            $em = $this->getDoctrine()->getManager();
            $em->persist($auction);
            $em->flush();

            $this->get("event_dispatcher")->dispatch(Events::AUCTION_EDIT, new AuctionEvent($auction));

            $this->addFlash("success", "Auction {$auction->getTitle()} edit successfully.");

            return $this->redirectToRoute("my_auction_details", ["id" => $auction->getId()]);
        }

        return ["form" => $form->createView()];
    }

    /**
     * @Route("my/auction/delete/{id}", name="my_auction_delete", methods={"DELETE"})
     * @param Auction $auction
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Auction $auction)
    {
        $this->isUserLoggedAndOwner($auction);

        $em = $this->getDoctrine()->getManager();
        $em->remove($auction);
        $em->flush();

        $this->get("event_dispatcher")->dispatch(Events::AUCTION_DELETE, new AuctionEvent($auction));

        $this->addFlash("success", "Auction {$auction->getTitle()} deleted.");

        return $this->redirectToRoute("my_auction_index");
    }

    /**
     * @Route("my/auction/finish/{id}", name="my_auction_finish", methods={"POST"})
     * @param Auction $auction
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function finishAction(Auction $auction)
    {
        $this->isUserLoggedAndOwner($auction);

        $auction
            ->setExpiresAt(new \DateTime())
            ->setStatus(Auction::STATUS_FINISHED);

        $em = $this->getDoctrine()->getManager();
        $em->persist($auction);
        $em->flush();

        $this->get("event_dispatcher")->dispatch(Events::AUCTION_FINISH, new AuctionEvent($auction));

        $this->addFlash("success", "Auction {$auction->getTitle()} finished.");

        return $this->redirectToRoute("my_auction_details", ["id" => $auction->getId()]);
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
