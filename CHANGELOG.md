# Adaptar Laravel y Symfony

## Client HTTP

### Curso en Udemy

[Cap 51](https://www.udemy.com/course/cliente-http-peticiones-laravel-guzzle-consumir-apis-servicios/learn/lecture/14257992#questions)

#### Ver el tema del navbar


####

Cap. 42. Obteniendo un token válido

Cap. 43. Obteniendo info del usuario

-[X] 1) Install some missing packages:
   composer require symfonycasts/verify-email-bundle
2) In RegistrationController::verifyUserEmail():
    * -[X] Customize the last redirectToRoute() after a successful email verification.
    * Make sure you're rendering success flash messages or change the $this->addFlash() line.
3) Review and customize the form, controller, and templates as needed.
-[X] 4) Run "php bin/console make:migration" to generate a migration for the newly added User::isVerified property.

Then open your browser, go to "/register" and enjoy your new form!



