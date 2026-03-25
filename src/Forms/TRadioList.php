<?php

class TRadioList extends TFormControl implements IFormControl
{
    public function __construct($name, $options = [])
    {
        parent::__construct('fieldset', ['name'=>$name]);
        $this->addOptions($options);
    }

    public function addOptions($options) :TFormControl
    {
        foreach($options as $value => $nodeValue)
            $this->childNodes->add(new TRadio($value, $nodeValue));

        return $this;
    }

    public function validate() :bool
    {
        $result = parent::validate();
        if($result)
        {
            if($this->childNodes->count() > 0)
            {
                foreach($this->childNodes->items as $node)
                {
                    if($node->attributes->items['value'] == $this->getValue())
                        return true;
                }

                $this->errors[':idselect'] = 'Neplatná hodnota';
                return false;
            } else
                return true;
        } else
            return false;
    }

    public function html() :string
    {
        $return = '';
        foreach($this->childNodes->items as $radio)
        {
            $radio->attributes->add('name', $this->attributes->items['name']);

            if($this->getGroup() != '')
                $radio->setGroup($this->getGroup());

            if($this->getValue() == $radio->getValue())
                $radio->setChecked('checked');

            if($this->attributes->items['disabled'] == 'disabled')
                $radio->setDisabled();

            if($return != '')
                $return .= '&nbsp;';

            $return .= $radio->html();
        }

        return $return;
    }
}
