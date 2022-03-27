<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordEncod;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->passwordEncod= $encoder;
    }
    public function load(ObjectManager $manager): void
    {
         $product = new User();
         $product->setLastName('ADMIN');
         $product->setFirstName('ADM');
         $product->setRoles(['ROLE_SUPER_ADMIN']);
         $product->setEmail('Admin@gmail.com');
         $product->setIsActive(true);
         $product->setCivility(1);
        $product->setPassword($this->passwordEncod->hashPassword($product, '1234567'));

        $product->setIsSuperAdmin(true);

         $manager->persist($product);
        $manager->flush();
    }
}
