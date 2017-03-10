Action Bar Documentation
=======================

The widget allows you to use the following properties and methods.

Widget Class
---------------

### Public Properties

  - `registerCss`: *boolean*, whether the CSS file should be registered.

  - `options`: *array*, the HTML attributes for the widget container tag. See [renderTagAttributes()] for details on how attributes are being rendered.

  - `renderContainer`: *boolean*, whether the action bar content should be included in a div container.

  - `containerOptions`: *array*, the HTML attributes for the content container tag. This is only used when `renderContainer` is true. See [renderTagAttributes()] for details on how attributes are being rendered.

  - `templates`: *array*, templates used to render action bar elements, in addition, may be specified the array keys with the HTML attributes for the container tag. Tokens enclosed within curly brackets are treated as controller action IDs (also called *element names* in the context of action column). They will be replaced by the corresponding element rendering values specified in `elements`. For example, the token `{bulk-actions}` will be replaced by the result of the value `elements['bulk-actions']`. If a value cannot be found, the token will be replaced with an empty string. See [renderTagAttributes()] for details on how attributes are being rendered.

  - `elements`: *array*, elements rendering values. The array keys are the element names (without curly brackets), and the values are the corresponding element rendering values.

  - `grid`: *string*, the grid ID. This property must be set if used the Bulk Actions default element.

  - `bulkActionsPrompt`: *string*, the text to the call to action for the Bulk Actions.

  - `bulkActionsItems`: *array*, the option data items for the Bulk Actions. See [dropDownList()] for details on how this is to be rendered.

  - `bulkActionsOptions`: *array*, the Bulk Actions options in terms of name-value pairs. The following attributes for the select option tag are specially handled:
    - url: string, used to send the array with the selected rows (based on the AJAX request) to the clienton the specified URL.
    - data-confirm: string, displays a confirm box before deleting selected items.

  See [dropDownList()] for details on how this is to be rendered.

### Public Methods

  - `t()`: see [t()] for details on how this is to function.

DeleteMultipleAction Class
--------------------------

### Public Properties

  - `modelClass`: *string*, the model class name. This property must be set.

  - `primaryKey`: *string*, the primary key name.

  - `beforeDeleteCallback`: *callable*, a callback that will be called after deleting selected items. The signature of the callback should be as `function ($action)`, where `$action` is the current action object.

  - `afterDeleteCallback`: *callable*, a callback that will be called after deleting selected items. The signature of the callback should be as `function ($action)`, where `$action` is the current action object.

  - `redirectUrl`: *string|array*, the URL to be redirected to after deleting.

### Public Methods

  - `redirect()`: redirects the browser to the previous page or the specified URL from `redirectUrl`.

[renderTagAttributes()]:http://www.yiiframework.com/doc-2.0/yii-helpers-basehtml.html#renderTagAttributes()-detail
[dropDownList()]:http://www.yiiframework.com/doc-2.0/yii-helpers-basehtml.html#dropDownList()-detail
[t()]:http://www.yiiframework.com/doc-2.0/yii-baseyii.html#t()-detail