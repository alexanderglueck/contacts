<?php

namespace Tests\Unit;

use App\Support\UserAgentInfo;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UserAgentInfoTest extends TestCase
{
    #[Test]
    public function it_identifies_chrome_on_macos_as_desktop()
    {
        $info = UserAgentInfo::fromUserAgent(
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36'
        );

        $this->assertSame('Chrome', $info['browser']);
        $this->assertSame('macOS', $info['platform']);
        $this->assertTrue($info['is_desktop']);
    }

    #[Test]
    public function it_identifies_safari_on_iphone_as_mobile()
    {
        $info = UserAgentInfo::fromUserAgent(
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1'
        );

        $this->assertSame('Safari', $info['browser']);
        $this->assertSame('iPhone', $info['platform']);
        $this->assertFalse($info['is_desktop']);
    }

    #[Test]
    public function it_prefers_edge_over_chrome_when_both_strings_appear()
    {
        // Edge's UA includes Chrome too; the Edg/ token has to win.
        $info = UserAgentInfo::fromUserAgent(
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36 Edg/126.0.0.0'
        );

        $this->assertSame('Edge', $info['browser']);
        $this->assertSame('Windows', $info['platform']);
        $this->assertTrue($info['is_desktop']);
    }

    #[Test]
    public function it_falls_back_to_generic_labels_for_unknown_agents()
    {
        $info = UserAgentInfo::fromUserAgent('something-the-parser-has-never-seen/1.0');

        $this->assertSame('Browser', $info['browser']);
        $this->assertSame('Unknown device', $info['platform']);
    }

    #[Test]
    public function it_handles_null_user_agent_without_crashing()
    {
        $info = UserAgentInfo::fromUserAgent(null);

        $this->assertSame('Browser', $info['browser']);
        $this->assertSame('Unknown device', $info['platform']);
    }
}
