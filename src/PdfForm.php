<?php

declare(strict_types=1);

namespace Wesnick\FdfUtility;

use Symfony\Component\Process\Process;
use Wesnick\FdfUtility\Fields\PdfField;
use Wesnick\FdfUtility\Parser\PdftkDumpParser;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 */
class PdfForm
{
    /**
     * @return array<PdfField>
     */
    public static function extractFieldsFromPdf(string $pdftkBinary, string $pdf): array
    {
        $dump = tempnam(sys_get_temp_dir(), 'fdf_dump');

        $process = new Process([$pdftkBinary, $pdf, 'dump_data_fields_utf8', 'output', $dump]);
        $process->mustRun();

        $parser = new PdftkDumpParser($dump);
        $fields = $parser->parse();
        unlink($dump);

        return $fields;
    }

    public static function generatePdfExample(string $pdftkBinary, string $sourcePdf, string $targetPdf): void
    {
        $fields = self::extractFieldsFromPdf($pdftkBinary, $sourcePdf);

        if ([] === $fields) {
            throw new \RuntimeException('PDF does not have any fields');
        }

        foreach ($fields as $field) {
            $field->setValue($field->getExampleValue());
        }

        $fdfFile = tempnam(sys_get_temp_dir(), 'fdf');

        $writer = new FdfWriter($fields);
        $writer->generate();
        $writer->save($fdfFile);

        $process = new Process([$pdftkBinary, $sourcePdf, 'fill_form', $fdfFile, 'output', $targetPdf]);
        $process->mustRun();

        unlink($fdfFile);
    }

    public static function generateCsvExport(string $pdftkBinary, string $sourcePdf, string $targetFile): void
    {
        $fields    = self::extractFieldsFromPdf($pdftkBinary, $sourcePdf);
        $csvFields = [
            [
                'name',
                'type',
                'description',
                'options',
                'max_length',
                'example_value',
            ],
        ];

        foreach ($fields as $field) {
            $csvEntry = [
                $field->name,
                $field->getType(),
                $field->description,
                '',
                '',
                '',
            ];

            if ($field instanceof Fields\ButtonField || $field instanceof Fields\ChoiceField) {
                $options     = array_values(array_flip($field->options));
                $csvEntry[3] = implode('|', $options);
                $csvEntry[4] = '';
                $csvEntry[5] = $field->getExampleValue();

                // Handle possible array for multi-select fields
                if (is_array($csvEntry[5])) {
                    $csvEntry[5] = '[' . implode(',', $csvEntry[5]) . ']';
                }
            } elseif ($field instanceof Fields\TextField) {
                // Just use the name as the value
                $csvEntry[4] = $field->getMaxLength();
                $csvEntry[5] = $field->getExampleValue();
            }

            $csvFields[] = $csvEntry;
        }

        $handle = fopen($targetFile, 'wb');
        if (false === $handle) {
            throw new \RuntimeException('Unable to open file: ' . $targetFile);
        }
        foreach ($csvFields as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
    }
}
