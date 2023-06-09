<?php

declare(strict_types=1);

namespace GatoGraphQL\GatoGraphQL\ModuleResolvers;

use GatoGraphQL\GatoGraphQL\ContentPrinters\CollapsibleContentPrinterTrait;
use WP_Post;

use function get_posts;

trait CommonModuleResolverTrait
{
    use CollapsibleContentPrinterTrait;

    /**
     * @param string[]|null $applicableItems
     */
    protected function getDefaultValueDescription(
        string $blockTitle,
        ?array $applicableItems = null
    ): string {
        $applicableItems ??= $this->getDefaultValueApplicableItems($blockTitle);
        return $this->getSettingsInfoContent(
            sprintf(
                \__('%s %s', 'gato-graphql'),
                \__('This is the default value for the schema configuration.', 'gato-graphql'),
                $this->getCollapsible(
                    sprintf(
                        '<br/>%s<ul><li>%s</li></ul>',
                        \__('It will be used whenever:', 'gato-graphql'),
                        implode(
                            '</li><li>',
                            $applicableItems
                        )
                    ),
                )
            )
        );
    }

    /**
     * @return string[]
     */
    protected function getDefaultValueApplicableItems(string $blockTitle): array
    {
        return [
            \__('The endpoint does not have a Schema Configuration assigned to it', 'gato-graphql'),
            \__('The endpoint has Schema Configuration <code>"None"</code> assigned to it', 'gato-graphql'),
            sprintf(
                \__('Block <code>%s</code> has not been added to the selected Schema Configuration', 'gato-graphql'),
                $blockTitle
            ),
            \__('The block in the Schema Configuration has value <code>"Default"</code>', 'gato-graphql')
        ];
    }

    protected function getPressCtrlToSelectMoreThanOneOptionLabel(): string
    {
        return \__('Press <code>ctrl</code> or <code>shift</code> keys to select more than one.', 'gato-graphql');
    }

    protected function getSettingsInfoContent(string $content): string
    {
        return sprintf(
            '<span class="settings-info">%s</span>',
            $content
        );
    }

    /**
     * @return WP_Post[]
     */
    protected function getSchemaEntityListCustomPosts(string $customPostType): array
    {
        return get_posts([
            'posts_per_page' => -1,
            'post_type' => $customPostType,
            'post_status' => 'publish',
        ]);
    }
}
