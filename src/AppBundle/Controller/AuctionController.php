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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuctionController extends Controller
{
    /**
     * @Route("/", name="auction_index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $auctions = $em->getRepository(Auction::class)->findAll();

        return $this->render("Auction/index.html.twig", ['auctions' => $auctions]);
    }

    /**
     * @Route("/{id}", name="auction_details")
     */
    public function detailsAuction($id)
    {
        return $this->render("Auction/detail.html.twig");
    }

    /**
     * @Route("/auction/add", name="auction_add")
     *
     */
    public function addAction(Request $request)
    {
        $auction = new Auction();

        $form = $this->createFormBuilder($auction)
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('price', NumberType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            $auction = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($auction);
            $em->flush();

            return $this->redirectToRoute('auction_index');
        }

        return $this->render('Auction/add.html.twig', ['form' => $form->createView()]);
    }
}

