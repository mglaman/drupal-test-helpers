# drupal-test-helpers
Helpers for writing better Kernel and Unit tests for Drupal

# Traits

The following traits are provided by this library for tests

## RequestTrait

Provides methods to assert requests and responses within Kernel tests.

```php
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
    }
}
```
