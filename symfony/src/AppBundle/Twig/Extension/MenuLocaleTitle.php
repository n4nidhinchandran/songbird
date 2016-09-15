<?php

namespace AppBundle\Twig\Extension;

/**
 * Twig Extension to get Menu title based on locale
 */
class MenuLocaleTitle extends \Twig_Extension
{
    /**
     * @var EntityManager
     */
    private $em;

	private $container;

    public function __construct($em, $request)
    {
        $this->em = $em;
        $this->request = $request->getCurrentRequest();
    }

    public function getName()
    {
        return 'menu_locale_title_extension';
    }

    public function getFunctions()
    {
        return array(
            'getMenuLocaleTitle' => new \Twig_Function_Method($this, 'getMenuLocaleTitle')
        );
    }

    public function getMenuLocaleTitle($slug)
    {
    	
    	$page = $this->em->getRepository('AppBundle:Page')->findOneBySlug($slug);
	    $pagemeta = $this->em->getRepository('AppBundle:PageMeta')->findPageMetaByLocale($page, $this->request->getLocale());

    	return $pagemeta->getMenuTitle();
    }
}
