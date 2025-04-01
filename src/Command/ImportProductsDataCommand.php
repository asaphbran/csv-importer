<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\CsvImporter;

#[AsCommand(
    name: 'app:import-products-data',
    description: 'Takes data out of a CSV file and inserts it into the DB',
)]
class ImportProductsDataCommand extends Command
{
    public function __construct(private CsvImporter $csvImporter)
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



        return Command::SUCCESS;
    }
}
