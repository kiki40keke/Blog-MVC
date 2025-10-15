<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Helpers\URL;

/** @covers \App\Helpers\URL    */

final class HelpersTest extends TestCase
{
    public function test_is_admin_url(): void
    {
       $this->assertTrue(URL::isAdminUrl('/admin/dashboard'));
       $this->assertFalse(URL::isAdminUrl('/user/profile'));
    }
}
