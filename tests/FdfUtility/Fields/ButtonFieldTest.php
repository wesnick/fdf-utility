<?php

namespace Wesnick\Tests\FdfUtility\Fields;

use Wesnick\FdfUtility\Fields\PdfField;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 * @covers \Wesnick\FdfUtility\Fields\ButtonField
 */
class ButtonFieldTest extends PdfFieldTest
{
    public function buttonFieldFlagsProvider()
    {
        return [
            [3, [], false],
            [4, [PdfField::NO_TOGGLE_OFF, PdfField::RADIO_BUTTON], true],
            [6, [PdfField::PUSH_BUTTON], true],
            [24, [], false],
            [25, [PdfField::NO_TOGGLE_OFF, PdfField::RADIO_BUTTON, PdfField::IN_UNISON], true],
        ];
    }

    /**
     * @dataProvider buttonFieldFlagsProvider
     *
     * @param int   $index
     * @param array $flags
     * @param bool  $expected
     */
    public function testButtonFlags($index, array $flags, bool $expected)
    {
        $field   = $this->fields[$index];
        $flagSum = 0;
        foreach ($flags as $flag) {
            $flagSum |= $flag;
            $this->assertTrue($field->checkBitValue($flag));
        }

        $out = $field->checkBitValue($flagSum);
        $this->assertSame($expected, $out);
    }

    /**
     * @dataProvider buttonFieldFlagsProvider
     *
     * @param int   $index
     * @param array $flags
     */
    public function testButtonConvenienceMethods($index, array $flags)
    {
        $field   = $this->fields[$index];
        $methods = PdfFieldTest::$conveninceMethods;
        foreach ($methods as $flag => $method) {
            if (in_array($flag, $flags, true)) {
                $this->assertTrue(call_user_func([$field, $method]), sprintf('Field Name %s, Index %d, %s is True', $field->getName(), $index, PdfField::$flags[$flag]));
            } else {
                $this->assertFalse(call_user_func([$field, $method]), sprintf('Field Name %s, Index %d, %s is False', $field->getName(), $index, PdfField::$flags[$flag]));
            }
        }
    }
}
