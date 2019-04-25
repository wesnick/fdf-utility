<?php

namespace Wesnick\FdfUtility\Fields;

use Wesnick\FdfUtility\FdfWriter;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 */
class ButtonField extends PdfField
{
    public function getEscapedValue()
    {
        return sprintf('(%s%s%s)', chr(0xFE), chr(0xFF), FdfWriter::escapePdfString($this->value));
    }

    /**
     * @return mixed
     */
    public function getExampleValue()
    {
        // Pushbuttons have no value
        if ($this->isPushButton()) {
            return null;
        }

        $keys = array_keys($this->options);

        return $this->options[$keys[mt_rand(0, (count($keys) - 1))]];
    }

    public function isNoToggleOff()
    {
        return $this->isRadioButton() && $this->checkBitValue(PdfField::NO_TOGGLE_OFF);
    }

    public function isPushButton()
    {
        return $this->checkBitValue(PdfField::PUSH_BUTTON);
    }

    public function isRadioButton()
    {
        return $this->checkBitValue(PdfField::RADIO_BUTTON);
    }

    public function isCheckBox()
    {
        return !$this->checkBitValue(PdfField::PUSH_BUTTON) && !$this->checkBitValue(PdfField::RADIO_BUTTON);
    }

    public function isInUnison()
    {
        return $this->checkBitValue(PdfField::IN_UNISON);
    }

    public function getType()
    {
        return 'button';
    }
}
