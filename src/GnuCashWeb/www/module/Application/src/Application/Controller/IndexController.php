<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Gnucash\Domain\Repository\AccountRepositoryInterface;

/**
 * Class IndexController
 * @package Application\Controller
 */
class IndexController extends AbstractActionController
{
    /**
     * @var AccountRepositoryInterface
     */
    protected $repository;

    /**
     * @param AccountRepositoryInterface $repo
     */
    public function __construct(AccountRepositoryInterface $repo)
    {
        $this->repository = $repo;
    }

    /**
     * @return array
     */
    public function indexAction()
    {
        $cash = $this->repository->getCashAccountsBalance();
        $shortTermDebt = $this->repository->getShortTermDebtBalance();
        $longTermDebt = $this->repository->getLongTermDebtBalance();

        return [
            'cash' => $cash,
            'shortTermDebt' => $shortTermDebt * -1,
            'adjustedCash' => ($cash + $shortTermDebt),
            'longTermDebt' => $longTermDebt * -1
        ];
    }
}
