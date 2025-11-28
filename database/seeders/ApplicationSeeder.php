<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\ApplicationAccount;
use App\Models\CrmAdmin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================
        // 🔐 ÉTAPE 1: Créer l'admin CRM (VOUS)
        // ============================================
        $admin = CrmAdmin::updateOrCreate(
            ['email' => 'admincrm@uits.ma'], 
            [
                'name' => 'Admin UITS',
                'password' => Hash::make('UitsMotDePasse123#@'), // ⚠️ Changez ceci !
                'is_active' => true,
            ]
        );

        $this->command->info("✅ Admin CRM créé: {$admin->email}");
        $this->command->newLine();

        // ============================================
        // 🔐 ÉTAPE 2: Créer les applications
        // ============================================
        $applications = [
            [
                'name' => 'PORTAIL ETUDIANT',
                'slug' => 'uits-portail',
                'url' => 'https://uits-portail.ma',
                'vps_location' => 'VPS 2',
                'icon' => '🌐',
                'description' => 'Portail de gestion des formations et des étudiants',
                'order' => 1,
                'roles' => [
                    
                    'Admin' => ['admin@gmail.com', 'etudiant@@UITSapp1'],
                    
                ]
                ],
            [
                'name' => 'UITS FACTURATION',
                'slug' => 'uits-admin',
                'url' => 'https://uits-admin.ma',
                'vps_location' => 'VPS 1',
                'icon' => '🔧',
                'description' => 'Portail Accès Stock Facturation',
                'order' => 2,
                'roles' => [
                    'Admin' => ['admin@gmail.com', 'P@ssw0rdP@ssw0rd'],
                    
                ]
            ],
            [
                'name' => 'UITS MANAGEMENT',
                'slug' => 'uits-mgmt',
                'url' => 'https://uits-mgmt.ma',
                'vps_location' => 'VPS 1',
                'icon' => '📊',
                'description' => 'Système de gestion des opérations',
                'order' => 3,
                'roles' => [
                    'Sup_Admin' => ['admin@gmail.com', 'rh@@UITSapp2'],
                   
                ]
            ]
            
        ];

        foreach ($applications as $appData) {
            $roles = $appData['roles'];
            unset($appData['roles']);

            // Créer l'application
            $app = Application::updateOrCreate(
                ['slug' => $appData['slug']],
                $appData
            );
            
            $this->command->info("📱 {$app->name}");

            // Créer les comptes
            foreach ($roles as $roleName => $credentials) {
                ApplicationAccount::updateOrCreate(
                    [
                        'application_id' => $app->id,
                        'crm_admin_id' => $admin->id,
                        'role_name' => $roleName
                    ],
                    [
                        'username' => $credentials[0],
                        'password' => $credentials[1],
                        'notes' => "Compte {$roleName}",
                        'is_active' => true
                    ]
                );

                $this->command->info("   → {$roleName}");
            }

            $this->command->newLine();
        }

        $this->command->info("🎉 Configuration terminée !");
        $this->command->newLine();
        $this->command->warn("📝 Identifiants de connexion CRM:");
        $this->command->line("   Email: admin@uits.ma");
        $this->command->line("   Mot de passe: VotreMotDePasse123");
        $this->command->newLine();
        $this->command->error("⚠️  IMPORTANT: Changez ces identifiants dans le fichier Seeder !");
    }
}