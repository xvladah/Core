<?php

namespace Core\Forms;

class TCheckBox extends TFormControl implements IFormControl
{
    public function __construct($name)
    {
        parent::__construct('input', ['type'=>'checkbox', 'name'=>$name]);
    }

    public function setChecked($checked) :TFormControl
    {
        $this->setValue($checked);
        return $this;
    }

    public function getChecked() :bool
    {
        return (bool)$this->getValue() === true;
    }

    public function setValue($checked) :TFormControl
    {
        $checked = mb_strtolower($checked);
        if($checked == '' || in_array($checked, ['false','0','off', false]))
            $this->attributes->delete('checked');
        else {
            if(in_array($checked, ['true','1','on','checked',true]) || intval($checked) != 0)
                $this->attributes->add('checked', 'checked');
            else
                $this->attributes->delete('checked');
        }
        return $this;
    }

    public function getValue() :string
    {
        if($this->attributes->items['checked'] == 'checked')
            return '1';
        else
            return '0';
    }
}
