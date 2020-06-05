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

Navigate to debug toolbar and click on XHProf menu item. Profit

![Picture](https://i.ibb.co/pLkYyHB/06-05-21-24-57-921b9b.png)

### Conditional profiling

If you need to enable profiling based on some condition, you can use special variable **ZIM_XHPROF_CONDITION**

The value of this variable is passed to Symfony ExpressionLanguage component, with
one variable called **ctx**. This expression must evaluate to bool value (true - enable profiling, false - disable)

CTX is initialized as follows:

```
$ctx->get = $_GET;
$ctx->post = $_POST;
$ctx->server = $_SERVER;
$ctx->cookie = $_COOKIE;
```

So for example to enable profiler only for your user session, you can use

```
ZIM_XHPROF_CONDITION='key_exists("PHPSESSID", ctx.cookie) && ctx.cookie["PHPSESSID"] === "bd465k5q41n8jsdd8iu99mmchk"'
```