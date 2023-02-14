# safer_immo
TP web


1- create a .env file

2- set in .env according to the given example in .env.test file

3- run composer install

4- run doctrine:database:create after setting up the DATABASE_URL

5- run doctrine:migrations:migrate

6- run doctrine:fixtures:load to load the defaut user with admin role to get started

7- run symfony serve to launch the app

8- Credentials to connect as admin: admin@safer.com, password: Password

9- for mail test, I used mailtrap (https://mailtrap.io/). You'll need to create an account and set 
the config using integration based on Symfony 5+ on your mailtrap's account panel.

That's all ;).
