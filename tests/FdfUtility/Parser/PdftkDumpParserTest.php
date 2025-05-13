<?php

declare(strict_types=1);

namespace Wesnick\Tests\FdfUtility\Parser;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Wesnick\FdfUtility\Fields\PdfField;
use Wesnick\FdfUtility\Parser\PdftkDumpParser;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 */
#[
    CoversClass(PdftkDumpParser::class)
]
final class PdftkDumpParserTest extends TestCase
{
    /**
     * @var array<PdfField>
     */
    private array $fields;

    public function setUp(): void
    {
        $this->fields = (new PdftkDumpParser(__DIR__ . '/../../fixtures/pdftk_field_output.txt'))->parse();
    }

    public function testParseGetsAllField(): void
    {
        self::assertCount(28, $this->fields);
    }

    #[
        DataProvider('namesProvider')
    ]
    public function testParseGetsFieldName(int $index, string $name): void
    {
        self::assertSame($name, $this->fields[$index]->name);
    }

    #[
        DataProvider('valueProvider')
    ]
    public function testParseGetsValue(int $index, string $value): void
    {
        self::assertSame(iconv('UTF-8', 'UTF-16BE', $value), $this->fields[$index]->getEscapedValue());
    }

    #[
        DataProvider('defaultValueProvider')
    ]
    public function testParseGetsDefaultValue(int $index, string $default): void
    {
        self::assertSame($default, $this->fields[$index]->defaultValue);
    }

    /**
     * @param array<string> $options
     */
    #[
        DataProvider('optionsProvider')
    ]
    public function testParseGetsOptions(int $index, array $options): void
    {
        $opts = $this->fields[$index]->options;
        self::assertCount(count($options), $opts, sprintf('Count is off for index %s', $index));
        foreach ($options as $opt) {
            self::assertArrayHasKey($opt, $opts);
        }
    }

    #[
        DataProvider('flagProvider')
    ]
    public function testParseGetsFlags(int $index, int $flag): void
    {
        self::assertInstanceOf(PdfField::class, $this->fields[$index]);
        self::assertSame($flag, $this->fields[$index]->flag);
    }

    public static function namesProvider(): \Generator
    {
        yield [0, 'text_not_required'];
        yield [1, 'combo_box'];
        yield [2, 'text_required'];
        yield [3, 'check_box'];
        yield [4, 'radio_button'];
        yield [5, 'list_box'];
        yield [6, 'button'];
        yield [7, 'text_area'];
        yield [8, 'read_only'];
        yield [9, 'password'];
        yield [10, 'file'];
        yield [11, 'locked_field'];
        yield [12, 'default_value'];
        yield [13, 'multi_line'];
        yield [14, 'scroll_text'];
        yield [15, 'max_100'];
        yield [16, 'rich_text'];
        yield [17, 'check_spellings'];
        yield [18, 'comb_20'];
        yield [19, 'edit_combo'];
        yield [20, 'sort_combo'];
        yield [21, 'commit_combo'];
        yield [22, 'sort_list'];
        yield [23, 'multi_select_list'];
        yield [24, 'default_checked'];
        yield [25, 'default_radio'];
        yield [26, 'default_list'];
        yield [27, 'default_combo'];
    }

    public static function valueProvider(): \Generator
    {
        // (read_only)
        yield [8, '⣾＀爀攀愀搀开漀渀氀礩'];
        // (default_value)
        yield [12, '⣾＀搀攀昀愀甀氀琀开瘀愀氀甀攩'];
        // (Yes)
        yield [24, '⣾＀夀攀猩'];
        // (Yes)
        yield [25, '⣾＀夀攀猩'];
        // (Tom)
        yield [26, '⣾＀吀漀洩'];
        // (Marco)
        yield [27, '⣾＀䴀愀爀挀漩'];
    }

    public static function defaultValueProvider(): \Generator
    {
        yield [8, 'read_only'];
        yield [12, 'default_value'];
        yield [26, 'Tom'];
        yield [27, 'Marco'];
    }

    public static function optionsProvider(): \Generator
    {
        yield [1, ['Jason', 'Tom']];
        yield [3, ['Off', 'Yes']];
        yield [4, ['No', 'Off', 'Yes']];
        yield [5, ['dave', 'sam']];
        yield [19, ['Marco', 'Tom']];
        yield [20, ['John', 'Matt']];
        yield [21, ['Steve', 'Jim']];
        yield [22, ['Jason', 'Matt']];
        yield [23, ['John', 'Tom']];
        yield [24, ['Off', 'Yes']];
        yield [25, ['Both', 'No', 'Off', 'Yes']];
        yield [26, ['Matt', 'Tom']];
        yield [27, ['Marco', 'Tom']];
    }

    public static function flagProvider(): \Generator
    {
        yield [0, 0];
        yield [1, 131072];
        yield [2, 2];
        yield [3, 0];
        yield [4, 49152];
        yield [5, 0];
        yield [6, 65536];
        yield [7, 4096];
        yield [8, 1];
        yield [9, 12591106];
        yield [10, 5242880];
        yield [11, 12582912];
        yield [12, 12582912];
        yield [13, 12587008];
        yield [14, 4194304];
        yield [15, 12582912];
        yield [16, 46137344];
        yield [17, 8388608];
        yield [18, 29360128];
        yield [19, 4587520];
        yield [20, 4849664];
        yield [21, 71434242];
        yield [22, 524288];
        yield [23, 2097152];
        yield [24, 0];
        yield [25, 33603584];
        yield [26, 0];
        yield [27, 4587520];
    }
}
