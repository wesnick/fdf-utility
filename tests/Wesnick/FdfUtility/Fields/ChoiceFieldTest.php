<?php
/**
 * @file ChoiceFieldTest.php
 */

namespace Wesnick\FdfUtility\Fields;


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

}
