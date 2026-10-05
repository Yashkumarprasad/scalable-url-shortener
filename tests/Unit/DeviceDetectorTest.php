<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\DeviceType;
use App\Services\DeviceDetectorService;
use PHPUnit\Framework\TestCase;

class DeviceDetectorTest extends TestCase
{
    private DeviceDetectorService $detector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->detector = new DeviceDetectorService();
    }

    public function test_it_detects_mobile_devices(): void
    {
        $iphoneUA = 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Mobile/15E148 Safari/604.1';
        $androidUA = 'Mozilla/5.0 (Linux; Android 13; SM-S901B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Mobile Safari/537.36';

        $this->assertSame(DeviceType::MOBILE, $this->detector->detect($iphoneUA));
        $this->assertSame(DeviceType::MOBILE, $this->detector->detect($androidUA));
    }

    public function test_it_detects_tablet_devices(): void
    {
        $ipadUA = 'Mozilla/5.0 (iPad; CPU OS 16_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Mobile/15E148 Safari/604.1';
        $tabletUA = 'Mozilla/5.0 (Linux; Android 12; Lenovo TB-X606F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36';

        $this->assertSame(DeviceType::TABLET, $this->detector->detect($ipadUA));
        $this->assertSame(DeviceType::TABLET, $this->detector->detect($tabletUA));
    }

    public function test_it_detects_bots(): void
    {
        $googlebot = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
        $curl = 'curl/7.88.1';

        $this->assertSame(DeviceType::BOT, $this->detector->detect($googlebot));
        $this->assertSame(DeviceType::BOT, $this->detector->detect($curl));
    }

    public function test_it_detects_desktop(): void
    {
        $desktopUA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36';
        $this->assertSame(DeviceType::DESKTOP, $this->detector->detect($desktopUA));
    }

    public function test_it_handles_empty_user_agent(): void
    {
        $this->assertSame(DeviceType::UNKNOWN, $this->detector->detect(null));
        $this->assertSame(DeviceType::UNKNOWN, $this->detector->detect(''));
    }
}
