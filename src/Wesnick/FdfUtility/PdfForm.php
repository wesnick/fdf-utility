<?php
/**
 * @file PdfForm.php
 */

namespace Wesnick\FdfUtility;


use Shuble\Slurpy\Factory;
use Wesnick\FdfUtility\Fields\ButtonField;
use Wesnick\FdfUtility\Fields\ChoiceField;
use Wesnick\FdfUtility\Fields\PdfField;
use Wesnick\FdfUtility\Fields\TextField;
use Wesnick\FdfUtility\Parser\PdftkDumpParser;

class PdfForm
{

    /**
     * @var string
     */
    private $pdf;

    /**
     * @var PdfField[]
     */
    private $fields;

    private function extractFieldsFromPdf(Factory $pdftk, $pdf)
    {

        $fields_dump = tempnam(sys_get_temp_dir(), 'fdf_dump');

        $dataDumper = $pdftk->dumpDataFields($pdf, $fields_dump);
        $dataDumper->generate(array(), true);

        $parser = new PdftkDumpParser($fields_dump);
        $this->fields = $parser->parse();
        unlink($fields_dump);
        return $this->fields;
    }

    public function generatePdfExample(Factory $pdftk, $sourcePdf, $targetPdf)
    {
        $this->fields = $this->extractFieldsFromPdf($pdftk, $sourcePdf);

        foreach ($this->fields as $field) {
            $field->setValue($field->getExampleValue());
        }

        $fdf_file = tempnam(sys_get_temp_dir(), 'fdf');

        $writer = new FdfWriter($this->fields);
        $writer->generate();
        $writer->save($fdf_file);

        $formFiller = $pdftk->fillForm($sourcePdf, $fdf_file, $targetPdf);
        $formFiller->generate();
        unlink($fdf_file);
    }

    public function generateCsvExport(Factory $pdftk, $sourcePdf, $targetFile)
    {
        $this->fields = $this->extractFieldsFromPdf($pdftk, $sourcePdf);

        $texts = array();
        $buttons = array();

        $csv_fields = array();
        $csv_fields[] = array(
            'name',
            'type',
            'description',
            'options',
            'max_length',
            'example_value',
        );

        foreach ($this->fields as $field) {

            $csv_entry = array(
                $field->getName(),
                get_class($field),
                $field->getDescription(),
                '',
                '',
                ''
            );

            if ($field instanceof ButtonField || $field instanceof ChoiceField) {
                /** @var $field ButtonField */
                if ($field->getOptions()) {
                    $options = array_values(array_flip($field->getOptions()));
                    $value = $options[mt_rand(0, (count($options) - 1))];
                    $buttons[$field->getName()] = $value;

                    $csv_entry[3] = implode("|", $options);
                    $csv_entry[4] = '';
                    $csv_entry[5] = $field->getExampleValue();

                    // Handle possible array for multi-select fields
                    if (is_array($csv_entry[5])) {
                        $csv_entry[5] = "[" . implode(",", $csv_entry[5]) . "]";
                    }

                }
            }

            if ($field instanceof TextField) {
                // Just use the name as the value
                /** @var $field TextField */
                $texts[$field->getName()] = $field->getName();
                $csv_entry[4] = $field->getMaxLength();
                $csv_entry[5] = $field->getExampleValue();
            }

            $csv_fields[] = $csv_entry;
        }

        $handle = fopen($targetFile, 'w');
        foreach ($csv_fields as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
    }

} 
