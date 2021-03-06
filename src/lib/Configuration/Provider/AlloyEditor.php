<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformRichText\Configuration\Provider;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformRichText\Configuration\UI\Mapper\OnlineEditorConfigMapper;
use EzSystems\EzPlatformRichText\SPI\Configuration\Provider;
use EzSystems\EzPlatformRichTextBundle\DependencyInjection\Configuration\Parser\FieldType\RichText;

/**
 * AlloyEditor configuration provider.
 *
 * @internal For internal use by RichText package
 */
final class AlloyEditor implements Provider
{
    /** @var array */
    private $alloyEditorConfiguration;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \EzSystems\EzPlatformRichText\Configuration\UI\Mapper\OnlineEditorConfigMapper */
    private $onlineEditorConfigMapper;

    public function __construct(
        array $alloyEditorConfiguration,
        ConfigResolverInterface $configResolver,
        OnlineEditorConfigMapper $onlineEditorConfigMapper
    ) {
        $this->alloyEditorConfiguration = $alloyEditorConfiguration;
        $this->configResolver = $configResolver;
        $this->onlineEditorConfigMapper = $onlineEditorConfigMapper;
    }

    public function getName(): string
    {
        return 'alloyEditor';
    }

    /**
     * @return array AlloyEditor config
     */
    public function getConfiguration(): array
    {
        return [
            'extraPlugins' => $this->getExtraPlugins(),
            'extraButtons' => $this->getExtraButtons(),
            'classes' => $this->getCssClasses(),
            'attributes' => $this->getDataAttributes(),
        ];
    }

    /**
     * @return array Custom plugins
     */
    private function getExtraPlugins(): array
    {
        return $this->alloyEditorConfiguration['extra_plugins'] ?? [];
    }

    /**
     * @deprecated 3.0.0 The alternative and more flexible solution will be introduced.
     * @deprecated 3.0.0 So you will need to update Online Editor Extra Buttons as part of eZ Platform 3.x upgrade.
     *
     * @return array Custom buttons
     */
    private function getExtraButtons(): array
    {
        if (empty($this->alloyEditorConfiguration['extra_buttons'])) {
            return [];
        }

        @trigger_error(
            '"ezrichtext.alloy_editor.extra_buttons" is deprecated since v2.5.1. ' .
            'There will be new and more flexible solution to manage buttons in Online Editor in 3.0.0',
            E_USER_DEPRECATED
        );

        return $this->alloyEditorConfiguration['extra_buttons'];
    }

    /**
     * Get custom CSS classes defined by the SiteAccess-aware configuration.
     *
     * @return array
     */
    private function getCssClasses(): array
    {
        return $this->onlineEditorConfigMapper->mapCssClassesConfiguration(
            $this->getSiteAccessConfigArray(RichText::CLASSES_SA_SETTINGS_ID)
        );
    }

    /**
     * Get custom data attributes defined by the SiteAccess-aware configuration.
     *
     * @return array
     */
    private function getDataAttributes(): array
    {
        return $this->onlineEditorConfigMapper->mapDataAttributesConfiguration(
            $this->getSiteAccessConfigArray(RichText::ATTRIBUTES_SA_SETTINGS_ID)
        );
    }

    /**
     * Get configuration array from the SiteAccess-aware configuration, checking first for its existence.
     *
     * @param string $paramName
     *
     * @return array
     */
    private function getSiteAccessConfigArray(string $paramName): array
    {
        return $this->configResolver->hasParameter($paramName)
            ? $this->configResolver->getParameter($paramName)
            : [];
    }
}
