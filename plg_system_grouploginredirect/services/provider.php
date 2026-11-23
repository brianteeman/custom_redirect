<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  System.grouploginredirect
 *
 * @copyright   (C) 2026 Brian Teeman
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\CMS\Extension\Service\Provider\PluginDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\PluginInterface;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;

return new class implements ServiceProviderInterface {
    public function register(Container $container): void
    {
        $container->registerServiceProvider(new PluginDispatcherFactory('system'));
        $container->registerServiceProvider(new MVCFactory('Joomla\\CMS\\Component\\'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('Joomla\\CMS\\Component\\'));
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $plugin = new GroupLoginRedirect(
                    $container->get(\Joomla\Event\DispatcherInterface::class),
                    (array) $container->get('Config')->get(' plg_system_grouploginredirect')
                );

                $plugin->setApplication(\Joomla\CMS\Factory::getApplication());

                return $plugin;
            }
        );
    }
};
