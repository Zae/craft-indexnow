# Installation

## Composer
`composer require zae/craft-indexnow`

## Module
Add the module to your config: 

~~~php
/* config/app.php */
return [
    'modules' => [
        'indexNow' => Zae\IndexNow\Module::class,
    ],
    'bootstrap' => [
        'indexNow'
    ],
];
~~~

## Key
Generate a random key

~~~
./craft indexNow/key/generate
~~~

and add it to the config:

~~~php
/* config/general.php */
return [
    '*' => [
        'indexNowKey' => getenv('INDEXNOW_KEY'),
    ]
];
~~~
~~~dotenv
#.env
INDEXNOW_KEY=XXX
~~~

Add it to your webroot, make a file called `{key}.txt` and put the
key in the file. This means the `web` folder in this craft installation
or the webroot of your headless frontend if you use that.
~~~
/* web/{key}.txt */
{key}
~~~
