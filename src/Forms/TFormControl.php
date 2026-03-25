<?php

/**
* Třídy pro generování prvků do formuláře
*
* @name TFormControl
* @version 1.2
* @author vladimir.horky
* @copyright Vladimír Horký, 2018
*
* version 1.2
* Modify TMultiComboBox::validate()
* Added TComboBox getOptions()
*
* version 1.1
* Modify TComboBox::validate()
*
* version 1.0
* new
*
*/

class TFormControl extends TElement implements IFormControl
{
    protected array $group = [];
    protected array $label = [];
    protected array $tag = [];
    protected string $div = '';

    protected mixed $value;

    protected bool $disabled = false;
    protected bool $visible = true;

    public array $errors = [];

    protected TRules $rules;

    public function __construct($nodeName, $attrs)
    {
        parent::__construct($nodeName, $attrs);
        $this->rules = new TRules($this);
        $this->value = null;
    }

    public function setName(string $value) :TFormControl
    {
        $this->attributes->add('name', $value);
        return $this;
    }

    public function getName()
    {
        return $this->attributes->items['name'];
    }

    public function onClick(string $js) :TFormControl
    {
        $this->attributes->add('onclick', $js);
        return $this;
    }

    public function onBlur(string $js) :TFormControl
    {
        $this->attributes->add('onblur', $js);
        return $this;
    }

    public function onFocus(string $js) :TFormControl
    {
        $this->attributes->add('onfocus', $js);
        return $this;
    }

    public function onChange(string $js) :TFormControl
    {
        $this->attributes->add('onchange', $js);
        return $this;
    }

    public function onKeyPress(string $js) :TFormControl
    {
        $this->attributes->add('onkeypress', $js);
        return $this;
    }

    public function onKeyUp(string $js) :TFormControl
    {
        $this->attributes->add('onkeyup', $js);
        return $this;
    }

    public function onKeyDown(string$js) :TFormControl
    {
        $this->attributes->add('onkeydown', $js);
        return $this;
    }

    public function addError($messages) :TFormControl
    {
        if(is_array($messages))
        {
            foreach($messages as $message)
                if (!in_array($message, $this->errors, true))
                    $this->errors[] = $message;
        } else
            if (!in_array($messages, $this->errors, true))
                $this->errors[] = $messages;

        return $this;
    }

    /**
    * Adds a validation rule.
    * @param  mixed $operation rule type
    * @param string|null $message message to display for invalid data
    * @param mixed|null $arg optional rule arguments
    * @return TFormControl  provides a fluent interface
    */
    public function addRule(string $operation, ?string $message = null, mixed $arg = null) :TFormControl
    {
        $this->rules->addRule($operation, $message, $arg);
        return $this;
    }

    /**
    * Adds a validation condition a returns new branch.
    * @param  mixed $operation condition type
    * @param  mixed $value optional condition arguments
    * @return TRules      new branch
    */
    final public function addCondition(string $operation, mixed $value = null): TRules
    {
        return $this->rules->addCondition($operation, $value);
    }

    final public function getCondition(string $operation): mixed
    {
        return $this->rules->getCondition($operation);
    }

    final public function getRules() :TRules
    {
        return $this->rules;
    }

    /**
    * Valid validator: is control valid?
    * @param  IFormControl
    * @return bool
    */
    public function validate() :bool
    {
        $result = $this->rules->validate();
        $this->errors = $this->rules->getMessages();
        return $result;
    }

    public function getErrors() :array
    {
        return $this->errors;
    }

    public function hasErrors() :bool
    {
        return (bool) $this->errors;
    }

    public function cleanErrors() :TFormControl
    {
        $this->errors = [];
        return $this;
    }

    /**
    * Sets control's value.
    * @param  mixed
    * @return TFormControl  provides a fluent interface
    */
    public function setValue($text): TFormControl
    {
        $this->attributes->add('value', $text);
        return $this;
    }

    /**
    * Returns control's value.
    * @return mixed
    */
    public function getValue()
    {
        return trim($this->attributes->items['value']);
    }

    /**
    * Disables or enables control.
    * @param  bool
    * @return TFormControl  provides a fluent interface
    */
    public function setDisabled($value = true) :TFormControl
    {
        if($value)
            $this->attributes->add('disabled','disabled');
        else
            $this->attributes->delete('disabled');

        return $this;
    }

