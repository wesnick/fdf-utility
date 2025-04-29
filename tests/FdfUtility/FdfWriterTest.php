<?php declare(strict_types=1);

namespace Wesnick\Tests\FdfUtility;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Wesnick\FdfUtility\FdfWriter;
use Wesnick\FdfUtility\Fields\TextField;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 */
#[
    CoversClass(FdfWriter::class)
]
final class FdfWriterTest extends TestCase
{
    #[
        DataProvider('pdfStringsProvider')
    ]
    public function testEscapePdfString(string $input, string $escaped): void
    {
        $this->assertSame(iconv('UTF-8', 'UTF-16BE', $escaped), FdfWriter::escapePdfString($input));
    }

    #[
        DataProvider('pdfNamesProvider')
    ]
    public function testEscapePdfNames(string $input, string $escaped): void
    {
        $this->assertSame(iconv('UTF-8', 'UTF-16BE', $escaped), FdfWriter::escapePdfString($input));
    }

    public function testAddFields(): void
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

        $refClass    = new \ReflectionObject($writer);
        $refProperty = $refClass->getProperty('fields');
        $refProperty->setAccessible(true);

        $this->assertSame($expected, $refProperty->getValue($writer));
    }

    public static function pdfStringsProvider(): \Generator
    {
        yield ['abcdef~', 'abcdef~']; // printable characters
        yield ['John Smith', 'John Smith']; // printable characters
        yield ['806 – 4815 Eldoràdo Mews', '806 – 4815 Eldoràdo Mews']; // printable characters
        yield ['çÇÀàÈèùÉéâÂÊêÎîÔôÛûëïöüÿæ', 'çÇÀàÈèùÉéâÂÊêÎîÔôÛûëïöüÿæ']; // printable characters
        yield ['4045 €', '4045 €']; // printable characters
        yield ['()', '\⠀尩'];  // escaped characters (,)
        yield ['xx xx', 'xx xx'];  // space print normally
        yield ['xx' . chr(10) . 'xx', 'xx' . chr(10) . 'xx'];  // non-printable characters

        // TODO test for backslash being scaped
        //  ['Thank you, broker Alison 855-000-0000\\', 'Thank you, broker Alison 855-000-0000\\\\'],  // escaped characters \, \
    }

    public static function pdfNamesProvider(): \Generator
    {
        yield ['abcdef~', 'abcdef~']; // printable characters
        yield ['()', '\⠀尩'];  //  (,) are not escaped
        yield ['xx xx', 'xx xx'];  // space print normally
        yield ['xx' . chr(10) . 'xx', 'xx' . chr(10) . 'xx'];  // non-printable characters
    }
}
