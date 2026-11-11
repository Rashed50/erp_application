-- HR: 15 employees (EMP-2001 .. EMP-2015) with salary structure and September 2026 work record.
-- Generated 2026-10-16 09:52 from the local database.
-- Import in phpMyAdmin into the live database (Import tab). Requires the same migrations as local.
-- Stops with a duplicate-key error (and imports nothing) if EMP-2001..EMP-2015 already exist.

SET NAMES utf8mb4;
SET time_zone = '+00:00';
START TRANSACTION;

-- Departments and designations (skipped when a row with the same name exists)
INSERT IGNORE INTO `departments` (`name`, `status`, `created_at`, `updated_at`) VALUES ('Administration', 1, NOW(), NOW());
INSERT IGNORE INTO `departments` (`name`, `status`, `created_at`, `updated_at`) VALUES ('Accounts', 1, NOW(), NOW());
INSERT IGNORE INTO `departments` (`name`, `status`, `created_at`, `updated_at`) VALUES ('HR', 1, NOW(), NOW());
INSERT IGNORE INTO `departments` (`name`, `status`, `created_at`, `updated_at`) VALUES ('Sales', 1, NOW(), NOW());
INSERT IGNORE INTO `departments` (`name`, `status`, `created_at`, `updated_at`) VALUES ('Production', 1, NOW(), NOW());
INSERT IGNORE INTO `departments` (`name`, `status`, `created_at`, `updated_at`) VALUES ('Store', 1, NOW(), NOW());
INSERT IGNORE INTO `departments` (`name`, `status`, `created_at`, `updated_at`) VALUES ('Transport', 1, NOW(), NOW());
INSERT IGNORE INTO `departments` (`name`, `status`, `created_at`, `updated_at`) VALUES ('IT', 1, NOW(), NOW());
INSERT IGNORE INTO `designations` (`name`, `status`, `created_at`, `updated_at`) VALUES ('General Manager', 1, NOW(), NOW());
INSERT IGNORE INTO `designations` (`name`, `status`, `created_at`, `updated_at`) VALUES ('Accounts Manager', 1, NOW(), NOW());
INSERT IGNORE INTO `designations` (`name`, `status`, `created_at`, `updated_at`) VALUES ('Accountant', 1, NOW(), NOW());
INSERT IGNORE INTO `designations` (`name`, `status`, `created_at`, `updated_at`) VALUES ('HR Executive', 1, NOW(), NOW());
INSERT IGNORE INTO `designations` (`name`, `status`, `created_at`, `updated_at`) VALUES ('Sales Manager', 1, NOW(), NOW());
INSERT IGNORE INTO `designations` (`name`, `status`, `created_at`, `updated_at`) VALUES ('Sales Executive', 1, NOW(), NOW());
INSERT IGNORE INTO `designations` (`name`, `status`, `created_at`, `updated_at`) VALUES ('Sales Representative', 1, NOW(), NOW());
INSERT IGNORE INTO `designations` (`name`, `status`, `created_at`, `updated_at`) VALUES ('Production Supervisor', 1, NOW(), NOW());
INSERT IGNORE INTO `designations` (`name`, `status`, `created_at`, `updated_at`) VALUES ('Machine Operator', 1, NOW(), NOW());
INSERT IGNORE INTO `designations` (`name`, `status`, `created_at`, `updated_at`) VALUES ('Store Keeper', 1, NOW(), NOW());
INSERT IGNORE INTO `designations` (`name`, `status`, `created_at`, `updated_at`) VALUES ('Driver', 1, NOW(), NOW());
INSERT IGNORE INTO `designations` (`name`, `status`, `created_at`, `updated_at`) VALUES ('Office Assistant', 1, NOW(), NOW());
INSERT IGNORE INTO `designations` (`name`, `status`, `created_at`, `updated_at`) VALUES ('Software Engineer', 1, NOW(), NOW());
INSERT IGNORE INTO `designations` (`name`, `status`, `created_at`, `updated_at`) VALUES ('Support Officer', 1, NOW(), NOW());

