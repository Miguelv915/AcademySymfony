<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Repository;

use CarlosChininin\App\Infrastructure\Repository\BaseRepository;
use CarlosChininin\Util\Filter\DoctrineValueSearch;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pidia\Apps\Demo\Entity\Menu;
use Pidia\Apps\Demo\Security\Security;
use Pidia\Apps\Demo\Util\Paginator;

/**
 * @method Menu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Menu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Menu[]    findAll()
 * @method Menu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, Menu::class);
    }

    public function findLatest(array $params): Paginator
    {
        $queryBuilder = $this->filterQuery($params);

        return Paginator::create($queryBuilder, $params);
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
        $queryBuilder = $this->createQueryBuilder('menu')
            ->select(['menu', 'parent'])
            ->join('menu.config', 'config')
            ->leftJoin('menu.parent', 'parent')
        ;

        $this->security->configQuery($queryBuilder, true);

        DoctrineValueSearch::apply($queryBuilder, $params->getNullableString('b'), ['menu.name', 'parent.name']);

        return $queryBuilder;
    }

    public function findAllActivo(): array
    {
        $queryBuilder = $this->createQueryBuilder('menu')
            ->select('parent.name as parent_name')
            ->addSelect('menu.id as menuId')
            ->addSelect('menu.name as name')
            ->addSelect('menu.route as route')
            ->addSelect('menu.icono as icono')
            ->join('menu.config', 'config')
            ->leftJoin('menu.parent', 'parent')
            ->where('menu.activo = TRUE')
            ->orderBy('parent.rank', 'ASC')
            ->addOrderBy('menu.rank', 'ASC')
            ->addOrderBy('menu.name', 'ASC')
            ;

        $this->security->configQuery($queryBuilder, true);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function allForMenus(): array
    {
        $queryBuilder = $this->createQueryBuilder('menu')
            ->select('parent.name as parentName')
            ->addSelect('menu.name as name')
            ->addSelect('menu.route as route')
            ->leftJoin('menu.parent', 'parent')
            ->leftJoin('menu.config', 'config')
            ->where('menu.activo = TRUE')
            ->andWhere('menu.parent IS NOT NULL')
            ->orderBy('parent.rank', 'ASC')
            ->addOrderBy('menu.rank', 'ASC')
            ->addOrderBy('menu.name', 'ASC')
        ;

        $this->security->configQuery($queryBuilder, true);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function allQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('menu')
            ->select(['menu', 'parent'])
            ->join('menu.config', 'config')
            ->leftJoin('menu.parent', 'parent')
            ;
    }

    /** @return Menu[] */
    public function searchAllActiveWithOrder(): array
    {
        return $this->allQuery()
            ->where('menu.activo = true')
            ->orderBy('parent.rank')
            ->addOrderBy('menu.rank')
            ->addOrderBy('menu.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
