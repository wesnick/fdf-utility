<?php
/**
 * @file PdftkDumpParserTest.php
 */

namespace Wesnick\FdfUtility\Parser;


use Wesnick\FdfUtility\Fields\PdfField;

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
        $fixture = __DIR__ . '/../../../fixtures/pdftk_field_output.txt';
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
        return array(
            array(0, 'text_not_required'),
            array(1, 'combo_box'),
            array(2, 'text_required'),
            array(3, 'check_box'),
            array(4, 'radio_button'),
            array(5, 'list_box'),
            array(6, 'button'),
            array(7, 'text_area'),
            array(8, 'read_only'),
            array(9, 'password'),
            array(10, 'file'),
            array(11, 'locked_field'),
            array(12, 'default_value'),
            array(13, 'multi_line'),
            array(14, 'scroll_text'),
            array(15, 'max_100'),
            array(16, 'rich_text'),
            array(17, 'check_spellings'),
            array(18, 'comb_20'),
            array(19, 'edit_combo'),
            array(20, 'sort_combo'),
            array(21, 'commit_combo'),
            array(22, 'sort_list'),
            array(23, 'multi_select_list'),
            array(24, 'default_checked'),
            array(25, 'default_radio'),
            array(26, 'default_list'),
            array(27, 'default_combo'),
        );

    }

    /**
     * @dataProvider namesProvider
     */
    public function testParseGetsFieldName($index, $name)
    {
        $this->assertEquals($name, $this->fields[$index]->getName());
    }

    public function valueProvider()
    {
        return array(
            array(8, '(read_only)'),
            array(12, '(default_value)'),
            array(24, '(Yes)'),
            array(25, '(Yes)'),
            array(26, '(Tom)'),
            array(27, '(Marco)'),
        );
    }

    /**
     * @dataProvider valueProvider
     */
    public function testParseGetsValue($index, $value)
    {
        $this->assertEquals($value, $this->fields[$index]->getEscapedValue());
    }

    public function defaultValueProvider()
    {
        return array(
            array(8, 'read_only'),
            array(12, 'default_value'),
            array(26, 'Tom'),
            array(27, 'Marco'),
        );
    }

    /**
     * @dataProvider defaultValueProvider
     */
    public function testParseGetsDefaultValue($index, $default)
    {
        $this->assertEquals($default, $this->fields[$index]->getDefaultValue());
    }

    public function optionsProvider()
    {
        return array(
            array(1, array('Jason', 'Tom')),
            array(3, array('Off', 'Yes')),
            array(4, array('No', 'Off', 'Yes')),
            array(5, array('dave', 'sam')),
            array(19, array('Marco', 'Tom')),
            array(20, array('John', 'Matt')),
            array(21, array('Steve', 'Jim')),
            array(22, array('Jason', 'Matt')),
            array(23, array('John', 'Tom')),
            array(24, array('Off', 'Yes')),
            array(25, array('Both', 'No', 'Off', 'Yes')),
            array(26, array('Matt', 'Tom')),
            array(27, array('Marco', 'Tom')),
        );

    }

    /**
     * @dataProvider optionsProvider
     */
    public function testParseGetsOptions($index, $options)
    {

        $opts = $this->fields[$index]->getOptions();
        $this->assertCount(count($options), $opts, sprintf("Count is off for index %s", $index));
        foreach ($options as $opt) {
            $this->assertArrayHasKey($opt, $opts);
        }
    }

    public function flagProvider()
    {
        return array(
            array(0, 0),
            array(1, 131072),
            array(2, 2),
            array(3, 0),
            array(4, 49152),
            array(5, 0),
            array(6, 65536),
            array(7, 4096),
            array(8, 1),
            array(9, 12591106),
            array(10, 5242880),
            array(11, 12582912),
            array(12, 12582912),
            array(13, 12587008),
            array(14, 4194304),
            array(15, 12582912),
            array(16, 46137344),
            array(17, 8388608),
            array(18, 29360128),
            array(19, 4587520),
            array(20, 4849664),
            array(21, 71434242),
            array(22, 524288),
            array(23, 2097152),
            array(24, 0),
            array(25, 33603584),
            array(26, 0),
            array(27, 4587520),
        );
    }

    /**
     * @dataProvider flagProvider
     */
    public function testParseGetsFlags($index, $flag)
    {
        $this->assertAttributeEquals($flag, 'flag', $this->fields[$index]);
    }

}
