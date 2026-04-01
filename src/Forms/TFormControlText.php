<?php

namespace Core\Forms;

class TFormControlText extends TFormControl
{
    public bool $ucfirst   = false;
    public bool $uppercase = false;
    public bool $lowercase = false;

    public function setMaxLength($length): static
    {
        $this->attributes->add('maxlength', $length);
        $this->addCondition(TRule::FILLED)
            ->addRule(TRule::MAX_LENGTH, sprintf(__('err.text_length_max', 'Maximální délka textu je %s'), $length), array($length));

        return $this;
    }

    public function setMinLength($length): static
    {
        $this->addCondition(TRule::FILLED)
            ->addRule(TRule::MIN_LENGTH, sprintf(__('err.text_length_min', 'Minimální délka textu je %s'), $length), array($length));

        return $this;
    }

    public function setReadOnly($readonly = true): static
    {
        if($readonly)
            $this->attributes->add('readonly', 'readonly');

        return $this;
    }

    public function setUCFirst($ucfirst = true): static
    {
        $this->ucfirst = $ucfirst;
        return $this;
    }

    public function setUpperCase($uppercase = true): static
    {
        $this->uppercase = $uppercase;
        return $this;
    }

    public function setLowerCase($lowercase = true): static
    {
        $this->lowercase = $lowercase;
        return $this;
    }

    public function setPlaceHolder($value): static
    {
        $this->attributes->add('placeholder', $value);
        return $this;
    }

    public function getPlaceHolder($value)
    {
        return $this->attributes->get('placeholder');
    }

    public function setAutocomplete(int $state): static
    {
        if($state == '1' || $state === true || $state == 'on')
            $value = 'on';
        else
            $value = 'off';

        $this->attributes->add('autocomplete', $value);
        return $this;
    }


    public static function mb_ucfirst($string, string $encoding = 'utf8'): string
    {
        $strlen 	= mb_strlen($string, $encoding);
        $firstChar 	= mb_substr($string, 0, 1, $encoding);
        $then 		= mb_substr($string, 1, $strlen - 1, $encoding);

        return mb_strtoupper($firstChar, $encoding) . $then;
    }

    public function getValue()
    {
        if($this->ucfirst === true)
            return self::mb_ucfirst(parent::getValue());
        else
            return parent::getValue();
    }
}
