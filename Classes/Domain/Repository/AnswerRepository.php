<?php
namespace DIU\Poll\Plugin\Domain\Repository;

/*
 * This file is part of the DIU.Poll.Plugin package.
 */

use DIU\Poll\Plugin\Domain\Model\Answer;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class AnswerRepository extends Repository
{

    /**
     * @param string $nodeName
     *
     * @return Answer
     */
    public function findByNodeName(string $nodeName) {
        $query = $this->createQuery();

        return $query->matching(
                    $query->equals('nodeName', $nodeName)
                )->execute()
                ->getFirst();
    }
}
