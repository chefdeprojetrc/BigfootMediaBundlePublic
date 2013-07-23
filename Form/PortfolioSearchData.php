<?php

namespace Bigfoot\Bundle\MediaBundle\Form;

use Symfony\Component\Validator\Constraints as Assert;

class PortfolioSearchData
{
    protected $search = '';

    protected $table = '';

    protected $column = '';

    public function getSearch()
    {
        return $this->search;
    }

    public function setSearch($search)
    {
        $this->search = $search;

        return $this;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    public function getColumn()
    {
        return $this->column;
    }

    public function setColumn($column)
    {
        $this->column = $column;

        return $this;
    }
}
