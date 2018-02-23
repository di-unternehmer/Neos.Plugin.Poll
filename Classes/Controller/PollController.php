<?php
namespace DIU\Poll\Plugin\Controller;

/*
 * This file is part of the DIU.Poll.Plugin package.
 */

use DIU\Poll\Plugin\Domain\Model\Answer;
use DIU\Poll\Plugin\Domain\Repository\AnswerRepository;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\Persistence\Exception\IllegalObjectTypeException;
use Neos\Flow\Mvc\View\JsonView;
use Neos\Flow\Persistence\PersistenceManagerInterface;

class PollController extends ActionController
{
    /**
     * @Flow\Inject
     * @var AnswerRepository
     */
    protected $answerRepository;

    /**
     * @Flow\Inject
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @var string
     */
    protected $defaultViewObjectName = JsonView::class;

    /**
     * Add or update a new answer and return a encoded json object with the status and all asked amounts of answers
     *
     * @Flow\SkipCsrfProtection
     *
     * @param string $nodeName The node name of the selected answer
     * @param array $nodeNames The node name of the selected answer
     *
     * @throws IllegalObjectTypeException
     * @return void
     */
    public function updateAction(string $nodeName, array $nodeNames)
    {
        /** @var Answer $node */
        $node = $this->answerRepository->findByNodeName($nodeName);

        if($node) {
            $this->updateExistingAnswer($node);
        } else {
            $this->addNewAnswer($nodeName);
        }

        $count = $this->generateAnswersSumUp($nodeNames);

        if ( $count ) {
            $status = 'success';
            $this->response->setStatus(200);
        } else {
            $status = 'ERROR: Nothing found';
            $this->response->setStatus(400);
        }

        $this->view->assign('value', array(
            'status' =>  $status ,
            'count' => $count,
            'total' => $this->totalAnswersGiven($count)
        ));
    }

    /**
     * Add a new answer
     *
     * @param string $nodeName
     *
     * @throws IllegalObjectTypeException
     */
    private function addNewAnswer(string $nodeName) {
        $newNode = new Answer($nodeName);
        $this->answerRepository->add($newNode);
        $this->persistenceManager->persistAll();
    }

    /**
     * Add +1 to the answer amount
     *
     * @param Answer $answer
     *
     * @throws IllegalObjectTypeException
     */
    private function updateExistingAnswer(Answer $answer) {
        $answer->addOneToAmount();
        $this->answerRepository->update($answer);
        $this->persistenceManager->persistAll();
    }

    /**
     * Generate array with all answers the request asked for
     *
     * @param array $nodeNames
     *
     * @return array
     */
    private function generateAnswersSumUp($nodeNames) {
        $answerSumUp = [];

        foreach ($nodeNames as $nodeName) {
            /** @var Answer $answer */
            $answer = $this->answerRepository->findByNodeName($nodeName);

            if ( $answer ) {
                $answerSumUp[$nodeName] = $answer->getAmount();
            } else {
                $answerSumUp[$nodeName] = 0;
            }

        }

        return $answerSumUp;
    }

    /**
     * Return total amount of given answers for the given answers
     *
     * @param array $answersSumUp
     *
     * @return int|mixed
     */
    private function totalAnswersGiven(array $answersSumUp) {
        $sum = 0;
        foreach ($answersSumUp as $key=>$value) {
            $sum += $value;
        }
        return $sum;
    }

}
