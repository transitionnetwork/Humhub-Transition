<?php
/**
 * Transition Movement
 * @link https://github.com/transitionnetwork/Humhub-Transition
 * @license https://github.com/transitionnetwork/Humhub-Transition/blob/main/docs/LICENCE.md
 * @author [Marc FARRE](https://marc.fun) for [Transition Movement](https://transitionnetwork.org/)
 */


namespace humhub\modules\transition\controllers;


use humhub\modules\admin\components\Controller;
use humhub\modules\admin\permissions\ManageUsers;
use humhub\modules\content\components\ContentContainerModuleManager;
use humhub\modules\space\models\Space;
use humhub\modules\transition\Module;
use humhub\modules\user\models\fieldtype\Select;
use humhub\modules\user\models\ProfileField;
use humhub\modules\user\models\User;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseInflector;
use yii\web\HttpException;


class AdminController extends Controller
{
    /**
     * @inheritdoc
     */
    public function getAccessRules()
    {
        return [
            ['permission' => ManageUsers::class],
        ];
    }

    /**
     * @return string
     */
    public function actionDefaultSpaces()
    {
        ContentContainerModuleManager::setDefaultState(User::class, 'transition', 1); //TODO: to remove
        $title = Yii::t('TransitionModule.config', 'Default spaces');
        $this->subLayout = '@admin/views/layouts/user';
        $this->appendPageTitle($title);

        // Get `region` field list
        $profileField = ProfileField::findOne(['internal_name' => 'region']);
        if ($profileField === null) {
            throw new HttpException(403, 'Error, the profile field "region" does not exists!');
        }
        if (!$profileField->fieldType instanceof Select) {
            throw new HttpException(403, 'Error, the profile field "region" is not a "select" type field!');
        }
        $select = $profileField->fieldType;
        $regionItems = $select->getSelectItems();

        // Get settings
        /** @var Module $module */
        $module = Yii::$app->getModule('transition');
        $settings = $module->settings;

        if ($post = Yii::$app->request->post()) {
            $defaultSpaces = [];
            foreach ($regionItems as $key => $label) {
                $key = BaseInflector::slug($key);
                $defaultSpaces[$key] = $post['space-' . $key] ?? null;
//                if (!isset($post['space-' . $key])) {
//                    continue;
//                }
                // Convert Space Guids to IDs
//                $defaultSpaces[$key] = array_keys(Space::find()->where(['guid' => $post['space-' . $key]])->indexBy('id')->all());
            }
            $settings->setSerialized('defaultSpaces', $defaultSpaces);
            $this->view->saved();
        }

        return $this->render('default-spaces', [
            'title' => $title,
            'regionItems' => $regionItems,
            'defaultSpaces' => (array)$settings->getSerialized('defaultSpaces'),
            'spaceItems' => ArrayHelper::map(Space::find()->all(), 'id', 'name'),
        ]);
    }
}