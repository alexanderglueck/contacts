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
            'autofocus' => $autofocus ? ' autofocus ' : ''
        ]);
    }

    public static function text($fieldName, $label, $required = false, $autofocus = false)
    {
        return view('components.text', [
            'type' => 'text',
            'fieldName' => $fieldName,
            'label' => $label,
            'required' => $required ? ' required ' : '',
            'autofocus' => $autofocus ? ' autofocus ' : ''
        ]);
    }

    public static function email($fieldName, $label, $required = true, $autofocus = false)
    {
        return view('components.text', [
            'type' => 'email',
            'fieldName' => $fieldName,
            'label' => $label,
            'required' => $required ? ' required ' : '',
            'autofocus' => $autofocus ? ' autofocus ' : ''
        ]);
    }
}