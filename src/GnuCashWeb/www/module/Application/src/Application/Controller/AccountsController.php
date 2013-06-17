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
        $data = [];

        $guid = $this->params('id');
        $page = $this->params('page', 1);

        $data['account'] = $this->repository->getById($guid);
        $data['balance'] = $this->repository->getAccountBalance($guid);
        $data['transactions'] = $this->repository->getTransactions($guid, ($page - 1) * 20);

        $data['paginator'] = new Paginator(new ZendDoctrineAdapter($data['transactions']));
        $data['paginator']->setCurrentPageNumber($page);
        $data['paginator']->setItemCountPerPage(20);

        if ($data['account']->getType() == 'EXPENSE') {
            $data['monthStats'] = $this->repository->getMonthlyChange($guid);
        }

        return $data;
    }
}
