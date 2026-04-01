<?php

/**
* Tříea pro validaci hodnot u prvků formuláře
*
* @name 	   TRule
* @version    2.0
* @author     vladimir.horky
* @copyright  Vladimír Horký, 2020.
*/

declare(strict_types=1);

namespace Core\Forms;

class TRules
{
    private array $rules = [];

    private array $messages = [];

    private IFormControl $control;

    public function __construct(IFormControl $control)
    {
        $this->control = $control;
    }

    public function addRule(string $operation, ?string $message = null, mixed $arg = null) :TRules
    {
        $rule = new TRule();

        $rule->control = $this->control;
        $rule->operation = $operation;
        $rule->arg = $arg;
        $rule->message = $message;
        $rule->type = TRule::VALIDATOR;
        $this->rules[] = $rule;

        return $this;
    }

    public function addCondition(string $operation, mixed $arg = null) :TRules
    {
        return $this->addConditionOn($this->control, $operation, $arg);
    }

    public function getCondition(string $operation): mixed
    {
        $return = null;

        foreach ($this->rules as $rule)
        {
            if($rule->operation = $operation && $rule->type == TRule::CONDITION)
            {
                $return = $rule;
                break;
            }
        }

        return $return;
    }

    public function addConditionOn(IFormControl $control, string $operation, mixed $arg = null) :TRules
    {
        $rule = new TRule();
        $rule->control = $control;
        $rule->operation = $operation;
        $rule->arg = $arg;
        $rule->type = TRule::CONDITION;
        $rule->subRules = new self($this->control);
        $rule->message = null;

        $this->rules[] = $rule;
        return $rule->subRules;
    }

    public function hasRule(string $operation) :bool
    {
        return !(is_null($this->getRule($operation)));
    }

    public function getRule(string $operation)
    {
        $return = null;

        foreach ($this->rules as $rule)
        {
            if($rule->operation = $operation && $rule->type != TRule::CONDITION)
            {
                $return = $rule;
                break;
            }
        }

        return $return;
    }

    public function getMessages() :array
    {
        return $this->messages;
    }

    public function validate(bool $onlyCheck = false) :bool
    {

        $this->messages = [];
        $valid = true;
        foreach ($this->rules as $rule)
        {
            if ($rule->control->isDisabled())
                continue;

            if(count($this->messages) > 0)
                break;

            if($rule->type === TRule::CONDITION)
            {
                $success = $rule->isValid();
                if($success)
                {
                    $success = $rule->subRules->validate($onlyCheck);
                    if(!$success)
                    {
                        $zal = $rule->subRules->getMessages();
                        foreach($zal as $key => $value)
                            $this->messages[$key] = $value;
                    }

                    $valid = $valid && $success;
                }
            } else
                if ($rule->type === TRule::VALIDATOR)
                {
                    $success = $rule->isValid();
                    if(!$success)
                        $this->messages[$rule->operation] = $rule->message;

                    $valid = $valid && $success;
                } else
                    $valid = false;

        }
        return $valid;
    }
}