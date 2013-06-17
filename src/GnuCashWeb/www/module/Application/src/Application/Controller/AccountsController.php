<?php

namespace Application\Controller;

use GnuCash\Persistence\Paginator\Adapter\ZendDoctrineAdapter;
use Zend\Mvc\Controller\AbstractActionController;
use Gnucash\Domain\Repository\AccountRepositoryInterface;
use Zend\Paginator\Paginator;

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
        $page = $this->params('page', 1);

        $account = $this->repository->getById($guid);
        $balance = $this->repository->getAccountBalance($guid);
        $transactions = $this->repository->getTransactions($guid, ($page - 1) * 20);

        $paginator = new Paginator(new ZendDoctrineAdapter($transactions));
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(20);

        return [
            'account' => $account,
            'paginator' => $paginator,
            'balance' => $balance
        ];
    }
}
