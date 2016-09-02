<?php

namespace Songbird\NestablePageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Songbird\NestablePageBundle\Entity\PageMeta;
use Songbird\NestablePageBundle\Form\PageMetaType;

/**
 * PageMeta controller.
 *
 * @Route("/songbird_pagemeta")
 */
class PageMetaController extends Controller
{
    /**
     * Lists all PageMeta entities.
     *
     * @Route("/", name="songbird_pagemeta_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pageMetas = $em->getRepository('SongbirdNestablePageBundle:PageMeta')->findAll();

        return $this->render('pagemeta/index.html.twig', array(
            'pageMetas' => $pageMetas,
        ));
    }

    /**
     * Creates a new PageMeta entity.
     *
     * @Route("/new", name="songbird_pagemeta_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $pageMetum = new PageMeta();
        $form = $this->createForm('Songbird\NestablePageBundle\Form\PageMetaType', $pageMetum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pageMetum);
            $em->flush();

            return $this->redirectToRoute('songbird_pagemeta_show', array('id' => $pageMetum->getId()));
        }

        return $this->render('pagemeta/new.html.twig', array(
            'pageMetum' => $pageMetum,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a PageMeta entity.
     *
     * @Route("/{id}", name="songbird_pagemeta_show")
     * @Method("GET")
     */
    public function showAction(PageMeta $pageMetum)
    {
    	$deleteForm = $this->createDeleteForm($pageMetum);

        return $this->render('pagemeta/show.html.twig', array(
            'pageMetum' => $pageMetum,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing PageMeta entity.
     *
     * @Route("/{id}/edit", name="songbird_pagemeta_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, PageMeta $pageMetum)
    {
        $deleteForm = $this->createDeleteForm($pageMetum);
        $editForm = $this->createForm('Songbird\NestablePageBundle\Form\PageMetaType', $pageMetum);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pageMetum);
            $em->flush();

            return $this->redirectToRoute('songbird_pagemeta_edit', array('id' => $pageMetum->getId()));
        }

        return $this->render('pagemeta/edit.html.twig', array(
            'pageMetum' => $pageMetum,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a PageMeta entity.
     *
     * @Route("/{id}", name="songbird_pagemeta_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, PageMeta $pageMetum)
    {
        $form = $this->createDeleteForm($pageMetum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($pageMetum);
            $em->flush();
        }

        return $this->redirectToRoute('songbird_pagemeta_index');
    }

    /**
     * Creates a form to delete a PageMeta entity.
     *
     * @param PageMeta $pageMetum The PageMeta entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PageMeta $pageMetum)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('songbird_pagemeta_delete', array('id' => $pageMetum->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
