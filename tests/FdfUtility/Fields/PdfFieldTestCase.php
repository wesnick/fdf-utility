<?php

declare(strict_types=1);

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
    protected array $fields;

    /**
     * @var array<int, string>
     */
    protected static array $convenienceMethods = [
        PdfField::COMBO_BOX        => 'isComboBox',
        PdfField::COMB_FORMATTING  => 'isCombFormatting',
        PdfField::COMMIT_ON_CHANGE => 'isCommitOnChange',
        PdfField::EDITABLE_LIST    => 'isEditableList',
        PdfField::FILE_INPUT       => 'isFileInput',
        PdfField::IN_UNISON        => 'isInUnison',
        PdfField::MULTI_LINE       => 'isMultiLine',
        PdfField::MULTI_SELECT     => 'isMultiSelect',
        PdfField::NO_EXPORT        => 'isNoExport',
        PdfField::NO_SCROLL        => 'isNoScroll',
        PdfField::NO_SPELL_CHECK   => 'isNoSpellCheck',
        PdfField::NO_TOGGLE_OFF    => 'isNoToggleOff',
        PdfField::PASSWORD         => 'isPassword',
        PdfField::PUSH_BUTTON      => 'isPushButton',
        PdfField::RADIO_BUTTON     => 'isRadioButton',
        PdfField::READ_ONLY        => 'isReadOnly',
        PdfField::REQUIRED         => 'isRequired',
        //        PdfField::RICH_TEXT        => 'isRichText',
        PdfField::SORTED_LIST      => 'isSortedList',
    ];

    /**
     * Human-readable description of bit-masks.
     *
     * @var array<int, string>
     */
    protected static array $flags = [
        PdfField::COMBO_BOX        => 'Combo Box',
        PdfField::COMB_FORMATTING  => 'Comb Formatting',
        PdfField::COMMIT_ON_CHANGE => 'Commit on Change',
        PdfField::EDITABLE_LIST    => 'Editable List',
        PdfField::FILE_INPUT       => 'File Input',
        PdfField::MULTI_LINE       => 'Multi-line',
        PdfField::MULTI_SELECT     => 'Multi-select',
        PdfField::NO_EXPORT        => 'No Export',
        PdfField::NO_SCROLL        => 'No Scroll',
        PdfField::NO_SPELL_CHECK   => 'No Spell Check',
        PdfField::NO_TOGGLE_OFF    => 'No Toggle Off',
        PdfField::PASSWORD         => 'Password',
        PdfField::PUSH_BUTTON      => 'Push Button',
        PdfField::RADIO_BUTTON     => 'Radio Button',
        PdfField::READ_ONLY        => 'Read Only',
        PdfField::REQUIRED         => 'Required',
        PdfField::RICH_TEXT        => 'Rich Text',
        PdfField::SORTED_LIST      => 'Sorted List',
    ];

    public function setUp(): void
    {
        $fixture      = __DIR__ . '/../../fixtures/pdftk_field_output.txt';
        $parser       = new PdftkDumpParser($fixture);
        $this->fields = $parser->parse();
    }
}
