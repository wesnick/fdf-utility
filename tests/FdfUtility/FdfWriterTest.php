<?php

declare(strict_types=1);

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
        self::assertSame(iconv('UTF-8', 'UTF-16BE', $escaped), FdfWriter::escapePdfString($input));
    }

    #[
        DataProvider('pdfNamesProvider')
    ]
    public function testEscapePdfNames(string $input, string $escaped): void
    {
        self::assertSame(iconv('UTF-8', 'UTF-16BE', $escaped), FdfWriter::escapePdfString($input));
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

        self::assertSame($expected, $refProperty->getValue($writer));
    }

    public static function pdfStringsProvider(): \Generator
    {
        // printable characters
        yield ['abcdef~', 'abcdef~'];
        // printable characters
        yield ['John Smith', 'John Smith'];
        // printable characters
        yield ['806 – 4815 Eldoràdo Mews', '806 – 4815 Eldoràdo Mews'];
        // printable characters
        yield ['çÇÀàÈèùÉéâÂÊêÎîÔôÛûëïöüÿæ', 'çÇÀàÈèùÉéâÂÊêÎîÔôÛûëïöüÿæ'];
        // printable characters
        yield ['4045 €', '4045 €'];
        // escaped characters (,)
        yield ['()', '\⠀尩'];
        // space print normally
        yield ['xx xx', 'xx xx'];
        // non-printable characters
        yield ['xx' . chr(10) . 'xx', 'xx' . chr(10) . 'xx'];

        // TODO test for backslash being scaped
        //  escaped characters \, \
        //  ['Thank you, broker Alison 855-000-0000\\', 'Thank you, broker Alison 855-000-0000\\\\'],
    }

    public static function pdfNamesProvider(): \Generator
    {
        // printable characters
        yield ['abcdef~', 'abcdef~'];
        //  (,) are not escaped
        yield ['()', '\⠀尩'];
        // space print normally
        yield ['xx xx', 'xx xx'];
        // non-printable characters
        yield ['xx' . chr(10) . 'xx', 'xx' . chr(10) . 'xx'];
    }
}
