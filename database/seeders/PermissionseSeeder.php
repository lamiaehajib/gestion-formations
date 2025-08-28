<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category; // Add this line to import the Category model
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class PermissionseSeeder extends Seeder
{
    public function run()
    {
        // Clear cache for roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Permissions
        $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'inscription-list',
            'inscription-create',
            'inscription-edit',
            'inscription-delete',
            'inscription-view-own',
            'profile-edit-own',



            // NEW: Payment Permissions
            'payment-list',            // Admin and student can see their own payments
            'payment-create',          // Admin and student can create payments
            'payment-edit',            // Admin can edit all, student can only edit their own
            'payment-delete',          // Only Admin can delete payments
            'payment-view',            // Admin can view all, student can view their own
            'payment-update-status',   // Only Admin can change payment status
            'payment-export',          // Only Admin can export payments
            'payment-bulk-update',     // Only Admin can perform bulk updates
            'payment-view-stats',      // Only Admin can see payment statistics

            // NEW: Course Permissions
            'course-list',       // Ability to view the list of courses
            'course-create',     // Ability to create new courses
            'course-edit',       // Ability to edit existing courses
            'course-delete',     // Ability to delete courses
            'course-view',       // Ability to view individual course details
            'course-join',       // Ability to join a course (e.g., click Zoom link)
            'course-download-document', // Ability to download course documents
            'course-manage-all', // Admin/Super Admin permission to bypass row-level security
            'course-manage-own', // Consultant permission to manage *their own* courses

              'inscription-create-own', 

            'category-list',          // Ability to view the list of categories
            'category-create',        // Ability to create new categories
            'category-edit',          // Ability to edit existing categories
            'category-delete',        // Ability to delete categories
            'category-toggle-status', // Ability to activate/deactivate categories
            'category-export',        // Ability to export categories
            'category-bulk-action',   // Ability to perform bulk actions on categories


             'formation-list',             // Ability to view the list of formations
            'formation-create',           // Ability to create new formations
            'formation-edit',             // Ability to edit existing formations
            'formation-delete',           // Ability to delete formations
            'formation-view',             // Ability to view individual formation details
           
            // Specific Formation-related permissions
            'formation-duplicate',          // Ability to duplicate a formation
            'formation-toggle-status',      // Ability to publish/unpublish a formation
            'formation-view-statistics',    // Ability to view formation statistics
            'formation-export',             // Ability to export formation data
            'formation-view-calendar',      // Ability to view formations in a calendar
            'formation-get-active-inscriptions', // Ability to get active inscriptions count (AJAX)
            'formation-get-by-category',    // Ability to get formations by category (AJAX, generally more public)



            // NEW: Reclamation Permissions
            'reclamation-list',           // Ability to view the list of reclamations
            'reclamation-create',         // Ability to create new reclamations
            'reclamation-edit',           // Ability to edit existing reclamations
            'reclamation-delete',         // Ability to delete reclamations
            'reclamation-view',           // Ability to view individual reclamation details
            'reclamation-view-own',       // Ability to view own reclamations only
            'reclamation-assign',         // Ability to assign reclamations to users
            'reclamation-respond',        // Ability to respond to reclamations
            'reclamation-rate',           // Ability to rate reclamation resolution
            'reclamation-statistics',     // Ability to view reclamation statistics
            'reclamation-export',         // Ability to export reclamation data
            'reclamation-update-status',  // Ability to update reclamation status

            'promotions',
            

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Create Roles (if they don't exist)
        $roles = ['Admin', 'Etudiant', 'Consultant', 'Finance', 'Super Admin'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 3. Assign ALL permissions to the 'Admin' role
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->syncPermissions(Permission::all()); // Admin has all permissions
        }

        // 4. Assign specific permissions to other roles
        $financeRole = Role::where('name', 'Finance')->first();
        if ($financeRole) {
            $financeRole->givePermissionTo([
                'inscription-list',
                'inscription-create',
                'inscription-edit',
                'course-list',       // Finance might need to see courses
                'course-view',       // Finance might need to view course details
            ]);
        }
        
        $etudiantRole = Role::where('name', 'Etudiant')->first();
        if ($etudiantRole) {
            $etudiantRole->givePermissionTo([
                'inscription-list',
                'inscription-view-own',
                'profile-edit-own',
                'course-list',       // Students need to see courses
                'course-view',       // Students need to view course details
                'course-join',       // Students need to join courses
                'course-download-document', // Students might need to download documents
            ]);
        }

        $consultantRole = Role::where('name', 'Consultant')->first();
        if ($consultantRole) {
            $consultantRole->givePermissionTo([
                'inscription-list',
                'user-list',
                'profile-edit-own',
                'course-list',           // Consultants need to see courses
                'course-view',           // Consultants need to view course details
                'course-create',         // Consultants create courses
                'course-edit',           // Consultants edit courses
                'course-delete',         // Consultants delete courses
                'course-join',           // Consultants join their own courses
                'course-download-document', // Consultants download their own documents
                'course-manage-own',     // Grants a general permission for their own courses
            ]);
        }

        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdminRole->syncPermissions(Permission::all()); // Super Admin also gets all
        }

        // ---
        ## Add Categories
        // Add the categories here
        $categories = [
            ['name' => 'Master Professionnelle', 'description' => 'Programmes de Master Professionnelle', 'is_active' => true],
            ['name' => 'Licence Professionnelle', 'description' => 'Programmes de Licence Professionnelle', 'is_active' => true],
            ['name' => 'Formation Continue', 'description' => 'Programmes de formation continue', 'is_active' => true],
            ['name' => 'All in One', 'description' => 'Catégorie regroupant toutes les formations', 'is_active' => true],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['name' => $categoryData['name']],
                $categoryData
            );
        }
        // ---

        // 5. Create or update users and assign their roles
        // Admin User
        $adminEmail = 'admin@gmail.com';
        $adminUser = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Admin User',
                'password' => Hash::make('123456'),
            ]
        );
        $adminUser->assignRole('Admin');

        // Etudiant User
        $etudiantEmail = 'etudiant@gmail.com';
        $etudiantUser = User::firstOrCreate(
            ['email' => $etudiantEmail],
            [
                'name' => 'Etudiant User',
                'password' => Hash::make('password'),
            ]
        );
        $etudiantUser->assignRole('Etudiant');

        // Consultant User
        $consultantEmail = 'consultant@gmail.com';
        $consultantUser = User::firstOrCreate(
            ['email' => $consultantEmail],
            [
                'name' => 'Consultant User',
                'password' => Hash::make('password'),
            ]
        );
        $consultantUser->assignRole('Consultant');
    }
}