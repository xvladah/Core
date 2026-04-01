<?php

namespace Core\Forms;

class TFormNumericControl extends TFormControlText implements IFormControl
{
    private $thousand_sep = ' ';

    public function __construct(string $name)
    {
        parent::__construct('input', ['type'=>'text','name'=>$name]);
    }

    public function setThousandSep(string $thousand_sep) :TFormNumericControl
    {
        $this->thousand_sep = $thousand_sep;
        return $this;
    }

    public function getThousandSep() :string
    {
        return $this->thousand_sep;
    }

    public function setCheckRange($value_min, $value_max) :TFormNumericControl
    {
        $this->addCondition(TRule::FILLED)
            ->addRule(TRule::RANGE, sprintf(__('err.bad_number_range', 'Číslo je mimo povolený rozsah %s až %s!'), $value_min, $value_max), [$value_min, $value_max]);

        return $this;
    }
}
