<?php

namespace Pidia\Apps\Demo\Repository;
use CarlosChininin\App\Infrastructure\Repository\BaseRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use CarlosChininin\Util\Filter\DoctrineValueSearch;
use CarlosChininin\Util\Http\ParamFetcher;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pidia\Apps\Demo\Entity\Pago;

/**
 * @extends ServiceEntityRepository<Pago>
 */
class PagoRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pago::class);
    }

    
    public function filterQuery(array|ParamFetcher $params, array $permissions = []): QueryBuilder
    {
        // dd("cadena ene jecucion");
        $queryBuilder = $this->allQuery()
        ->select('pago.monto as monto ')
        ->addSelect('pago.fecha as fecha ')
        ->addSelect('pago.uuid as uuid ')
        ->addSelect('alumno.nombre as nombre')
        ->addSelect('alumno.apellido as apellido')
        ->addSelect('pago.isActive as isActive')
        ->addSelect('usuario.username as username');
        ;

        DoctrineValueSearch::apply($queryBuilder, $params->getNullableString('b'), ['pago.monto']);
        dump($queryBuilder);
        // dd($queryBuilder);
        return $queryBuilder;
    }
    public function allQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('pago')
        ->select('pago', 'matricula', 'alumno','usuario')
        ->join('pago.usuario', 'usuario')
        ->join('pago.matricula', 'matricula')
        ->join('matricula.alumno', 'alumno');
            // ->leftJoin('parametro.parent', 'padre');
    }
    // use App\Repository\ProductoRepository;
    public function getPagosNombre(): array
    {
        $queryBuilder = $this->createQueryBuilder('pago')
            // ->select('pago', 'matricula', 'alumno')
            ->select('pago.monto as monto ')
            ->addSelect('pago.fecha as fecha ')
            ->addSelect('alumno.nombre as nombre')
            ->addSelect('alumno.apellido as apellido')
            ->join('pago.matricula', 'matricula')
            ->join('matricula.alumno', 'alumno')
            ->where('alumno.activo = true')
            // ->orderBy('producto.fechaCreacion', 'DESC')
        ;

        // if (null !== $categoriaId) {
        //     $queryBuilder
        //         ->andWhere('categoria.id = :categoriaId')
        //         ->setParameter('categoriaId', $categoriaId);
        // }

        return $queryBuilder
            // ->setMaxResults($results)
            ->getQuery()
            ->getResult();
    }

    public function getPagosNombre2(): array
    {
        $queryBuilder = $this->createQueryBuilder('pago')
            // ->select('pago', 'matricula', 'alumno')
            ->select('pago.monto as monto ')
            ->addSelect('pago.fecha as fecha ')
            ->addSelect('alumno.nombre as nombre')
            ->addSelect('alumno.apellido as apellido')
            ->join('pago.matricula', 'matricula')
            ->join('matricula.alumno', 'alumno')
            ->where('alumno.activo = true')
            // ->orderBy('producto.fechaCreacion', 'DESC')
        ;

        // if (null !== $categoriaId) {
        //     $queryBuilder
        //         ->andWhere('categoria.id = :categoriaId')
        //         ->setParameter('categoriaId', $categoriaId);
        // }

        return $queryBuilder
            // ->setMaxResults($results)
            ->getQuery()
            ->getResult();
    }

    public function graficoPagos(): array
    {
        $queryBuilder = $this->createQueryBuilder('data')
        // ->select('pago', 'matricula', 'alumno')
        ->select('data.fecha as fecha')
        ->addSelect('SUM(data.monto) as monto')
        // ->where('data.activo = true')
        ->groupBy('data.fecha')
        ->getQuery()
        ->getResult();
        return $queryBuilder;
            // ->setMaxResults($results)
            
    }
}
