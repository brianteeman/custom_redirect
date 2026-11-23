<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  System.grouploginredirect
 *
 * @copyright   (C) 2026 Brian Teeman
 * @license     GNU General Public License version 2 or later
 */

namespace Joomla\Plugin\System\GroupLoginRedirect\Extension;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\Route;
use Joomla\Component\Users\Administrator\Model\UserModel;
use Joomla\Event\SubscriberInterface;
use Joomla\Registry\Registry;

/**
 * Redirect frontend users to a menu item based on their assigned groups.
 */
final class GroupLoginRedirect extends CMSPlugin implements SubscriberInterface
{
    protected $autoloadLanguage = true;

    /**
     * Subscribe to successful user login events.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onUserAfterLogin' => 'onUserAfterLogin',
        ];
    }

    /**
     * Redirect a frontend user to the first matching configured menu item.
     *
     * @param   array  $options  Login event options
     *
     * @return  void
     */
    public function onUserAfterLogin(array $options): void
    {
        $app = $this->getApplication();

        // Do not interfere with administrator logins or CLI requests.
        if (!$app instanceof CMSApplication || !$app->isClient('site')) {
            return;
        }

        $user = $options['user'] ?? null;

        if (!$user || empty($user->id) || empty($user->groups)) {
            return;
        }

        $rules = $this->params->get('rules', []);

        if (!is_array($rules) || $rules === []) {
            return;
        }

        $userGroups = array_map('intval', array_keys($user->groups));

        foreach ($rules as $rule) {
            $groupId = (int) ($rule->usergroup ?? 0);
            $menuItemId = (int) ($rule->menuitem ?? 0);

            if (!$groupId || !$menuItemId || !in_array($groupId, $userGroups, true)) {
                continue;
            }

            $menuItem = $app->getMenu()->getItem($menuItemId);

            if (!$menuItem) {
                continue;
            }

            // Build an internal URL from the selected menu item. Route::link()
            // returns a site-relative URL suitable for the current application.
            $url = Route::_($menuItem->link . '&Itemid=' . $menuItem->id, false);

            $app->redirect($url);
            $app->close();

            return;
        }
    }
}