    /**
    * Is control disabled?
    * @return bool
    */
    public function isDisabled() :bool
    {
        return ($this->attributes->items['disabled'] == 'disabled');
    }

    final public function setRequired($message = null) :TFormControl
    {
        if($message == null)
            $message = __('err.must_filled','Musí být vyplněno');

        $this->rules->addRule(TRule::FILLED, $message);
        return $this;
    }

    /**
    * Is control mandatory?
    * @return bool
    */
    final public function isRequired() :bool
    {
        $rule = $this->rules->getRule(TRule::FILLED);
        return ($rule != '') && ($rule instanceof TRule);
    }

    public function setTitle($title) :TFormControl
    {
        $this->attributes->add('title', $title);
        return $this;
    }

    public function setTabIndex($index) :TFormControl
    {
        $this->attributes->add('tabindex', $index);
        return $this;
    }

    public function setFocus() :TFormControl
    {
        $this->attributes->add('autofocus', 'true');
        return $this;
    }

    public function setLabel($label) :TFormControl
    {
        $this->label['value'] = $label;
        return $this;
    }

    public function getLabel()
    {
        return $this->label['value'];
    }

    public function setLabelId($id) :TFormControl
    {
        $this->label['id'] = $id;
        return $this;
    }

    public function getLabelId()
    {
        return $this->label['id'];
    }

    public function setLabelStyle($style) :TFormControl
    {
        $this->label['style'] = $style;
        return $this;
    }

    public function getLabelStyle()
    {
        return $this->label['style'];
    }

    public function setLabelClass($class) :TFormControl
    {
        $this->label['class'] = $class;
        return $this;
    }

    public function getLabelClass()
    {
        return $this->label['class'];
    }

    public function addLabelClass($class) :TFormControl
    {
        if($this->label['class'] != '')
            $this->label['class'] .= ' '.$class;
        else
            $this->setClass($class);

        return $this;
    }

    public function setLabelWidth($width) :TFormControl
    {
        $this->label['width'] = $width;
        return $this;
    }

    public function getLabelWidth()
    {
        return $this->label['width'];
    }

    public function setLegend($legend) :TFormControl
    {
        $this->label['legend'] = $legend;
        return $this;
    }

    public function getLegend()
    {
        return $this->label['legend'];
    }

    public function setPostfix($title) :TFormControl
    {
        $this->label['postfix'] = $title;
        return $this;
    }

    public function getPostfix()
    {
        return $this->label['postfix'];
    }

    public function setPrefix($title) :TFormControl
    {
        $this->label['prefix'] = $title;
        return $this;
    }

    public function getPrefix()
    {
        return $this->label['prefix'];
    }

    public function setGroup($group) :TFormControl
    {
        $this->group['group'] = $group;
        return $this;
    }

    public function getGroup()
    {
        return $this->group['group'];
    }

    public function setColumnWidth($width) :TFormControl
    {
        $this->group['width'] = $width;
        return $this;
    }

    public function getColumnWidth()
    {
        return $this->group['width'];
    }

    public function setColumnClass($class) :TFormControl
    {
        $this->group['class'] = $class;
        return $this;
    }

    public function getColumnClass()
    {
        return $this->group['class'];
    }

    public function setColumn($cols, $width = '') :TFormControl
    {
        $this->group['column'] = $cols;
        if($width != '')
            $this->setColumnWidth($width);

        return $this;
    }

    public function getColumn()
    {
        return $this->group['column'];
    }

    public function setTag($name, $value) :TFormControl
    {
        $this->tag[$name] = $value;
        return $this;
    }

    public function getTag($name)
    {
        return $this->tag[$name];
    }

    public function hasTag($name) :bool
    {
        return (!empty($this->tag[$name]));
    }

    public function setVisible($state) :TFormControl
    {
        $this->visible = $state;
        return $this;
    }

    public function isVisible() :bool
    {
        return $this->visible;
    }

    public function addDiv($id) :TFormControl
    {
        $this->div = $id;
        return $this;
    }

    public function getDiv()
    {
        return $this->div;
    }

    public function html() :string
    {
        if(!empty($this->div))
            return '<div id="'.$this->div.'">'.parent::html().'</div>';
        else
            return parent::html();
    }
}
