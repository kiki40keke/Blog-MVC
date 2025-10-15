<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Helpers\URL;

/** @covers \App\Helpers\URL */
final class HelpersTest extends TestCase
{
    protected function tearDown(): void
    {
        // Réinitialise la query après chaque test pour éviter les effets de bord
        URL::setQuery([]);
        parent::tearDown();
    }

    public function test_get_int_returns_int_when_valid(): void
    {
        URL::setQuery(['page' => '5']);
        $this->assertSame(5, URL::getInt('page'));
    }

    public function test_get_int_returns_zero_when_zero_string(): void
    {
        URL::setQuery(['page' => '0']);
        $this->assertSame(0, URL::getInt('page'));
    }

    public function test_get_int_returns_zero_when_zero_integer(): void
    {
        URL::setQuery(['page' => 0]);
        $this->assertSame(0, URL::getInt('page'));
    }

    public function test_get_int_returns_default_when_not_set(): void
    {
        URL::setQuery([]);
        $this->assertSame(3, URL::getInt('page', 3));
    }

    public function test_get_int_returns_null_when_not_set_and_no_default(): void
    {
        URL::setQuery([]);
        $this->assertNull(URL::getInt('page'));
    }

    public function test_get_int_throws_exception_on_invalid(): void
    {
        URL::setQuery(['page' => 'abc']);
        $this->expectException(\Exception::class);
        URL::getInt('page');
    }

    public function test_get_positive_int_returns_int_when_valid(): void
    {
        URL::setQuery(['id' => 8]);
        $this->assertSame(8, URL::getPositiveInt('id'));
    }

    public function test_get_positive_int_throws_exception_on_negative(): void
    {
        URL::setQuery(['id' => '-2']);
        $this->expectException(\Exception::class);
        URL::getPositiveInt('id');
    }

    public function test_get_positive_int_throws_exception_on_zero(): void
    {
        URL::setQuery(['id' => '0']);
        $this->expectException(\Exception::class);
        URL::getPositiveInt('id');
    }

    public function test_get_positive_int_returns_default_when_not_set(): void
    {
        URL::setQuery([]);
        $this->assertSame(7, URL::getPositiveInt('id', 7));
    }

    public function test_get_positive_int_returns_null_when_not_set_and_no_default(): void
    {
        URL::setQuery([]);
        $this->assertNull(URL::getPositiveInt('id'));
    }

    public function test_is_admin_url_returns_true_for_admin_paths(): void
    {
        $this->assertTrue(URL::isAdminUrl('/admin/dashboard'));
        $this->assertTrue(URL::isAdminUrl('/admin'));
        $this->assertTrue(URL::isAdminUrl('/admin/settings/user'));
    }

    public function test_is_admin_url_returns_false_for_non_admin_paths(): void
    {
        $this->assertFalse(URL::isAdminUrl('/user/profile'));
        $this->assertFalse(URL::isAdminUrl('/'));
        $this->assertFalse(URL::isAdminUrl('/administrator'));
    }
}