<?php

declare(strict_types=1);

namespace Wesnick\FdfUtility;

use Wesnick\FdfUtility\Fields\PdfField;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 */
class FdfWriter implements \Stringable
{
    public const HEADER                = "%FDF-1.2\x0d%\xe2\xe3\xcf\xd3\x0d\x0a";
    public const FOOTER                = "trailer\x0d<<\x0d/Root 1 0 R \x0d\x0d>>\x0d%%EOF\x0d\x0a";
    public const ROOT_DICTIONARY_OPEN  = "1 0 obj\x0d<< ";
    public const ROOT_DICTIONARY_CLOSE = ">> \x0dendobj\x0d";
    public const FDF_DICTIONARY_OPEN   = "\x0d/FDF << ";
    public const FDF_DICTIONARY_CLOSE  = ">> \x0d";
    public const FIELD_TAG_OPEN        = '/Fields [ ';
    public const FIELD_TAG_CLOSE       = "] \x0d";

    private string $buffer = '';

    /**
     * @var array<PdfField>
     */
    private array $fields = [];

    /**
     * @param array<PdfField> $fields
     */
    public function __construct(array $fields = [])
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }
    }

    public function __toString(): string
    {
        return $this->buffer;
    }

    public function addField(PdfField $field): void
    {
        $nameParts = explode('.', $field->name);
        $fields    = &$this->fields;

        $it = new \ArrayIterator($nameParts);
        while ($it->valid()) {
            $fields = &$fields[$it->current()];
            $it->next();
        }

        $fields = $field;
    }

    public function generate(): void
    {
        $this->buffer = self::HEADER;
        $this->buffer .= self::ROOT_DICTIONARY_OPEN;
        $this->buffer .= self::FDF_DICTIONARY_OPEN;
        $this->buffer .= self::FIELD_TAG_OPEN;
        $this->writeFields($this->fields);
        $this->buffer .= self::FIELD_TAG_CLOSE;
        $this->buffer .= self::FDF_DICTIONARY_CLOSE;
        $this->buffer .= self::ROOT_DICTIONARY_CLOSE;
        $this->buffer .= self::FOOTER;
    }

    /**
     * Writes contents as a file.
     */
    public function save(string $filename): int|bool
    {
        return file_put_contents($filename, $this->buffer);
    }

    /**
     * Encoding solution ported from php-pdftk.
     *
     * @see https://github.com/mikehaertl/php-pdftk/blob/master/src/FdfFile.php#L60
     */
    public static function escapePdfString(?string $string): string
    {
        // Create UTF-16BE string encode as ASCII hex
        $string ??= $string;
        $utf16Value = mb_convert_encoding($string, 'UTF-16BE', 'UTF-8');
        if (false === $utf16Value) {
            return $string;
        }

        return strtr($utf16Value, ['(' => '\(', ')' => '\)', '\\' => '\\\\']);
    }

    /**
     * @param array<string|int, PdfField|array<PdfField>> $fields
     */
    private function writeFields(array $fields): void
    {
        foreach ($fields as $key => $field) {
            if (is_array($field)) {
                $this->appendKey((string) $key);
                $this->openKids();
                $this->writeFields($field);
                $this->closeKids();
            } elseif ($field instanceof PdfField) {
                if ($field->isPushButton() || null === $field->getValue()) {
                    continue;
                }
                $this->appendKey((string) $key);
                $this->appendValue($field);
            }
        }
    }

    private function appendKey(string $key): void
    {
        $this->buffer .= sprintf('<< /T (%s%s%s)', chr(0xFE), chr(0xFF), self::escapePdfString($key));
    }

    private function openKids(): void
    {
        $this->buffer .= ' /Kids [';
    }

    private function closeKids(): void
    {
        $this->buffer .= '] >>';
    }

    private function appendValue(PdfField $field): void
    {
        $this->buffer .= sprintf(
            '/%s %s %s >>',
            $field->isRichText() ? 'RV' : 'V',
            $field->getEscapedValue(),
            $this->getFieldFlags($field)
        );
        $this->buffer .= "\n";
    }

    private function getFieldFlags(PdfField $field): string
    {
        $flags = $field->isHidden() ? '/SetF 2 ' : '/ClrF 2 ';
        $flags .= $field->isReadOnly() ? '/SetFf 1 ' : '/ClrFf 1 ';

        return $flags;
    }
}
