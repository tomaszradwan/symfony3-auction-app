<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: tomasz
 * Date: 2018-05-03
 * Time: 21:28
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Auction[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Auction", mappedBy="owner")
     * @ORM\JoinColumn(name="owner", referencedColumnName="id")
     */
    private $auctions;

    /**
     * @var Offer[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Offer", mappedBy="owner")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $offers;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->auctions = new ArrayCollection();
        $this->offers = new ArrayCollection();
    }

    /**
     * @return Auction[]|ArrayCollection
     */
    public function getAuctions()
    {
        return $this->auctions;
    }

    /**
     * @param Auction $auction
     * @return $this
     */
    public function addAuctions(Auction $auction)
    {
        $this->auctions[] = $auction;

        return $this;
    }

    /**
     * @return Offer[]|ArrayCollection
     */
    public function getOffers()
    {
        return $this->offers;
    }

    /**
     * @param Offer $offer
     * @return $this
     */
    public function addOffer(Offer $offer)
    {
        $this->offer[] = $offer;

        return $this;
    }
}
