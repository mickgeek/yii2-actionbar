<?php
namespace mickgeek\actionbar;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\i18n\PhpMessageSource;

/**
 * \mickgeek\actionbar\Widget renders the drop-down list to manipulation selected GridView items and control buttons.
 *
 * @author Oleg Belostotskiy <olgblst@gmail.com>
 */
class Widget extends \yii\base\Widget
{
    /**
     * The session variable name associated with the URL to be remembered.
     */
    const RETURN_URL_PARAM = '__actionBarUrl';

    /**
     * @var boolean whether the CSS file should be registered.
     */
    public $registerCss = true;
    /**
     * @var array the HTML attributes for the widget container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = ['class' => 'widget-action-bar'];
    /**
     * @var boolean whether the action bar content should be included in a div container.
     */
    public $renderContainer = true;
    /**
     * @var array the HTML attributes for the content container tag. This is only used when [[renderContainer]] is true.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $containerOptions = ['class' => 'row'];
    /**
     * @var array templates used to render action bar elements, in addition, may be specified the array keys with
     * the HTML attributes for the container tag. Tokens enclosed within curly brackets are treated as
     * controller action IDs (also called *element names* in the context of action column). They will be replaced
     * by the corresponding element rendering values specified in [[elements]]. For example,
     * the token `{bulk-actions}` will be replaced by the result of the value `elements['bulk-actions']`.
     * If a value cannot be found, the token will be replaced with an empty string.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     * @see [[elements]]
     */
    public $templates = [
        '{bulk-actions}' => ['class' => 'col-xs-4'],
        '{create}' => ['class' => 'col-xs-8 text-right'],
    ];
    /**
     * @var array elements rendering values. The array keys are the element names (without curly brackets),
     * and the values are the corresponding element rendering values.
     */
    public $elements = [];
    /**
     * @var string the grid ID. This property must be set if used the Bulk Actions default element.
     */
    public $grid;
    /**
     * @var string the text to the call to action for the Bulk Actions.
     */
    public $bulkActionsPrompt;
    /**
     * @var array the option data items for the Bulk Actions.
     * @see \yii\helpers\Html::dropDownList() for details on how this is to be rendered.
     */
    public $bulkActionsItems = [];
    /**
     * @var array the Bulk Actions options in terms of name-value pairs. The following attributes
     * for the select option tag are specially handled:
     *
     * - url: string, used to send the array with the selected rows (based on the AJAX request) to the client
     *   on the specified URL.
     * - data-confirm: string, displays a confirm box before deleting selected items.
     *
     * @see \yii\helpers\Html::dropDownList() for details on how this is to be rendered.
     */
    public $bulkActionsOptions = ['class' => 'form-control'];

