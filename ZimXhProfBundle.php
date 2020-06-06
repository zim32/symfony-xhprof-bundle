<?php

namespace Zim\XhProfBundle;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ZimXhProfBundle extends Bundle
{
    public static $needToCollect = false;

    public function boot()
    {
        if (
            !isset($_ENV['ZIM_XHPROF_ENABLE']) ||
            !$_ENV['ZIM_XHPROF_ENABLE'] ||
            false === extension_loaded('tideways_xhprof') ||
            false === $this->checkCondition()
        ) {
            return;
        }

        self::$needToCollect = true;

        tideways_xhprof_enable(TIDEWAYS_XHPROF_FLAGS_MEMORY);
    }

    public function checkCondition()
    {
        if (
            !isset($_ENV['ZIM_XHPROF_CONDITION']) ||
            !$_ENV['ZIM_XHPROF_CONDITION']
        ) {
            return true;
        }

        $expressionLanguage = new ExpressionLanguage();
        $expressionLanguage->addFunction(ExpressionFunction::fromPhp('array_key_exists', 'key_exists'));

        $context = new \stdClass();

        $context->get = $_GET;
        $context->post = $_POST;
        $context->server = $_SERVER;
        $context->cookie = $_COOKIE;

        return $expressionLanguage->evaluate($_ENV['ZIM_XHPROF_CONDITION'], ['ctx' => $context]);
    }

}