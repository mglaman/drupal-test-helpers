<?php

declare(strict_types=1);

namespace mglaman\DrupalTestHelpers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class TestHttpKernel implements HttpKernelInterface
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
}
