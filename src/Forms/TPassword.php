<?php

namespace Core\Forms;
class TPassword extends TFormControlText implements IFormControl
{
    public function __construct($name)
    {
        parent::__construct('input', ['type'=>'password','name'=>$name,'autocomplete'=>'new-password']);
    }

    public function setMaxLength($length) :static
    {
        $this->attributes->add('maxlength', $length);
        return $this;
    }
}
