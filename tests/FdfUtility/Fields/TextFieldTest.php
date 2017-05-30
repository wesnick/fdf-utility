<?php

namespace Wesnick\Tests\FdfUtility\Fields;

use Wesnick\FdfUtility\Fields\PdfField;
use Wesnick\FdfUtility\Fields\TextField;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 */
class TextFieldTest extends PdfFieldTest
{


    public function textFieldFlagsProvider()
    {
        return array(
            array(0, array()),
            array(2, array(PdfField::REQUIRED)),
            array(7, array(PdfField::MULTI_LINE)),
            array(8, array(PdfField::READ_ONLY)),
            array(9, array(PdfField::PASSWORD, PdfField::REQUIRED, PdfField::NO_SPELL_CHECK, PdfField::NO_SCROLL)),
            array(10, array(PdfField::FILE_INPUT, PdfField::NO_SPELL_CHECK)),
            array(11, array(PdfField::NO_SPELL_CHECK, PdfField::NO_SCROLL)),
            array(12, array(PdfField::NO_SPELL_CHECK, PdfField::NO_SCROLL)),
            array(13, array(PdfField::MULTI_LINE, PdfField::NO_SPELL_CHECK, PdfField::NO_SCROLL)),
            array(14, array(PdfField::NO_SPELL_CHECK)),
            array(15, array(PdfField::NO_SPELL_CHECK, PdfField::NO_SCROLL)),
            array(16, array(PdfField::NO_SPELL_CHECK, PdfField::NO_SCROLL, PdfField::IN_UNISON)),
            array(17, array(PdfField::NO_SCROLL)),
            array(18, array(PdfField::NO_SPELL_CHECK, PdfField::NO_SCROLL, PdfField::COMB_FORMATTING))

        );
    }

    /**
     * @dataProvider textFieldFlagsProvider
     */
    public function testTextFlags($index, $flags)
    {
        $field = $this->fields[$index];
        $flagSum = 0;
        foreach ($flags as $flag) {
            $flagSum |= $flag;
            $this->assertTrue($field->checkBitValue($flag));
        }

        $out = $field->checkBitValue($flagSum);
        $sum = array();
        foreach (PdfField::$flags as $f => $d) {
            if ($out = $field->checkBitValue($f)) {
                $sum[$f] = $d;
            }
        }
    }

    /**
     * @dataProvider textFieldFlagsProvider
     */
    public function testTextConvenienceMethods($index, $flags)
    {
        $field = $this->fields[$index];
        $methods = PdfFieldTest::$conveninceMethods;
        foreach ($methods as $flag => $method) {
            if (in_array($flag, $flags)) {
                $this->assertTrue(call_user_func(array($field, $method)), sprintf("Field Name %s, Index %d, %s is True", $field->getName(), $index, PdfField::$flags[$flag]));
            }
            else {
                $this->assertFalse(call_user_func(array($field, $method)), sprintf("Field Name %s, Index %d, %s is False", $field->getName(), $index, PdfField::$flags[$flag]));
            }

        }
    }


    public function testDefaultValueIsRespected()
    {

        $field = new TextField('default_value', 0, 'default', array(), null);

        $this->assertEquals('(default)', $field->getEscapedValue(), "Default Value is respected on null value");

        $field->setValue('value');
        $this->assertEquals('(value)', $field->getEscapedValue(), "Default Value is ignored if value not null");

        $field->setValue('');
        $this->assertEquals('(\)', $field->getEscapedValue(), "Default Value is ignored if value is empty");

    }

}
