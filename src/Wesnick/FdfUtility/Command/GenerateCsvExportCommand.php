<?php


namespace Wesnick\FdfUtility\Command;


use Shuble\Slurpy\Factory as PDFTKFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesnick\FdfUtility\PdfForm;

class GenerateCsvExportCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('wesnick:fdf:csv-export')
            ->setDescription('Generate a CSV export of form information from a blank PDF Form.')
            ->addArgument(
                'source-pdf',
                InputArgument::REQUIRED,
                'The empty PDF Form file to read'
            )
            ->addArgument(
                'target-csv',
                InputArgument::REQUIRED,
                'The target path to write the CSV file'
            )
            ->addOption(
                'pdftk',
                null,
                InputOption::VALUE_OPTIONAL,
                'PDFTK Binary path if not in system PATH',
                'pdftk'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $source = $input->getArgument('source-pdf');
        $target = $input->getArgument('target-csv');
        $pdftk_path = $input->getOption('pdftk');

        $pdftk = new PDFTKFactory($pdftk_path);

        $pdfForm = new PdfForm();
        $pdfForm->generateCsvExport($pdftk, $source, $target);

    }

}
