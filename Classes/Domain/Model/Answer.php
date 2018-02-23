<?php
namespace DIU\Poll\Plugin\Domain\Model;

use Neos\Flow\Annotations as Flow;

/**
 * A Tag, to organize Assets
 *
 * @Flow\Entity
 */
class Answer
{

    /**
     * @var string
     */
    protected $nodeName;
    
    /**
     * @var integer
     */
    protected $amount;


    /**
     * Question constructor.
     * @var string $nodeName
     */
    public function __construct(string $nodeName)
    {
        $this->setNodeName($nodeName);
        $this->amount = 1;
    }

    /**
     * @return string
     */
    public function getNodeName()
    {
        return $this->nodeName;
    }

    /**
     * @param string $nodeName
     */
    public function setNodeName($nodeName)
    {
        $this->nodeName = $nodeName;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    public function addOneToAmount()
    {
        ++$this->amount;
    }
}