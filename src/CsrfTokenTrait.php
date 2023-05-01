<?php

declare(strict_types=1);

namespace mglaman\DrupalTestHelpers;

use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\MetadataBag;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait CsrfTokenTrait
{
  /**
   * @var \Drupal\Core\DependencyInjection\ContainerBuilder
   */
    protected $container;

  /**
   * Get the string URL for a CSRF protected route.
   *
   * @param \Drupal\Core\Url $url
   *   The URL.
   *
   * @return string
   *   The URL string.
   */
    protected function getCsrfUrlString(Url $url): string
    {
        $context = new RenderContext();
        $renderer = $this->container->get('renderer');
        assert($renderer instanceof RendererInterface);
        $url = $renderer->executeInRenderContext($context, function () use ($url) {
            return $url->toString();
        });
        $bubbleable_metadata = $context->pop();
        assert($bubbleable_metadata instanceof BubbleableMetadata);
        $build = [
        '#plain_text' => $url,
        ];
        $bubbleable_metadata->applyTo($build);
        return (string) $renderer->renderPlain($build);
    }

    protected function ensureCsrfTokenSeed(): void
    {
        $metadata_bag = $this->container->get('session_manager.metadata_bag');
        assert($metadata_bag instanceof MetadataBag);
        $metadata_bag->stampNew();
    }
}
