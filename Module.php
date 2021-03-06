<?php
/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */

namespace humhub\modules\transition;

use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\content\components\ContentContainerModuleManager;
use humhub\modules\user\models\User;

class Module extends ContentContainerModule
{

    /**
     * @var string defines the icon
     */
    public $icon = 'eye';

    /**
     * @var string defines path for resources, including the screenshots' path for the marketplace
     */
    public $resourcesPath = 'resources';


    public function getName()
    {
        return 'Transition Movement';
    }


    /**
     * @inheritdoc
     */
    public function enable()
    {
        if (!parent::enable()) {
            return false;
        }
        ContentContainerModuleManager::setDefaultState(User::class, 'transition', 1);
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerTypes()
    {
        return [
            User::class,
        ];
    }
}
