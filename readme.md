# QTE Rest
This plugin can be used for making forms, newsletter signup (mailchimp) and search.

## Settings
This plugin needs to be configured before uploaded to wordpress. There's a file called `settings.php` where you can configure which parts of the plugin you want to use. 
```php
$qterest_settings = array(
    'search' => false, // (boolean) Adds a search endpoint
    'contact' => true, // (boolean) Adds a contact endpoint for forms
    'mailchimp' => false, // (boolean) Adds a signup endpoint for mailchimp
);
```

## Contact
This part of the plugin handles the forms. 

### Creating forms
Creating forms can be done with `qterest_render_form(array $args, bool $echo = true)`

#### Arguments (array)
The following arguments are accepted
```php
$args = [
    'wrapper_class' => "qterest-form-container", // (string) Overrides the default wrapper class
    'form_class' => "qterest-form", // (string) Overrides the default form class
    'form_row_class' => "qterest-form-row", // (string) Overrides the default form row class
    'form_misc_class' => "qterest-form-misc", // (string) Overrides the default form misc class
    'form_title' => "Contact form", // (string) Adds a h3-tag with the given content
    'error_messages_class' => "qterest-error-messages", // (string) Overrides the default error message class
    'success_message_class' => "qterest-success-messages", // (string) Overrides the default success message class
    'form_fields_class' => "qterest-form-fields", // (string) Overrides the default form fields class
    'submit_label' => __("Submit", 'qterest'), // (string) Overrides the default submit label
    'submit_class' => 'button submit', // (string) Overrides the default submit class
    'fields' => $fields, // (array) Check the next section
]
```

#### Fields
The following fields are accepted
```php
$fields = [
    [
        'type' => 'text', // REQUIRED (string) Possible values are text, email, tel, hidden and textarea
        'name' => 'my_text_field', // REQUIRED (string) HTML Attribute name
        'placeholder' => 'Text', // (string) HTML Attribute placeholder
        'value' => 'some_value', // (string) HTML Attribute value
        'label' => 'Text', // (string) Label text
        'class' => 'form-control', // (string) Overrides default class
        'required' => true, // (string) Is the field required?
        'toggles_on' => 'my_checkbox_field', // (string) The id of the field that you want to toogle this field
    ],
    [
        'type' => 'checkbox', // REQUIRED (string) Possible values are text, email, tel, hidden and textarea
        'name' => 'my_checkbox_field', // REQUIRED (string) HTML Attribute name
        'value' => 'yes', // (string) HTML Attribute value
        'label' => 'Checkbox', // (string) Label text
        'class' => 'form-control', // (string) Overrides default class
        'toggles' => true, // (bool) Can this field toggle other fields? ONLY checbox
    ],
    [
        'type' => 'select', // REQUIRED (string) Possible values are select
        'name' => 'my_text_field', // REQUIRED (string) HTML Attribute name
        'placeholder' => 'Text', // (string) HTML Attribute placeholder
        'value' => 'some_value', // (string) HTML Attribute value
        'label' => 'Text', // (string) Label text
        'class' => 'form-control', // (string) Overrides default class
        'toggles_on' => 'my_checkbox_field', // (string) The id of the field that you want to toogle this field
        'options' => [
            [
                'name' => 'My first option', // REQUIRED (string) The name of this option
                'value' => 'my_first_option', // REQUIRED (string) The value of this option
            ],
            [
                'name' => 'My second option', // REQUIRED (string) The name of this option
                'value' => 'my_second_option', // REQUIRED (string) The value of this option
            ]
        ]
    ],
]
```


#### Misc
It's also possible to put misc in the fields array. These miscs are accepted
```php
$fields = [
	[
		'type' => 'title',
		'text' => 'Hejsan',
		'size' => '3' // This will be a h3 element
	],
	[
		'type' => 'paragraph',
		'text' => 'Lorem ipsum dolor sit'
	],
	[
		'type' => 'link',
		'href' => 'https://google.se',
		'text' => 'Google.se'
	]
]
```

#### Example form
This is a example of a very basic contact form
```php
qterest_render_form([
    'form_title' => 'Simple contact form',
    'submit_label' => __('Send', 'text_domain')
    'fields' => [
        [
            'type' => 'text',
            'name' => 'name',
            'label' => 'Name',
            'placeholder' => 'Sven Svensson',
        ],
        [
            'type' => 'email',
            'name' => 'email', // This field is required by the plugin
            'label' => 'Email *',
            'required' => true,
            'placeholder' => 'sven.svensson@company.com',
        ],
        [
            'type' => 'textarea',
            'name' => 'message',
            'label' => 'Message',
            'placeholder' => 'Hello! I have a question about...',
        ],
        [
            'type' => 'checkbox',
            'name' => 'gdpr',
            'label' => 'I agree to the following <a href="google.se">terms</a>',
            'required' => true,
        ]
    ]
]);
```

### Filters and Actions
There is a few filters and actions availible for you to customize som things

#### Messages
Example for customizing messages
```php
function theme_custom_qterest_messages($messages) {
	
	$messages['success'] = __("Thank you! We will contact you as soon as possible!", 'text_domain');

	return $messages;

}
add_filter('qterest_contact_messages', 'theme_custom_qterest_messages');
```

The following messages can be changed
* name_empty
* email_empty
* email_invalid
* failed
* success
* mail_subject
* mail_body

#### Fomatting keys (ADMIN)
When looking at a contact request you can see that the name of the field identifies the value. This can be changed by adding a filter to display a better name for the customer. The filter name looks like this `qterest_format_key_${key}`.
```php
function theme_format_keys_bulk($key) {
    return "Does this person have a dog?";
}
add_filter('qterest_format_key_has_dog', 'theme_format_key_has_dog');
```

You can also bulk format keys with the filter `qterest_format_bulk_keys`.
```php
function theme_format_bulk_keys($key) {
    return [
        'has_dog' => "Does this person have a dog?",
        'email' => __("Email address", 'text_domain'), // TIP: You can use localization here if the site is multi language
    ]
}
add_filter('qterest_format_bulk_keys', 'theme_format_bulk_keys');
```

## Comming soon
* Search Documentation
* Mailchimp Documentation

## Future plans
* Form builder in React
* Frontend form in React

---
_This plugin is built and maintained by Noah Olsson_