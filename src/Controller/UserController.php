<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use App\Entity\AppBankAccount;

use App\Entity\AppUser;
use App\Entity\AppBank;

use Doctrine\Persistence\ManagerRegistry;

class UserController extends AbstractController
{
    #[Route('/user', name: 'show', defaults: ['name' => '', 'cpf' => '', 'email' => '', 'itensPerPage' => '', 'orderBy' => '', 'orientation' => ''], methods: 'get')]

    public function show(ManagerRegistry $doctrine, Request $request): Response
    { //Busca usuário pelos parâmetros name, cpf e email e organiza os resultados pelos parâmetros itensPerPage, orderBy e orientation -> Todos os parâmetros são opcionais.

        $name = $request->query->get('name');
        $cpf = $request->query->get('cpf');
        $email = $request->query->get('email');
        $itensPerPage = $request->query->get('itensPerPage');
        $orderBy = $request->query->get('orderBy');
        $orientation = $request->query->get('orientation');

        $paramsSearch = array(); //Array com os parâmetros preenchidos para busca 
        $paramsOrder = array(); //Array com os parâmetros preenchidos para organização 

        $users = $doctrine->getRepository(AppUser::class);

        if (!empty($orderBy)) {
            $paramsOrder = array($orderBy => (!empty($orientation) ? $orientation : 'ASC'));
        }

        if (!empty($name)) {
            $paramsSearch['name'] = $name;
        }


        if (!empty($cpf)) {
            $paramsSearch['cpf'] = $cpf;
        }

        if (!empty($email)) {
            $paramsSearch['email'] = $email;
        }

        $users = $users->findBy(
            $paramsSearch,
            $paramsOrder,
            (!empty($itensPerPage) ? $itensPerPage : null)
        );


        if (!$users) {
            throw $this->createNotFoundException(
                'No user found.'
            );
        }

        $data = array();

        foreach ($users as $user) { //Loop para organizar dos dados de retorno do json
            $data[] = array(
                'id' => $user->getId(),
                'name' => $user->getName(),
                'cpf' => $user->getCpf(),
                'email' => $user->getEmail()
            );
        }

        return $this->json([
            $data
        ]);
    }

    #[Route('/user/{id}', name: 'searchId', methods: 'get')]

    public function searchId(ManagerRegistry $doctrine, int $id): Response
    { //Busca usuário pelo Id
        $user = $doctrine->getRepository(AppUser::class)->findOneById($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No user found.'
            );
        }

