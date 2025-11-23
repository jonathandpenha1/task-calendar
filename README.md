# Calendar Application - Laravel Project

## Overview
This is a simple calendar application built with **Laravel 12.x** that allows you to create, manage, and categorize tasks. It includes functionalities such as task management, category assignment, and a simple UI to display the calendar.

---

## Table of Contents
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database](#database)
- [License](#license)

---

## Prerequisites

Ensure the following software is installed:

- **PHP >= 8.2**  
  Ensure that PHP 8.2 or higher is installed on your system.

- **Laravel 12.x**  
  Laravel 12.x framework is required to run this project.

- **Composer**  
  Composer is used for dependency management in PHP.

- **MySQL** (or any another relational database of your choice)  
  You can also use **SQLite** for local development.

- **Bootstrap CSS**  
  Bootstrap is used for responsive and user-friendly UI design.

- **AJAX**  
  Used for dynamic task management and interactivity without reloading the page.

---

## Installation

Follow these steps to set up the project on your local machine:

1. **Clone the repository** (if applicable):

    Clone the project repository from GitHub:
    
    ```bash
    git clone https://github.com/your-username/task-calendar.git
    cd task-calendar
    ```

2. **Install dependencies** using Composer:

    ```bash
    composer install
    ```

3. **Set up environment variables:**
   
    Copy the `.env.example` file to `.env`:

    ```bash
    cp .env.example .env
    ```

4. **Generate application key:**

    ```bash
    php artisan key:generate
    ```
    
5. **Set up the database:**

    Update your `.env` file with the correct database credentials:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=calendar
    DB_USERNAME=root
    DB_PASSWORD=your-password
    ```

   Then, run the migrations to create the necessary tables:

    ```bash
    php artisan migrate --force
    ```
    
6. **Run the application:**

    ```bash
    php artisan serve
    ```
    
---

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.


