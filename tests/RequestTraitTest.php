<?php

declare(strict_types=1);

namespace mglaman\DrupalTestHelpers\Tests;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Url;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\user\Traits\UserCreationTrait;
use Drupal\user\Entity\User;
use mglaman\DrupalTestHelpers\RequestTrait;
use Symfony\Component\HttpFoundation\Request;

final class RequestTraitTest extends KernelTestBase
{
    use RequestTrait;
    use UserCreationTrait;

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

    public function testFormSubmitUserLogin(): void
    {
        $this->installConfig(['system', 'user']);
        $this->installEntitySchema('user');
        $user = User::create([
          'mail' => 'foo@example.com',
          'name' => 'foo',
          'pass' => 'barbaz',
          'status' => 1,
        ]);
        $user->save();
        $uri = Url::fromRoute('user.login')->toString();
        self::assertIsString($uri);
        $response = $this->doFormSubmit(
            $uri,
            [
            'name' => 'foo',
              'pass' => 'barbaz',
            ]
        );
        self::assertEquals(200, $response->getStatusCode());
    }

    public function testFormSubmitUserLoginInvalid(): void
    {
        $this->installConfig(['system', 'user']);
        $this->installEntitySchema('user');
        $user = User::create([
          'mail' => 'foo@example.com',
          'name' => 'foo',
          'pass' => 'barbaz',
          'status' => 1,
        ]);
        $user->save();
        $uri = Url::fromRoute('user.login')->toString();
        self::assertIsString($uri);
        $response = $this->doFormSubmit(
            $uri,
            [
            'name' => 'foo',
              'pass' => 'bar1baz',
            ]
        );
        self::assertEquals(200, $response->getStatusCode());
        $this->assertText('Unrecognized username or password.');
    }
}
