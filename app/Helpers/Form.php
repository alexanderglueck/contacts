<?php

namespace App\Helpers;

class Form
{
    public static function password($fieldName, $label, $required = true, $autofocus = false)
    {
        return view('components.text', [
            'type' => 'password',
            'fieldName' => $fieldName,
            'label' => $label,
            'required' => $required ? ' required ' : '',
            'autofocus' => $autofocus ? ' autofocus ' : '',
            'value' => null
        ]);
    }

    public static function text($fieldName, $label, $value = null, $required = false, $autofocus = false)
    {
        return view('components.text', [
            'type' => 'text',
            'fieldName' => $fieldName,
            'label' => $label,
            'required' => $required ? ' required ' : '',
            'autofocus' => $autofocus ? ' autofocus ' : '',
            'value' => $value
        ]);
    }

    public static function email($fieldName, $label, $value = null, $required = true, $autofocus = false)
    {
        return view('components.text', [
            'type' => 'email',
            'fieldName' => $fieldName,
            'label' => $label,
            'required' => $required ? ' required ' : '',
            'autofocus' => $autofocus ? ' autofocus ' : '',
            'value' => $value
        ]);
    }

    public static function textarea($fieldName, $label, $value = null, $required = false, $autofocus = false)
    {
        return view('components.textarea', [
            'fieldName' => $fieldName,
            'label' => $label,
            'required' => $required ? ' required ' : '',
            'autofocus' => $autofocus ? ' autofocus ' : '',
            'value' => $value
        ]);
    }
}
