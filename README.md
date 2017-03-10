Action Bar
==========

Action Bar is a Yii 2 widget that render the drop-down list for manipulation selected GridView items and control buttons. The widget permits you to fully customize elements.

![Screenshot](https://cloud.githubusercontent.com/assets/8091481/3558541/f447ff48-093c-11e4-91ad-b784c929ed32.png)

Installation
------------

You can install the widget in two ways, using [Composer] or by downloading an archive file.

### Installing via Composer

Run the following commands under your application folder:

```
php composer.phar require --prefer-dist "mickgeek/yii2-actionbar *"
```

> Note: You can just do `composer update --prefer-dist` if pre-adding the following to `require` section of your `composer.json`:
>
>     "mickgeek/yii2-actionbar": "*"
>

### Installing from an Archive File

Download [the archive file], unpack it to `path/to/app/vendor/mickgeek` folder and modify the main configuration file like this:

```php
$vendorDir = dirname(__DIR__) . '/vendor';

return [
    'vendorPath' => $vendorDir,
    'extensions' => array_merge(
        require($vendorDir . '/yiisoft/extensions.php'),
        [
            'mickgeek/yii2-actionbar' => [
                'name' => 'mickgeek/yii2-actionbar',
                'version' => '9999999-dev',
                'alias' => [
                    '@mickgeek/actionbar' => $vendorDir . '/mickgeek/yii2-actionbar',
                ],
            ],
        ]
    ),
    ...
];
```

where `$vendorDir` is the path to the directory that stores vendor files.

Usage
-----

```php
use mickgeek\actionbar\Widget as ActionBar;

<?= ActionBar::widget([
    'grid' => 'user-grid',
]) ?>
```

But first, add the action to your controller:

```php
public function actions()
{
    return [
        'delete-multiple' => [
            'class' => 'mickgeek\actionbar\DeleteMultipleAction',
            'modelClass' => 'app\models\User',
        ],
    ];
}
```

> Note: You can write your action without using `DeleteMultipleAction` class.

> Tip: For information about properties and methods of the widget, see the bundled `DOCUMENTATION.md`.

### CSRF validation
For enable CSRF validation make sure you have ```<?= Html::csrfMetaTags() ?>``` in main layout.

Examples
--------

Below are two examples showing some features of the widget.

### Advanced Bulk Actions

![Advanced Bulk Actions Screenshot](https://cloud.githubusercontent.com/assets/8091481/3558567/1e257c1e-093d-11e4-9441-abcfc6f58da2.png)

The code in the view:

```php
use yii\helpers\Url;
use mickgeek\actionbar\Widget as ActionBar;

<?= ActionBar::widget([
    'grid' => 'user-grid',
    'templates' => [
        '{bulk-actions}' => ['class' => 'col-xs-4'],
        '{create}' => ['class' => 'col-xs-8 text-right'],
    ],
    'bulkActionsItems' => [
        'Update Status' => [
            'status-active' => 'Active',
            'status-blocked' => 'Blocked',
        ],
        'General' => ['general-delete' => 'Delete'],
    ],
    'bulkActionsOptions' => [
        'options' => [
            'status-active' => [
                'url' => Url::toRoute(['update-status', 'status' => 'active']),
                'disabled' => !Yii::$app->user->can('updateUserStatus'),
            ],
            'status-blocked' => [
                'url' => Url::toRoute(['update-status', 'status' => 'blocked']),
                'disabled' => !Yii::$app->user->can('updateUserStatus'),
            ],
            'general-delete' => [
                'url' => Url::toRoute('delete-multiple'),
                'data-confirm' => 'Are you sure?',
                'disabled' => !Yii::$app->user->can('deleteUser'),
            ],
        ],
        'class' => 'form-control',
    ],
]) ?>
```

The code in the User controller:

```php
public function actions()
{
    return [
        'delete-multiple' => [
            'class' => 'mickgeek\actionbar\DeleteMultipleAction',
            'modelClass' => 'app\models\User',
            'beforeDeleteCallback' => function ($action) {
                if (!Yii::$app->user->can('deleteOwnAccount', Yii::$app->getRequest()->post('ids'))) {
                    Yii::$app->getSession()->setFlash('error', 'You cannot delete your own account.');

                    $action->redirect();
                    Yii::$app->end();
                }
            },
            'afterDeleteCallback' => function ($action) {
                Yii::$app->getSession()->setFlash('success', 'The selected users have been deleted successfully.');
            },
        ],
    ];
}

public function actionUpdateStatus($status)
{
    ...
}
```

### Custom Buttons

![Custom Buttons Screenshot](https://cloud.githubusercontent.com/assets/8091481/3534952/d53d4fe6-07f6-11e4-8598-97fdb7ff101a.png)

The code:

```php
use mickgeek\actionbar\Widget as ActionBar;

/* @var $model app\models\User */
<?= ActionBar::widget([
    'templates' => [
        '{back}' => ['class' => 'col-xs-4'],
        '{update} {delete}' => ['class' => 'col-xs-8 text-right'],
    ],
    'elements' => [
        'back' => Html::a(
            '<span class="glyphicon glyphicon-chevron-left"></span> ' . 'Back',
            ['/users/index'],
            ['class' => 'btn btn-default']
        ),
        'update' => Html::a(
            '<span class="glyphicon glyphicon-pencil"></span> ' . 'Update',
            ['/users/update', 'id' => $model->id],
            ['class' => 'btn btn-default']
        ),
        'delete' => Html::a(
            '<span class="glyphicon glyphicon-trash"></span> ' . 'Delete',
            ['/users/delete', 'id' => $model->id],
            ['class' => 'btn btn-default']
        ),
    ],
]) ?>
```

License
-------

This extension is released under the BSD 3-Clause License. See the bundled `LICENSE.md` for details.

[Composer]:https://getcomposer.org
[the archive file]:https://github.com/mickgeek/yii2-actionbar/archive/master.zip
