<?php

declare(strict_types=1);

namespace mglaman\DrupalTestHelpers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;

final class TestHttpKernel implements HttpKernelInterface, TerminableInterface
{
    public function __construct(
        private readonly HttpKernelInterface $http_kernel
    ) {
    }

    public function handle(
        Request $request,
        int $type = self::MAIN_REQUEST,
        bool $catch = true
    ): Response {
        return $this->http_kernel->handle($request, $type, false);
    }

    public function terminate(Request $request, Response $response): void
    {
        if ($this->http_kernel instanceof TerminableInterface) {
            $this->http_kernel->terminate($request, $response);
        }
    }
}
