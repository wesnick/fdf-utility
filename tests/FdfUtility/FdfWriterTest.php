<?php

namespace Wesnick\Tests\FdfUtility;

use Wesnick\FdfUtility\FdfWriter;
use Wesnick\FdfUtility\Fields\TextField;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 * @covers \Wesnick\FdfUtility\FdfWriter
 */
class FdfWriterTest extends \PHPUnit_Framework_TestCase
{
    public function pdfStrings()
    {
        return [
            ['abcdef~', 'abcdef~'], // printable characters
            ['\()', '\\\\\(\)'],  // escaped characters \,(,)
            ['xx xx', 'xx xx'],  // space print normally
            ['xx'.chr(10).'xx', 'xx\012xx'],  // non-printable characters
        ];
    }

    /**
     * @dataProvider PdfStrings
     *
     * @param string $input
     * @param string $escaped
     */
    public function testEscapePdfString($input, $escaped)
    {
        $this->assertSame($escaped, FdfWriter::escapePdfString($input));
    }

    public function pdfNames()
    {
        return [
            ['abcdef~', 'abcdef~'], // printable characters
            ['\()', '\()'],  //  \,(,) are not escaped
            ['xx xx', 'xx\040xx'],  // space print normally
            ['xx'.chr(10).'xx', 'xx\012xx'],  // non-printable characters
        ];
    }

    /**
     * @dataProvider PdfNames
     *
     * @param string $input
     * @param string $escaped
     */
    public function testEscapePdfNames($input, $escaped)
    {
        $this->assertSame($escaped, FdfWriter::escapePdfName($input));
    }

    public function testAddFields()
    {
        $f1 = new TextField('test.string', 0);

        $f2 = new TextField('test.string2', 0);

        $expected = [
            'test' => [
                'string'  => $f1,
                'string2' => $f2,
            ],
        ];

        $writer = new FdfWriter([$f1, $f2]);
        $this->assertAttributeSame($expected, 'fields', $writer);
    }
}
