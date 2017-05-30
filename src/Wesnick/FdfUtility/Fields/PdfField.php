<?php

namespace Wesnick\FdfUtility\Fields;

/**
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 */
abstract class PdfField
{
    // Bitmask      // Binary Position (1-based index)
    const READ_ONLY         = 1;            // 1
    const REQUIRED          = 2;            // 2
    const NO_EXPORT         = 4;            // 3
    const MULTI_LINE        = 4096;         // 13
    const PASSWORD          = 8192;         // 14
    const NO_TOGGLE_OFF     = 16384;        // 15
    const RADIO_BUTTON      = 32768;        // 16
    const PUSH_BUTTON       = 65536;        // 17
    const COMBO_BOX         = 131072;       // 18
    const EDITABLE_LIST     = 262144;       // 19
    const SORTED_LIST       = 524288;       // 20
    const FILE_INPUT        = 1048576;      // 21
    const MULTI_SELECT      = 2097152;      // 22
    const NO_SPELL_CHECK    = 4194304;      // 23
    const NO_SCROLL         = 8388608;      // 24
    const COMB_FORMATTING   = 16777216;     // 25
    const RICH_TEXT         = 33554432;     // 26
    const IN_UNISON         = 33554432;     // 26
    const COMMIT_ON_CHANGE  = 67108864;     // 27

    /**
     * Human-readable description of bit-masks.
     *
     * @var array
     */
    public static $flags = [
        self::READ_ONLY         => 'Read Only',
        self::REQUIRED          => 'Required',
        self::NO_EXPORT         => 'No Export',
        self::MULTI_LINE        => 'Multi-line',
        self::PASSWORD          => 'Password',
        self::NO_TOGGLE_OFF     => 'No Toggle Off',
        self::RADIO_BUTTON      => 'Radio Button',
        self::PUSH_BUTTON       => 'Push Button',
        self::COMBO_BOX         => 'Combo Box',
        self::EDITABLE_LIST     => 'Editable List',
        self::SORTED_LIST       => 'Sorted List',
        self::FILE_INPUT        => 'File Input',
        self::MULTI_SELECT      => 'Multi-select',
        self::NO_SPELL_CHECK    => 'No Spell Check',
        self::NO_SCROLL         => 'No Scroll',
        self::COMB_FORMATTING   => 'Comb Formatting',
        self::RICH_TEXT         => 'Rich Text',
        self::IN_UNISON         => 'In Unison',
        self::COMMIT_ON_CHANGE  => 'Commit on Change',
    ];

    /** @var int */
    protected $id;
    protected $document;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string
     */
    protected $justification = 'Left';

    /**
     * @var int
     */
    protected $flag;

    /**
     * @var string
     */
    protected $question;

    /**
     * @var string
     */
    protected $defaultValue = null;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param string      $name
     * @param int         $flag
     * @param string|null $defaultValue
     * @param array       $options
     * @param string|null $value
     */
    public function __construct($name, $flag, $defaultValue = null, $options = [], $value = null)
    {
        $this->name         = $name;
        $this->flag         = $flag;
        $this->defaultValue = $defaultValue;
        $this->options      = $options;
        $this->value        = $value;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function addOption($key, $value)
    {
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $justification
     *
     * @return $this
     */
    public function setJustification($justification)
    {
        $this->justification = $justification;

        return $this;
    }

    /**
     * @return string
     */
    public function getJustification()
    {
        return $this->justification;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * @param int $flag
     *
     * @return $this
     */
    public function setFlag($flag)
    {
        $this->flag = (int) $flag;

        return $this;
    }

    /**
     * @param string $defaultValue
     *
     * @return $this
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @TODO: figure out how this flag works
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->checkBitValue(self::READ_ONLY);
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->checkBitValue(self::REQUIRED);
    }

    /**
     * @return bool
     */
    public function isNoExport()
    {
        return $this->checkBitValue(self::NO_EXPORT);
    }

    /**
     * @param string $question
     *
     * @return PdfField
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /*
     * Button Field Convenience Tests
     */
    public function isPushButton()
    {
        return false;
    }

    public function isRadioButton()
    {
        return false;
    }

    public function isCheckBox()
    {
        return false;
    }

    public function isNoToggleOff()
    {
        return false;
    }

    /**
     * Text Field Convenience Tests.
     */
    public function isMultiLine()
    {
        return false;
    }

    public function isPassword()
    {
        return false;
    }

    public function isFileInput()
    {
        return false;
    }

    public function isNoScroll()
    {
        return false;
    }

    public function isRichText()
    {
        return false;
    }

    public function isCombFormatting()
    {
        return false;
    }

    /*
     * Choice Fields Convenience Methods
     */
    public function isMultiSelect()
    {
        return false;
    }

    public function isComboBox()
    {
        return false;
    }

    public function isListBox()
    {
        return false;
    }

    public function isEditableList()
    {
        return false;
    }

    public function isSortedList()
    {
        return false;
    }

    public function isCommitOnChange()
    {
        return false;
    }

    /*
     * Both Text and Button Fields
     */
    public function isInUnison()
    {
        return false;
    }

    /*
     * Both Text and Choice Fields
     */

    public function isNoSpellCheck()
    {
        return false;
    }

    /**
     * @param int $position
     *
     * @return bool
     */
    public function checkBitValue($position)
    {
        return (bool) ($this->flag & $position);
    }

    /**
     * @return string
     */
    abstract public function getEscapedValue();

    /**
     * @return string
     */
    abstract public function getExampleValue();

    /**
     * @return string
     */
    abstract public function getType();
}
