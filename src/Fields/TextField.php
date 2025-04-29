<?php declare(strict_types=1);

namespace Wesnick\FdfUtility\Fields;

use Wesnick\FdfUtility\FdfWriter;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 */
class TextField extends PdfField
{
    public ?int $maxLength = null;

    public function getEscapedValue(): string
    {
        $value = $this->value ?? $this->defaultValue;

        return sprintf('(%s%s%s)', chr(0xFE), chr(0xFF), FdfWriter::escapePdfString($value));
    }

    public function getExampleValue(): string
    {
        $value = $this->name;
        // Multilines get extra lines
        if ($this->isMultiLine()) {
            $value .= "\n" . 'Multi-line next line.' . "\n" . 'More Lines...';
        }
        // Comb formatting fills entire comb
        if ($this->isCombFormatting()) {
            $value = str_pad($value, $this->maxLength, 'X');
        }
        // Format a rich text string
        if ($this->isRichText()) {
            $value = '<body><b>' . $value . '</b></body>';
        }

        return $value;
    }

    public function isMultiLine(): bool
    {
        return $this->checkBitValue(PdfField::MULTI_LINE);
    }

    public function isPassword(): bool
    {
        return $this->checkBitValue(PdfField::PASSWORD);
    }

    public function isFileInput(): bool
    {
        return $this->checkBitValue(PdfField::FILE_INPUT);
    }

    public function isNoSpellCheck(): bool
    {
        return $this->checkBitValue(PdfField::NO_SPELL_CHECK);
    }

    public function isNoScroll(): bool
    {
        return $this->checkBitValue(PdfField::NO_SCROLL);
    }

    public function isCombFormatting(): bool
    {
        return $this->checkBitValue(PdfField::COMB_FORMATTING);
    }

    public function isRichText(): bool
    {
        return $this->checkBitValue(PdfField::RICH_TEXT);
    }

    public function isInUnison(): bool
    {
        return $this->checkBitValue(PdfField::IN_UNISON);
    }

    public function getType(): string
    {
        return 'text';
    }
}
