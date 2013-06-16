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
     * @throws \Exception
     * @return array
     */
    public function indexAction()
    {
        // this will be widgetized at some point:

        $snapshots = [
            [
                'title' => 'Cash in Bank',
                'account' => '226efaedec0f3c3e280b46fb2633708a',
                'icon' => '/images/bank-48.png'
            ],
            [
                'title' => 'Short Term Debt',
                'account' => 'd955d071f5b0096d25de2562ef758dbd',
                'icon' => '/images/credit-cards-48.png'
            ],
            [
                'title' => 'Adjusted Cash',
                'account' => ['226efaedec0f3c3e280b46fb2633708a', 'd955d071f5b0096d25de2562ef758dbd'],
                'operation' => 'add',
                'icon' => '/images/cash-48.png'
            ],
            [
                'title' => 'Long Term Debt',
                'account' => '2ddc0f2ca7f65595e256271f25a0e372',
                'icon' => '/images/house-48.png'
            ]
        ];

        foreach ($snapshots as $snapshotIndex => $snapshot) {
            if (!is_array($snapshot['account']) || count($snapshot['account']) == 1) {
                $account = is_array($snapshot['account']) ? reset($snapshot['account']) : $snapshot['account'];
                $amount = $this->repository->getAccountBalance($account);
                $snapshots[$snapshotIndex]['amount'] = $amount;
            } else {
                $totalAmount = 0;

                foreach ($snapshot['account'] as $accountIndex => $account) {
                    if ($accountIndex == 0) {
                        $totalAmount = $this->repository->getAccountBalance($account);
                    } else {
                        switch ($snapshot['operation']) {
                            case 'subtract':
                                $totalAmount -= $this->repository->getAccountBalance($account);
                                break;
                            case 'add':
                                $totalAmount += $this->repository->getAccountBalance($account);
                                break;
                            default:
                                throw new \Exception('Invalid operation requested');
                        }
                    }
                }

                $snapshots[$snapshotIndex]['amount'] = $totalAmount;
            }
        }

        return [
            'snapshots' => $snapshots
        ];
    }
}
