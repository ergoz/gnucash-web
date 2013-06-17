<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Gnucash\Domain\Repository\AccountRepositoryInterface;

/**
 * Class AccountsController
 * @package Application\Controller
 */
class AccountsController extends AbstractActionController
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
        return [
            'accounts' => $this->repository->getBy(['parent' => null],['name' => 'ASC'])
        ];
    }

    /**
     * @return array
     */
    public function viewAction()
    {
        $guid = $this->params('id');

        $account = $this->repository->getById($guid);

        return [
            'account' => $account
        ];
    }
}
