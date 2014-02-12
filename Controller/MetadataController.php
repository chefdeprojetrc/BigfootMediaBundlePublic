<?php

namespace Bigfoot\Bundle\MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

use Bigfoot\Bundle\CoreBundle\Controller\CrudController;

/**
 * Metadata controller.
 *
 * @Cache(maxage="0", smaxage="0", public="false")
 * @Route("/admin/portfolio_metadata")
 */
class MetadataController extends CrudController
{
    /**
     * @return string
     */
    protected function getName()
    {
        return 'admin_portfolio_metadata';
    }

    /**
     * @return string
     */
    protected function getEntity()
    {
        return 'BigfootMediaBundle:Metadata';
    }

    protected function getFields()
    {
        return array(
            'id'   => 'ID',
            'name' => 'Name',
        );
    }
    /**
     * Lists all Metadata entities.
     *
     * @Route("/", name="admin_portfolio_metadata")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->doIndex();
    }

    /**
     * Displays a form to create a new Metadata entity.
     *
     * @Route("/new", name="admin_portfolio_metadata_new")
     */
    public function newAction(Request $request)
    {

        return $this->doNew($request);
    }

    /**
     * Displays a form to edit an existing Metadata entity.
     *
     * @Route("/{id}/edit", name="admin_portfolio_metadata_edit")
     */
    public function editAction(Request $request, $id)
    {
        return $this->doEdit($request, $id);
    }

    /**
     * Deletes a Metadata entity.
     *
     * @Route("/{id}", name="admin_portfolio_metadata_delete")
     * @Method("GET|DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        return $this->doDelete($request, $id);
    }
}
