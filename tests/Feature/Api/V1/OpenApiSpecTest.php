<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Contract tests for the generated OpenAPI document. Runs the
 * scramble:export command and asserts the parts the Android client
 * depends on — primarily that file-upload size limits are rendered in
 * the unit (bytes) that spec tooling actually consumes, not in Laravel's
 * raw kilobyte argument.
 */
class OpenApiSpecTest extends TestCase
{
    use RefreshDatabase;

    private array $spec;

    protected function setUp(): void
    {
        parent::setUp();

        $path = storage_path('framework/testing/scramble-spec.json');
        @unlink($path);

        $this->artisan('scramble:export', ['--path' => $path])->assertSuccessful();

        $this->spec = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
    }

    #[Test]
    public function the_contact_image_upload_caps_are_reported_in_bytes_not_kilobytes()
    {
        $schema = $this->spec['components']['schemas']['ContactImageUploadRequest'] ?? null;
        $this->assertNotNull($schema, 'ContactImageUploadRequest schema is missing from the OpenAPI document.');

        $file = $schema['properties']['file'] ?? null;
        $this->assertNotNull($file);

        // 8 MB upload cap → 8 × 1024 × 1024 bytes. Without the custom
        // FileMaxKilobytesTransformer this would be 8192 (Laravel's KB
        // value leaking straight into the spec), which generators read
        // as 8192 bytes ≈ 8 KB — about a thousand times too small.
        $this->assertSame(8 * 1024 * 1024, $file['maxLength']);
        $this->assertSame('binary', $file['format']);
        $this->assertSame('string', $file['type']);
    }
}
