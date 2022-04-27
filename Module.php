<?php
/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */

namespace humhub\modules\transition;

class Module extends \humhub\components\Module
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
}
