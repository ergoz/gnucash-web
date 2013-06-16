<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * Class IndexController
 * @package Application\Controller
 */
class IndexController extends AbstractActionController
{
    /**
     * @var \PDO
     */
    protected $db;

    /**
     * @param \PDO $db
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * @return array
     */
    public function indexAction()
    {
        // Dirty as shit. Will need to turn these in to doctrine named native queries. Will work on that later.

        $sql = 'SELECT
            SUM(value_num::numeric(20,2) / value_denom::numeric(20,2))
        FROM splits
        WHERE account_guid IN(
            SELECT guid FROM accounts WHERE parent_guid = \'226efaedec0f3c3e280b46fb2633708a\' AND hidden = 0
        );';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $cash = (float)$stmt->fetchColumn();

        $sql = 'SELECT
            SUM(value_num::numeric(20,2) / value_denom::numeric(20,2))
        FROM splits
        WHERE account_guid IN(
            SELECT guid FROM accounts WHERE parent_guid = \'d955d071f5b0096d25de2562ef758dbd\' AND hidden = 0
        );';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $shortTermDebt = (float)$stmt->fetchColumn();

        $sql = 'SELECT
            SUM(value_num::numeric(20,2) / value_denom::numeric(20,2))
        FROM splits
        WHERE account_guid IN(
            SELECT guid FROM accounts WHERE parent_guid = \'2ddc0f2ca7f65595e256271f25a0e372\' AND hidden = 0
        );';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $longTermDebt = (float)$stmt->fetchColumn();

        return [
            'cash' => $cash,
            'shortTermDebt' => $shortTermDebt * -1,
            'adjustedCash' => ($cash + $shortTermDebt),
            'longTermDebt' => $longTermDebt * -1
        ];
    }
}
