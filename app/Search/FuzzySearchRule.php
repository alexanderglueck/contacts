<?php

namespace App\Search;

use ScoutElastic\SearchRule;

class FuzzySearchRule extends SearchRule
{
    public function buildQueryPayload()
    {
        return [
            'must' => [
                'multi_match' => [
                    'query' => $this->builder->query,
                    'fuzziness' => 'auto'
                ]
            ]
        ];
    }
}
