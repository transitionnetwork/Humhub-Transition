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
use humhub\modules\transition\jobs\SyncAllSpaceAdmins;
use humhub\modules\user\models\User;
use Yii;

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

    /**
     * @var int Group ID for the administrators of spaces
     */
    public $spaceAdminsGroupId;


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

        Yii::$app->queue->push(new SyncAllSpaceAdmins());
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
