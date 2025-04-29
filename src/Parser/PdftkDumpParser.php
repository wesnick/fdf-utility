<?php declare(strict_types=1);

namespace Wesnick\FdfUtility\Parser;

use Wesnick\FdfUtility\Fields\ButtonField;
use Wesnick\FdfUtility\Fields\ChoiceField;
use Wesnick\FdfUtility\Fields\PdfField;
use Wesnick\FdfUtility\Fields\TextField;

/**
 * Parses output from pdftk dump_data_fields
 */
class PdftkDumpParser
{
    /**
     * Current index in the contents array.
     */
    private int $currentIndex;

    /**
     * Array of file lines in the PDFTK Dump File.
     *
     * @var array<string>
     */
    private array $currentContents;

    /**
     * @var array<PdfField>
     */
    private array $fields = [];

    public function __construct(string $file)
    {
        $this->currentContents = file($file);
        $this->currentIndex    = 0;
    }

    /**
     * Parse PDFTK Form Field Dump.
     *
     * @return array<PdfField>
     */
    public function parse(): array
    {
        while ($this->nextBlockIndex()) {
            $currentIndex = $this->currentIndex;
            $nextIndex    = $this->getNextBlockIndex();
            $fieldValues  = $this->processFieldBlock($currentIndex, $nextIndex);
            if ($field = $this->createFieldFromPdftkDump($fieldValues)) {
                $this->fields[] = $field;
            }
        }

        return $this->fields;
    }

    /**
     * Process a Field Element.
     */
    private function processFieldBlock(int $start, int $stop): array
    {
        $itemValues = [];

        for ($x = $start; $x < $stop; ++$x) {
            if (!str_contains($this->currentContents[$x], ':')) {
                continue;
            }

            [$index, $value] = array_map('trim', explode(':', $this->currentContents[$x], 2));

            // Options are an array
            if ('FieldStateOption' === $index) {
                $itemValues[$index][] = $value;
            } else {
                $itemValues[$index] = $value;
            }
        }

        return $itemValues;
    }

    /**
     * Advance the index pointer to the next block.
     */
    private function nextBlockIndex(): bool
    {
        while ($this->currentIndex < count($this->currentContents) - 1) {
            if (str_starts_with($this->currentContents[$this->currentIndex++], '---')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find the next block index.
     */
    private function getNextBlockIndex(): int
    {
        $index = $this->currentIndex;

        while ($index < count($this->currentContents) - 1) {
            if (str_starts_with($this->currentContents[++$index], '---')) {
                return $index--;
            }
        }

        return count($this->currentContents);
    }

    private function createFieldFromPdftkDump(array $dump): ?PdfField
    {
        $name         = $dump['FieldName'];
        $flag         = (int) $dump['FieldFlags'];
        $defaultValue = $dump['FieldValueDefault'] ?? null;

        $options      = [];
        $stateOptions = $dump['FieldStateOption'] ?? [];
        if (!empty($stateOptions)) {
            foreach ($stateOptions as $option) {
                $options[$option] = $option;
            }
        }

        $justification = $dump['FieldJustification'] ?? 'Left';
        $description   = $dump['FieldNameAlt'] ?? '';

        switch ($dump['FieldType']) {
            case 'Button':
                $field = new ButtonField($name, $flag, $defaultValue, $options, $description, $justification);
                break;
            case 'Choice':
                $field = new ChoiceField($name, $flag, $defaultValue, $options, $description, $justification);
                break;
            case 'Text':
                $field = new TextField($name, $flag, $defaultValue, $options, $description, $justification);
                // Update max length property.
                $field->maxLength = isset($dump['FieldMaxLength']) ? (int) $dump['FieldMaxLength'] : null;
                break;
            default:
                return null;
        }

        $field->value = $dump['FieldValue'] ?? null;

        return $field;
    }
}
