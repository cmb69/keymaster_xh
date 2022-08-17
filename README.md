# Keymaster_XH

Keymaster_XH facilitates locking the back-end of a CMSimple_XH
website, if a user is already logged in. This is necessary in case
there is more than one user who has access to the back-end, as the
back-end of CMSimple_XH does not take any precautions against
concurrent editing. After a configurable period of inactivity the
admin is automatically logged out, to avoid locking the site
“forever”, if they forgot to log out properly. Before this happens
the admin is warned and offered the possibility to prolong the
session, to avoid loosing unsaved changes.

- [Requirements](#requirements)
- [Download](#download)
- [Installation](#installation)
- [Settings](#settings)
- [Usage](#usage)
- [Limitations](#limitations)
- [Troubleshooting](#troubleshooting)
- [License](#license)
- [Credits](#credits)

## Requirements

Keymaster_XH is a plugin for CMSimple_XH. It requires a UTF-8
encoded CMSimple_XH version, and PHP ≥ 7.1.0 with the Json extension.

## Download

The [lastest release](https://github.com/cmb69/keymaster_xh/releases/latest)
is available for download on Github.

## Installation

The installation is done as with many other CMSimple_XH plugins. See the
[CMSimple_XH Wiki](https://wiki.cmsimple-xh.org/doku.php/installation#plugins)
for further details.

1.  **Backup the data on your server.**
1.  Unzip the distribution on your computer.
1.  Upload the whole directory `keymaster/` to your server into
    the `plugins/` directory of CMSimple_XH.
1.  Set write permissions to the subdirectories `config/`, `css/`,
    `languages/`, and the file `key`.
1.  Switch to `Plugins` → `Keymaster` in the back-end
    to check if all requirements are fulfilled.

## Settings

The configuration of the plugin is done as with many other
CMSimple_XH plugins in the back-end of the Website. Select
`Plugins` → `Keymaster`.

You can change the default settings of Keymaster_XH under
`Config`. Hints for the options will be displayed when hovering
over the help icons with your mouse.

Localization is done under `Language`. You can translate the
character strings to your own language if there is no appropriate
language file available, or customize them according to your
needs.

The look of Keymaster_XH can be customized under `Stylesheet`.

## Usage

After installation the plugin is already fully functional.

### How does it work?

If no user is logged in to the back-end, the keymaster holds a
single key. As soon as a user logs in, the key is given to this
user and access to the back-end of CMSimple_XH is granted. As
there is only one key, no other users can log in; they are simply
rejected with an appropriate message (but see [limitations](#limitations)).

After some configurable period of inactivity the user is logged
out, what returns the key to the keymaster. Inactivity means a
period in which no request, which can be recognized by the
keymaster, to the server is made. A configurable time before users
are logged out, they are prompted to prolong the session, which
then triggers a recognizable request.

Having multiple browser windows (or tabs; in the following the
term browser window refers to browser tabs as well) open for the
same CMSimple_XH installation is possible; activity in one window
is recognized in the others by the keymaster, what may take a
little while, though (depending on the poll interval).

⚠ Caution! Editing a CMSimple_XH site in more than one browser
window does not generally work. Unless you know exactly what you
are doing, make modifications only in the main window, and treat
the others as read-only.

In case you have missed to prolong the session in time and you
have been logged out by the keymaster while there were some
unsaved changes, there is a *chance* to get them back: log in to
the site from another window of the same browser and press the
back button in the original window. If you are lucky, your changes
are still there

## Limitations

For `security_type=javascript` and `wwwaut`, respectively, the
login attempt of a second user will not show any message; the user
simply cannot login. Solution: switch to `security_type=page`.

Some extensions (e.g. [Chat_XH](https://github.com/cmb69/chat_xh))
periodically send background requests to the server, which might
be recognized by the keymaster as activity. If such an extension
is active, the automatic logout does not work, i.e. other users
will not be able to log in until the browser window is closed.
Solution: always log out properly after you are finished with
editing the website.

If a logged in user closes the browser window (but not the
complete browser), and another user logs in later, the first user
is able to circumvent the check of the keymaster by browsing to
the CMSimple_XH installation again. Solution: log out properly
when you are done with the editing (at least close the browser).

After changing the admin password, the website is locked for the
duration of the automatic logout period. Solution: change the
admin password after you are finished with other editing
activities.

When you are logged in to another CMSimple_XH installation in a
parent folder, Keymaster does not permit log in. Solution: log out
from the other installation first.

## Troubleshooting

Report bugs and ask for support either on
[Github](https://github.com/cmb69/keymaster_xh/issues)
or in the [CMSimple_XH Forum](https://cmsimpleforum.com/).

## License

Keymaster_XH is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Keymaster_XH is distributed in the hope that it will be useful,
but *without any warranty*; without even the implied warranty of
*merchantibility* or *fitness for a particular purpose*. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Keymaster_XH.  If not, see <https://www.gnu.org/licenses/>.

Copyright 2013-2019 Christoph M. Becker

French translation © 2014 Patrick Varlet

## Credits

Keymaster_XH was inspired by
[LoginLocker](https://ge-webdesign.de/cmsimpleplugins/?Eigene_Plugins___LoginLocker)
by Martin Damken and Gert Ebersbach,
and a brilliant Movie (guess which one ;).

The plugin icon is designed by [Alessandro Rei](http://www.mentalrey.it/).
Many thanks for publishing this icon under GPL.

This plugin uses free applications icons from [Aha-Soft](http://www.aha-soft.com/).
Many thanks for making these icons freely available.

Many thanks to the community at the
[CMSimple_XH Forum](https://www.cmsimpleforum.com/) for tips, suggestions
and testing.

And last but not least many thanks to [Peter Harteg](httsp://www.harteg.dk),
the “father” of CMSimple,
and all developers of [CMSimple_XH](https://www.cmsimple-xh.org)
without whom this amazing CMS would not exist.
