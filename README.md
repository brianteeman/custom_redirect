# Group Login and Logout Redirect Plugin

A Joomla 6 system plugin that redirects frontend users to selected menu items after a successful login or logout, based on their assigned Joomla user groups.

<img width="1200" height="525" alt="Woman pointing in two different directions" src="https://github.com/user-attachments/assets/17d0c175-aa49-40da-b2ef-a6bbd6f785ec" />

## Configuration

Add as many group rules as required. Each rule contains:

- **User Group** – selected from the user groups configured on the Joomla site.
- **Redirect to Menu Item on Login** – the frontend menu item to open after a successful login.
- **Redirect to Menu Item on Logout** – the frontend menu item to open after a successful logout. This can be left blank to retain Joomla's normal logout redirect.

The plugin stores the selected group ID and menu item IDs. It does not require hard-coded group names or manually entered URLs.

## Rule Order

Rules are evaluated from top to bottom. The first configured rule matching any of the user's assigned groups is used. Reorder the rules to control precedence when a user belongs to more than one configured group.

Login and logout destinations are evaluated independently. The first matching rule with a configured destination for the relevant action is used.

## Fallback

If no matching rule has a destination configured for the relevant action, the plugin leaves Joomla's normal redirect behaviour unchanged.

## Scope

Only frontend logins and logouts are affected. Administrator logins and logouts are not changed.

## Update Server

The plugin registers its update site in the installation manifest. Joomla checks the update metadata in [update.xml](update.xml), which points to the installable ZIP attached to the latest [GitHub Release](https://github.com/brianteeman/custom_redirect/releases).

The ZIP attached to a release is the Joomla installation package. The automatically generated GitHub source archives are not installable plugin packages.

## Building a New Release

Releases are built and published by the GitHub Actions workflow [Build and publish plugin release](.github/workflows/release.yml).

For each new release:

1. Make and test the changes on a development branch.
2. Merge the changes into `main`.
3. Update the extension version in both places:
   - `plg_system_grouploginredirect/grouploginredirect.xml` — the plugin manifest's `<version>` value.
   - `update.xml` — the `<version>` value inside the update entry.
4. Commit and push those version changes to `main`.
5. Create a Git tag using the same version, prefixed with `v`. For example, for version `1.1.0`:
   ```bash
   git checkout main
   git pull
   git tag v1.1.0
   git push origin v1.1.0
   ```
6. GitHub Actions runs automatically when the tag is pushed. It checks out the tagged source, builds `plg_system_grouploginredirect.zip`, and attaches the package to the matching GitHub Release. If the release does not already exist, the workflow creates it.

### Manual release steps

No manual ZIP creation or upload is normally required. Check the workflow run under the repository's **Actions** tab and confirm it completes successfully. Then check the matching release under **Releases** and verify that it contains `plg_system_grouploginredirect.zip`.

If a GitHub Release was created manually before the workflow ran, the workflow uploads the package to that existing release and replaces an asset with the same filename if necessary.

To run the workflow manually (for example, to retry packaging an existing tag), open **Actions → Build and publish plugin release → Run workflow**, enter the tag (such as `v1.1.0`), and start the run. The tag must already exist and point to the source you want to package.

After publishing, Joomla can discover the new version through its extension update check. The update XML uses the latest GitHub Release download URL, so keep the manifest version, update XML version, and release tag in sync.
