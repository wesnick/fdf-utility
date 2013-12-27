<?php
/**
 * @file FdfWriterTest.php
 */

namespace Wesnick\FdfUtility;


use Wesnick\FdfUtility\Fields\TextField;

class FdfWriterTest extends \PHPUnit_Framework_TestCase
{

    public function pdfStrings()
    {
        return array(
            array('abcdef~', 'abcdef~'), // printable characters
            array('\()', '\\\\\(\)'),  // escaped characters \,(,)
            array('xx xx', 'xx xx'),  // space print normally
            array('xx' . chr(10) . 'xx', 'xx\012xx'),  // non-printable characters
        );
    }

    /**
     * @dataProvider PdfStrings
     */
    public function testEscapePdfString($input, $escaped)
    {
        $this->assertEquals($escaped, FdfWriter::escapePdfString($input));
    }

    public function pdfNames()
    {
        return array(
            array('abcdef~', 'abcdef~'), // printable characters
            array('\()', '\()'),  //  \,(,) are not escaped
            array('xx xx', 'xx\040xx'),  // space print normally
            array('xx' . chr(10) . 'xx', 'xx\012xx'),  // non-printable characters
        );
    }

    /**
     * @dataProvider PdfNames
     */
    public function testEscapePdfNames($input, $escaped)
    {
        $this->assertEquals($escaped, FdfWriter::escapePdfName($input));
    }

    public function testAddFields()
    {

        $f1 = new TextField();
        $f1->setName('test.string');

        $f2 = new TextField();
        $f2->setName('test.string2');

        $expected = array(
            'test' => array(
                'string' => $f1,
                'string2' => $f2,
            ),
        );

        $writer = new FdfWriter(array($f1, $f2));
        $this->assertAttributeEquals($expected, 'fields', $writer);
    }
}
