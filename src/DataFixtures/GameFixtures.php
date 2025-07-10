<?php

namespace App\DataFixtures;

use App\Entity\Game;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GameFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $games = [
            [
                'title' => 'Cyberpunk 2077',
                'description' => 'Jeu de rôle futuriste dans une ville dystopique. Incarnez V, un mercenaire à la recherche d\'un implant unique qui est la clé de l\'immortalité.',
                'price' => 59.99,
                'image' => 'cyberpunk2077.jpg'
            ],
            [
                'title' => 'The Witcher 3: Wild Hunt',
                'description' => 'Aventure RPG dans un monde de fantasy. Incarnez Geralt de Riv dans sa quête pour retrouver Ciri.',
                'price' => 39.99,
                'image' => 'witcher3.jpg'
            ],
            [
                'title' => 'Minecraft',
                'description' => 'Jeu de construction et d\'aventure en monde ouvert. Construisez, explorez et survivez dans un monde fait de blocs.',
                'price' => 26.95,
                'image' => 'minecraft.jpg'
            ],
            [
                'title' => 'Grand Theft Auto V',
                'description' => 'Jeu d\'action en monde ouvert. Explorez Los Santos et suivez les aventures de trois criminels.',
                'price' => 29.99,
                'image' => 'gtav.jpg'
            ],
            [
                'title' => 'Red Dead Redemption 2',
                'description' => 'Western épique. Incarnez Arthur Morgan dans l\'Amérique de 1899 avec la bande de Dutch van der Linde.',
                'price' => 49.99,
                'image' => 'rdr2.jpg'
            ],
            [
                'title' => 'Among Us',
                'description' => 'Jeu multijoueur de déduction sociale. Travaillez en équipe pour maintenir votre vaisseau spatial... mais méfiez-vous des imposteurs !',
                'price' => 4.99,
                'image' => 'amongus.jpg'
            ]
        ];

        foreach ($games as $gameData) {
            $game = new Game();
            $game->setTitle($gameData['title']);
            $game->setDescription($gameData['description']);
            $game->setPrice($gameData['price']);
            $game->setImage($gameData['image']);
            
            $manager->persist($game);
        }

        $manager->flush();
    }
}
