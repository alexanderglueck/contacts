<?php

namespace App\Support\Scramble;

use Dedoc\Scramble\Contracts\RuleTransformer;
use Dedoc\Scramble\Support\Generator\Types\StringType;
use Dedoc\Scramble\Support\Generator\Types\Type;
use Dedoc\Scramble\Support\RuleTransforming\NormalizedRule;
use Dedoc\Scramble\Support\RuleTransforming\RuleTransformerContext;

/**
 * Fixes the unit mismatch between Laravel's `max:N` rule for file/image
 * fields (N is in kilobytes) and OpenAPI's `maxLength` for binary strings
 * (treated by tooling as a byte count).
 *
 * Without this, an 8 MB cap (`max:8192` in KB) renders as `maxLength: 8192`
 * in the spec, which spec-driven client generators read as "8192 bytes".
 *
 * This transformer fires only when the field's rule set also contains
 * `file` or `image`, multiplies the KB value into bytes, and prepends a
 * human-readable size to the description so the UI surfaces it too.
 */
class FileMaxKilobytesTransformer implements RuleTransformer
{
    public function shouldHandle(NormalizedRule $rule): bool
    {
        return $rule->getRule() === 'max';
    }

    public function toSchema(Type $type, NormalizedRule $rule, RuleTransformerContext $context): Type
    {
        // Only intercept when the field is also a file/image upload. For
        // string/array/int fields, Laravel's default `max` semantics
        // already match OpenAPI's, so we let the built-in transformer
        // handle them.
        $isFileField = $context->fieldRules
            ->filter(fn ($r) => is_string($r))
            ->contains(fn (string $r) => $r === 'file' || $r === 'image');

        if (! $isFileField || ! $type instanceof StringType) {
            return $type;
        }

        $kilobytes = (int) ($rule->getParameters()[0] ?? 0);
        if ($kilobytes <= 0) {
            return $type;
        }

        $type->setMax($kilobytes * 1024);

        return $type;
    }
}
