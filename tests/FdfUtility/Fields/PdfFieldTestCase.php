<?php declare(strict_types=1);

namespace Wesnick\Tests\FdfUtility\Fields;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Wesnick\FdfUtility\Fields\PdfField;
use Wesnick\FdfUtility\Parser\PdftkDumpParser;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 */
#[
    CoversClass(PdfField::class)
]
abstract class PdfFieldTestCase extends TestCase
{
    /**
     * @var array<PdfField>
     */
    protected ?array $fields;

    protected static array $convenienceMethods = [
        PdfField::READ_ONLY        => 'isReadOnly',
        PdfField::REQUIRED         => 'isRequired',
        PdfField::NO_EXPORT        => 'isNoExport',
        PdfField::MULTI_LINE       => 'isMultiLine',
        PdfField::PASSWORD         => 'isPassword',
        PdfField::NO_TOGGLE_OFF    => 'isNoToggleOff',
        PdfField::RADIO_BUTTON     => 'isRadioButton',
        PdfField::PUSH_BUTTON      => 'isPushButton',
        PdfField::COMBO_BOX        => 'isComboBox',
        PdfField::EDITABLE_LIST    => 'isEditableList',
        PdfField::SORTED_LIST      => 'isSortedList',
        PdfField::FILE_INPUT       => 'isFileInput',
        PdfField::MULTI_SELECT     => 'isMultiSelect',
        PdfField::NO_SPELL_CHECK   => 'isNoSpellCheck',
        PdfField::NO_SCROLL        => 'isNoScroll',
        PdfField::COMB_FORMATTING  => 'isCombFormatting',
        PdfField::RICH_TEXT        => 'isRichText',
        PdfField::IN_UNISON        => 'isInUnison',
        PdfField::COMMIT_ON_CHANGE => 'isCommitOnChange',
    ];

    public function setUp(): void
    {
        $fixture      = __DIR__ . '/../../fixtures/pdftk_field_output.txt';
        $parser       = new PdftkDumpParser($fixture);
        $this->fields = $parser->parse();
    }
}
