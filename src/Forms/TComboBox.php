<?php

class TComboBox extends TFormControl implements IFormControl
{

    public function __construct($name, $options = [])
    {
        parent::__construct('select', ['name'=>$name]);
        $this->addOptions($options);
        return $this;
    }

    public function addOption($value, $nodeValue) :TFormControl
    {
        $this->childNodes->add(new TOption($value, $nodeValue));
        return $this;
    }

    public function addOptions($options) :TFormControl
    {
        if(count($options) > 0)
            foreach($options as $value => $nodeValue)
                $this->childNodes->add(new TOption($value, $nodeValue));

        return $this;
    }

    public function setOptions($options = [])
    {
        if($this->childNodes->items)
            $this->childNodes->clear();

        return $this->addOptions($options);
    }

    public function getOptions() : array
    {
        $result = [];

        foreach($this->childNodes->items as $node)
        {
            foreach($node->attributes->items as $value)
            {
                $result[] = $value;
            }
        }

        return $result;
    }

    public function addOptionsEx($optionsex)
    {
        foreach($optionsex as $optionex)
        {
            $nodeOption = new TOption($optionex['value'], $optionex['nodeValue']);
            foreach($optionex as $key => $value)
            {
                if($key != 'value' && $key != 'nodeValue')
                    $nodeOption->attributes->add($key, $value);
            }

            $this->childNodes->add($nodeOption);
        }

        return $this;
    }

    public function setSize(int $size) :TFormControl
    {
        $this->attributes->add('size', $size);
        return $this;
    }

    public function getSize()
    {
        return $this->attributes->get('size');
    }

    public function setSelected($value)
    {
        return $this->setValue($value);
    }

    public function getSelected()
    {
        return $this->getValue();
    }

    public function setValue($text) :TFormControl
    {
        $this->value = (string)$text;
        return $this;
    }

    public function getValue()
    {
        return (string)$this->value;
    }

    public function validate() :bool
    {
        $result = parent::validate();
        if($result)
        {
            if($this->isRequired())
            {
                if($this->childNodes->count() > 0)
                {
                    foreach($this->childNodes->items as $node)
                    {
                        if((string)$node->attributes->items['value'] == $this->getValue())
                            return true;
                    }

                    $this->errors[':idselect'] = __('err.bad_value', 'Neplatná hodnota: \''.$this->getValue().'\'!');
                } else
                    $this->errors[':idselect'] = __('err.no_values', 'Nenalezeny hodnoty');

                return false;
            } else
                return true;
        } else
            return false;
    }

    public function html() :string
    {
        foreach($this->childNodes->items as $node)
            $node->attributes->delete('selected');

        if($this->getValue() != '')
        {
            foreach($this->childNodes->items as $node)
                if((string)$node->attributes->items['value'] == $this->getValue())
                {
                    $node->attributes->add('selected', 'selected');
                    break;
                }
        } else
            if($this->childNodes->count() === 1)
            {
                foreach($this->childNodes->items as $node)
                {
                    $this->setValue($node->attributes->items['value']);
                    $node->attributes->add('selected', 'selected');
                    break;
                }
            }

        return parent::html();
    }
}
