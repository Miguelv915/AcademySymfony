<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Manager;

use CarlosChininin\App\Infrastructure\Manager\CRUDManager;
use Doctrine\Common\Collections\Expr\Value;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Arabic;
use Pidia\Apps\Demo\Repository\MatriculaRepository;
use Pidia\Apps\Demo\Repository\PagoRepository;
use Symfony\Bundle\SecurityBundle\Security;

final class PagoManager extends CRUDManager
{
    public function __construct(private PagoRepository $repository, Security $security, MatriculaRepository $matriculaRepository)
    {
        // $this->matriculaRepository = $matriculaRepository;
        parent::__construct($repository, $security);
    }

    public  function listadoPagos():array
    {

        return $this->repository->getPagosNombre();
    }
    public  function getPagosFechas():array
    {
        $res = $this->repository->graficoPagos();
        foreach ($res as $key => $value) {
            $res[$key]["monto"]=(float) $value["monto"];
            $res[$key]['fecha']= $value['fecha']->format('Y-m-d');
            // $obj["monto "]= (int) $value["monto"];
        }
        return $res;
    }


}
