<?php

declare(strict_types=1);

namespace Wesnick\Tests\FdfUtility\Fields;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Wesnick\FdfUtility\Fields\ButtonField;
use Wesnick\FdfUtility\Fields\PdfField;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 */
#[
    CoversClass(ButtonField::class)
]
final class ButtonFieldTest extends PdfFieldTestCase
{
    public function testConstructor(): void
    {
        $field = new ButtonField('name', 0, 'default', ['foo' => 'bar'], 'description', 'justification');

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
        DataProvider('buttonFieldFlagsProvider')
    ]
    public function testButtonFlags(int $index, array $flags, bool $expected): void
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
        DataProvider('buttonFieldFlagsProvider')
    ]
    public function testButtonConvenienceMethods(int $index, array $flags): void
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

    public static function buttonFieldFlagsProvider(): \Generator
    {
        yield [3, [], false];
        yield [4, [PdfField::NO_TOGGLE_OFF, PdfField::RADIO_BUTTON], true];
        yield [6, [PdfField::PUSH_BUTTON], true];
        yield [24, [], false];
        yield [
            25,
            [PdfField::NO_TOGGLE_OFF, PdfField::RADIO_BUTTON, PdfField::IN_UNISON],
            true,
        ];
    }
}
