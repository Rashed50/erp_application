<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // Clear Cache Permission
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();


    $permissions = [
      'role-list',
      'role-create',
      'role-edit',
      'role-delete',
      // Bank Informations
      'bank-info-list',
      // main contractor
      'main-contractor-list',
      // company Profile
      'company-profile',
      // Sub company Profile
      'subcompany-list',
      'subcompany-edit',
      'subcompany-delete',
      // E Voucher
      'evoucher-list',
      // banner
      'banner-list',
      'banner-create',
      'banner-edit',
      'banner-delete',
      // country
      'country-list',
      'country-create',
      'country-edit',
      // division
      'division-list',
      'division-create',
      'division-edit',
      // district
      'district-list',
      'district-create',
      'district-edit',
      // employee designation
      'designation-list',
      'designation-create',
      'designation-edit',
      'designation-delete',
      // sponser
      'sponser-list',
      'sponser-create',
      'sponser-edit',
      'sponser-delete',
      // qr code
      'qrcode-list',
      'qrcode-create',
      'qrcode-edit',
      'qrcode-delete',
      // project
      'project-list',
      'project-add',
      'project-edit',
      'project-delete',
      // project incharge
      'projectincharge-list',
      'projectincharge-add',
      'projectincharge-edit',
      'projectincharge-delete',
      // project image
      'projectimage-list',
      'projectimage-add',
      'projectimage-edit',
      'projectimage-delete',
      // approver
      'income-approve',
      'expenditure-approve',
      'job-approve',
      'leave-approve',
      // Employee
      'employee-list',
      'employee-add',
      'employee-edit',
      'employee-delete',

      'employee-search',
      'employee-status',
      'employee-leave',
      // salary details
      'salarydetails-list',
      'salarydetails-edit',
      'salarydetails-delete',
      // salary others
      'salary-pending',
      'salary-payment',
      'salary-correction',
      'employee-contribution',
      'employee-anualsfee',
      'set-iqama',
      'set-advance',
      'summary-adjuget',
      'advance-adjuget',
      // employee contact
      'employee-contact-persion',
      'employee-job-experience',
      'employee-iqama-expire',
      'employee-passport-expire',
      'employee-promotion',
      // work history
      'month-work-history',
      'month-work-report',
      'project-work-report',
      'attendence-in',
      'attendence-out',
      'attendence-edit',
      // All Report
      'report-attendence',
      'report-employee-iqama-single',
      'report-employee-iqama-project',
      'report-employee-salary-history',
      'report-employee-salary-summary',
      'report-monthly-salary-report',
      'report-total-hour-project',
      'report-total-salary-project',
      'report-employee-list-project',
      'report-employee-list-trade',
      'report-employee-list-sponser',
      'report-cpf-contribution',
      'report-sponser-employee',
      'report-expenditure',
      'report-income',
      'report-tools-metarials',
      'report-item-stock',
      'report-log-book',
      // salary system
      'salary-processing',
      // income & Expenditure
      'income-add',
      'expenditure-add',
      'expenditure-edit',
      'expenditure-list',
      'expenditure-delete',
      //expenditure
      'expenditure-type',
      // category
      'tools-category-add',
      'tools-category-delete',
      // sub category
      'tools-subcategory-add',
      'tools-subcategory-delete',
      // item
      'tools-item-add',
      // item details
      'tools-item-details-add',
      // sub category
      'tools-purchase',
      'tools-condem',
      // vichle
      'vichle-add',
      'vichle-maintanece',
      'log-book',
      // rent new building
      'rent-new-building',
      'BD Payment Setup',
      'BD Payment List',

      // Chart of Accounts
      'chart-of-account-manage'
    ];

    foreach ($permissions as $permission) {
      Permission::create(['name' => $permission]);
    }
  }
}
