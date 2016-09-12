<?php
namespace AppBundle\Controller;

use AppBundle\Entity\PageMeta;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use JavierEguiluz\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends BaseAdminController
{
    /**
     * @Route("/dashboard", name="dashboard")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */
    public function dashboardAction(Request $request)
    {
        return $this->render('easy_admin/dashboard.html.twig');
    }

    public function createNewUserEntity()
    {
        return $this->get('fos_user.user_manager')->createUser();
    }

    public function prePersistUserEntity($user)
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
    }
    
    public function preUpdateUserEntity($user)
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
    }

	/**
	 * Show Page List page
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function listPageAction()
    {
		$rootMenuItems = $this->container->get('doctrine')->getRepository('AppBundle\Entity\Page')->findParent();

		return $this->render('AppBundle:Page:list.html.twig', array(
			'tree' => $rootMenuItems,
		));
	}

	/**
	 * Redirect to Page listing page
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function listPageMetaAction()
	{
		return $this->redirect($this->generateUrl('easyadmin', array('entity' => 'Page', 'action' => 'list')));
	}

	/**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showUserAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_SHOW);
        $id = $this->request->query->get('id');
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];

        $fields = $this->entity['show']['fields'];

        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            unset($fields['created']);
        }

        $deleteForm = $this->createDeleteForm($this->entity['name'], $id);

        return $this->render($this->entity['templates']['show'], array(
            'entity' => $entity,
            'fields' => $fields,
            'delete_form' => $deleteForm->createView(),
        ));
    }

	protected function newPageMetaAction()
	{

		$entity = $this->createNewEntity();
		$easyadmin['item'] = $entity;
		$this->request->attributes->set('easyadmin', $easyadmin);
		$fields = $this->entity['new']['fields'];
		$newForm = $this->createNewForm($entity, $fields);
		$newForm->handleRequest($this->request);

		if ($newForm->isValid()) {

			$em = $this->container->get('doctrine')->getManager();
			if ( $em->getRepository( 'AppBundle\Entity\PageMeta' )->findPageMetaByLocale( $entity->getPage(), $entity->getLocale() ) ) {
				// throw new \RuntimeException($this->get('translator')->trans('one_locale_per_pagemeta_only', array(), 'BpehNestablePageBundle') );
			}

			$this->em->persist($entity);
			$this->em->flush();

			$refererUrl = $this->request->query->get('referer', '');

			return !empty($refererUrl)
				? $this->redirect(urldecode($refererUrl))
				: $this->redirect($this->generateUrl('easyadmin', array('action' => 'list', 'entity' => $this->entity['name'])));
		}

		return $this->render($this->entity['templates']['new'], array(
			'form' => $newForm->createView(),
			'entity_fields' => $fields,
			'entity' => $entity,
		));
	}

	public function prePersistPageMetaEntity(PageMeta $pageMeta)
	{

		// if page and local is the same, dont need to check locale count
		if ($origLocale == $pageMeta->getLocale() && $origId == $pageMeta->getPage()->getId()) {
			// all good
		}
		elseif ( $em->getRepository( $this->entity_meta )->findPageMetaByLocale( $pageMeta->getPage(), $pageMeta->getLocale(), true ) ) {
			throw new \RuntimeException($this->get('translator')->trans('one_locale_per_pagemeta_only', array(), 'BpehNestablePageBundle') );
		}

	}

	public function preUpdatePageMetaEntity(PageMeta $pageMeta)
	{
		echo "update".print_r($entity);exit;
	}

    /**
     * The method that is executed when the user performs a 'edit' action on an entity.
     *
     * @return RedirectResponse|Response
     */
    protected function editUserAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_EDIT);
        $id = $this->request->query->get('id');
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];

        if ($this->request->isXmlHttpRequest() && $property = $this->request->query->get('property')) {
            $newValue = 'true' === strtolower($this->request->query->get('newValue'));
            $fieldsMetadata = $this->entity['list']['fields'];

            if (!isset($fieldsMetadata[$property]) || 'toggle' !== $fieldsMetadata[$property]['dataType']) {
                throw new \RuntimeException(sprintf('The type of the "%s" property is not "toggle".', $property));
            }

            $this->updateEntityProperty($entity, $property, $newValue);

            return new Response((string)$newValue);
        }

        $fields = $this->entity['edit']['fields'];

        $editForm = $this->createEditForm($entity, $fields);
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            $editForm->remove('enabled');
            $editForm->remove('roles');
            $editForm->remove('locked');
            $editForm->remove('expired');
        }

        $deleteForm = $this->createDeleteForm($this->entity['name'], $id);

        $editForm->handleRequest($this->request);
        if ($editForm->isValid()) {
            $this->preUpdateUserEntity($entity);
            $this->em->flush();

            $refererUrl = $this->request->query->get('referer', '');

            return !empty($refererUrl)
                ? $this->redirect(urldecode($refererUrl))
                : $this->redirect($this->generateUrl('easyadmin', array('action' => 'show', 'entity' => $this->entity['name'], 'id' => $id)));
        }

        return $this->render($this->entity['templates']['edit'], array(
            'form' => $editForm->createView(),
            'entity_fields' => $fields,
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }
}
