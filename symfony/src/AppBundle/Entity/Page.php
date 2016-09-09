<?php

namespace AppBundle\Entity;

use Bpeh\NestablePageBundle\Entity\Page as BasePage;
use Doctrine\ORM\Mapping as ORM;

/**
 * PageMeta
 *
 * @ORM\Table(name="page")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Page extends BasePage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

}
