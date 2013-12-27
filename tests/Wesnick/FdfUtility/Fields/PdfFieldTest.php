<?php
/**
 * @file PdfFieldTest.php
 */

namespace Wesnick\FdfUtility\Fields;


use Wesnick\FdfUtility\Parser\PdftkDumpParser;

class PdfFieldTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var PdfField
     */
    private $stub;

    /**
     * @var PdfField[]
     */
    protected $fields;
    
    protected static $conveninceMethods = array(
        PdfField::READ_ONLY         => 'isReadOnly',
        PdfField::REQUIRED          => 'isRequired',
        PdfField::NO_EXPORT         => 'isNoExport',
        PdfField::MULTI_LINE        => 'isMultiLine',
        PdfField::PASSWORD          => 'isPassword',
        PdfField::NO_TOGGLE_OFF     => 'isNoToggleOff',
        PdfField::RADIO_BUTTON      => 'isRadioButton',
        PdfField::PUSH_BUTTON       => 'isPushButton',
        PdfField::COMBO_BOX         => 'isComboBox',
        PdfField::EDITABLE_LIST     => 'isEditableList',
        PdfField::SORTED_LIST       => 'isSortedList',
        PdfField::FILE_INPUT        => 'isFileInput',
        PdfField::MULTI_SELECT      => 'isMultiSelect',
        PdfField::NO_SPELL_CHECK    => 'isNoSpellCheck',
        PdfField::NO_SCROLL         => 'isNoScroll',
        PdfField::COMB_FORMATTING   => 'isCombFormatting',
        PdfField::RICH_TEXT         => 'isRichText',
        PdfField::IN_UNISON         => 'isInUnison',
        PdfField::COMMIT_ON_CHANGE  => 'isCommitonChange',
    );

    public function setUp()
    {
        $this->stub = $this->getMockForAbstractClass(__NAMESPACE__ . '\\PdfField');

        $fixture = __DIR__ . '/../../../fixtures/pdftk_field_output.txt';
        $parser = new PdftkDumpParser($fixture);
        $this->fields = $parser->parse();
    }

    public function tearDown()
    {
        $this->stub = null;
        $this->fields = null;
    }

    public function testMethodChaining()
    {
        $this->stub
            ->setDefaultValue("default_value")
            ->setFlag(0)
            ->setValue("value")
            ->setDescription("description")
            ->setJustification("Left")
        ;

        $this->assertEquals("default_value", $this->stub->getDefaultValue());
        $this->assertEquals("Left", $this->stub->getJustification());
        $this->assertEquals("description", $this->stub->getDescription());


    }



}
