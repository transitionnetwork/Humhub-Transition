<?php

/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */

namespace humhub\modules\transition;

use humhub\libs\DynamicConfig;
use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\content\components\ContentContainerModuleManager;
use humhub\modules\transition\jobs\SyncAllSpaceHosts;
use humhub\modules\ui\view\helpers\ThemeHelper;
use humhub\modules\user\models\User;
use Yii;

class Module extends ContentContainerModule
{
    public const THEME_NAME = 'transition';

    /**
     * @var string defines the icon
     */
    public $icon = 'eye';

    /**
     * @var string defines path for resources, including the screenshots' path for the marketplace
     */
    public $resourcesPath = 'resources';

    /**
     * @var int Group ID for the "Space hosts" group (for spaces' administrators and moderators)
     */
    public $spaceHostsGroupId;


    public function getName()
    {
        return 'Transition Movement';
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerTypes()
    {
        return [User::class];
    }

    /**
     * @inheritdoc
     */
    public function disable()
    {
        $this->disableTheme();
        parent::disable();
    }

    /**
     * @inheritdoc
     */
    public function enable()
    {
        if (parent::enable()) {
            $this->enableTheme();
            ContentContainerModuleManager::setDefaultState(User::class, 'transition', 1);
            Yii::$app->queue->push(new SyncAllSpaceHosts());
            return true;
        }
        return false;
    }

    /**
     * @return void
     */
    private function enableTheme()
    {
        // Check if already active
        foreach (ThemeHelper::getThemeTree(Yii::$app->view->theme) as $theme) {
            if ($theme->name === self::THEME_NAME) {
                return;
            }
        }

        $theme = ThemeHelper::getThemeByName(self::THEME_NAME);
        if ($theme !== null) {
            $theme->activate();
            DynamicConfig::rewrite();
        }
    }

    /**
     * @return void
     */
    private function disableTheme()
    {
        foreach (ThemeHelper::getThemeTree(Yii::$app->view->theme) as $theme) {
            if ($theme->name === self::THEME_NAME) {
                $ceTheme = ThemeHelper::getThemeByName('HumHub');
                $ceTheme->activate();
                break;
            }
        }
    }
}
