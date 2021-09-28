<?php

namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

class CommentCollection extends Collection
{
    /**
     * Thread the comment tree.
     */
    public function threaded(): self
    {
        $comments = parent::groupBy('parent_id');

        if (count($comments)) {
            $comments['root'] = $comments[''];
            unset($comments['']);
        }

        return $comments;
    }
}
