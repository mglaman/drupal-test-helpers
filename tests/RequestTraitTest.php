<?php

declare(strict_types=1);

namespace mglaman\DrupalTestHelpers\Tests;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\KernelTests\KernelTestBase;
use mglaman\DrupalTestHelpers\RequestTrait;
use Symfony\Component\HttpFoundation\Request;

final class RequestTraitTest extends KernelTestBase
{
    use RequestTrait;

    /**
     * @var string[]
     */
    protected static $modules = ['system', 'user'];

    public function testDoRequest(): void
    {
        $this->installConfig(['system']);
        $this->doRequest(Request::create('/user/login'));
        self::assertStringContainsString(
            'Enter your  username.',
            $this->getRawContent()
        );
        $cache = $this->container->get('cache.bootstrap');
        self::assertInstanceOf(CacheBackendInterface::class, $cache);
        self::assertFalse(
            $cache->get('module_implements'),
            'Module hook implementation not written since response was not terminated',
        );
    }

    public function testDoRequestWithTerminate(): void
    {
        $this->installConfig(['system']);
        $this->doRequest(Request::create('/user/login'), true);
        self::assertStringContainsString(
            'Enter your  username.',
            $this->getRawContent()
        );
        $cache = $this->container->get('cache.bootstrap');
        self::assertInstanceOf(CacheBackendInterface::class, $cache);
        self::assertNotEmpty(
            $cache->get('module_implements'),
            'Module hook implementation was written since response terminated',
        );
    }
}
