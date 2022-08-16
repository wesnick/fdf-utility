<?php

namespace Wesnick\Tests\FdfUtility\Fields;

use Wesnick\FdfUtility\Fields\PdfField;
use Wesnick\FdfUtility\Fields\TextField;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 * @covers \Wesnick\FdfUtility\Fields\TextField
 */
class TextFieldTest extends PdfFieldTest
{
    public function textFieldFlagsProvider()
    {
        return [
            [0, [], false],
            [2, [PdfField::REQUIRED], true],
            [7, [PdfField::MULTI_LINE], true],
            [8, [PdfField::READ_ONLY], true],
            [9, [PdfField::PASSWORD, PdfField::REQUIRED, PdfField::NO_SPELL_CHECK, PdfField::NO_SCROLL], true],
            [10, [PdfField::FILE_INPUT, PdfField::NO_SPELL_CHECK], true],
            [11, [PdfField::NO_SPELL_CHECK, PdfField::NO_SCROLL], true],
            [12, [PdfField::NO_SPELL_CHECK, PdfField::NO_SCROLL], true],
            [13, [PdfField::MULTI_LINE, PdfField::NO_SPELL_CHECK, PdfField::NO_SCROLL], true],
            [14, [PdfField::NO_SPELL_CHECK], true],
            [15, [PdfField::NO_SPELL_CHECK, PdfField::NO_SCROLL], true],
            [16, [PdfField::NO_SPELL_CHECK, PdfField::NO_SCROLL, PdfField::IN_UNISON], true],
            [17, [PdfField::NO_SCROLL], true],
            [18, [PdfField::NO_SPELL_CHECK, PdfField::NO_SCROLL, PdfField::COMB_FORMATTING], true],
        ];
    }

    /**
     * @dataProvider textFieldFlagsProvider
     *
     * @param int   $index
     * @param array $flags
     * @param bool  $expected
     */
    public function testTextFlags($index, array $flags, bool $expected)
    {
        $field   = $this->fields[$index];
        $flagSum = 0;
        foreach ($flags as $flag) {
            $flagSum |= $flag;
            $this->assertTrue($field->checkBitValue($flag));
        }

        $out = $field->checkBitValue($flagSum);
        $sum = [];
        foreach (PdfField::$flags as $f => $d) {
            if ($field->checkBitValue($f)) {
                $sum[$f] = $d;
            }
        }

        $this->assertCount(count($flags), $sum);
        $this->assertSame($expected, $out);
    }

    /**
     * @dataProvider textFieldFlagsProvider
     *
     * @param int   $index
     * @param array $flags
     */
    public function testTextConvenienceMethods($index, array $flags)
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

    public function testDefaultValueIsRespected()
    {
        $field = new TextField('default_value', 0, 'default', [], null);

        $this->assertSame(iconv('UTF-8', 'UTF-16BE', '⣾＀搀攀昀愀甀氀琩'), $field->getEscapedValue(), 'Default Value is respected on null value');

        $field->setValue('value');
        $this->assertSame(iconv('UTF-8', 'UTF-16BE', '⣾＀瘀愀氀甀攩'), $field->getEscapedValue(), 'Default Value is ignored if value not null');

        $field->setValue('');
        $this->assertSame(iconv('UTF-8', 'UTF-16BE', '⣾Ｉ'), $field->getEscapedValue(), 'Default Value is ignored if value is empty');
    }
}
