<?php

namespace Wesnick\Tests\FdfUtility;

use PHPUnit\Framework\TestCase;
use Wesnick\FdfUtility\FdfWriter;
use Wesnick\FdfUtility\Fields\TextField;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 * @covers \Wesnick\FdfUtility\FdfWriter
 */
class FdfWriterTest extends TestCase
{
    public function pdfStrings()
    {
        return [
            ['abcdef~', 'abcdef~'], // printable characters
            ['John Smith', 'John Smith'], // printable characters
            ['806 – 4815 Eldoràdo Mews', '806 – 4815 Eldoràdo Mews'], // printable characters
            ['çÇÀàÈèùÉéâÂÊêÎîÔôÛûëïöüÿæ', 'çÇÀàÈèùÉéâÂÊêÎîÔôÛûëïöüÿæ'], // printable characters
            ['4045 €', '4045 €'], // printable characters
            ['()', '\⠀尩'],  // escaped characters (,)
            ['xx xx', 'xx xx'],  // space print normally
            ['xx'.chr(10).'xx', 'xx'.chr(10).'xx'],  // non-printable characters
            // TODO test for backslash being scaped
//            ['Thank you, broker Alison 855-000-0000\\', 'Thank you, broker Alison 855-000-0000\\\\'],  // escaped characters \, \
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
        $this->assertSame(iconv('UTF-8', 'UTF-16BE', $escaped), FdfWriter::escapePdfString($input));
    }

    public function pdfNames()
    {
        return [
            ['abcdef~', 'abcdef~'], // printable characters
            ['()', '\⠀尩'],  //  (,) are not escaped
            ['xx xx', 'xx xx'],  // space print normally
            ['xx'.chr(10).'xx', 'xx'.chr(10).'xx'],  // non-printable characters
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
        $this->assertSame(iconv('UTF-8', 'UTF-16BE', $escaped), FdfWriter::escapePdfString($input));
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

        $refClass = new \ReflectionObject($writer);
        $refProperty = $refClass->getProperty('fields');
        $refProperty->setAccessible(true);

        $this->assertSame($expected, $refProperty->getValue($writer));
    }
}
