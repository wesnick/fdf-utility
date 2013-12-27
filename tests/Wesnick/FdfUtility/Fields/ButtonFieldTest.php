<?php
/**
 * @file ButtonFieldTest.php
 */

namespace Wesnick\FdfUtility\Fields;


class ButtonFieldTest extends PdfFieldTest
{

    public function buttonFieldFlagsProvider()
    {
        return array(
            array(3, array()),
            array(4, array(PdfField::NO_TOGGLE_OFF, PdfField::RADIO_BUTTON)),
            array(6, array(PdfField::PUSH_BUTTON)),
            array(24, array()),
            array(25, array(PdfField::NO_TOGGLE_OFF, PdfField::RADIO_BUTTON, PdfField::IN_UNISON)),
        );
    }

    /**
     * @dataProvider buttonFieldFlagsProvider
     */
    public function testButtonFlags($index, $flags)
    {
        $field = $this->fields[$index];
        $flagSum = 0;
        foreach ($flags as $flag) {
            $flagSum |= $flag;
            $this->assertTrue($field->checkBitValue($flag));
        }

        $out = $field->checkBitValue($flagSum);


    }

    /**
     * @dataProvider buttonFieldFlagsProvider
     */
    public function testButtonConvenienceMethods($index, $flags)
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
