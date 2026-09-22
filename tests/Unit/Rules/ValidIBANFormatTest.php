<?php

namespace Tests\Unit\Rules;

use App\Rules\ValidIBANFormat;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * The rule is a thin wrapper around php-iban's verify_iban(), so what is worth
 * pinning here is the behaviour the app depends on: null is allowed through for
 * optional fields, and the library's country registry decides the rest. That
 * registry is data, not code, so a library upgrade can change which IBANs are
 * accepted without any change here.
 */
class ValidIBANFormatTest extends TestCase
{
    private ValidIBANFormat $rule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rule = new ValidIBANFormat();
    }

    #[Test]
    public function it_allows_null_so_the_field_stays_optional(): void
    {
        $this->assertTrue($this->rule->passes('iban', null));
    }

    #[Test]
    public function it_accepts_valid_ibans(): void
    {
        foreach (['AT611904300234573201', 'DE89370400440532013000', 'GB29NWBK60161331926819'] as $iban) {
            $this->assertTrue($this->rule->passes('iban', $iban), "expected {$iban} to be valid");
        }
    }

    #[Test]
    public function it_rejects_malformed_ibans(): void
    {
        foreach (['not-an-iban', '', 'AT61190430023457320', 'XX611904300234573201'] as $iban) {
            $this->assertFalse($this->rule->passes('iban', $iban), "expected {$iban} to be rejected");
        }
    }

    #[Test]
    public function it_follows_the_registry_when_a_country_format_is_corrected(): void
    {
        // php-iban 4 carried a 16-character Burundi format and accepted this;
        // 5.0 corrected the registry to the real 27-character one.
        $this->assertFalse($this->rule->passes('iban', 'BI33123412341234'));
        $this->assertTrue($this->rule->passes('iban', 'BI4210000100010000332045181'));
    }
}
