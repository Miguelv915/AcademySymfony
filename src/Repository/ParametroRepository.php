<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Repository;

use CarlosChininin\App\Infrastructure\Repository\BaseRepository;
use CarlosChininin\Util\Filter\DoctrineValueSearch;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pidia\Apps\Demo\Entity\Parametro;

/**
 * @method Parametro|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parametro|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parametro[]    findAll()
 * @method Parametro[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParametroRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parametro::class);
    }

    public function filter(array|ParamFetcher $params, bool $inArray = true, array $permissions = []): array
    {
        $queryBuilder = $this->filterQuery($params, $permissions);

        if (true === $inArray) {
            return $queryBuilder->getQuery()->getArrayResult();
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function filterQuery(array|ParamFetcher $params, array $permissions = []): QueryBuilder
    {
        $queryBuilder = $this->allQuery();

        DoctrineValueSearch::apply($queryBuilder, $params->getNullableString('b'), ['parametro.name', 'padre.name']);

        return $queryBuilder;
    }

    public function findByPadreAlias(string $padreAlias, bool $inArray = false): array
    {
        if (true === $inArray) {
            return $this->queryPadreAlias($padreAlias)->getQuery()->getArrayResult();
        }

        return $this->queryPadreAlias($padreAlias)->getQuery()->getResult();
    }

    public function queryPadreAlias(string $padreAlias): QueryBuilder
    {
        return $this->allQuery()
            ->where('parametro.isActive = TRUE')
            ->andWhere('padre.alias = :padre_alias')
            ->setParameter('padre_alias', $padreAlias);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByAlias(string $alias, ?string $padreAlias = null): ?Parametro
    {
        $query = $this->createQueryBuilder('parametro')
            ->select('parametro')
            ->where('parametro.isActive = TRUE')
            ->andWhere('parametro.alias = :alias')
            ->setParameter('alias', $alias);

        if (null !== $padreAlias && '' !== $padreAlias) {
            $query = $query
                ->join('parametro.parent', 'padre')
                ->andWhere('padre.alias = :padre_alias')
                ->setParameter('padre_alias', $padreAlias);
        }

        return $query->getQuery()->getOneOrNullResult();
    }

    public function allQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('parametro')
            ->select(['parametro', 'padre'])
            ->leftJoin('parametro.parent', 'padre');
    }
}
