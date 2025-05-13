<?php

declare(strict_types=1);

namespace Wesnick\FdfUtility\Fields;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 */
abstract class PdfField
{
    // Bitmask      // Binary Position (1-based index)
    // phpcs:disable SlevomatCodingStandard.Commenting.DisallowCommentAfterCode
    public const READ_ONLY        = 1;            // 1
    public const REQUIRED         = 2;            // 2
    public const NO_EXPORT        = 4;            // 3
    public const MULTI_LINE       = 4096;         // 13
    public const PASSWORD         = 8192;         // 14
    public const NO_TOGGLE_OFF    = 16384;        // 15
    public const RADIO_BUTTON     = 32768;        // 16
    public const PUSH_BUTTON      = 65536;        // 17
    public const COMBO_BOX        = 131072;       // 18
    public const EDITABLE_LIST    = 262144;       // 19
    public const SORTED_LIST      = 524288;       // 20
    public const FILE_INPUT       = 1048576;      // 21
    public const MULTI_SELECT     = 2097152;      // 22
    public const NO_SPELL_CHECK   = 4194304;      // 23
    public const NO_SCROLL        = 8388608;      // 24
    public const COMB_FORMATTING  = 16777216;     // 25
    public const RICH_TEXT        = 33554432;     // 26
    public const IN_UNISON        = 33554432;     // 26
    public const COMMIT_ON_CHANGE = 67108864;     // 27

    // phpcs:enable
    protected ?string $value = null;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        public readonly string  $name,
        public readonly int     $flag,
        public readonly ?string $defaultValue = null,
        public readonly array   $options = [],
        public readonly ?string $description = '',
        public readonly string  $justification = 'Left',
    ) {
    }

    abstract public function getEscapedValue(): string;

    abstract public function getExampleValue(): mixed;

    abstract public function getType(): string;

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @TODO: figure out how this flag works
     */
    public function isHidden(): bool
    {
        return false;
    }

    public function isReadOnly(): bool
    {
        return $this->checkBitValue(self::READ_ONLY);
    }

    public function isRequired(): bool
    {
        return $this->checkBitValue(self::REQUIRED);
    }

    public function isNoExport(): bool
    {
        return $this->checkBitValue(self::NO_EXPORT);
    }

    /*
     * Button Field Convenience Tests
     */
    public function isPushButton(): bool
    {
        return false;
    }

    public function isRadioButton(): bool
    {
        return false;
    }

    public function isCheckBox(): bool
    {
        return false;
    }

    public function isNoToggleOff(): bool
    {
        return false;
    }

    /**
     * Text Field Convenience Tests.
     */
    public function isMultiLine(): bool
    {
        return false;
    }

    public function isPassword(): bool
    {
        return false;
    }

    public function isFileInput(): bool
    {
        return false;
    }

    public function isNoScroll(): bool
    {
        return false;
    }

    public function isRichText(): bool
    {
        return false;
    }

    public function isCombFormatting(): bool
    {
        return false;
    }

    /*
     * Choice Fields Convenience Methods
     */
    public function isMultiSelect(): bool
    {
        return false;
    }

    public function isComboBox(): bool
    {
        return false;
    }

    public function isListBox(): bool
    {
        return false;
    }

    public function isEditableList(): bool
    {
        return false;
    }

    public function isSortedList(): bool
    {
        return false;
    }

    public function isCommitOnChange(): bool
    {
        return false;
    }

    /*
     * Both Text and Button Fields
     */
    public function isInUnison(): bool
    {
        return false;
    }

    /*
     * Both Text and Choice Fields
     */
    public function isNoSpellCheck(): bool
    {
        return false;
    }

    final public function checkBitValue(int $position): bool
    {
        return (bool) ($this->flag & $position);
    }
}
