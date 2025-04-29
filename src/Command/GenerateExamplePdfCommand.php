<?php

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
class GenerateExamplePdfCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('wesnick:fdf:example-pdf')
            ->setDescription('Generate an example filled PDF Form from a blank PDF Form.')
            ->addArgument(
                'source-pdf',
                InputArgument::REQUIRED,
                'The empty PDF Form file to fill'
            )
            ->addArgument(
                'target-pdf',
                InputArgument::REQUIRED,
                'The target path to write the filled PDF Form file'
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
        $source     = $input->getArgument('source-pdf');
        $target     = $input->getArgument('target-pdf');
        $pdftk_path = $input->getOption('pdftk');

        $pdfForm = new PdfForm();
        $pdfForm->generatePdfExample($pdftk_path, $source, $target);

        return 0;
    }
}
