<?php declare(strict_types=1);

namespace Wesnick\FdfUtility\Fields;

use Wesnick\FdfUtility\FdfWriter;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 */
class ChoiceField extends PdfField
{
    public function getEscapedValue(): string
    {
        $value = $this->value ?? $this->defaultValue;

        if (is_array($value) && $this->isMultiSelect()) {
            $out = '';
            foreach ($value as $val) {
                $out .= '(' . FdfWriter::escapePdfString($val) . ')';
            }

            return '[ ' . $out . ' ]';
        }

        return sprintf('(%s%s%s)', chr(0xFE), chr(0xFF), FdfWriter::escapePdfString($value));
    }

    public function getExampleValue(): mixed
    {
        if ($this->isEditableList()) {
            return 'Edited Value';
        }

        if ($this->isMultiSelect()) {
            return $this->options;
        }

        $keys = array_keys($this->options);

        return $this->options[$keys[random_int(0, (count($keys) - 1))]];
    }

    public function isMultiSelect(): bool
    {
        return $this->checkBitValue(PdfField::MULTI_SELECT);
    }

    public function isComboBox(): bool
    {
        return $this->checkBitValue(PdfField::COMBO_BOX);
    }

    public function isListBox(): bool
    {
        return !$this->checkBitValue(PdfField::COMBO_BOX);
    }

    public function isEditableList(): bool
    {
        return $this->checkBitValue(PdfField::EDITABLE_LIST) && $this->isComboBox();
    }

    public function isSortedList(): bool
    {
        return $this->checkBitValue(PdfField::SORTED_LIST);
    }

    public function isCommitOnChange(): bool
    {
        return $this->checkBitValue(PdfField::COMMIT_ON_CHANGE);
    }

    public function isNoSpellCheck(): bool
    {
        return $this->checkBitValue(PdfField::NO_SPELL_CHECK);
    }

    public function getType(): string
    {
        return 'choice';
    }
}
