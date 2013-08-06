<?php

namespace Bigfoot\Bundle\MediaBundle\Form;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PortfolioSearchData
 * @package Bigfoot\Bundle\MediaBundle\Form
 */
class PortfolioSearchData
{
    /**
     * @var string
     */
    protected $search = '';

    /**
     * @var string
     */
    protected $table = '';

    /**
     * @var string
     */
    protected $column = '';

    /**
     * @return string
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param $search
     * @return $this
     */
    public function setSearch($search)
    {
        $this->search = $search;

        return $this;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param $table
     * @return $this
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @return string
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @param $column
     * @return $this
     */
    public function setColumn($column)
    {
        $this->column = $column;

        return $this;
    }
}
