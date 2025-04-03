<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\CsvImporter;
use App\Service\ProductImporter;

#[AsCommand(
    name: 'app:import-products-data',
    description: 'Takes data out of a CSV file and inserts it into the DB',
)]
class ImportProductsDataCommand extends Command
{
    public function __construct(
        private CsvImporter $csvImporter,
        private ProductImporter $productImporter
    )
    {
        parent::__construct();
    }   

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the CSV file')
            ->addOption('test', null, InputOption::VALUE_NONE, 'Run in test mode without inserting into the database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        $testMode = $input->getOption('test');

        $output->writeln("Start process from $file...");

        $output->writeln("Retrieving data from file...");

        $products = $this->csvImporter->import(['filePath' => $file]);

        if (empty($products)) {
            $output->writeln("There has been an error during the retrieval process.");
            $output->writeln("Please check your file and try again.");

            return Command::FAILURE;
        }

        $output->writeln("Successfully retrieved data");

        [$processed, $successful, $skipped, $validationErrors] = $this->productImporter->import($products, $testMode);

        if (!empty($validationErrors)) {
            $output->writeln("\nSome value had errors. This is a breakdown of what I found:");

            foreach ($validationErrors as $productCode => $errors) {
                $hasMoreThanOneError = count($errors) > 1;
                $issuesWordFormattedInPlural = "issue".($hasMoreThanOneError ? "s" : "");

                $output->writeln("\nProduct with product code: $productCode had the following $issuesWordFormattedInPlural: ");

                if ($hasMoreThanOneError) {
                    foreach ($errors as $error) {
                        $output->writeln($error);
                    }
                } else {
                    $output->writeln($errors[0]);
                }
            }
        }

        $output->writeln("\nProcessed: $processed, Successful: $successful, Skipped: $skipped");

        return Command::SUCCESS;
    }
}
