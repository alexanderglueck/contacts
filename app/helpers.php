<?php

/*
 * Flashing
 */
function flash($message = null, $level = 'info')
{
    session()->flash('alert-' . $level, $message);
}

function flashSuccess($message = null)
{
    flash($message, 'success');
}

function flashError($message = null)
{
    flash($message, 'danger');
}

function flashWarning($message = null)
{
    flash($message, 'warning');
}

function flashInfo($message = null)
{
    flash($message);
}

/*
 * On Page active nav helper
 */
function on_page($path)
{
    return request()->is($path);
}

function return_if($condition, $value)
{
    if ($condition) {
        return $value;
    }
}
