<?php

declare(strict_types=1);

namespace mglaman\DrupalTestHelpers\Tests;

use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Access\AccessResultReasonInterface;
use Drupal\Core\Access\CsrfAccessCheck;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Url;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\user\Traits\UserCreationTrait;
use mglaman\DrupalTestHelpers\CsrfTokenTrait;
use mglaman\DrupalTestHelpers\RequestTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

final class CsrfTokenTraitTest extends KernelTestBase
{
    use CsrfTokenTrait;
    use RequestTrait;
    use UserCreationTrait;

  /**
   * @var string[]
   */
    protected static $modules = ['system', 'user'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->installEntitySchema('user');
        $this->createUser();
    }

  /**
   * @testWith [false, false, "'csrf_token' URL query argument is invalid."]
   *           [true, true, ""]
   */
    public function testMethod(bool $withMethod, bool $isAllowed, string $reason): void
    {
        $user = $this->createUser(['administer site configuration']);
        // @phpstan-ignore-next-line
        $this->container->get('current_user')->setAccount($user);

        $url = Url::fromRoute('system.run_cron');
        $urlString = $withMethod ? $this->getCsrfUrlString($url) : $url->toString();
        // @phpstan-ignore-next-line
        $access = $this->checkAccess($urlString);
        self::assertEquals($isAllowed, $access->isAllowed());
        if ($isAllowed) {
            self::assertNotInstanceOf(AccessResultReasonInterface::class, $access);
        } else {
            self::assertInstanceOf(AccessResultReasonInterface::class, $access);
            self::assertEquals($reason, $access->getReason());
        }
    }

    private function checkAccess(string $url): AccessResultInterface
    {
        $request = Request::create($url);
        $route = new Route($request->getPathInfo());

        $sut = $this->container->get('access_check.csrf');
        self::assertInstanceOf(CsrfAccessCheck::class, $sut);
        // @phpstan-ignore-next-line
        return $sut->access($route, $request, $this->container->get('current_route_match'));
    }
}
