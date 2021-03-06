<?php

namespace App\Repository;

use App\doctrine\pgsql\Connection;
use App\model\collection\CountryCollection;
use App\model\CountryFuture;
use App\model\CountryInterface;
use App\model\mapper\CountryMapper;
use Doctrine\Common\Collections\Criteria;
use Lib\types\IntegerType;

class CountryRepository extends AbstractRepository
{
    private const TABLE = 'countries';

    /**
     * CountryRepository constructor.
     * @param Connection $connection
     * @param CountryMapper $mapper
     */
    public function __construct(Connection $connection, CountryMapper $mapper)
    {
        parent::__construct($connection, $mapper);
    }

    /**
     * @param Criteria|null $criteria
     * @return CountryCollection
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAll(Criteria $criteria = null): CountryCollection
    {
        $query = $this->getBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->execute();

        return new CountryCollection(function() use ($query) {
            $countries = [];
            foreach($query->fetchAllAssociative() as $item) {
                $countries[] = $this->mapper->fromArray($item);
            }
            return $countries;
        });
    }

    /**
     * @param IntegerType $id
     * @return CountryInterface
     * @throws \Doctrine\DBAL\Exception
     */
    public function getByIdAsync(IntegerType $id): CountryInterface
    {
        $query = $this->getBuilder()
            ->select('*')
            ->from(self::TABLE . ', pg_sleep(1)')
            ->where('id = ' . $id->toString())
            ->execute();

        return new CountryFuture(function() use ($query) {
            return $this->mapper->fromArray($query->fetchAssociative());
        });
    }

    /**
     * @param IntegerType $id
     * @return CountryInterface
     * @throws \Doctrine\DBAL\Exception
     */
    public function getById(IntegerType $id): CountryInterface
    {
        $query = $this->getBuilder()
            ->select('*')
            ->from(self::TABLE . ', pg_sleep(1)')
            ->where('id = ' . $id->toString())
            ->execute();

        return $this->mapper->fromArray($query->fetchAssociative());
    }
}