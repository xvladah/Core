<?php

namespace Core\Forms;
class TFormEmail extends TFormControlText implements IFormControl
{
    public function __construct($name)
    {
        parent::__construct('input', ['type'=>'text','name'=>$name]);
        $this->addCondition(TRule::FILLED)
            ->addRule(TRule::EMAIL, __('err.bad_email_format', 'Chybný formát e-mailu'));
    }
}
