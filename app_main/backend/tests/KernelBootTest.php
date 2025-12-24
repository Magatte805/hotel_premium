<?php

namespace App\Tests;

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

final class KernelBootTest extends KernelTestCase
{
    /**
     * Override KernelTestCase::createKernel() to avoid relying on KERNEL_CLASS autoloading.
     */
    protected static function createKernel(array $options = []): KernelInterface
    {
        // Force test env for consistency with the rest of the suite.
        $env = $options['environment'] ?? 'test';
        $debug = $options['debug'] ?? true;

        return new Kernel($env, (bool) $debug);
    }

    public function testKernelBoots(): void
    {
        self::bootKernel();
        $this->assertNotNull(self::$kernel);
    }
}