    /**
     * @var string the Bulk Actions ID.
     */
    private $_bulkActionsId = 'bulk-actions';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->id;
        }
        $this->registerTranslations();
        $this->initDefaultElements();

        Url::remember('', self::RETURN_URL_PARAM);
    }

    /**
     * Registers translation files.
     */
    public function registerTranslations()
    {
        if (!isset(Yii::$app->i18n->translations['mickgeek/actionbar/*'])) {
            Yii::$app->i18n->translations['mickgeek/actionbar/*'] = [
                'class' => PhpMessageSource::className(),
                'sourceLanguage' => 'en-US',
                'basePath' => '@mickgeek/actionbar/messages',
                'fileMap' => [
                    'mickgeek/actionbar/widget' => 'widget.php',
                ],
            ];
        }
    }

    /**
     * @see \Yii::t() for details on how this is to function.
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('mickgeek/actionbar/' . $category, $message, $params, $language);
    }

    /**
     * Initializes the default elements.
     */
    protected function initDefaultElements()
    {
        if (!isset($this->elements['bulk-actions'])) {
            if ($this->bulkActionsPrompt === null) {
                $this->bulkActionsPrompt = $this->t('widget', 'Bulk Actions');
            }
            if (empty($this->bulkActionsItems)) {
                $this->bulkActionsItems = [
                    $this->t('widget', 'General') => [
                        'general-delete' => $this->t('widget', 'Delete'),
                    ],
                ];
            }
            if (isset($this->bulkActionsOptions['id'])) {
                $this->_bulkActionsId = $this->bulkActionsOptions['id'];
            }
            if (!isset($this->bulkActionsOptions['options'])) {
                $this->bulkActionsOptions = ArrayHelper::merge($this->bulkActionsOptions, [
                    'options' => [
                        'general-delete' => [
                            'url' => Url::toRoute('delete-multiple'),
                            'data-confirm' => $this->t('widget', 'Are you sure you want to delete these items?'),
                        ],
                    ],
                ]);
            }

            $this->elements['bulk-actions'] = Html::dropDownList('bulkactions', null, $this->bulkActionsItems,
                ArrayHelper::merge([
                    'prompt' => $this->bulkActionsPrompt,
                    'id' => $this->_bulkActionsId,
                    'disabled' => $this->grid === null,
                ], $this->bulkActionsOptions)
            );
        }
        if (!isset($this->elements['create'])) {
            $this->elements['create'] = Html::a(
                '<span class="glyphicon glyphicon-plus-sign"></span> ' . $this->t('widget', 'Create New'),
                Url::toRoute('create'),
                ['class' => 'btn btn-default']
            );
        }
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        echo Html::beginTag('div', $this->options) . "\n";
        echo $this->renderContainer ? Html::beginTag('div', $this->containerOptions) . "\n" : '';
        foreach ($this->templates as $template => $options) {
            if (!is_array($options)) {
                echo $this->renderElements($options);
            } else {
                echo Html::beginTag('div', $options);
                echo $this->renderElements($template, $options);
                echo Html::endTag('div');
            }
        }
        echo $this->renderContainer ? Html::endTag('div') . "\n" : '';
        echo Html::endTag('div') . "\n";

        if ($this->registerCss) {
            ActionBarAsset::register($this->view);
        }
    }

    /**
     * Renders elements.
     *
     * @param string $template the elements template.
     * @return string the rendering result.
     */
    protected function renderElements($template)
    {
        return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) {
            $name = $matches[1];
            if (isset($this->elements[$name])) {
                if ($name === 'bulk-actions' && $this->grid !== null) {
                    $id = $this->options['id'];
                    $this->view->registerJs("$('#{$id} #{$this->_bulkActionsId}').change(function() {
                        if (this.value) {
                            var ids = $('#{$this->grid}').yiiGridView('getSelectedRows'),
                                dataConfirm = this.options[this.selectedIndex].getAttribute('data-confirm'),
                                opts = this.options[this.selectedIndex],
                                url = opts.getAttribute('url');

                            if (!ids.length) {
                                alert('" . $this->t('widget', 'Please select one or more items from the list.') . "');
                                this.value = '';
                            } else if (dataConfirm && !confirm(dataConfirm)) {
                                this.value = '';
                                return;
                            } else if (url) {
                                var form = $('<form action=' + url + ' method=\"POST\"></form>'),
                                    csrfParam = $('meta[name=csrf-param]').prop('content'),
                                    csrfToken = $('meta[name=csrf-token]').prop('content');

                                if (csrfParam) {
                                    form.append('<input type=\"hidden\" name=' + csrfParam + ' value=' + csrfToken + ' />');
                                }
                                $.each(ids, function(index, id) {
                                    form.append('<input type=\"hidden\" name=\"' + (opts.getAttribute('name') ? opts.getAttribute('name') : 'ids') + '[]\" value=' + id + ' />');
                                });
                                form.appendTo('body').submit();
                            }
                        }
                    });");
                }

                return $this->elements[$name];
            } else {
                return '';
            }
        }, $template);
    }
}
