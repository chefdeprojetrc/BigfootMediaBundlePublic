<?php

namespace Bigfoot\Bundle\MediaBundle\Controller;

use Bigfoot\Bundle\CoreBundle\Theme\Menu\Item;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Bigfoot\Bundle\MediaBundle\Entity\Metadata;
use Bigfoot\Bundle\MediaBundle\Form\MetadataType;

/**
 * Metadata controller.
 *
 * @Route("/admin/portfolio_metadata")
 */
class MetadataController extends Controller
{

    /**
     * Lists all Metadata entities.
     *
     * @Route("/", name="admin_portfolio_metadata")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BigfootMediaBundle:Metadata')->findAll();

        $theme = $this->container->get('bigfoot.theme');
        $theme['page_content']['globalActions']->addItem(new Item('crud_add', 'Add a metadata', 'admin_portfolio_metadata_new'));

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Metadata entity.
     *
     * @Route("/", name="admin_portfolio_metadata_create")
     * @Method("POST")
     * @Template("BigfootMediaBundle:Metadata:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Metadata();
        $form = $this->createForm(new MetadataType(), $entity);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_portfolio_metadata'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Metadata entity.
     *
     * @Route("/new", name="admin_portfolio_metadata_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Metadata();
        $form   = $this->createForm(new MetadataType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Metadata entity.
     *
     * @Route("/{id}/edit", name="admin_portfolio_metadata_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BigfootMediaBundle:Metadata')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Metadata entity.');
        }

        $editForm = $this->createForm(new MetadataType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Metadata entity.
     *
     * @Route("/{id}", name="admin_portfolio_metadata_update")
     * @Method("PUT")
     * @Template("BigfootMediaBundle:Metadata:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BigfootMediaBundle:Metadata')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Metadata entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new MetadataType(), $entity);
        $editForm->submit($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_portfolio_metadata_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Metadata entity.
     *
     * @Route("/{id}", name="admin_portfolio_metadata_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BigfootMediaBundle:Metadata')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Metadata entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_portfolio_metadata'));
    }

    /**
     * Creates a form to delete a Metadata entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
