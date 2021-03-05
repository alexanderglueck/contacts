<?php

function create($class, $attributes = [], $times = 1)
{
    return $class::factory()->times($times)->create($attributes);
}

function make($class, $attributes = [], $times = 1)
{
    return $class::factory()->times($times)->make($attributes);
}
