<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  System.grouploginredirect
 *
 * @copyright   (C) 2026 Brian Teeman
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Plugin\System\GroupLoginRedirect\Extension;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Event\User\AfterLoginEvent;
use Joomla\CMS\Event\User\AfterLogoutEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\Route;
use Joomla\Event\SubscriberInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Redirect frontend users to menu items based on their assigned groups.
 */
final class GroupLoginRedirect extends CMSPlugin implements SubscriberInterface
{
    protected $autoloadLanguage = true;

    public static function getSubscribedEvents(): array
    {
        return [
            'onUserAfterLogin'  => 'onUserAfterLogin',
            'onUserAfterLogout' => 'onUserAfterLogout',
        ];
    }

    public function onUserAfterLogin(AfterLoginEvent $event): void
    {
        $app = $this->getApplication();

        if (!$app instanceof CMSApplicationInterface || !$app->isClient('site')) {
            return;
        }

        $user = $event->getOptions()['user'] ?? null;

        if (!$user || empty($user->id)) {
            return;
        }

        $this->redirectForUser($app, $user, 'menuitem');
    }

    public function onUserAfterLogout(AfterLogoutEvent $event): void
    {
        $app = $this->getApplication();

        if (!$app instanceof CMSApplicationInterface || !$app->isClient('site')) {
            return;
        }

        $userId = (int) ($event->getParameters()['id'] ?? 0);

        if (!$userId) {
            return;
        }

        $user = Factory::getUser($userId);

        if (!$user || empty($user->id)) {
            return;
        }

        $this->redirectForUser($app, $user, 'menuitem_logout');
    }

    /**
     * Redirect a user using the first matching group rule with a configured menu item.
     *
     * @param   CMSApplicationInterface  $app        The application.
     * @param   object                   $user       The user.
     * @param   string                   $menuField  The rule property containing the menu item ID.
     *
     * @return  void
     */
    private function redirectForUser(CMSApplicationInterface $app, object $user, string $menuField): void
    {
        $rules = (array) $this->params->get('rules', []);

        if ($rules === []) {
            return;
        }

        $userGroups = array_map('intval', $user->getAuthorisedGroups());

        foreach ($rules as $rule) {
            $groupId    = (int) ($rule->usergroup ?? 0);
            $menuItemId = (int) ($rule->$menuField ?? 0);

            if (!$groupId || !$menuItemId || !in_array($groupId, $userGroups, true)) {
                continue;
            }

            $menuItem = $app->getMenu()->getItem($menuItemId);

            if (!$menuItem) {
                continue;
            }

            $url = Route::_('index.php?Itemid=' . $menuItemId, false);

            if (!$url) {
                continue;
            }

            $app->redirect($url);
            $app->close();

            return;
        }
    }
}
