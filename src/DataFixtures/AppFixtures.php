<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $categories = [
            'category1' => 'this is the first category',
            'category2' => 'this is the second category',
            'category3' => 'this is the third category',
        ];

        $users = [
            'user1@mail.com' => '12345678',
            'user2@mail.com' => '12345678',
            'user3@mail.com' => 'password',
        ];

        // on fait les 3 catégories
        foreach ($categories as $name => $value) {
            $category = new Category();
            $category->setLabel($name);
            $category->setDescription($value);

            $manager->persist($category);

            // on fait 10 produits aléatoires par catégories
            for ($i = 0 ; $i < 10; $i++) {
                $product = new Product();
                $product->setCategory($category);
                $product->setLabel($faker->words(3, true));
                $product->setPrice($faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 100));
                $product->setStock($faker->randomNumber($nbDigits = 4, $strict = false));
                $now = new \DateTimeImmutable();
                $product->setCreatedAt($now);
                $product->setUpdatedAt($now);

                $manager->persist($product);
            }
        }

        foreach ($users as $email => $password) {
            $user = new User();
            $user->setEmail($email);
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            $user->setRoles(['ROLE_USER']);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
