<?php

namespace Bigfoot\Bundle\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MediaUsage
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Bigfoot\Bundle\MediaBundle\Entity\MediaUsageRepository")
 */
class MediaUsage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Media", inversedBy="usages")
     * @ORM\Column(name="media_id", type="integer")
     */
    private $mediaId;

    /**
     * @var string
     *
     * @ORM\Column(name="table_ref", type="string", length=255)
     */
    private $tableRef;

    /**
     * @var string
     *
     * @ORM\Column(name="column_ref", type="string", length=255)
     */
    private $columnRef;

    /**
     * @var integer
     *
     * @ORM\Column(name="element_id", type="integer")
     */
    private $elementId;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set mediaId
     *
     * @param integer $mediaId
     * @return MediaUsage
     */
    public function setMediaId($mediaId)
    {
        $this->mediaId = $mediaId;
    
        return $this;
    }

    /**
     * Get mediaId
     *
     * @return integer 
     */
    public function getMediaId()
    {
        return $this->mediaId;
    }

    /**
     * Set tableRef
     *
     * @param string $tableRef
     * @return MediaUsage
     */
    public function setTableRef($tableRef)
    {
        $this->tableRef = $tableRef;
    
        return $this;
    }

    /**
     * Get tableRef
     *
     * @return string 
     */
    public function getTableRef()
    {
        return $this->tableRef;
    }

    /**
     * Set columnRef
     *
     * @param string $columnRef
     * @return MediaUsage
     */
    public function setColumnRef($columnRef)
    {
        $this->columnRef = $columnRef;
    
        return $this;
    }

    /**
     * Get columnRef
     *
     * @return string 
     */
    public function getColumnRef()
    {
        return $this->columnRef;
    }

    /**
     * Set elementId
     *
     * @param integer $elementId
     * @return MediaUsage
     */
    public function setElementId($elementId)
    {
        $this->elementId = $elementId;
    
        return $this;
    }

    /**
     * Get elementId
     *
     * @return integer 
     */
    public function getElementId()
    {
        return $this->elementId;
    }
}
