
# Purchase and Sell Manage Our Shop and Track your Expenses with Payment.

Welcome to Inventory Management, this is a beginner-friendly PHP project that helps you maintain your buying and selling activities, track expenses, and manage essential business data.


## Overview

This project is very basic, easy to use, and includes the following key sections:

* **Dashboard** - Displays today's purchases and sales, overall sales and purchases, and graphical representations of the data.

## Dashboard

![Dashboard](https://github.com/sagar-bodlekar/geo/blob/master/screenshot/Dashboard.png)


## Supplier Section
* **Supplier Section**: Allows adding, editing, deleting, and updating supplier information.


![Supplier](https://github.com/sagar-bodlekar/geo/blob/master/screenshot/supplier.png)

## Party Section
* **Party Section**: Similar functionalities as the supplier section for managing party details.

![Party](https://github.com/sagar-bodlekar/geo/blob/master/screenshot/party.png)

## Purchase Section
* **Purchase Section**: Create and manage new purchases and handle payment details in the "Purchase Receipts" section.

![Purchase](https://github.com/sagar-bodlekar/geo/blob/master/screenshot/purchase.png)

![Purchase Details](https://github.com/sagar-bodlekar/geo/blob/master/screenshot/PurchaseOrderDetails.png)

## Sales Section
* **Sales Section**: Generate sales to parties and manage sales payments in the "Sales Transaction" section and ganrate recipts in pdf download of each sales order.

![Purchase Details](https://github.com/sagar-bodlekar/geo/blob/master/screenshot/salesdetails.png)

![Purchase Details](https://github.com/sagar-bodlekar/geo/blob/master/screenshot/salesreciptspdf.png)

## Expenses Section
* **Expenses Section**: A simple section to record and manage additional expenses, which are reflected on the Dashboard.

![Expenses](https://github.com/sagar-bodlekar/geo/blob/master/screenshot/expenses.png)
## Installation

Clone the repository:

```bash
https://github.com/sagar-bodlekar/geo.git
```
Navigate to the project directory:

```bash
cd project-name
```
Set up a local server (e.g., XAMPP, WAMP, or LAMP) and place the project files in the server's root directory (e.g., htdocs for XAMPP).

Import the database:

Locate the database.sql file in the project directory.

Use a database management tool (e.g., phpMyAdmin) to import the file into your local database.

Update_ the `config/database.php` in the project to match your local setup.

Install Composer dependencies:
```bash
composer install
```

Install the tcpdf package for PDF generation:
```bash
composer require tecnickcom/tcpdf
```

_Open your browser and navigate to:_
```bash
http://localhost/project-name
```
## Contributing
😊We welcome contributions from developers of all skill levels. If you have an idea or improvement, feel free to:
1. Fork the repository.
2. Create a new branch for your feature or fix:
```bash
git checkout -b feature-name
```
3. Commit your changes:
```bash
git commit -m "Add a meaningful commit message"
```
4. Push your branch:
```bash
git push origin feature-name
```
5. _Submit a pull request for review._

![Logo](https://github.com/sagar-bodlekar/geo/blob/master/img/favicon/android-chrome-512x512.png)



