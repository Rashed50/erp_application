
## About Enterprise Resource Planning (ERP) Software 

This is the Payroll, Inventory and Human Resource Module of Enterprise Resource Planning (ERP) Software. Existing system analysis, Requirement analysis, Design, Development, Testing, Deployment and Maintenance through agile development methodology with sprint and scrum.  Managed codebase with Git, creating and merging branches, resolving conflicts, and reviewing pull requests. Bug fixing of software.  
Modules of ERP Software:
- Payroll Module;
- HR Module;
- Attendance Module;
- Inventory Management Module; 

## Payroll Module

Employee Salary Processing according to data of Attendance module. Employee advance and deduction from salary, salary correction after processing. salary increment, contribution provident fund, other deduction. incentive etc. salary sheet print and upload paid salary sheet.

## HR Module

New employee add, employee related documents upload, preview and download, employee information update, employee salary information update. Employee assign to project, employee transfer from one to another project. Employee performance evaluation. Employee Job status like active, inactive, terminate and vacation etc. Employee salary holding.

## Attendance Module
Employee daily attendance, Various types of attendance report as like daily present employees, daily manhours, monthly manhours, project manhours, designation base present/absent employees, project base manhours, monthly attendance history and employee base total days and work hours for salary processing.

## Inventory Management
Inventory items classification with consumable and no consumable. Items add, update with item code. Inventory Items purchase and stock management, Items issue to project, Items transfer from main store to sub store. Item Purchase notification to procurement section. Inventory items current stock, daily, monthly and yearly purchase, report. Yearly item inventory report, project base item issue report, item transfer from store to store report.


## Run Job in background 
First add  your background process into the queue system. then your job will be store in jobs table in database, If you run the below command then server will take 
the process from jobs table and execute your background process.

```
php artisan queue:work

```

## Clear NPM cache

``` 
npm cache clean --force

```

## Create Mail class 
```
    php artisan make:mail MyTestMail

```
## module:make-migration
Generate a migration for specified module.
```
php artisan module:make-migration migration_name module_name

```

## Delete GIT local branch 
```
git branch -D branch_name
```

## Delete GIT remote branch 
```
git push origin --delete branch_name
```


## remove uncommitted changes from branch 
```
git reset --hard HEAD
  or 
git reset

or 
git checkout .
```
 
