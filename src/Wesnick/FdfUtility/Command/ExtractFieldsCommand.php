<?php


namespace Wesnick\FdfUtility\Command;


use Shuble\Slurpy\Factory as PDFTKFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesnick\FdfUtility\FdfWriter;
use Wesnick\FdfUtility\Fields\ButtonField;
use Wesnick\FdfUtility\Fields\ChoiceField;
use Wesnick\FdfUtility\Fields\PdfField;
use Wesnick\FdfUtility\Fields\TextField;
use Wesnick\FdfUtility\Parser\FieldFactory;
use Wesnick\FdfUtility\Parser\PdftkDumpParser;
use Wesnick\FdfUtility\PdfForm;

class ExtractFieldsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('wesnick:fdf:utility')
            ->setDescription('Extract PDF Field Data.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $pdftk = new PDFTKFactory('pdftk');

        $dir = __DIR__ . '/../../../..';
        $pdf = $dir . '/tests/fixtures/spec.fields.pdf';

        $pdfForm = new PdfForm();
        $pdfForm->generatePdfExample($pdftk, $pdf, '/home/wes/sample.pdf');



    }

}
