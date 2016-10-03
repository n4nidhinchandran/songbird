<?php

namespace AppBundle\Entity;

use Bpeh\NestablePageBundle\Model\PageMetaBase as PageMetaBase;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

/**
 * PageMeta.
 *
 * @ORM\Table(name="pagemeta")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PageMetaRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class PageMeta extends PageMetaBase
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $featuredImage;

    /**
     * @Vich\UploadableField(mapping="featured_image", fileNameProperty="featuredImage")
     *
     * @var File
     */
    private $featuredImageFile;

    public function __toString()
    {
        return $this->getLocale().': '.$this->getMenuTitle();
    }

    public function setFeaturedImageFile(File $image = null)
    {
        $this->featuredImageFile = $image;

        if ($image) {
            $this->setModified(new \DateTime());
        }
    }

    public function getFeaturedImageFile()
    {
        return $this->featuredImageFile;
    }

    public function setFeaturedImage($image)
    {
        $this->featuredImage = $image;
    }

    public function getFeaturedImage()
    {
        return $this->featuredImage;
    }
}
