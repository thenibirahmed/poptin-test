## How to install and run the project

- Clone the repository
- Setup Env File (From Example) and Database
- Run 
  - `composer install` 
  - `npm install` 
  - `npm run dev`
- On another tab of terminal run `php artisan reverb:start`
- Run `php artisan migrate:fresh --seed`

## Login Credentials

*Admin*
- email: `admin@admin.com`
- password: `password`

*User One*
- email: `gal@gal.com`
- password: `password`
  
*User Two*
- email: `tomer@tomer.com`
- password: `password`

## How to check realtime functionalities
- Visit same poll in two windows, then vote on one window and the other one should also be updated
