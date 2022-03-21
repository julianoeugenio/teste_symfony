<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\AppBank;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;

class BankController extends AbstractController
{
    #[Route('/bank', name: 'showBanks', defaults: ['name' => '', 'itensPerPage' => '', 'orderBy' => '', 'orientation' => ''], methods: 'get')]

    public function show(ManagerRegistry $doctrine, Request $request): Response
    {//Busca bancos pelo parâmetro name e organiza os resultados pelos parâmetros itensPerPage, orderBy e orientation -> Todos os parâmetros são opcionais.


        $name = $request->query->get('name');
        $itensPerPage = $request->query->get('itensPerPage');
        $orderBy = $request->query->get('orderBy');
        $orientation = $request->query->get('orientation');

        $paramsSearch = array();//Array com os parâmetros preenchidos para busca 
        $paramsOrder = array(); //Array com os parâmetros preenchidos para organização 

        $banks = $doctrine->getRepository(AppBank::class);

        if (!empty($orderBy)) {
            $paramsOrder = array($orderBy => (!empty($orientation) ? $orientation : 'ASC'));
        }

        if (!empty($name)) {
            $paramsSearch['name'] = $name;
        }


        $banks = $banks->findBy(
            $paramsSearch,
            $paramsOrder,
            (!empty($itensPerPage) ? $itensPerPage : null)
        );


        if (!$banks) {
            throw $this->createNotFoundException(
                'No bank found.'
            );
        }

        $data = array();

        foreach ($banks as $bank) {
            $data[] = array(
                'id' => $bank->getId(),
                'name' => $bank->getName(),
                'number' => $bank->getNumber()
            );
        }

        return $this->json([
            $data
        ]);
    }

    #[Route('/bank/{id}', name: 'searchBankId', methods: 'get')]

    public function searchBankId(ManagerRegistry $doctrine, int $id): Response
    { //Busca usuário pelo Id
        $bank = $doctrine->getRepository(AppBank::class)->findOneById($id);

        if (!$bank) {
            throw $this->createNotFoundException(
                'No bank found.'
            );
        }

        return $this->json([
            'id' => $bank->getId(),
            'name' => $bank->getName(),
            'number' => $bank->getNumber()
        ]);
    }
}
