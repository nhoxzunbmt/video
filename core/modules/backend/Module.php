<?php

namespace Phanbook\Backend;

use Phalcon\Loader;
use Phalcon\DiInterface;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\ModuleDefinitionInterface;

class Module implements ModuleDefinitionInterface
{
    public function registerAutoloaders(DiInterface $di = null)
    {
        $loader = new Loader();

        $loader->registerNamespaces([
            'Phanbook\Backend\Controllers' => __DIR__ . '/controllers/',
            'Phanbook\Backend\Forms'       => __DIR__ . '/forms/'
        ]);

        $loader->register();
    }

    /**
     * Register the services here to make them general
     * or register in the ModuleDefinition to make them module-specific
     */
    public function registerServices(DiInterface $di)
    {
        //Read configuration
        $config = include __DIR__ . "/config/config.php";

        $configGlobal = $di->getConfig();

        // The URL component is used to generate all kind of urls in the application
        // The URL component is used to generate all kind of urls in the application
        $di->set('url', function () use ($config, $configGlobal) {
            $url = new Url();
            if (isset($configGlobal->application->staticBaseUri)) {
                $url->setStaticBaseUri($configGlobal->application->staticBaseUri);
            } else {
                $url->setStaticBaseUri('/');
            }

            if (isset($config->application->baseUri)) {
                $url->setBaseUri($config->application->baseUri);
            } else {
                $url->setBaseUri('/');
            }

            return $url;
        });
        //Registering a dispatcher
        $di->set('dispatcher', function () use ($di) {
            //Create/Get an EventManager
            $eventsManager = new EventsManager();
            //Attach a listener
            $eventsManager->attach('dispatch', function ($event, $dispatcher, $exception) use ($di) {
                //controller or action doesn't exist
                if ($event->getType() == 'beforeException') {
                    $message  = $exception->getMessage();
                    $response = $di->getResponse();
                    switch ($exception->getCode()) {
                        case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                            $response->redirect();
                            return false;
                        case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                            $response->redirect('action-not-found?msg=' . $message);
                            return false;

                        case Dispatcher::EXCEPTION_CYCLIC_ROUTING:
                            $response->redirect('cyclic-routing?msg=' . $message);
                            return false;
                    }
                }
            });
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace("Phanbook\Backend\Controllers");
            $dispatcher->setEventsManager($eventsManager);
            return $dispatcher;
        });
        /**
         * Setting up the view component
         */
        $di->set(
            'view',
            function () use ($config) {
                $view = new View();
                $view->setViewsDir($config->application->viewsDir);
                $view->disableLevel([View::LEVEL_MAIN_LAYOUT => true, View::LEVEL_LAYOUT => true]);
                $view->registerEngines(['.volt' => 'volt']);

                // Create an event manager
                $eventsManager = new EventsManager();

                // Attach a listener for type 'view'
                $eventsManager->attach(
                    'view',
                    function ($event, $view) {
                        if ($event->getType() == 'notFoundView') {
                            throw new \Exception('View not found!!! (' . $view->getActiveRenderPath() . ')');
                        }
                    }
                );

                // Bind the eventsManager to the view component
                $view->setEventsManager($eventsManager);

                return $view;
            }
        );
        $configMenu = include __DIR__ . "/config/config.menu.php";
        $di->setShared('menuStruct', function () use ($configMenu) {
            // if structure received from db table instead getting from $config
            // we need to store it to cache for reducing db connections
            $struct = $configMenu->get('menuStruct')->toArray();
            return $struct;
        });
    }
}
