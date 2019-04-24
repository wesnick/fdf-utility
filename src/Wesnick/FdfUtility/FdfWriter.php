<?php

namespace Wesnick\FdfUtility;

use Wesnick\FdfUtility\Fields\PdfField;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 */
class FdfWriter
{
    const HEADER                =   "%FDF-1.2\x0d%\xe2\xe3\xcf\xd3\x0d\x0a";
    const FOOTER                =   "trailer\x0d<<\x0d/Root 1 0 R \x0d\x0d>>\x0d%%EOF\x0d\x0a";
    const ROOT_DICTIONARY_OPEN  =   "1 0 obj\x0d<< ";
    const ROOT_DICTIONARY_CLOSE =   ">> \x0dendobj\x0d";
    const FDF_DICTIONARY_OPEN   =   "\x0d/FDF << ";
    const FDF_DICTIONARY_CLOSE  =   ">> \x0d";
    const FIELD_TAG_OPEN        =   '/Fields [ ';
    const FIELD_TAG_CLOSE       =   "] \x0d";

    /**
     * @var string
     */
    private $buffer;

    /**
     * @var array
     */
    private $fields = [];

    public function __construct(array $fields = [])
    {
        $this->buffer = '';
        foreach ($fields as $field) {
            $this->addField($field);
        }
    }

    public function __toString()
    {
        return $this->buffer;
    }

    public function addField(PdfField $field)
    {
        $nameParts = explode('.', $field->getName());

        $fields =&$this->fields;
        while (!empty($nameParts)) {
            $key    = array_shift($nameParts);
            $fields =&$fields[$key];
        }

        $fields = $field;
    }

    /**
     * @param $string
     *
     * @return string
     */
    public static function escapePdfString($string)
    {
        $utf16Value = mb_convert_encoding($string,'UTF-16BE', 'UTF-8');
        $utf16Value = strtr($utf16Value, array('(' => '\\(', ')'=>'\\)'));
        return $utf16Value;
    }

    public function generate()
    {
        $this->buffer  = self::HEADER;
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
     *
     * @param string $filename
     *
     * @return int|bool Bytes written
     */
    public function save($filename)
    {
        return file_put_contents($filename, $this->buffer);
    }

    private function writeFields($fields)
    {
        foreach ($fields as $key => $field) {
            if (is_array($field)) {
                $this->appendKey($key);
                $this->openKids();
                $this->writeFields($field);
                $this->closeKids();
            } elseif ($field instanceof PdfField) {
                if ($field->isPushButton() || null === $field->getValue()) {
                    continue;
                }
                $this->appendKey($key);
                $this->appendValue($field);
            }
        }
    }

    private function appendKey($key)
    {
        $this->buffer .= sprintf('<< /T (%s%s%s)', chr(0xFE), chr(0xFF), self::escapePdfString($key));
    }

    private function openKids()
    {
        $this->buffer .= ' /Kids [';
    }

    private function closeKids()
    {
        $this->buffer .= '] >>';
    }

    private function appendValue(PdfField $field)
    {
        $valueFormat = $field->isRichText() ? 'RV' : 'V';
        $this->buffer .= sprintf('/%s %s %s >>', $valueFormat, $field->getEscapedValue(), $this->getFieldFlags($field))."\n";
    }

    private function getFieldFlags(PdfField $field)
    {
        $flags = $field->isHidden() ? '/SetF 2 ' : '/ClrF 2 ';
        $flags .= $field->isReadOnly() ? '/SetFf 1 ' : '/ClrFf 1 ';

        return $flags;
    }
}
