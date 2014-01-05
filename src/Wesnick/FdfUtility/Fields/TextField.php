<?php
/**
 * @file
 * TextField.php
 */

namespace Wesnick\FdfUtility\Fields;


use Wesnick\FdfUtility\FdfWriter;

class TextField extends PdfField
{

    /**
     * @var int
     */
    protected $maxLength;

    /**
     * @param int $maxLength
     */
    public function setMaxLength($maxLength)
    {
        $this->maxLength = $maxLength;
    }

    /**
     * @return int
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }


    public function getEscapedValue()
    {

        $value = is_null($this->value) ? $this->defaultValue : $this->value;

        return '(' .FdfWriter::escapePdfString($value) . ')';
    }

    /**
     * @return string
     */
    public function getExampleValue()
    {
        $value = $this->name;
        // Multilines get extra lines
        if ($this->isMultiLine()) {
            $value .= "\n" . "Multi-line next line." . "\n" . "More Lines...";
        }
        // Comb formatting fills entire comb
        if ($this->isCombFormatting()) {
            $value = str_pad($value, $this->getMaxLength(), 'X');
        }
        // Format a rich text string
        if ($this->isRichText()) {
            $value = "<body><b>" . $value . "</b></body>";
        }

        return $value;
    }

    public function isMultiLine()
    {
        return $this->checkBitValue(PdfField::MULTI_LINE);
    }

    public function isPassword()
    {
        return $this->checkBitValue(PdfField::PASSWORD);
    }

    public function isFileInput()
    {
        return $this->checkBitValue(PdfField::FILE_INPUT);
    }

    public function isNoSpellCheck()
    {
        return $this->checkBitValue(PdfField::NO_SPELL_CHECK);
    }

    public function isNoScroll()
    {
        return $this->checkBitValue(PdfField::NO_SCROLL);
    }

    public function isCombFormatting()
    {
        return $this->checkBitValue(PdfField::COMB_FORMATTING);
    }

    public function isRichText()
    {
        return $this->checkBitValue(PdfField::RICH_TEXT);
    }

    public function isInUnison()
    {
        return $this->checkBitValue(PdfField::IN_UNISON);
    }

}
