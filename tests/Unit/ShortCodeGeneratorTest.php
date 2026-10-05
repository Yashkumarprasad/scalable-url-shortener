<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\ShortCodeGeneratorService;
use PHPUnit\Framework\TestCase;

class ShortCodeGeneratorTest extends TestCase
{
    private ShortCodeGeneratorService $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generator = new ShortCodeGeneratorService();
    }

    public function test_it_generates_code_of_specified_length(): void
    {
        $code6 = $this->generator->generate(6);
        $code8 = $this->generator->generate(8);

        $this->assertSame(6, strlen($code6));
        $this->assertSame(8, strlen($code8));
    }

    public function test_it_generates_url_safe_characters(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $code = $this->generator->generate(6);
            $this->assertTrue($this->generator->isValid($code));
            $this->assertMatchesRegularExpression('/^[a-zA-Z0-9_-]+$/', $code);
        }
    }

    public function test_it_rejects_reserved_words(): void
    {
        $this->assertFalse($this->generator->isValid('api'));
        $this->assertFalse($this->generator->isValid('admin'));
        $this->assertFalse($this->generator->isValid('health'));
        $this->assertFalse($this->generator->isValid('login'));
    }

    public function test_it_validates_alias_length_constraints(): void
    {
        $this->assertFalse($this->generator->isValid('ab')); // too short (<3)
        $this->assertTrue($this->generator->isValid('abc'));
        $this->assertTrue($this->generator->isValid('my-custom-slug_123'));
        $this->assertFalse($this->generator->isValid(str_repeat('a', 65))); // too long (>64)
    }
}