        return $this->json([
            'id' => $user->getId(),
            'name' => $user->getName(),
            'cpf' => $user->getCpf(),
            'email' => $user->getEmail()
        ]);
    }

    #[Route('/user', name: 'addUser', methods: 'post')]

    public function addUser(ValidatorInterface $validator, ManagerRegistry $doctrine, Request $request): Response
    { //Adiciona um usuário no banco de dados

        $name = $request->query->get('name');
        $cpf = $request->query->get('cpf');
        $email = $request->query->get('email');


        $entityManager = $doctrine->getManager();

        $user = new AppUser();
        $user->setName($name);
        $user->setCpf($cpf);
        $user->setEmail($email);


        $errors = $validator->validate($user); //Valida os dados da entidade AppUser
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return new Response('Saved new user with id ' . $user->getId());
    }

    #[Route('/user', name: 'updateUser', methods: 'put')]

    public function updateUser(ValidatorInterface $validator, ManagerRegistry $doctrine, Request $request): Response
    { //Atualiza um usuário no banco de dados

        $id = $request->query->get('id');
        $name = $request->query->get('name');
        $cpf = $request->query->get('cpf');
        $email = $request->query->get('email');

        $entityManager = $doctrine->getManager();

        $user = $entityManager->getRepository(AppUser::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No user found.'
            );
        }

        $user->setName($name);
        $user->setCpf($cpf);
        $user->setEmail($email);

        $errors = $validator->validate($user); //Valida os dados da entidade AppUser
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return new Response('user with id ' . $user->getId() . ' updated');
    }


    #[Route('/user/{id}', name: 'deleteById', methods: 'delete')]

    public function deleteById(ManagerRegistry $doctrine, int $id): Response
    { //Remove um usuário do banco de dados

        $user = $doctrine->getRepository(AppUser::class)->findOneById($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No user found.'
            );
        }

        $entityManager = $doctrine->getManager();

        $entityManager->remove($user);
        $entityManager->flush();

        return new Response('user with id ' . $id . ' deleted');
    }

    //------------------------------------------------------------------------------------------------------------------------


    #[Route('/user/{id}/bank_account', name: 'addBankUser', methods: 'post')]

    public function addBankUser(ValidatorInterface $validator, ManagerRegistry $doctrine, Request $request, int $id): Response
    { //Adiciona uma conta bancária associada a um usuário

        $banksAccountsArray = json_decode($request->query->get('arrayCollection')); //Decodifica o json com os dados de entrada

        $ids = array(); // Armazena os Ids incluidos no banco de dados


        foreach ($banksAccountsArray as $bankAccountArray) {
            $entityManager = $doctrine->getManager();

            $bankAccount = new AppBankAccount();

            $user = $entityManager->getRepository(AppUser::class)->find($id); // Busca o usuário a ser associado ao banco de dados
            $bank = $entityManager->getRepository(AppBank::class)->find($bankAccountArray->app_bank_id); // Busca o banco a ser associado ao banco de dados

            if (!$user) {
                throw $this->createNotFoundException(
                    'User ' . $id . ' not found.'
                );
            }

            if (!$bank) {
                throw $this->createNotFoundException(
                    'Bank ' . $bankAccountArray->app_bank_id . ' not found.'
                );
            }

            $bankAccount->setAccountName($bankAccountArray->account_name);
            $bankAccount->setAgency($bankAccountArray->agency);
            $bankAccount->setAgencyDigit($bankAccountArray->agency_digit);
            $bankAccount->setAccountNumber($bankAccountArray->account_number);
            $bankAccount->setAccountDigit($bankAccountArray->account_digit);
            $bankAccount->setAccountType($bankAccountArray->account_type);
            $bankAccount->setAppUserId($user);
            $bankAccount->setAppBankId($bank);


            $errors = $validator->validate($bankAccount); //Valida os dados da entidade AppBankAccount
            if (count($errors) > 0) {
                return new Response((string) $errors, 400);
            }

            $entityManager->persist($bankAccount);
            $entityManager->flush();

            $ids[] = $bankAccount->getId();
        }

        return new Response('Saved new BankAccount with id(s): ' . implode(',', $ids));
    }

    #[Route('/user/{id}/bank_account', name: 'getBankUser', methods: 'get')]

    public function getBankUser(ManagerRegistry $doctrine, Request $request, int $id): Response
    { //Retorna todos os registros de dados bancarios de um usuário

        $banksAccountsArray = $doctrine->getRepository(AppBankAccount::class)->findBy(array('app_user_id' => $id));

        if (!$banksAccountsArray) {
            throw $this->createNotFoundException(
                'No records found.'
            );
        }

        $data = array();

        foreach ($banksAccountsArray as $bankAccount) { //Loop para organizar o json de retorno
            $user = $bankAccount->getAppUserId();
            $bank = $bankAccount->getAppBankId();
            $data[] = array(
                'account_name' => $bankAccount->getAccountName(),
                'agency' => $bankAccount->getAgency(),
                'agency_digit' => $bankAccount->getAgencyDigit(),
                'account_number' => $bankAccount->getAccountNumber(),
                'account_digit' => $bankAccount->getAccountDigit(),
                'account_type' => $bankAccount->getAccountType(),
                'User' =>
                [
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'cpf' => $user->getCpf(),
                    'email' => $user->getEmail()
                ],
                'Bank' =>
                [
                    'id' => $bank->getId(),
                    'name' => $bank->getName(),
                    'number' => $bank->getNumber()
                ]
            );
        }

        return $this->json([
            $data
        ]);
    }
    #[Route('/user/{id}/bank_account/{bankId}', name: 'getBankUserId', methods: 'get')]

    public function getBankUserId(ManagerRegistry $doctrine, Request $request, int $id, int $bankId): Response
    { //Retorna os dados bancarios de um registro especifico de um usuário
        $bankAccount = $doctrine->getRepository(AppBankAccount::class)->findOneBy(array('app_user_id' => $id, 'app_bank_id' => $bankId));

        if (!$bankAccount) {
            throw $this->createNotFoundException(
                'No records found.'
            );
        }

        $data = array();

        //Array para organizar o json de retorno
        $user = $bankAccount->getAppUserId();
        $bank = $bankAccount->getAppBankId();
        $data[] = array(
            'account_name' => $bankAccount->getAccountName(),
            'agency' => $bankAccount->getAgency(),
            'agency_digit' => $bankAccount->getAgencyDigit(),
            'account_number' => $bankAccount->getAccountNumber(),
            'account_digit' => $bankAccount->getAccountDigit(),
            'account_type' => $bankAccount->getAccountType(),
            'User' =>
            [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'cpf' => $user->getCpf(),
                'email' => $user->getEmail()
            ],
            'Bank' =>
            [
                'id' => $bank->getId(),
                'name' => $bank->getName(),
                'number' => $bank->getNumber()
            ]
        );


        return $this->json([
            $data
        ]);
    }

    #[Route('/user/{id}/bank_account/{bankId}', name: 'updateBankUserId', methods: 'post')]

    public function updateBankUserId(ValidatorInterface $validator, ManagerRegistry $doctrine, Request $request, int $id, int $bankId): Response
    {//Atualiza um registro bancario especifico de um usuário

        $account_name = $request->query->get('account_name');
        $agency = $request->query->get('agency');
        $agency_digit = $request->query->get('agency_digit');
        $account_number = $request->query->get('account_number');
        $account_digit = $request->query->get('account_digit');
        $account_type = $request->query->get('account_type');


        $entityManager = $doctrine->getManager();

        $bankAccount = $entityManager->getRepository(AppBankAccount::class)->findOneBy(array('app_user_id' => $id, 'app_bank_id' => $bankId));

        if (!$bankAccount) {
            throw $this->createNotFoundException(
                'No records found.'
            );
        }

        $user = $entityManager->getRepository(AppUser::class)->find($id);
        $bank = $entityManager->getRepository(AppBank::class)->find($bankId);

        if (!$user) {
            throw $this->createNotFoundException(
                'User ' . $id . ' not found.'
            );
        }

        if (!$bank) {
            throw $this->createNotFoundException(
                'Bank ' . $bankId . ' not found.'
            );
        }

        $bankAccount->setAccountName($account_name);
        $bankAccount->setAgency($agency);
        $bankAccount->setAgencyDigit($agency_digit);
        $bankAccount->setAccountNumber($account_number);
        $bankAccount->setAccountDigit($account_digit);
        $bankAccount->setAccountType($account_type);
        $bankAccount->setAppUserId($user);
        $bankAccount->setAppBankId($bank);

        $errors = $validator->validate($bankAccount);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        $entityManager->persist($bankAccount);
        $entityManager->flush();

        return new Response('BankUser with id ' . $bankAccount->getId() . ' updated');
    }

    #[Route('/user/{id}/bank_account/{bankId}', name: 'deleteBankById', methods: 'delete')]

    public function deleteBankById(ManagerRegistry $doctrine, int $id, int $bankId): Response
    {//Remove um registro bancario especifico de um usuário
        $bankAccount = $doctrine->getRepository(AppBankAccount::class)->findOneBy(array('app_user_id' => $id, 'app_bank_id' => $bankId));

        if (!$bankAccount) {
            throw $this->createNotFoundException(
                'No records found.'
            );
        }

        $entityManager = $doctrine->getManager();

        $entityManager->remove($bankAccount);
        $entityManager->flush();

        return new Response('BankUser with id ' . $id . ' deleted');
    }
}
