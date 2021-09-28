<?php

function create($class, $attributes = [], $times = 1)
{
    if ($times > 1) {
        return $class::factory()->times($times)->create($attributes);
    }

    return $class::factory()->create($attributes);
}

function make($class, $attributes = [], $times = 1)
{
    if ($times > 1) {
        return $class::factory()->times($times)->make($attributes);
    }
    return $class::factory()->make($attributes);
}
