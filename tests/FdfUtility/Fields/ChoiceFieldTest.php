<?php

declare(strict_types=1);

namespace Wesnick\Tests\FdfUtility\Fields;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Wesnick\FdfUtility\Fields\ChoiceField;
use Wesnick\FdfUtility\Fields\PdfField;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 */
#[
    CoversClass(ChoiceField::class)
]
final class ChoiceFieldTest extends PdfFieldTestCase
{
    public function testConstructor(): void
    {
        $field = new ChoiceField('name', 0, 'default', ['foo' => 'bar'], 'description', 'justification');

        self::assertSame('name', $field->name);
        self::assertSame(0, $field->flag);
        self::assertSame('default', $field->defaultValue);
        self::assertSame(['foo' => 'bar'], $field->options);
        self::assertSame('description', $field->description);
        self::assertSame('justification', $field->justification);
        self::assertNull($field->getValue());
    }

    /**
     * @param array<int> $flags
     */
    #[
        DataProvider('choiceFieldFlagsProvider')
    ]
    public function testChoiceFlags(int $index, array $flags, bool $expected): void
    {
        $field   = $this->fields[$index];
        $flagSum = 0;
        foreach ($flags as $flag) {
            $flagSum |= $flag;
            self::assertTrue($field->checkBitValue($flag));
        }

        $out = $field->checkBitValue($flagSum);
        self::assertSame($expected, $out);
    }

    /**
     * @param array<int> $flags
     */
    #[
        DataProvider('choiceFieldFlagsProvider')
    ]
    public function testChoiceConvenienceMethods(int $index, array $flags): void
    {
        $field   = $this->fields[$index];
        $methods = PdfFieldTestCase::$convenienceMethods;
        foreach ($methods as $flag => $method) {
            // @phpstan-ignore method.dynamicName
            $value = $field->$method();
            if (in_array($flag, $flags, true)) {
                self::assertTrue(
                    $value,
                    sprintf(
                        'Field Name %s, Index %d, %s is True',
                        $field->name,
                        $index,
                        self::$flags[$flag]
                    )
                );
            } else {
                self::assertFalse(
                    $value,
                    sprintf(
                        'Field Name %s, Index %d, %s is False',
                        $field->name,
                        $index,
                        self::$flags[$flag]
                    )
                );
            }
        }
    }

    public function testDefaultValueIsRespected(): void
    {
        $field = new ChoiceField(
            'default_value',
            0,
            'default',
            ['default' => 'default', 'default1' => 'default1'],
            null
        );

        self::assertSame(
            iconv('UTF-8', 'UTF-16BE', '⣾＀搀攀昀愀甀氀琩'),
            $field->getEscapedValue(),
            'Default Value is respected on null value'
        );

        $field->setValue('default1');
        self::assertSame(
            iconv('UTF-8', 'UTF-16BE', '⣾＀搀攀昀愀甀氀琀ㄩ'),
            $field->getEscapedValue(),
            'Default Value is ignored if value not null'
        );

        $field->setValue('');
        self::assertSame(
            iconv('UTF-8', 'UTF-16BE', '⣾Ｉ'),
            $field->getEscapedValue(),
            'Default Value is ignored if value is empty'
        );
    }

    public static function choiceFieldFlagsProvider(): \Generator
    {
        yield [1, [PdfField::COMBO_BOX], true];
        yield [5, [], false];
        yield [
            19,
            [PdfField::COMBO_BOX, PdfField::EDITABLE_LIST, PdfField::NO_SPELL_CHECK],
            true,
        ];
        yield [
            20,
            [PdfField::COMBO_BOX, PdfField::SORTED_LIST, PdfField::NO_SPELL_CHECK],
            true,
        ];
        yield [
            21,
            [
                PdfField::REQUIRED,
                PdfField::COMBO_BOX,
                PdfField::NO_SPELL_CHECK,
                PdfField::COMMIT_ON_CHANGE,
            ],
            true,
        ];
        yield [22, [PdfField::SORTED_LIST], true];
        yield [23, [PdfField::MULTI_SELECT], true];
        yield [26, [], false];
        yield [
            27,
            [PdfField::COMBO_BOX, PdfField::EDITABLE_LIST, PdfField::NO_SPELL_CHECK],
            true,
        ];
    }
}
