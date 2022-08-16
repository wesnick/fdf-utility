<?php

namespace Wesnick\Tests\FdfUtility\Fields;

use Wesnick\FdfUtility\Fields\ChoiceField;
use Wesnick\FdfUtility\Fields\PdfField;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 * @covers \Wesnick\FdfUtility\Fields\ChoiceField
 */
class ChoiceFieldTest extends PdfFieldTest
{
    public function choiceFieldFlagsProvider()
    {
        return [
            [1, [PdfField::COMBO_BOX], true],
            [5, [], false],
            [19, [PdfField::COMBO_BOX, PdfField::EDITABLE_LIST, PdfField::NO_SPELL_CHECK], true],
            [20, [PdfField::COMBO_BOX, PdfField::SORTED_LIST, PdfField::NO_SPELL_CHECK], true],
            [21, [PdfField::REQUIRED, PdfField::COMBO_BOX, PdfField::NO_SPELL_CHECK, PdfField::COMMIT_ON_CHANGE], true],
            [22, [PdfField::SORTED_LIST], true],
            [23, [PdfField::MULTI_SELECT], true],
            [26, [], false],
            [27, [PdfField::COMBO_BOX, PdfField::EDITABLE_LIST, PdfField::NO_SPELL_CHECK], true],
        ];
    }

    /**
     * @dataProvider choiceFieldFlagsProvider
     *
     * @param int   $index
     * @param array $flags
     * @param bool  $expected
     */
    public function testChoiceFlags($index, array $flags, bool $expected)
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
     * @dataProvider choiceFieldFlagsProvider
     *
     * @param int   $index
     * @param array $flags
     */
    public function testChoiceConvenienceMethods($index, array $flags)
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
        $field = new ChoiceField('default_value', 0, 'default', ['default' => 'default', 'default1' => 'default1'], null);

        $this->assertSame(iconv('UTF-8', 'UTF-16BE', '⣾＀搀攀昀愀甀氀琩'), $field->getEscapedValue(), 'Default Value is respected on null value');

        $field->setValue('default1');
        $this->assertSame(iconv('UTF-8', 'UTF-16BE', '⣾＀搀攀昀愀甀氀琀ㄩ'), $field->getEscapedValue(), 'Default Value is ignored if value not null');

        $field->setValue('');
        $this->assertSame(iconv('UTF-8', 'UTF-16BE', '⣾Ｉ'), $field->getEscapedValue(), 'Default Value is ignored if value is empty');
    }
}
