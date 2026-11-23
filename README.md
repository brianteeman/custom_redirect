# Group Login Redirect

A Joomla 6 system plugin that redirects users after a successful **frontend** login according to their assigned Joomla user groups.

## Configuration

Add as many rules as required. Each rule contains:

- **User group** – selected from the user groups configured on the Joomla site.
- **Redirect menu item** – selected from the site's existing menu items.

The plugin stores the selected group ID and menu item ID. It does not require hard-coded group names or manually entered URLs.

## Rule order

Rules are evaluated from top to bottom. The first configured rule matching any of the user's assigned groups is used. Reorder the rules to control precedence when a user belongs to more than one configured group.

## Fallback

If no rule matches, the plugin leaves Joomla's normal login redirect behaviour unchanged.

## Scope

Only frontend logins are affected. Administrator logins are not changed.
