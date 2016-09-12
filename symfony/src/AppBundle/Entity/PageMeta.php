<?php

namespace AppBundle\Entity;

use Bpeh\NestablePageBundle\Entity\PageMeta as BasePageMeta;
use Doctrine\ORM\Mapping as ORM;

/**
 * PageMeta
 *
 * @ORM\Table(name="pagemeta")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PageMetaRepository")
 */
class PageMeta extends BasePageMeta
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

	public function __toString()
	{
		return $this->getLocale().': '.$this->getMenuTitle();
	}
}
