<?php

namespace Wesnick\Tests\FdfUtility\Parser;

use Wesnick\FdfUtility\FdfWriter;
use Wesnick\FdfUtility\Fields\PdfField;
use Wesnick\FdfUtility\Parser\PdftkDumpParser;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 * @covers \Wesnick\FdfUtility\Parser\PdftkDumpParser
 */
class PdftkDumpParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PdftkDumpParser
     */
    private $object;

    /**
     * @var PdfField[]
     */
    private $fields;

    public function setUp()
    {
        $fixture      = __DIR__.'/../../fixtures/pdftk_field_output.txt';
        $this->object = new PdftkDumpParser($fixture);
        $this->fields = $this->object->parse();
    }

    public function tearDown()
    {
        $this->object = null;
    }

    public function testParseGetsAllField()
    {
        $this->assertCount(28, $this->fields);
    }

    public function namesProvider()
    {
        return [
            [0, 'text_not_required'],
            [1, 'combo_box'],
            [2, 'text_required'],
            [3, 'check_box'],
            [4, 'radio_button'],
            [5, 'list_box'],
            [6, 'button'],
            [7, 'text_area'],
            [8, 'read_only'],
            [9, 'password'],
            [10, 'file'],
            [11, 'locked_field'],
            [12, 'default_value'],
            [13, 'multi_line'],
            [14, 'scroll_text'],
            [15, 'max_100'],
            [16, 'rich_text'],
            [17, 'check_spellings'],
            [18, 'comb_20'],
            [19, 'edit_combo'],
            [20, 'sort_combo'],
            [21, 'commit_combo'],
            [22, 'sort_list'],
            [23, 'multi_select_list'],
            [24, 'default_checked'],
            [25, 'default_radio'],
            [26, 'default_list'],
            [27, 'default_combo'],
        ];
    }

    /**
     * @dataProvider namesProvider
     *
     * @param int    $index
     * @param string $name
     */
    public function testParseGetsFieldName($index, $name)
    {
        $this->assertSame($name, $this->fields[$index]->getName());
    }

    public function valueProvider()
    {
        return [
            [8, '⣾＀爀攀愀搀开漀渀氀礩'], // (read_only)
            [12, '⣾＀搀攀昀愀甀氀琀开瘀愀氀甀攩'], // (default_value)
            [24, '⠀夀攀猩'], // (Yes)
            [25, '⠀夀攀猩'], // (Yes)
            [26, '⠀吀漀洩'], // (Tom)
            [27, '⠀䴀愀爀挀漩'], // (Marco)
        ];
    }

    /**
     * @dataProvider valueProvider
     *
     * @param int    $index
     * @param string $value
     */
    public function testParseGetsValue($index, $value)
    {
        $this->assertSame(iconv('UTF-8', 'UTF-16BE', $value), $this->fields[$index]->getEscapedValue());
    }

    public function defaultValueProvider()
    {
        return [
            [8, 'read_only'],
            [12, 'default_value'],
            [26, 'Tom'],
            [27, 'Marco'],
        ];
    }

    /**
     * @dataProvider defaultValueProvider
     *
     * @param int    $index
     * @param string $default
     */
    public function testParseGetsDefaultValue($index, $default)
    {
        $this->assertSame($default, $this->fields[$index]->getDefaultValue());
    }

    public function optionsProvider()
    {
        return [
            [1, ['Jason', 'Tom']],
            [3, ['Off', 'Yes']],
            [4, ['No', 'Off', 'Yes']],
            [5, ['dave', 'sam']],
            [19, ['Marco', 'Tom']],
            [20, ['John', 'Matt']],
            [21, ['Steve', 'Jim']],
            [22, ['Jason', 'Matt']],
            [23, ['John', 'Tom']],
            [24, ['Off', 'Yes']],
            [25, ['Both', 'No', 'Off', 'Yes']],
            [26, ['Matt', 'Tom']],
            [27, ['Marco', 'Tom']],
        ];
    }

    /**
     * @dataProvider optionsProvider
     *
     * @param int   $index
     * @param array $options
     */
    public function testParseGetsOptions($index, array $options)
    {
        $opts = $this->fields[$index]->getOptions();
        $this->assertCount(count($options), $opts, sprintf('Count is off for index %s', $index));
        foreach ($options as $opt) {
            $this->assertArrayHasKey($opt, $opts);
        }
    }

    public function flagProvider()
    {
        return [
            [0, 0],
            [1, 131072],
            [2, 2],
            [3, 0],
            [4, 49152],
            [5, 0],
            [6, 65536],
            [7, 4096],
            [8, 1],
            [9, 12591106],
            [10, 5242880],
            [11, 12582912],
            [12, 12582912],
            [13, 12587008],
            [14, 4194304],
            [15, 12582912],
            [16, 46137344],
            [17, 8388608],
            [18, 29360128],
            [19, 4587520],
            [20, 4849664],
            [21, 71434242],
            [22, 524288],
            [23, 2097152],
            [24, 0],
            [25, 33603584],
            [26, 0],
            [27, 4587520],
        ];
    }

    /**
     * @dataProvider flagProvider
     *
     * @param int $index
     * @param int $flag
     */
    public function testParseGetsFlags($index, $flag)
    {
        $this->assertAttributeSame($flag, 'flag', $this->fields[$index]);
    }
}
