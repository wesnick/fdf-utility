<?php declare(strict_types=1);

namespace Wesnick\Tests\FdfUtility\Fields;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Wesnick\FdfUtility\Fields\PdfField;
use Wesnick\FdfUtility\Fields\TextField;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 */
#[
    CoversClass(TextField::class)
]
final class TextFieldTest extends PdfFieldTestCase
{
    #[
        DataProvider('textFieldFlagsProvider')
    ]
    public function testTextFlags(int $index, array $flags, bool $expected): void
    {
        $field   = $this->fields[$index];
        $flagSum = 0;
        foreach ($flags as $flag) {
            $flagSum |= $flag;
            self::assertTrue($field->checkBitValue($flag));
        }

        $out = $field->checkBitValue($flagSum);
        $sum = [];
        foreach (PdfField::$flags as $f => $d) {
            if ($field->checkBitValue($f)) {
                $sum[$f] = $d;
            }
        }

        self::assertCount(count($flags), $sum);
        self::assertSame($expected, $out);
    }

    #[
        DataProvider('textFieldFlagsProvider')
    ]
    public function testTextConvenienceMethods(int $index, array $flags): void
    {
        $field   = $this->fields[$index];
        $methods = PdfFieldTestCase::$convenienceMethods;
        foreach ($methods as $flag => $method) {
            if (in_array($flag, $flags, true)) {
                self::assertTrue(
                    $field->$method(),
                    sprintf('Field Name %s, Index %d, %s is True', $field->name, $index, PdfField::$flags[$flag])
                );
            } else {
                self::assertFalse(
                    $field->$method(),
                    sprintf('Field Name %s, Index %d, %s is False', $field->name, $index, PdfField::$flags[$flag])
                );
            }
        }
    }

    public function testDefaultValueIsRespected(): void
    {
        $field = new TextField('default_value', 0, 'default', [], null);

        self::assertSame(
            iconv('UTF-8', 'UTF-16BE', '⣾＀搀攀昀愀甀氀琩'),
            $field->getEscapedValue(),
            'Default Value is respected on null value'
        );

        $field->value = 'value';
        self::assertSame(
            iconv('UTF-8', 'UTF-16BE', '⣾＀瘀愀氀甀攩'),
            $field->getEscapedValue(),
            'Default Value is ignored if value not null'
        );

        $field->value = '';
        self::assertSame(
            iconv('UTF-8', 'UTF-16BE', '⣾Ｉ'),
            $field->getEscapedValue(),
            'Default Value is ignored if value is empty'
        );
    }

    public static function textFieldFlagsProvider(): \Generator
    {
        yield [0, [], false];
        yield [2, [PdfField::REQUIRED], true];
        yield [7, [PdfField::MULTI_LINE], true];
        yield [8, [PdfField::READ_ONLY], true];
        yield [9, [PdfField::PASSWORD, PdfField::REQUIRED, PdfField::NO_SPELL_CHECK, PdfField::NO_SCROLL], true];
        yield [10, [PdfField::FILE_INPUT, PdfField::NO_SPELL_CHECK], true];
        yield [11, [PdfField::NO_SPELL_CHECK, PdfField::NO_SCROLL], true];
        yield [12, [PdfField::NO_SPELL_CHECK, PdfField::NO_SCROLL], true];
        yield [13, [PdfField::MULTI_LINE, PdfField::NO_SPELL_CHECK, PdfField::NO_SCROLL], true];
        yield [14, [PdfField::NO_SPELL_CHECK], true];
        yield [15, [PdfField::NO_SPELL_CHECK, PdfField::NO_SCROLL], true];
        yield [16, [PdfField::NO_SPELL_CHECK, PdfField::NO_SCROLL, PdfField::IN_UNISON], true];
        yield [17, [PdfField::NO_SCROLL], true];
        yield [18, [PdfField::NO_SPELL_CHECK, PdfField::NO_SCROLL, PdfField::COMB_FORMATTING], true];
    }
}
