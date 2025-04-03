# csv-importer

In order to run this project successfully, you need to:

1. Clone the repository or download it as a zip file
2. Run ``composer install`` and wait for the process to finish
3. Run once the previous steps is done, you can now run the command
  ``php bin/console app:import-products-data <path/to/file.csv>`` 
  and if you want to run it without actually inserting data into the database you can add the ``--test`` flag to go test mode
  ``php bin/console app:import-products-data <path/to/file.csv> --test`` 

Things to consider here:

1. This project uses the PHP version 8.2 (my machine runs PHP 8.4)
2. Since I'm using PHP 8.2, this project uses the latest stable version of Symfony (Symfony 7.2)
3. Since this is a Symfony project, remember to run the migrations

Thank you for stopping by!
