<?php

define('ZF2_PATH',dirname(__FILE__) .'zf2';

chdir(dirname(__DIR__));

require_once(ZF2_PATH . '/Zend/Loader/AutoloaderFactory.php';
Zend\Loader\AutoloaderFactory::factory();

$appConfig = include 'config/application.config.php';

$listenerOptions = new Zend\Module\Listener\ListenerOptions(
    $appConfig['module_listener_options']);
$defaultListeners = new Zend\Module\Listener\DefaultListenerAggregate($listenerOptions);
$defaultListeners->getConfigListener()
    ->addConfigGlobPath("config/autoload/{,*.}{global,local}.config.php");
$moduleManager = new Zend\Modules\Manager($appConfig['modules']);
$moduleManager->events()->attachAggregate($defaultListeners);
$moduleManager->loadModules();

$bootstrap = new Zend\Mvc\Bootstrap(
    $defaultListeners->getConfigListener()->getMergedConfig());
$application = new Zend\Mvc\Application;
$bootstrap->bootstrap($application);

$application->run()->send();
