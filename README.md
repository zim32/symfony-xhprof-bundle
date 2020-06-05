### Installation

#### Install tideways_xhprof extension
See https://github.com/tideways/php-xhprof-extension for instructions

#### Install this bundle

```
composer require zim32/xhprof-profiler-bundle
```

Open your **.env.local** file and add

```
ZIM_XHPROF_ENABLE=1
```

This will activate profiler.

Set ZIM_XHPROF_ENABLE=0, or remove entirely, to deactivate profiler

Navigate to debug toolbar and click on XHProf menu item