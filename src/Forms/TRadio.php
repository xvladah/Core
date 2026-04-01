<?php

namespace Core\Forms;
class TRadio extends TFormControl implements IFormControl
{
    public function __construct($value, $nodeValue)
    {
        parent::__construct('input', ['type'=>'radio','value'=>$value]);
        $this->nodeValue = $nodeValue;
    }

    public function setChecked($checked) :TFormControl
    {
        if(empty($checked))
            $this->attributes->delete('checked');
        else
            $this->attributes->add('checked', $checked);

        return $this;
    }

    public function getChecked() :bool
    {
        return (bool) $this->attributes->items['checked'] != '';
    }

    public function html() :string
    {
        if($this->getLabel() != '' && $this->getId() != '')
            $labelcode = '<label for="'.$this->getId().'">'.$this->getLabel().'</label>';
        else
            $labelcode = '';

        return parent::html() . $labelcode;
    }
}
