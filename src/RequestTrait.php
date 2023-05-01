<?php

declare(strict_types=1);

namespace mglaman\DrupalTestHelpers;

use Drupal\KernelTests\AssertContentTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;

trait RequestTrait
{
    use AssertContentTrait;

    /**
     * @var \Drupal\Core\DependencyInjection\ContainerBuilder
     */
    protected $container;

    /**
     * Passes a request to the HTTP kernel and returns a response.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *   The request.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *   The response.
     *
     * @throws \Exception
     */
    protected function doRequest(Request $request, bool $terminate = false): Response
    {
        $http_kernel = $this->container->get('http_kernel');
        self::assertInstanceOf(
            HttpKernelInterface::class,
            $http_kernel
        );
        $response = $http_kernel->handle($request);
        $content = $response->getContent();
        self::assertNotFalse($content);
        $this->setRawContent($content);

        if ($terminate) {
            self::assertInstanceOf(
                TerminableInterface::class,
                $http_kernel
            );
            $http_kernel->terminate($request, $response);
        }

        return $response;
    }
}
