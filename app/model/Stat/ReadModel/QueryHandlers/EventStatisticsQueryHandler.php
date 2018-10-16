<?php

declare(strict_types=1);

namespace Model\Event\ReadModel\QueryHandlers;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Model\Cashbook\Operation;
use Model\Event\ReadModel\Queries\EventStatisticsQuery;
use Model\Event\SkautisEventId;
use function array_map;

class EventStatisticsQueryHandler
{
    /** @var Connection */
    private $db;

    public function __construct(Connection $connection)
    {
        $this->db = $connection;
    }

    /**
     * @return float[]
     */
    public function handle(EventStatisticsQuery $query) : array
    {
        $params = [
        array_map(function (SkautisEventId $id) {
            return $id->toInt();
        }, $query->getEventIds()),
            $query->getYear(),
            ];
        $types  = [Connection::PARAM_INT_ARRAY, ParameterType::INTEGER];
        $sql    = 'SELECT eventId, SUM(price) as sum ' .
            'FROM `ac_chits` ' .
            'WHERE eventId IN (?) AND category_operation_type = \'' . Operation::EXPENSE . '\' ' .
            'AND YEAR(date) = ? ';

        $sql .= 'GROUP BY eventId';

        $stmt = $this->db->executeQuery($sql, $params, $types);

        return array_map('floatval', $stmt->fetchAll(\PDO::FETCH_KEY_PAIR));
    }
}
