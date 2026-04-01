<?php

namespace Core\Forms;

class TFormInteger extends TFormNumericControl implements IFormControl
{
    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->addCondition(TRule::FILLED)
            ->addRule(TRule::INTEGER, __('err.bad_integer_format', 'Chybný formát celého čísla'));
    }

    public function setValue($text) :TFormControl
    {
        $zal =  str_replace(' ', '', $text);
        if($zal[0] == '-' || $zal[0] == '+')
            $test = substr($zal, 1);
        else
            $test = $zal;

        if(is_int($test) || ctype_digit(strval($test)))
            parent::setValue(number_format($zal, 0, '.', $this->getThousandSep()));
        else
            parent::setValue($text);

        return $this;
    }

    public function getValue()
    {
        $value = parent::getValue();
        return str_replace($this->getThousandSep(), '', $value);
    }
}
