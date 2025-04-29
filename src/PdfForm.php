<?php

namespace Wesnick\FdfUtility;

use Symfony\Component\Process\Process;
use Wesnick\FdfUtility\Parser\PdftkDumpParser;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 */
class PdfForm
{
    /**
     * @param string $pdftkBinary The path to pdftk binary
     * @param string $pdf         The path to the PDF file
     *
     * @return Fields\PdfField[]
     */
    public static function extractFieldsFromPdf($pdftkBinary, $pdf)
    {
        $fields_dump = tempnam(sys_get_temp_dir(), 'fdf_dump');

        $process = new Process([$pdftkBinary, $pdf, 'dump_data_fields_utf8', 'output', $fields_dump]);
        $process->mustRun();

        $parser = new PdftkDumpParser($fields_dump);
        $fields = $parser->parse();
        unlink($fields_dump);

        return $fields;
    }

    public static function generatePdfExample($pdftkBinary, $sourcePdf, $targetPdf)
    {
        $fields = self::extractFieldsFromPdf($pdftkBinary, $sourcePdf);

        if (empty($fields)) {
            throw new \RuntimeException('PDF does not have any fields');
        }

        foreach ($fields as $field) {
            $field->setValue($field->getExampleValue());
        }

        $fdf_file = tempnam(sys_get_temp_dir(), 'fdf');

        $writer = new FdfWriter($fields);
        $writer->generate();
        $writer->save($fdf_file);

        $process = new Process([$pdftkBinary, $sourcePdf, 'fill_form', $fdf_file, 'output', $targetPdf]);
        $process->mustRun();

        unlink($fdf_file);
    }

    public static function generateCsvExport($pdftkBinary, $sourcePdf, $targetFile)
    {
        $fields = self::extractFieldsFromPdf($pdftkBinary, $sourcePdf);

        $texts   = [];
        $buttons = [];

        $csv_fields   = [];
        $csv_fields[] = [
            'name',
            'type',
            'description',
            'options',
            'max_length',
            'example_value',
        ];

        foreach ($fields as $field) {
            $csv_entry = [
                $field->getName(),
                $field->getType(),
                $field->getDescription(),
                '',
                '',
                '',
            ];

            if ($field instanceof Fields\ButtonField || $field instanceof Fields\ChoiceField) {
                /** @var $field Fields\ButtonField */
                if ($field->getOptions()) {
                    $options                    = array_values(array_flip($field->getOptions()));
                    $value                      = $options[mt_rand(0, (count($options) - 1))];
                    $buttons[$field->getName()] = $value;

                    $csv_entry[3] = implode('|', $options);
                    $csv_entry[4] = '';
                    $csv_entry[5] = $field->getExampleValue();

                    // Handle possible array for multi-select fields
                    if (is_array($csv_entry[5])) {
                        $csv_entry[5] = '['.implode(',', $csv_entry[5]).']';
                    }
                }
            }

            if ($field instanceof Fields\TextField) {
                // Just use the name as the value
                /* @var $field Fields\TextField */
                $texts[$field->getName()] = $field->getName();
                $csv_entry[4]             = $field->getMaxLength();
                $csv_entry[5]             = $field->getExampleValue();
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
