<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\AppBank;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {//Popula o banco de dados com 10 bancos

        for ($i = 1; $i <= 10; $i++) {
            $bank = new AppBank();
            $bank->setNumber(mt_rand(100, 10000));
            $bank->setName('Teste_'.$i);

            $manager->persist($bank);
            $manager->flush();
        }
    }
}
