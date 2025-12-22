<?php

namespace Database\Seeders;


use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

use App\Models\ProfileUser;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class PermissionRoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        // app()[PermissionRegistrar::class]->forgetCachedPermissions();
        // We have to follow strict protocol for adding permissions.
        // It must always be actionName_moduleName !!!VERY IMPORTANT

        // create permissions
        Permission::create(['name' => 'index permissions']);
        Permission::create(['name' => 'show permissions']);
        Permission::create(['name' => 'create permissions']);
        Permission::create(['name' => 'edit permissions']);
        Permission::create(['name' => 'destroy permissions']);

        // create permissions for roles
        Permission::create(['name' => 'index roles']);
        Permission::create(['name' => 'show roles']);
        Permission::create(['name' => 'create roles']);
        Permission::create(['name' => 'edit roles']);
        Permission::create(['name' => 'destroy roles']);

        // create setting types
        Permission::create(['name' => 'index setting_types']);
        Permission::create(['name' => 'show setting_types']);
        Permission::create(['name' => 'create setting_types']);
        Permission::create(['name' => 'edit setting_types']);
        Permission::create(['name' => 'destroy setting_types']);

        // create settings
        Permission::create(['name' => 'index settings']);
        Permission::create(['name' => 'show settings']);
        Permission::create(['name' => 'create settings']);
        Permission::create(['name' => 'edit settings']);
        Permission::create(['name' => 'destroy settings']);

        // create contractors
        Permission::create(['name' => 'index contractors']);
        Permission::create(['name' => 'show contractors']);
        Permission::create(['name' => 'create contractors']);
        Permission::create(['name' => 'edit contractors']);
        Permission::create(['name' => 'destroy contractors']);
        Permission::create(['name' => 'appointment contractors']);
        Permission::create(['name' => 'evaluation contractors']);
        Permission::create(['name' => 'requisition contractors']);
        Permission::create(['name' => 'work_order contractors']);

        // create contractor_bank_details
        Permission::create(['name' => 'index contractor_bank_details']);
        Permission::create(['name' => 'show contractor_bank_details']);
        Permission::create(['name' => 'create contractor_bank_details']);
        Permission::create(['name' => 'edit contractor_bank_details']);
        Permission::create(['name' => 'destroy contractor_bank_details']);

        // create contractor_documents
        Permission::create(['name' => 'index contractor_documents']);
        Permission::create(['name' => 'show contractor_documents']);
        Permission::create(['name' => 'create contractor_documents']);
        Permission::create(['name' => 'edit contractor_documents']);
        Permission::create(['name' => 'destroy contractor_documents']);

        // create contractor_director_technical_staffs
        Permission::create(['name' => 'index contractor_director_technical_staffs']);
        Permission::create(['name' => 'show contractor_director_technical_staffs']);
        Permission::create(['name' => 'create contractor_director_technical_staffs']);
        Permission::create(['name' => 'edit contractor_director_technical_staffs']);
        Permission::create(['name' => 'destroy contractor_director_technical_staffs']);

        // create contractor_equipments
        Permission::create(['name' => 'index contractor_equipments']);
        Permission::create(['name' => 'show contractor_equipments']);
        Permission::create(['name' => 'create contractor_equipments']);
        Permission::create(['name' => 'edit contractor_equipments']);
        Permission::create(['name' => 'destroy contractor_equipments']);

        // create contractor_works
        Permission::create(['name' => 'index contractor_works']);
        Permission::create(['name' => 'show contractor_works']);
        Permission::create(['name' => 'create contractor_works']);
        Permission::create(['name' => 'edit contractor_works']);
        Permission::create(['name' => 'destroy contractor_works']);

        // create contractor_quality_certificates
        Permission::create(['name' => 'index contractor_quality_certificates']);
        Permission::create(['name' => 'show contractor_quality_certificates']);
        Permission::create(['name' => 'create contractor_quality_certificates']);
        Permission::create(['name' => 'edit contractor_quality_certificates']);
        Permission::create(['name' => 'destroy contractor_quality_certificates']);

        // create contractor_contacts
        Permission::create(['name' => 'index contractor_contacts']);
        Permission::create(['name' => 'show contractor_contacts']);
        Permission::create(['name' => 'create contractor_contacts']);
        Permission::create(['name' => 'edit contractor_contacts']);
        Permission::create(['name' => 'destroy contractor_contacts']);

        // create users
        Permission::create(['name' => 'index users']);
        Permission::create(['name' => 'show users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'destroy users']);

        // create tenders
        Permission::create(['name' => 'index tenders']);
        Permission::create(['name' => 'show tenders']);
        Permission::create(['name' => 'create tenders']);
        Permission::create(['name' => 'edit tenders']);
        Permission::create(['name' => 'destroy tenders']);
        Permission::create(['name' => 'inactive tenders']);
        Permission::create(['name' => 'cancelled tenders']);
        Permission::create(['name' => 'upcoming tenders']);
        Permission::create(['name' => 're_tenders tenders']);
        Permission::create(['name' => 'start_bidding tenders']);

        Permission::create(['name' => 'index payments']);
        Permission::create(['name' => 'show payments']);
        Permission::create(['name' => 'destroy payments']);
        Permission::create(['name' => 'external site_managers']);
        Permission::create(['name' => 'evaluate site_managers']);

        Permission::create(['name' => 'overall contractor_tenders']);
        Permission::create(['name' => 'external contractor_tenders']);
        Permission::create(['name' => 'detail contractor_tenders']);
        Permission::create(['name' => 'automaticComparison contractor_tenders']);
        Permission::create(['name' => 'contractorWise contractor_tenders']);
        Permission::create(['name' => 'accept contractor_tenders']);
        Permission::create(['name' => 'reject contractor_tenders']);

        Permission::create(['name' => 'index notes']);
        Permission::create(['name' => 'create notes']);
        Permission::create(['name' => 'edit notes']);
        Permission::create(['name' => 'destroy notes']);

        Permission::create(['name' => 'external payments']);
        Permission::create(['name' => 'invoice payments']);

        Permission::create(['name' => 'index projects']);
        Permission::create(['name' => 'show projects']);
        Permission::create(['name' => 'create projects']);
        Permission::create(['name' => 'edit projects']);
        Permission::create(['name' => 'destroy projects']);

        Permission::create(['name' => 'index contractor_tender_work_orders']);
        Permission::create(['name' => 'create contractor_tender_work_orders']);
        Permission::create(['name' => 'edit contractor_tender_work_orders']);
        Permission::create(['name' => 'show contractor_tender_work_orders']);
        Permission::create(['name' => 'approve contractor_tender_work_orders']);

        // -- new permissions for backend upgrade --
        Permission::create(['name' => 'upload signed_work_order']);
        Permission::create(['name' => 'upload budget']);
        Permission::create(['name' => 'manage subscriptions']);
        Permission::create(['name' => 'manage invoices']);
        Permission::create(['name' => 'approve buyers']);
        Permission::create(['name' => 'vendor partial registration']);
        Permission::create(['name' => 'view calendar']);
        Permission::create(['name' => 'send otp_whatsapp']);
        Permission::create(['name' => 'view dashboard']);
        Permission::create(['name' => 'manage vendor_history']);
        Permission::create(['name' => 'manage project access']);

        // Now that we have created all the permissions, we will now create roles and start assigning permissions to it.
        // Let's GO
        $superAdmin = Role::create(['name' => 'Super Admin']);
        // all admins have all permissions
        $superAdminPermissions = Permission::all();
        $superAdmin->syncPermissions($superAdminPermissions);

        $superAdminUser1 = User::create(
            [
                'name' => 'Himanshu',
                'email' => 'himanshu.wandhre@meritest.in',
                'password' => Hash::make(123456789),
            ],
        );

        $superAdminUser2 = User::create(
            [
                'name' => 'Pallavi Dighe',
                'email' => 'superadmin@meritest.in',
                'password' => Hash::make(123456789),
            ],
        );

        $superAdminUser1->profile_user()->save(new ProfileUser([
            'mobile_no' => 7030501188,
            'avatar' => 'Super Admin',
        ]));

        $superAdminUser1->assignRole($superAdmin);

        $superAdminUser2->profile_user()->save(new ProfileUser([
            'mobile_no' => 7030501188,
            'avatar' => 'Super Admin',
        ]));

        $superAdminUser2->assignRole($superAdmin);

        $adminPermissions = [
            'index contractors',
            'show contractors',
            'create contractors',
            // 'edit contractors',
            // 'destroy contractors',
            'appointment contractors',
            'index contractor_documents',
            'show contractor_documents',
            'create contractor_documents',
            'edit contractor_documents',
            'destroy contractor_documents',
            'index contractor_director_technical_staffs',
            'show contractor_director_technical_staffs',
            'create contractor_director_technical_staffs',
            'edit contractor_director_technical_staffs',
            'destroy contractor_director_technical_staffs',
            'index contractor_equipments',
            'show contractor_equipments',
            'create contractor_equipments',
            'edit contractor_equipments',
            'destroy contractor_equipments',
            'index contractor_works',
            'show contractor_works',
            'create contractor_works',
            'edit contractor_works',
            'destroy contractor_works',
            'index contractor_quality_certificates',
            'show contractor_quality_certificates',
            'create contractor_quality_certificates',
            'edit contractor_quality_certificates',
            'destroy contractor_quality_certificates',
            'index contractor_contacts',
            'show contractor_contacts',
            'create contractor_contacts',
            'edit contractor_contacts',
            'destroy contractor_contacts',
            // 'index projects',
            // 'show projects',
            // 'create projects',
            // 'edit projects',
            // 'destroy projects',
            'index tenders',
            'show tenders',
            'create tenders',
            'edit tenders',
            'destroy tenders',
            'inactive tenders',
            'cancelled tenders',
            'upcoming tenders',
            're_tenders tenders',
            'overall contractor_tenders',
            'external contractor_tenders',
            'detail contractor_tenders',
            'automaticComparison contractor_tenders',
            'contractorWise contractor_tenders',
            'accept contractor_tenders',
            'reject contractor_tenders',
            'index notes',
            'create notes',
            'edit notes',
            'destroy notes',
            'index contractor_tender_work_orders',
            'create contractor_tender_work_orders',
            'edit contractor_tender_work_orders',
            'show contractor_tender_work_orders',
            'approve contractor_tender_work_orders',
            'upload signed_work_order',
            'upload budget',
            'manage subscriptions',
            'manage invoices',
            'approve buyers',
            'vendor partial registration',
            'view calendar',
            'send otp_whatsapp',
            'view dashboard',
            'manage vendor_history',
            'manage project access',
        ];
        $admin = Role::create(['name' => 'Admin']);
        $admin->syncPermissions($adminPermissions);

        $siteManagerPermissions = [
            'index contractors',
            'show contractors',
            'requisition contractors',
            'external site_managers',
            'evaluate site_managers',
            'index notes',
            'create notes',
            'edit notes',
            'destroy notes',
        ];
        $sitePM = Role::create(['name' => 'Site Project Manager']);
        $sitePM->syncPermissions($siteManagerPermissions);

        $organizationPermissions = Permission::all();
        $organizationPermissions = [
            'index contractors',
            'show contractors',
            'create contractors',
            // 'edit contractors',
            // 'destroy contractors',
            'appointment contractors',
            'index contractor_documents',
            'show contractor_documents',
            'create contractor_documents',
            'edit contractor_documents',
            'destroy contractor_documents',
            'index contractor_director_technical_staffs',
            'show contractor_director_technical_staffs',
            'create contractor_director_technical_staffs',
            'edit contractor_director_technical_staffs',
            'destroy contractor_director_technical_staffs',
            'index contractor_equipments',
            'show contractor_equipments',
            'create contractor_equipments',
            'edit contractor_equipments',
            'destroy contractor_equipments',
            'index contractor_works',
            'show contractor_works',
            'create contractor_works',
            'edit contractor_works',
            'destroy contractor_works',
            'index contractor_quality_certificates',
            'show contractor_quality_certificates',
            'create contractor_quality_certificates',
            'edit contractor_quality_certificates',
            'destroy contractor_quality_certificates',
            'index contractor_contacts',
            'show contractor_contacts',
            'create contractor_contacts',
            'edit contractor_contacts',
            'destroy contractor_contacts',
            'index projects',
            'show projects',
            'create projects',
            'edit projects',
            'destroy projects',
            'index tenders',
            'show tenders',
            'create tenders',
            'edit tenders',
            'destroy tenders',
            'inactive tenders',
            'cancelled tenders',
            'upcoming tenders',
            're_tenders tenders',
            'overall contractor_tenders',
            'external contractor_tenders',
            'detail contractor_tenders',
            'automaticComparison contractor_tenders',
            'contractorWise contractor_tenders',
            'accept contractor_tenders',
            'reject contractor_tenders',
            'index notes',
            'create notes',
            'edit notes',
            'destroy notes',
            'index contractor_tender_work_orders',
            'create contractor_tender_work_orders',
            'edit contractor_tender_work_orders',
            'show contractor_tender_work_orders',
            'approve contractor_tender_work_orders',
        ];

        $organization = Role::create(['name' => 'Organization']);
        $organization->syncPermissions($organizationPermissions);

        $contractorPermissions = [
            'index contractors',
            'show contractors',
            'appointment contractors',
            'index contractor_documents',
            'show contractor_documents',
            'create contractor_documents',
            'edit contractor_documents',
            'destroy contractor_documents',
            'index contractor_director_technical_staffs',
            'show contractor_director_technical_staffs',
            'create contractor_director_technical_staffs',
            'edit contractor_director_technical_staffs',
            'destroy contractor_director_technical_staffs',
            'index contractor_equipments',
            'show contractor_equipments',
            'create contractor_equipments',
            'edit contractor_equipments',
            'destroy contractor_equipments',
            'index contractor_works',
            'show contractor_works',
            'create contractor_works',
            'edit contractor_works',
            'destroy contractor_works',
            'index contractor_quality_certificates',
            'show contractor_quality_certificates',
            'create contractor_quality_certificates',
            'edit contractor_quality_certificates',
            'destroy contractor_quality_certificates',
            'index contractor_contacts',
            'show contractor_contacts',
            'create contractor_contacts',
            'edit contractor_contacts',
            'destroy contractor_contacts',
            'index tenders',
            'show tenders',
            'start_bidding tenders',
            'index notes',
            'create notes',
            'edit notes',
            'destroy notes',
        ];
        $contractor = Role::create(['name' => 'Contractor']);
        $contractor->syncPermissions($contractorPermissions);

        $accountantPermissions = [
            'index payments',
            'show payments',
            'destroy payments',
            'index contractors',
            'index contractor_bank_details',
            'show contractor_bank_details',
            'create contractor_bank_details',
            'edit contractor_bank_details',
            'index notes',
            'create notes',
            'edit notes',
            'destroy notes',
        ];
        $accountant = Role::create(['name' => 'Accountant']);
        $accountant->syncPermissions($accountantPermissions);
        //event(new Registered($superAdminUser));

        $gmPermissions = [
            'index contractor_tender_work_orders',
            'approve contractor_tender_work_orders',
            'index notes',
            'create notes',
            'edit notes',
            'destroy notes',
        ];

        $generalManager = Role::create(['name' => 'General Manager']);
        $generalManager->syncPermissions($gmPermissions);
    }
}
