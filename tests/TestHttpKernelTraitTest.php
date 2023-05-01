<?php

declare(strict_types=1);

namespace mglaman\DrupalTestHelpers\Tests;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\KernelTests\KernelTestBase;
use mglaman\DrupalTestHelpers\RequestTrait;
use mglaman\DrupalTestHelpers\TestHttpKernelTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TestHttpKernelTraitTest extends KernelTestBase
{
    use RequestTrait;
    use TestHttpKernelTrait;

    public function register(ContainerBuilder $container): void
    {
        parent::register($container);
        $this->registerTestHttpKernel($container);
    }

    public function testHttpKernel(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('No route found for "GET http://localhost/foobar"');
        $request = Request::create('/foobar');
        $this->doRequest($request);
    }
}