-- EMP-2001 Md. Rashedul Islam
INSERT INTO `employee_info` (`employee_code`, `name`, `father_name`, `mother_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `joining_date`, `last_working_date`, `employment_type`, `status`, `created_at`, `updated_at`, `department_id`, `designation_id`)
VALUES ('EMP-2001', 'Md. Rashedul Islam', 'Md. Abdur Rahim', NULL, '1984-03-12', 'Male', '01711-234567', 'md.rashedul.islam@example.com', 'House 12, Road 5, Dhanmondi, Dhaka', '2019-01-01', NULL, 'Permanent', 'Active', '2026-10-16 09:31:54', '2026-10-16 09:31:54', (SELECT id FROM `departments` WHERE `name` = 'Administration'), (SELECT id FROM `designations` WHERE `name` = 'General Manager'));
SET @emp_id = LAST_INSERT_ID();
INSERT INTO `emp_details` (`national_id`, `passport_no`, `marital_status`, `blood_group`, `permanent_address`, `payment_method`, `emergency_contact_name`, `emergency_contact_relation`, `emergency_contact_phone`, `notes`, `created_at`, `updated_at`, `employee_id`)
VALUES ('1984261234567', NULL, 'Married', 'B+', 'Vill: Chandina, Cumilla', 'Bank', 'Shahnaz Parvin', 'Spouse', '01819-445566', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);
INSERT INTO `emp_bank_details` (`bank_name`, `branch_name`, `account_name`, `account_no`, `routing_no`, `created_at`, `updated_at`, `employee_id`)
VALUES ('Dutch-Bangla Bank PLC', 'Dhanmondi', 'Md. Rashedul Islam', '1101510045671', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);
INSERT INTO `salary_details` (`effective_date`, `basic_salary`, `house_rent`, `medical_allowance`, `transport_allowance`, `food_allowance`, `other_allowance`, `overtime_rate`, `other_deduction`, `deduction_basis`, `status`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2019-01-01', '60000.00', '0.00', '0.00', '0.00', '2000.00', '0.00', '500.00', '0.00', 'basic', '1', NULL, '2026-10-16 09:31:54', '2026-10-16 09:38:10', @emp_id);
INSERT INTO `emp_works` (`salary_month`, `working_days`, `present_days`, `absent_days`, `paid_leave_days`, `unpaid_leave_days`, `overtime_hours`, `bonus`, `other_addition`, `other_deduction`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2026-09-01', '30.00', '30.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);
INSERT INTO `emp_works` (`salary_month`, `working_days`, `present_days`, `absent_days`, `paid_leave_days`, `unpaid_leave_days`, `overtime_hours`, `bonus`, `other_addition`, `other_deduction`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2026-10-01', '26.00', '22.00', '0.00', '0.00', '0.00', '30.00', '500.00', '0.00', '0.00', NULL, '2026-10-16 09:38:35', '2026-10-16 09:38:35', @emp_id);

-- EMP-2002 Farzana Akter
INSERT INTO `employee_info` (`employee_code`, `name`, `father_name`, `mother_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `joining_date`, `last_working_date`, `employment_type`, `status`, `created_at`, `updated_at`, `department_id`, `designation_id`)
VALUES ('EMP-2002', 'Farzana Akter', 'Md. Jalal Uddin', NULL, '1990-07-22', 'Female', '01712-345678', 'farzana.akter@example.com', 'Flat 4B, Mirpur 10, Dhaka', '2020-03-15', NULL, 'Permanent', 'Active', '2026-10-16 09:31:54', '2026-10-16 09:31:54', (SELECT id FROM `departments` WHERE `name` = 'Accounts'), (SELECT id FROM `designations` WHERE `name` = 'Accounts Manager'));
SET @emp_id = LAST_INSERT_ID();
INSERT INTO `emp_details` (`national_id`, `passport_no`, `marital_status`, `blood_group`, `permanent_address`, `payment_method`, `emergency_contact_name`, `emergency_contact_relation`, `emergency_contact_phone`, `notes`, `created_at`, `updated_at`, `employee_id`)
VALUES ('1990269876543', NULL, 'Married', 'O+', 'Vill: Sreepur, Gazipur', 'Bank', 'Md. Kamrul Hasan', 'Spouse', '01913-778899', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);
INSERT INTO `emp_bank_details` (`bank_name`, `branch_name`, `account_name`, `account_no`, `routing_no`, `created_at`, `updated_at`, `employee_id`)
VALUES ('BRAC Bank PLC', 'Mirpur', 'Farzana Akter', '1501203345678', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);
INSERT INTO `salary_details` (`effective_date`, `basic_salary`, `house_rent`, `medical_allowance`, `transport_allowance`, `food_allowance`, `other_allowance`, `overtime_rate`, `other_deduction`, `deduction_basis`, `status`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2020-03-15', '45000.00', '22500.00', '2500.00', '2000.00', '1500.00', '0.00', '0.00', '2250.00', 'basic', '1', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);
INSERT INTO `emp_works` (`salary_month`, `working_days`, `present_days`, `absent_days`, `paid_leave_days`, `unpaid_leave_days`, `overtime_hours`, `bonus`, `other_addition`, `other_deduction`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2026-09-01', '30.00', '28.00', '0.00', '2.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Two days paid leave', '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);

-- EMP-2003 Md. Tanvir Hossain
INSERT INTO `employee_info` (`employee_code`, `name`, `father_name`, `mother_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `joining_date`, `last_working_date`, `employment_type`, `status`, `created_at`, `updated_at`, `department_id`, `designation_id`)
VALUES ('EMP-2003', 'Md. Tanvir Hossain', 'Md. Nurul Hossain', NULL, '1993-11-05', 'Male', '01713-456789', 'md.tanvir.hossain@example.com', 'House 7, Sector 11, Uttara, Dhaka', '2022-06-01', NULL, 'Permanent', 'Active', '2026-10-16 09:31:54', '2026-10-16 09:31:54', (SELECT id FROM `departments` WHERE `name` = 'Accounts'), (SELECT id FROM `designations` WHERE `name` = 'Accountant'));
SET @emp_id = LAST_INSERT_ID();
INSERT INTO `emp_details` (`national_id`, `passport_no`, `marital_status`, `blood_group`, `permanent_address`, `payment_method`, `emergency_contact_name`, `emergency_contact_relation`, `emergency_contact_phone`, `notes`, `created_at`, `updated_at`, `employee_id`)
VALUES ('1993261122334', NULL, 'Single', 'A+', 'Vill: Shibchar, Madaripur', 'Bank', 'Md. Nurul Hossain', 'Father', '01715-112233', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);
INSERT INTO `emp_bank_details` (`bank_name`, `branch_name`, `account_name`, `account_no`, `routing_no`, `created_at`, `updated_at`, `employee_id`)
VALUES ('Islami Bank Bangladesh PLC', 'Uttara', 'Md. Tanvir Hossain', '2050177700123', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);
INSERT INTO `salary_details` (`effective_date`, `basic_salary`, `house_rent`, `medical_allowance`, `transport_allowance`, `food_allowance`, `other_allowance`, `overtime_rate`, `other_deduction`, `deduction_basis`, `status`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2022-06-01', '28000.00', '14000.00', '1500.00', '1500.00', '1000.00', '0.00', '0.00', '0.00', 'basic', '1', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);
INSERT INTO `emp_works` (`salary_month`, `working_days`, `present_days`, `absent_days`, `paid_leave_days`, `unpaid_leave_days`, `overtime_hours`, `bonus`, `other_addition`, `other_deduction`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2026-09-01', '30.00', '27.00', '3.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Absent 3 days', '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);

-- EMP-2004 Nusrat Jahan
INSERT INTO `employee_info` (`employee_code`, `name`, `father_name`, `mother_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `joining_date`, `last_working_date`, `employment_type`, `status`, `created_at`, `updated_at`, `department_id`, `designation_id`)
VALUES ('EMP-2004', 'Nusrat Jahan', 'Md. Abul Kalam', NULL, '1995-02-18', 'Female', '01714-567890', 'nusrat.jahan@example.com', 'House 22, Mohammadpur, Dhaka', '2023-01-10', NULL, 'Permanent', 'Active', '2026-10-16 09:31:54', '2026-10-16 09:31:54', (SELECT id FROM `departments` WHERE `name` = 'HR'), (SELECT id FROM `designations` WHERE `name` = 'HR Executive'));
SET @emp_id = LAST_INSERT_ID();
INSERT INTO `emp_details` (`national_id`, `passport_no`, `marital_status`, `blood_group`, `permanent_address`, `payment_method`, `emergency_contact_name`, `emergency_contact_relation`, `emergency_contact_phone`, `notes`, `created_at`, `updated_at`, `employee_id`)
VALUES ('1995264455667', NULL, 'Single', 'AB+', 'Vill: Kotalipara, Gopalganj', 'Bank', 'Rokeya Begum', 'Mother', '01716-998877', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);
INSERT INTO `emp_bank_details` (`bank_name`, `branch_name`, `account_name`, `account_no`, `routing_no`, `created_at`, `updated_at`, `employee_id`)
VALUES ('City Bank PLC', 'Mohammadpur', 'Nusrat Jahan', '1252003456701', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);
INSERT INTO `salary_details` (`effective_date`, `basic_salary`, `house_rent`, `medical_allowance`, `transport_allowance`, `food_allowance`, `other_allowance`, `overtime_rate`, `other_deduction`, `deduction_basis`, `status`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2023-01-10', '25000.00', '12500.00', '1500.00', '1500.00', '1000.00', '0.00', '0.00', '0.00', 'basic', '1', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);
INSERT INTO `emp_works` (`salary_month`, `working_days`, `present_days`, `absent_days`, `paid_leave_days`, `unpaid_leave_days`, `overtime_hours`, `bonus`, `other_addition`, `other_deduction`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2026-09-01', '30.00', '29.00', '0.00', '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);

-- EMP-2005 Md. Sohel Rana
INSERT INTO `employee_info` (`employee_code`, `name`, `father_name`, `mother_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `joining_date`, `last_working_date`, `employment_type`, `status`, `created_at`, `updated_at`, `department_id`, `designation_id`)
VALUES ('EMP-2005', 'Md. Sohel Rana', 'Md. Lutfar Rahman', NULL, '1991-09-30', 'Male', '01715-678901', 'md.sohel.rana@example.com', 'House 3, Badda, Dhaka', '2021-04-01', NULL, 'Permanent', 'Active', '2026-10-16 09:31:54', '2026-10-16 09:31:54', (SELECT id FROM `departments` WHERE `name` = 'Sales'), (SELECT id FROM `designations` WHERE `name` = 'Sales Manager'));
SET @emp_id = LAST_INSERT_ID();
INSERT INTO `emp_details` (`national_id`, `passport_no`, `marital_status`, `blood_group`, `permanent_address`, `payment_method`, `emergency_contact_name`, `emergency_contact_relation`, `emergency_contact_phone`, `notes`, `created_at`, `updated_at`, `employee_id`)
VALUES ('1991267788990', NULL, 'Married', 'O+', 'Vill: Sherpur Sadar, Sherpur', 'Bank', 'Taslima Akter', 'Spouse', '01817-334455', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);
INSERT INTO `emp_bank_details` (`bank_name`, `branch_name`, `account_name`, `account_no`, `routing_no`, `created_at`, `updated_at`, `employee_id`)
VALUES ('Dutch-Bangla Bank PLC', 'Badda', 'Md. Sohel Rana', '1101510098765', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);
INSERT INTO `salary_details` (`effective_date`, `basic_salary`, `house_rent`, `medical_allowance`, `transport_allowance`, `food_allowance`, `other_allowance`, `overtime_rate`, `other_deduction`, `deduction_basis`, `status`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2021-04-01', '40000.00', '20000.00', '2000.00', '3000.00', '1500.00', '0.00', '0.00', '2000.00', 'basic', '1', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);
INSERT INTO `emp_works` (`salary_month`, `working_days`, `present_days`, `absent_days`, `paid_leave_days`, `unpaid_leave_days`, `overtime_hours`, `bonus`, `other_addition`, `other_deduction`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2026-09-01', '30.00', '30.00', '0.00', '0.00', '0.00', '0.00', '5000.00', '0.00', '0.00', 'Sales target bonus', '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);

-- EMP-2006 Sharmin Sultana
INSERT INTO `employee_info` (`employee_code`, `name`, `father_name`, `mother_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `joining_date`, `last_working_date`, `employment_type`, `status`, `created_at`, `updated_at`, `department_id`, `designation_id`)
VALUES ('EMP-2006', 'Sharmin Sultana', 'Md. Mofizul Islam', NULL, '1997-05-14', 'Female', '01716-789012', 'sharmin.sultana@example.com', 'House 9, Rampura, Dhaka', '2023-08-01', NULL, 'Permanent', 'Active', '2026-10-16 09:31:54', '2026-10-16 09:31:54', (SELECT id FROM `departments` WHERE `name` = 'Sales'), (SELECT id FROM `designations` WHERE `name` = 'Sales Executive'));
SET @emp_id = LAST_INSERT_ID();
INSERT INTO `emp_details` (`national_id`, `passport_no`, `marital_status`, `blood_group`, `permanent_address`, `payment_method`, `emergency_contact_name`, `emergency_contact_relation`, `emergency_contact_phone`, `notes`, `created_at`, `updated_at`, `employee_id`)
VALUES ('1997262233445', NULL, 'Single', 'B-', 'Vill: Bhairab, Kishoreganj', 'Bank', 'Md. Mofizul Islam', 'Father', '01718-667788', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);
INSERT INTO `emp_bank_details` (`bank_name`, `branch_name`, `account_name`, `account_no`, `routing_no`, `created_at`, `updated_at`, `employee_id`)
VALUES ('BRAC Bank PLC', 'Rampura', 'Sharmin Sultana', '1501203398712', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);
INSERT INTO `salary_details` (`effective_date`, `basic_salary`, `house_rent`, `medical_allowance`, `transport_allowance`, `food_allowance`, `other_allowance`, `overtime_rate`, `other_deduction`, `deduction_basis`, `status`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2023-08-01', '18000.00', '9000.00', '1000.00', '1500.00', '1000.00', '0.00', '120.00', '0.00', 'basic', '1', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);
INSERT INTO `emp_works` (`salary_month`, `working_days`, `present_days`, `absent_days`, `paid_leave_days`, `unpaid_leave_days`, `overtime_hours`, `bonus`, `other_addition`, `other_deduction`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2026-09-01', '30.00', '26.00', '2.00', '0.00', '2.00', '10.00', '2000.00', '0.00', '0.00', 'Two days unpaid leave', '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);

-- EMP-2007 Md. Arif Hasan
INSERT INTO `employee_info` (`employee_code`, `name`, `father_name`, `mother_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `joining_date`, `last_working_date`, `employment_type`, `status`, `created_at`, `updated_at`, `department_id`, `designation_id`)
VALUES ('EMP-2007', 'Md. Arif Hasan', 'Md. Shamsul Haque', NULL, '1998-12-01', 'Male', '01717-890123', 'md.arif.hasan@example.com', 'House 15, Khilgaon, Dhaka', '2024-02-01', NULL, 'Contract', 'Active', '2026-10-16 09:31:54', '2026-10-16 09:31:54', (SELECT id FROM `departments` WHERE `name` = 'Sales'), (SELECT id FROM `designations` WHERE `name` = 'Sales Representative'));
SET @emp_id = LAST_INSERT_ID();
INSERT INTO `emp_details` (`national_id`, `passport_no`, `marital_status`, `blood_group`, `permanent_address`, `payment_method`, `emergency_contact_name`, `emergency_contact_relation`, `emergency_contact_phone`, `notes`, `created_at`, `updated_at`, `employee_id`)
VALUES ('1998265566778', NULL, 'Single', 'A-', 'Vill: Daudkandi, Cumilla', 'Cash', 'Md. Shamsul Haque', 'Father', '01719-223344', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);
INSERT INTO `salary_details` (`effective_date`, `basic_salary`, `house_rent`, `medical_allowance`, `transport_allowance`, `food_allowance`, `other_allowance`, `overtime_rate`, `other_deduction`, `deduction_basis`, `status`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2024-02-01', '12000.00', '5000.00', '1000.00', '1000.00', '1000.00', '0.00', '100.00', '0.00', 'basic', '1', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);
INSERT INTO `emp_works` (`salary_month`, `working_days`, `present_days`, `absent_days`, `paid_leave_days`, `unpaid_leave_days`, `overtime_hours`, `bonus`, `other_addition`, `other_deduction`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2026-09-01', '30.00', '20.00', '10.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Absent 10 days', '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);

-- EMP-2008 Md. Kamal Uddin
INSERT INTO `employee_info` (`employee_code`, `name`, `father_name`, `mother_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `joining_date`, `last_working_date`, `employment_type`, `status`, `created_at`, `updated_at`, `department_id`, `designation_id`)
VALUES ('EMP-2008', 'Md. Kamal Uddin', 'Md. Jamal Uddin', NULL, '1986-08-20', 'Male', '01718-901234', 'md.kamal.uddin@example.com', 'House 40, Tongi, Gazipur', '2018-10-01', NULL, 'Permanent', 'Active', '2026-10-16 09:31:54', '2026-10-16 09:31:54', (SELECT id FROM `departments` WHERE `name` = 'Production'), (SELECT id FROM `designations` WHERE `name` = 'Production Supervisor'));
SET @emp_id = LAST_INSERT_ID();
INSERT INTO `emp_details` (`national_id`, `passport_no`, `marital_status`, `blood_group`, `permanent_address`, `payment_method`, `emergency_contact_name`, `emergency_contact_relation`, `emergency_contact_phone`, `notes`, `created_at`, `updated_at`, `employee_id`)
VALUES ('1986268899001', NULL, 'Married', 'O+', 'Vill: Nabinagar, Brahmanbaria', 'Bank', 'Hasina Begum', 'Spouse', '01911-556677', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);
INSERT INTO `emp_bank_details` (`bank_name`, `branch_name`, `account_name`, `account_no`, `routing_no`, `created_at`, `updated_at`, `employee_id`)
VALUES ('Sonali Bank PLC', 'Tongi', 'Md. Kamal Uddin', '0102033004567', NULL, '2026-10-16 09:31:54', '2026-10-16 09:31:54', @emp_id);
INSERT INTO `salary_details` (`effective_date`, `basic_salary`, `house_rent`, `medical_allowance`, `transport_allowance`, `food_allowance`, `other_allowance`, `overtime_rate`, `other_deduction`, `deduction_basis`, `status`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2018-10-01', '30000.00', '15000.00', '2000.00', '1500.00', '1500.00', '0.00', '180.00', '1500.00', 'basic', '1', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);
INSERT INTO `emp_works` (`salary_month`, `working_days`, `present_days`, `absent_days`, `paid_leave_days`, `unpaid_leave_days`, `overtime_hours`, `bonus`, `other_addition`, `other_deduction`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2026-09-01', '30.00', '29.00', '1.00', '0.00', '0.00', '24.00', '0.00', '0.00', '0.00', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);

-- EMP-2009 Md. Rubel Mia
INSERT INTO `employee_info` (`employee_code`, `name`, `father_name`, `mother_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `joining_date`, `last_working_date`, `employment_type`, `status`, `created_at`, `updated_at`, `department_id`, `designation_id`)
VALUES ('EMP-2009', 'Md. Rubel Mia', 'Md. Abdul Malek', NULL, '1994-04-11', 'Male', '01719-012345', 'md.rubel.mia@example.com', 'Ershad Nagar, Tongi, Gazipur', '2021-11-15', NULL, 'Permanent', 'Active', '2026-10-16 09:31:55', '2026-10-16 09:31:55', (SELECT id FROM `departments` WHERE `name` = 'Production'), (SELECT id FROM `designations` WHERE `name` = 'Machine Operator'));
SET @emp_id = LAST_INSERT_ID();
INSERT INTO `emp_details` (`national_id`, `passport_no`, `marital_status`, `blood_group`, `permanent_address`, `payment_method`, `emergency_contact_name`, `emergency_contact_relation`, `emergency_contact_phone`, `notes`, `created_at`, `updated_at`, `employee_id`)
VALUES ('1994260011223', NULL, 'Married', 'B+', 'Vill: Gaibandha Sadar, Gaibandha', 'Cash', 'Mst. Rina Begum', 'Spouse', '01912-889900', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);
INSERT INTO `salary_details` (`effective_date`, `basic_salary`, `house_rent`, `medical_allowance`, `transport_allowance`, `food_allowance`, `other_allowance`, `overtime_rate`, `other_deduction`, `deduction_basis`, `status`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2021-11-15', '15000.00', '6000.00', '1000.00', '1000.00', '1500.00', '0.00', '110.00', '0.00', 'basic', '1', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);
INSERT INTO `emp_works` (`salary_month`, `working_days`, `present_days`, `absent_days`, `paid_leave_days`, `unpaid_leave_days`, `overtime_hours`, `bonus`, `other_addition`, `other_deduction`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2026-09-01', '30.00', '28.00', '1.00', '1.00', '0.00', '32.00', '0.00', '0.00', '0.00', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);

-- EMP-2010 Mst. Shapla Khatun
INSERT INTO `employee_info` (`employee_code`, `name`, `father_name`, `mother_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `joining_date`, `last_working_date`, `employment_type`, `status`, `created_at`, `updated_at`, `department_id`, `designation_id`)
VALUES ('EMP-2010', 'Mst. Shapla Khatun', 'Md. Habibur Rahman', NULL, '1996-06-25', 'Female', '01720-123456', 'mst.shapla.khatun@example.com', 'Cherag Ali, Tongi, Gazipur', '2022-03-01', NULL, 'Permanent', 'Active', '2026-10-16 09:31:55', '2026-10-16 09:31:55', (SELECT id FROM `departments` WHERE `name` = 'Production'), (SELECT id FROM `designations` WHERE `name` = 'Machine Operator'));
SET @emp_id = LAST_INSERT_ID();
INSERT INTO `emp_details` (`national_id`, `passport_no`, `marital_status`, `blood_group`, `permanent_address`, `payment_method`, `emergency_contact_name`, `emergency_contact_relation`, `emergency_contact_phone`, `notes`, `created_at`, `updated_at`, `employee_id`)
VALUES ('1996263344556', NULL, 'Married', 'A+', 'Vill: Ullapara, Sirajganj', 'Cash', 'Md. Selim Reza', 'Spouse', '01913-990011', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);
INSERT INTO `salary_details` (`effective_date`, `basic_salary`, `house_rent`, `medical_allowance`, `transport_allowance`, `food_allowance`, `other_allowance`, `overtime_rate`, `other_deduction`, `deduction_basis`, `status`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2022-03-01', '14000.00', '5600.00', '1000.00', '1000.00', '1500.00', '0.00', '100.00', '0.00', 'basic', '1', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);
INSERT INTO `emp_works` (`salary_month`, `working_days`, `present_days`, `absent_days`, `paid_leave_days`, `unpaid_leave_days`, `overtime_hours`, `bonus`, `other_addition`, `other_deduction`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2026-09-01', '30.00', '25.00', '3.00', '0.00', '2.00', '16.00', '0.00', '0.00', '0.00', 'Absent 3 days, 2 days unpaid leave', '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);

-- EMP-2011 Md. Jahangir Alam
INSERT INTO `employee_info` (`employee_code`, `name`, `father_name`, `mother_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `joining_date`, `last_working_date`, `employment_type`, `status`, `created_at`, `updated_at`, `department_id`, `designation_id`)
VALUES ('EMP-2011', 'Md. Jahangir Alam', 'Md. Siraj Mia', NULL, '1989-01-08', 'Male', '01721-234567', 'md.jahangir.alam@example.com', 'House 5, Shyamoli, Dhaka', '2020-09-01', NULL, 'Permanent', 'Active', '2026-10-16 09:31:55', '2026-10-16 09:31:55', (SELECT id FROM `departments` WHERE `name` = 'Store'), (SELECT id FROM `designations` WHERE `name` = 'Store Keeper'));
SET @emp_id = LAST_INSERT_ID();
INSERT INTO `emp_details` (`national_id`, `passport_no`, `marital_status`, `blood_group`, `permanent_address`, `payment_method`, `emergency_contact_name`, `emergency_contact_relation`, `emergency_contact_phone`, `notes`, `created_at`, `updated_at`, `employee_id`)
VALUES ('1989266677889', NULL, 'Married', 'O-', 'Vill: Bhola Sadar, Bhola', 'Bank', 'Mst. Salma Akter', 'Spouse', '01714-121212', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);
INSERT INTO `emp_bank_details` (`bank_name`, `branch_name`, `account_name`, `account_no`, `routing_no`, `created_at`, `updated_at`, `employee_id`)
VALUES ('Islami Bank Bangladesh PLC', 'Shyamoli', 'Md. Jahangir Alam', '2050177700456', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);
INSERT INTO `salary_details` (`effective_date`, `basic_salary`, `house_rent`, `medical_allowance`, `transport_allowance`, `food_allowance`, `other_allowance`, `overtime_rate`, `other_deduction`, `deduction_basis`, `status`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2020-09-01', '20000.00', '10000.00', '1500.00', '1000.00', '1000.00', '0.00', '0.00', '0.00', 'basic', '1', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);
INSERT INTO `emp_works` (`salary_month`, `working_days`, `present_days`, `absent_days`, `paid_leave_days`, `unpaid_leave_days`, `overtime_hours`, `bonus`, `other_addition`, `other_deduction`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2026-09-01', '30.00', '30.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);

-- EMP-2012 Md. Habibullah
INSERT INTO `employee_info` (`employee_code`, `name`, `father_name`, `mother_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `joining_date`, `last_working_date`, `employment_type`, `status`, `created_at`, `updated_at`, `department_id`, `designation_id`)
VALUES ('EMP-2012', 'Md. Habibullah', 'Md. Ataur Rahman', NULL, '1987-10-16', 'Male', '01722-345678', 'md.habibullah@example.com', 'Kamrangirchar, Dhaka', '2019-07-01', NULL, 'Permanent', 'Active', '2026-10-16 09:31:55', '2026-10-16 09:31:55', (SELECT id FROM `departments` WHERE `name` = 'Transport'), (SELECT id FROM `designations` WHERE `name` = 'Driver'));
SET @emp_id = LAST_INSERT_ID();
INSERT INTO `emp_details` (`national_id`, `passport_no`, `marital_status`, `blood_group`, `permanent_address`, `payment_method`, `emergency_contact_name`, `emergency_contact_relation`, `emergency_contact_phone`, `notes`, `created_at`, `updated_at`, `employee_id`)
VALUES ('1987269900112', NULL, 'Married', 'B+', 'Vill: Muktagacha, Mymensingh', 'Cash', 'Mst. Parvin Akter', 'Spouse', '01815-343434', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);
INSERT INTO `salary_details` (`effective_date`, `basic_salary`, `house_rent`, `medical_allowance`, `transport_allowance`, `food_allowance`, `other_allowance`, `overtime_rate`, `other_deduction`, `deduction_basis`, `status`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2019-07-01', '16000.00', '6400.00', '1000.00', '0.00', '1500.00', '0.00', '120.00', '0.00', 'basic', '1', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);
INSERT INTO `emp_works` (`salary_month`, `working_days`, `present_days`, `absent_days`, `paid_leave_days`, `unpaid_leave_days`, `overtime_hours`, `bonus`, `other_addition`, `other_deduction`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2026-09-01', '30.00', '29.00', '0.00', '0.00', '1.00', '40.00', '0.00', '0.00', '0.00', 'One day unpaid leave', '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);

-- EMP-2013 Md. Ripon Sheikh
INSERT INTO `employee_info` (`employee_code`, `name`, `father_name`, `mother_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `joining_date`, `last_working_date`, `employment_type`, `status`, `created_at`, `updated_at`, `department_id`, `designation_id`)
VALUES ('EMP-2013', 'Md. Ripon Sheikh', 'Md. Hanif Sheikh', NULL, '2000-02-29', 'Male', '01723-456789', 'md.ripon.sheikh@example.com', 'Lalbagh, Dhaka', '2024-05-01', NULL, 'Contract', 'Active', '2026-10-16 09:31:55', '2026-10-16 09:31:55', (SELECT id FROM `departments` WHERE `name` = 'Administration'), (SELECT id FROM `designations` WHERE `name` = 'Office Assistant'));
SET @emp_id = LAST_INSERT_ID();
INSERT INTO `emp_details` (`national_id`, `passport_no`, `marital_status`, `blood_group`, `permanent_address`, `payment_method`, `emergency_contact_name`, `emergency_contact_relation`, `emergency_contact_phone`, `notes`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2000261234098', NULL, 'Single', 'AB-', 'Vill: Tungipara, Gopalganj', 'Cash', 'Md. Hanif Sheikh', 'Father', '01716-565656', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);
INSERT INTO `salary_details` (`effective_date`, `basic_salary`, `house_rent`, `medical_allowance`, `transport_allowance`, `food_allowance`, `other_allowance`, `overtime_rate`, `other_deduction`, `deduction_basis`, `status`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2024-05-01', '10000.00', '4000.00', '500.00', '500.00', '1000.00', '0.00', '0.00', '0.00', 'basic', '1', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);
INSERT INTO `emp_works` (`salary_month`, `working_days`, `present_days`, `absent_days`, `paid_leave_days`, `unpaid_leave_days`, `overtime_hours`, `bonus`, `other_addition`, `other_deduction`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2026-09-01', '30.00', '27.00', '2.00', '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);

-- EMP-2014 Md. Imran Khan
INSERT INTO `employee_info` (`employee_code`, `name`, `father_name`, `mother_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `joining_date`, `last_working_date`, `employment_type`, `status`, `created_at`, `updated_at`, `department_id`, `designation_id`)
VALUES ('EMP-2014', 'Md. Imran Khan', 'Md. Aminul Khan', NULL, '1992-12-24', 'Male', '01724-567890', 'md.imran.khan@example.com', 'House 18, Bashundhara R/A, Dhaka', '2022-01-03', NULL, 'Permanent', 'Active', '2026-10-16 09:31:55', '2026-10-16 09:31:55', (SELECT id FROM `departments` WHERE `name` = 'IT'), (SELECT id FROM `designations` WHERE `name` = 'Software Engineer'));
SET @emp_id = LAST_INSERT_ID();
INSERT INTO `emp_details` (`national_id`, `passport_no`, `marital_status`, `blood_group`, `permanent_address`, `payment_method`, `emergency_contact_name`, `emergency_contact_relation`, `emergency_contact_phone`, `notes`, `created_at`, `updated_at`, `employee_id`)
VALUES ('1992265678901', NULL, 'Married', 'A+', 'Vill: Sonargaon, Narayanganj', 'Bank', 'Sadia Afrin', 'Spouse', '01717-787878', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);
INSERT INTO `emp_bank_details` (`bank_name`, `branch_name`, `account_name`, `account_no`, `routing_no`, `created_at`, `updated_at`, `employee_id`)
VALUES ('Eastern Bank PLC', 'Bashundhara', 'Md. Imran Khan', '1041060789012', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);
INSERT INTO `salary_details` (`effective_date`, `basic_salary`, `house_rent`, `medical_allowance`, `transport_allowance`, `food_allowance`, `other_allowance`, `overtime_rate`, `other_deduction`, `deduction_basis`, `status`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2022-01-03', '50000.00', '25000.00', '2500.00', '2500.00', '2000.00', '0.00', '0.00', '2500.00', 'basic', '1', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);
INSERT INTO `emp_works` (`salary_month`, `working_days`, `present_days`, `absent_days`, `paid_leave_days`, `unpaid_leave_days`, `overtime_hours`, `bonus`, `other_addition`, `other_deduction`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2026-09-01', '30.00', '28.00', '0.00', '2.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);

-- EMP-2015 Tasnim Rahman
INSERT INTO `employee_info` (`employee_code`, `name`, `father_name`, `mother_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `joining_date`, `last_working_date`, `employment_type`, `status`, `created_at`, `updated_at`, `department_id`, `designation_id`)
VALUES ('EMP-2015', 'Tasnim Rahman', 'Md. Mizanur Rahman', NULL, '1999-09-09', 'Female', '01725-678901', 'tasnim.rahman@example.com', 'House 2, Banasree, Dhaka', '2026-09-15', NULL, 'Probation', 'Active', '2026-10-16 09:31:55', '2026-10-16 09:31:55', (SELECT id FROM `departments` WHERE `name` = 'IT'), (SELECT id FROM `designations` WHERE `name` = 'Support Officer'));
SET @emp_id = LAST_INSERT_ID();
INSERT INTO `emp_details` (`national_id`, `passport_no`, `marital_status`, `blood_group`, `permanent_address`, `payment_method`, `emergency_contact_name`, `emergency_contact_relation`, `emergency_contact_phone`, `notes`, `created_at`, `updated_at`, `employee_id`)
VALUES ('1999267890123', NULL, 'Single', 'O+', 'Vill: Savar, Dhaka', 'Bank', 'Md. Mizanur Rahman', 'Father', '01718-909090', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);
INSERT INTO `emp_bank_details` (`bank_name`, `branch_name`, `account_name`, `account_no`, `routing_no`, `created_at`, `updated_at`, `employee_id`)
VALUES ('BRAC Bank PLC', 'Banasree', 'Tasnim Rahman', '1501203321098', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);
INSERT INTO `salary_details` (`effective_date`, `basic_salary`, `house_rent`, `medical_allowance`, `transport_allowance`, `food_allowance`, `other_allowance`, `overtime_rate`, `other_deduction`, `deduction_basis`, `status`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2026-09-15', '22000.00', '11000.00', '1000.00', '1000.00', '1000.00', '0.00', '0.00', '0.00', 'basic', '1', NULL, '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);
INSERT INTO `emp_works` (`salary_month`, `working_days`, `present_days`, `absent_days`, `paid_leave_days`, `unpaid_leave_days`, `overtime_hours`, `bonus`, `other_addition`, `other_deduction`, `remarks`, `created_at`, `updated_at`, `employee_id`)
VALUES ('2026-09-01', '16.00', '16.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Joined 15 September', '2026-10-16 09:31:55', '2026-10-16 09:31:55', @emp_id);

COMMIT;
