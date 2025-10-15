<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Helpers\Text;

final class TextTest extends TestCase
{
    public function test_excerpt_short_text_returns_same()
    {
        $this->assertSame('Hello', Text::excerpt('Hello', 10));
    }

    public function test_excerpt_long_text_is_truncated()
    {
        $txt = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';
        $this->assertSame('Lorem ipsum dolor sit amet, consectetur...', Text::excerpt($txt, 40));
    }

    public function test_excerpt_no_space_truncates_at_max()
    {
        $txt = 'abcdefghijabcdefghijabcdefghijabcdefghijabcdefghijabcdefghij';
        $this->assertSame(substr($txt,0,20).'...', Text::excerpt($txt, 20));
    }

    public function test_e_escapes_html()
    {
        $this->assertSame('&lt;a&gt;hello&lt;/a&gt;', Text::e('<a>hello</a>'));
    }

    public function test_e_null_returns_empty_string()
    {
        $this->assertSame('', Text::e(null));
    }
}