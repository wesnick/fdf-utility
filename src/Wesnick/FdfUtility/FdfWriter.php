<?php
/**
 * @file FdfWriter.php
 */

namespace Wesnick\FdfUtility;


use Wesnick\FdfUtility\Fields\PdfField;

class FdfWriter
{
    const HEADER                =   "%FDF-1.2\x0d%\xe2\xe3\xcf\xd3\x0d\x0a";
    const FOOTER                =   "trailer\x0d<<\x0d/Root 1 0 R \x0d\x0d>>\x0d%%EOF\x0d\x0a";
    const ROOT_DICTIONARY_OPEN  =   "1 0 obj\x0d<< ";
    const ROOT_DICTIONARY_CLOSE =   ">> \x0dendobj\x0d";
    const FDF_DICTIONARY_OPEN   =   "\x0d/FDF << ";
    const FDF_DICTIONARY_CLOSE  =   ">> \x0d";
    const FIELD_TAG_OPEN        =   "/Fields [ ";
    const FIELD_TAG_CLOSE       =   "] \x0d";


    /**
     * @var string
     */
    private $buffer;

    /**
     * @var array
     */
    private $fields = array();

    function __construct(array $fields = array())
    {
        $this->buffer = '';
        foreach ($fields as $field) {
            $this->addField($field);
        }
    }

    public function addField(PdfField $field)
    {

        $nameParts = explode('.', $field->getName());

        $fields =& $this->fields;
        while (!empty($nameParts)) {
            $key = array_shift($nameParts);
            $fields =& $fields[$key];
        }

        $fields = $field;
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
                if ($field->isPushButton()) {
                    continue;
                }
                $this->appendKey($key);
                $this->appendValue($field);
            }
        }
    }

    private function appendKey($key)
    {
        $this->buffer .= sprintf('<< /T (%s)', $this->escapePdfString($key));
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
        $this->buffer .= sprintf("/%s %s %s >>", $valueFormat, $field->getEscapedValue($this), $this->getFieldFlags($field)) . "\n";
    }

    private function getFieldFlags(PdfField $field)
    {
        $flags = $field->isHidden() ? "/SetF 2 " : "/ClrF 2 ";
        $flags .= $field->isReadOnly() ? "/SetFf 1 " : "/ClrFf 1 ";
        return $flags;
    }

    /**
     * @param $string
     * @return string
     */
    public static function escapePdfString($string)
    {

        $escaped = '';

        foreach (str_split($string) as $char) {
            switch ($ordinal = ord($char)) {
                // If open paren, close paren, or backslash, escape character
                case in_array($ordinal, array(0x28, 0x29, 0x5c)):
                    // backslash == chr(0x5c)
                    $escaped .= chr(0x5c) . $char;
                    break;
                case $ordinal < 32:
                case $ordinal > 126:
                    $escaped .= sprintf("\\%03o", $ordinal);
                    break;
                default:
                    $escaped .= $char;
            }
        }

        return $escaped;
    }

    /**
     * @param $string
     * @return string
     */
    public static function escapePdfName($string)
    {
        $escaped = '';

        foreach (str_split($string) as $char) {
            switch ($ordinal = ord($char)) {
                // If ascii not between 33 and 126, or a hash mark, use a hex code
                case $ordinal < 33:
                case $ordinal > 126:
                case $ordinal == 23:
                    $escaped .= sprintf("\\%03o", $ordinal);
                    break;
                default:
                    $escaped .= $char;
            }
        }

        return $escaped;
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
     * Writes contents as a file
     *
     * @param string $filename
     *
     * @return integer|boolean Bytes written
     */
    public function save($filename)
    {
        return file_put_contents($filename, $this->buffer);
    }

    function __toString()
    {
        return $this->buffer;
    }
} 
