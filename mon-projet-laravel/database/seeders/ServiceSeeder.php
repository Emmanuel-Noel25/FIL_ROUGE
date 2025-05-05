<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Services; // Assurez-vous que le chemin d'accès à votre modèle Service est correct
use App\Models\Prestataire; // Assurez-vous que le chemin d'accès à votre modèle Prestataire est correct
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $prices = [5000, 2000, 3000]; // Définir un tableau de prix

        $services = [
            ['title' => 'Vaisselle', 'disponibilite' => 'Disponible'],
            ['title' => 'Ménage',  'disponibilite' => 'Disponible'],
            ['title' => 'Lessive',  'disponibilite' => 'Disponible'],
            ['title' => 'Tonte de gazon', 'disponibilite' => 'Disponible'],
            ['title' => 'Plantation d\'arbres',  'disponibilite' => 'Sur demande'],
            ['title' => 'Coupe d\'arbres',  'disponibilite' => 'Sur demande'],
            ['title' => 'Éducation thérapeutique',  'disponibilite' => 'Disponible'],
            ['title' => 'Soins médicaux spécialisés',  'disponibilite' => 'Disponible'],
            ['title' => 'Coordination des soins',  'disponibilite' => 'Disponible'],
            ['title' => 'Livraison de marchandises volumineuses ou fragiles', 'disponibilite' => 'Sur demande'],
            ['title' => 'Déménagement complet ou partiel',  'disponibilite' => 'Sur demande'],
            ['title' => 'Transport de personnes',  'disponibilite' => 'Disponible'],
        ];

        // Méthode 1 : Utilisation de create() sur le modèle
        foreach ($services as $serviceData) {
            $serviceData['prestataires_id'] = Prestataire::inRandomOrder()->value('id');
            $serviceData['price'] = $prices[array_rand($prices)]; // Ajouter un prix aléatoire
            Services::create($serviceData);
        }

        // Méthode 2 : Utilisation de DB::table()->insert() (plus rapide pour de gros volumes)
        // $servicesWithPrestataire = array_map(function ($service) use ($prices) {
        //     $service['prestataire_id'] = Prestataire::inRandomOrder()->value('id');
        //     $service['price'] = $prices[array_rand($prices)];
        //     $service['created_at'] = now();
        //     $service['updated_at'] = now();
        //     return $service;
        // }, $services);
        // DB::table('services')->insert($servicesWithPrestataire);
    }
}
