<?php

declare(strict_types=1);

namespace Wesnick\FdfUtility\Fields;

use Wesnick\FdfUtility\FdfWriter;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 */
class ButtonField extends PdfField
{
    public function getEscapedValue(): string
    {
        return sprintf('(%s%s%s)', chr(0xFE), chr(0xFF), FdfWriter::escapePdfString($this->value));
    }

    public function getExampleValue(): mixed
    {
        // Push Buttons have no value.
        if ($this->isPushButton()) {
            return null;
        }

        $keys  = array_keys($this->options);
        $count = count($keys);
        if (0 === $count) {
            return null;
        }

        return $this->options[$keys[random_int(0, $count - 1)]];
    }

    public function isNoToggleOff(): bool
    {
        return $this->isRadioButton() && $this->checkBitValue(PdfField::NO_TOGGLE_OFF);
    }

    public function isPushButton(): bool
    {
        return $this->checkBitValue(PdfField::PUSH_BUTTON);
    }

    public function isRadioButton(): bool
    {
        return $this->checkBitValue(PdfField::RADIO_BUTTON);
    }

    public function isCheckBox(): bool
    {
        return !$this->checkBitValue(PdfField::PUSH_BUTTON) && !$this->checkBitValue(PdfField::RADIO_BUTTON);
    }

    public function isInUnison(): bool
    {
        return $this->checkBitValue(PdfField::IN_UNISON);
    }

    public function getType(): string
    {
        return 'button';
    }
}
