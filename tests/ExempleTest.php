<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

final class ExempleTest extends TestCase
{
    public function test_somme_basique(): void
    {
        $resultat = 2 + 2;
        $this->assertSame(4, $resultat);
    }
}
