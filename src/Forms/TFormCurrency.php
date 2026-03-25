<?php

class TFormCurrency extends TFormFloat implements IFormControl
{
    public function __construct($name, int $decimals = 2)
    {
        // parent::__construct($name);
        TFormNumericControl::__construct($name);
        $this->setDecimals($decimals);
        $this->setSeparator(',');

        $this->addCondition(TRule::FILLED)
            ->addRule(TRule::CURRENCY, __('err.bad_currency_format', 'Chybný formát částky'));
    }
}
