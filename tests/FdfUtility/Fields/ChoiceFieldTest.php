<?php

namespace Wesnick\Tests\FdfUtility\Fields;

use Wesnick\FdfUtility\Fields\ChoiceField;
use Wesnick\FdfUtility\Fields\PdfField;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 */
class ChoiceFieldTest extends PdfFieldTest
{

    public function choiceFieldFlagsProvider()
    {
        return array(
            array(1, array(PdfField::COMBO_BOX)),
            array(5, array()),
            array(19, array(PdfField::COMBO_BOX, PdfField::EDITABLE_LIST, PdfField::NO_SPELL_CHECK)),
            array(20, array(PdfField::COMBO_BOX, PdfField::SORTED_LIST, PdfField::NO_SPELL_CHECK)),
            array(21, array(PdfField::REQUIRED, PdfField::COMBO_BOX, PdfField::NO_SPELL_CHECK, PdfField::COMMIT_ON_CHANGE)),
            array(22, array(PdfField::SORTED_LIST)),
            array(23, array(PdfField::MULTI_SELECT)),
            array(26, array()),
            array(27, array(PdfField::COMBO_BOX, PdfField::EDITABLE_LIST, PdfField::NO_SPELL_CHECK)),
        );
    }

    /**
     * @dataProvider choiceFieldFlagsProvider
     */
    public function testChoiceFlags($index, $flags)
    {
        $field = $this->fields[$index];
        $flagSum = 0;
        foreach ($flags as $flag) {
            $flagSum |= $flag;
            $this->assertTrue($field->checkBitValue($flag));
        }

    }

    /**
     * @dataProvider choiceFieldFlagsProvider
     */
    public function testChoiceConvenienceMethods($index, $flags)
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

        $field = new ChoiceField('default_value', 0, 'default', array('default' => 'default', 'default1' => 'default1'), null);

        $this->assertEquals('(default)', $field->getEscapedValue(), "Default Value is respected on null value");

        $field->setValue('default1');
        $this->assertEquals('(default1)', $field->getEscapedValue(), "Default Value is ignored if value not null");

        $field->setValue('');
        $this->assertEquals('(\000)', $field->getEscapedValue(), "Default Value is ignored if value is empty");

    }
}
