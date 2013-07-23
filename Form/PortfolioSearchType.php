<?php

namespace Bigfoot\Bundle\MediaBundle\Form;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PortfolioSearchType extends AbstractType
{
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $this->em;

        $builder->add('search', 'text', array(
            'label'     => 'context.term',
            'required'  => false,
        ));

        $query = $em->createQuery(
            'SELECT mu.tableRef
            FROM BigfootMediaBundle:MediaUsage mu'
        );
        $tables = $query->getResult(AbstractQuery::HYDRATE_ARRAY);

        $tableChoices = array();
        foreach ($tables as $table) {
            $tableChoices[$table['tableRef']] = $table['tableRef'];
        }

        $builder->add('table', 'choice', array(
            'label'         => 'Table',
            'choices'       => $tableChoices,
            'required'      => false,
            'empty_value'   => 'Select a table',
            'attr'          => array(
                'class'    => 'portfolio-search-form-table',
            ),
        ));

        $query = $em->createQuery(
            'SELECT mu.columnRef
            FROM BigfootMediaBundle:MediaUsage mu'
        );
        $columns = $query->getResult(AbstractQuery::HYDRATE_ARRAY);

        $columnChoices = array();
        foreach ($columns as $column) {
            $columnChoices[$column['columnRef']] = $column['columnRef'];
        }

        $builder->add('column', 'choice', array(
            'label'         => 'Field',
            'choices'       => $columnChoices,
            'required'      => false,
            'empty_value'   => 'Select a field',
            'attr'          => array(
                'disabled' => 'disabled',
                'class'    => 'portfolio-search-form-column',
            ),
        ));
    }

    public function getName()
    {
        return 'bigfoot_portfolio_search';
    }
}
