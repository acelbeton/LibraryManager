# Library Manager

## Installation Guide:
  1. git clone https://github.com/acelbeton/LibraryManager.git
  1. cd LibraryManager/library-manager
  1. composer install
  1. npm install
  1. cp .env.example .env
  1. php artisan key:generate
  1. php artisan storage:link
  1. php artisan migrate --seed
  1. npm run dev
  1. php artisan serve

## Seeders
  - Added dummy data to the tables

# Language
  - Default language for the non-translation tables is english
