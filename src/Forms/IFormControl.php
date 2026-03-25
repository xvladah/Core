<?php

interface IFormControl
{
    function setName(string $value);

    function onClick(string $js);

    function onFocus(string $js);

    function onBlur(string $js);

    function setValue($value);

    function getValue();

    function isDisabled();

    function isVisible();

    function isRequired();

    function validate();

    function getErrors();

    function hasErrors();

    function html();
}
