<?php

declare(strict_types=1);

namespace mglaman\DrupalTestHelpers;

use Drupal\Core\Session\AccountProxyInterface;
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
     * @param array<string, string|int|bool|float> $formData
     */
    protected function doFormSubmit(
        string $uri,
        array $formData,
        string $button = '',
        bool $followRedirect = true
    ): Response {
        $response = $this->doRequest(Request::create($uri));
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $formData += [
          'form_build_id' => $this->getInputValue('form_build_id'),
          'form_id' => $this->getInputValue('form_id'),
          'op' => $button === '' ? $this->getInputValue('op') : $button,
        ];
        $currentUser = $this->container->get('current_user');
        self::assertInstanceOf(AccountProxyInterface::class, $currentUser);
        if ($currentUser->isAuthenticated()) {
            $formData['form_token'] = $this->getInputValue('form_token');
        }

        try {
            $response = $this->doRequest(Request::create($uri, 'POST', $formData));
            if ($followRedirect && $response->getStatusCode() === Response::HTTP_SEE_OTHER) {
                $request = Request::create((string) $response->headers->get('Location'));
                return $this->doRequest($request);
            }
        } catch (EnforcedResponseException $e) {
            if ($followRedirect) {
                $request = Request::create((string) $e->getResponse()->headers->get('Location'));
                return $this->doRequest($request);
            }
            throw $e;
        }
        return $response;
    }

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

    private function getInputValue(string $name): string
    {
        $input = $this->cssSelect(sprintf('input[name="%s"]', $name));
        self::assertNotCount(
            0,
            $input,
            sprintf('Expected to find at least one element with selector [input[name="%s"]]', $name)
        );
        return (string) $input[0]->attributes()?->value[0];
    }
}
