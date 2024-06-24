<?php

namespace Pidia\Apps\Demo\Repository;
use CarlosChininin\App\Infrastructure\Repository\BaseRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use CarlosChininin\Util\Filter\DoctrineValueSearch;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pidia\Apps\Demo\Entity\Matricula;

/**
 * @extends ServiceEntityRepository<Matricula>
 */
class MatriculaRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Matricula::class);
    }

    
    public function filterQuery(array|ParamFetcher $params, array $permissions = []): QueryBuilder
    {
        $queryBuilder = $this->allQuery();

        DoctrineValueSearch::apply($queryBuilder, $params->getNullableString('b'), ['matricula.precio']);

        return $queryBuilder;
    }
    public function allQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('matricula')
            ->select('matricula');
            // ->leftJoin('parametro.parent', 'padre');
    }

}
