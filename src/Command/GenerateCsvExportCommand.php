<?php

declare(strict_types=1);

namespace Wesnick\FdfUtility\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesnick\FdfUtility\PdfForm;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 */
class GenerateCsvExportCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('wesnick:fdf:csv-export')
            ->setDescription('Generate a CSV export of form information from a blank PDF Form.')
            ->addArgument('source-pdf', InputArgument::REQUIRED, 'The empty PDF Form file to read')
            ->addArgument('target-csv', InputArgument::REQUIRED, 'The target path to write the CSV file')
            ->addOption('pdftk', null, InputOption::VALUE_OPTIONAL, 'PDFTK Binary path if not in system PATH', 'pdftk')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source    = $input->getArgument('source-pdf');
        $target    = $input->getArgument('target-csv');
        $pdftkPath = $input->getOption('pdftk');

        PdfForm::generateCsvExport($pdftkPath, $source, $target);

        return self::SUCCESS;
    }
}
