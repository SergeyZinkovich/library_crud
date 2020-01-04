<?php


namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class BookFilter
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $description;

    /**
     * @var integer
     */
    public $authors;

    /**
     * @var DateTime
     * @Assert\DateTime
     */
    public $dateFrom;

    /**
     * @var DateTime
     * @Assert\DateTime
     */
    public $dateTo;
}