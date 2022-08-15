# About the Event Manager application

The **event manager** is an application that allows you to organise tasks or events for a company in such a way that each participant can have an overview of the company calendar.

## What technologies are used
The technologies used are :
- On the back-end side:
	- Laravel 8 with its Jetstream package and its Inertia stack
- On the front-end side:
	- Vue.js 2 with the moment.js package

## How to install it
Nothing could be easier:
1. Rename the ".env.example" file at the root of the project to ".env" and fill in the connection information to the database:  
    DB_CONNECTION=mysql  
    DB_HOST=127.0.0.1  
    DB_PORT=3306  
    DB_DATABASE=  
    DB_USERNAME=  
    DB_PASSWORD=
2. Execute the following commands from your console at the root of the project:
    - composer install
	- php artisan migrate
    - php artisan db:seed
    - npm install
    - npm run build
3. Once the above commands have been executed, access the **event manager** from your browser via the URL entered during the configuration of your environment.