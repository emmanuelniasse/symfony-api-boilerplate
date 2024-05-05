<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Product;
use App\Entity\User;

class ProductTest extends WebTestCase
{
    public function testCreateProduct(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine')->getRepository(User::class);
        $firstUser = $userRepository->findOneBy([]);

        $client->request('POST', '/products', [], [], [], json_encode([
            "owner_id" => $firstUser->getId(),
            "name" => "souris",
            "description" => "Souris Logitech MX Master 3"
        ]));

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
    }

    public function testGetProducts(): void
    {
        $client = static::createClient();
        $client->request('GET', '/products');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }
    
    public function testGetProduct(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine')->getRepository(User::class);
        $firstUser = $userRepository->findOneBy([]);
    
        $product = new Product();
        $product->setOwner($firstUser);
        $product->setName('souris');
        $product->setDescription('Souris Logitech MX Master 3');
    
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($product);
        $entityManager->flush();
    
        $productId = $product->getId();

        $client->request('GET', '/products/' . $productId);
    
        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }
}