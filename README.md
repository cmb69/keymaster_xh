# Keymaster_XH

Keymaster_XH facilitates locking the back-end of a CMSimple_XH
website, if an admin is already logged in. This is necessary in case
there is more than one admin who has access to the back-end, as the
back-end of CMSimple_XH does not take any precautions against
concurrent editing.

If an admin is already logged in to the site, other admins are informed
that they cannot edit the site, and logging out is suggested to them.
After a configurable period of inactivity, another admin can take over
the administration session, in which case the previous admin is denied
further editing of the site for the time being.

- [Requirements](#requirements)
- [Download](#download)
- [Installation](#installation)
- [Settings](#settings)
- [Usage](#usage)
- [Troubleshooting](#troubleshooting)
- [License](#license)
- [Credits](#credits)

## Requirements

Keymaster_XH is a plugin for [CMSimple_XH](https://cmsimple-xh.org/).
It requires CMSimple_XH ≥ 1.7.0, and PHP ≥ 7.1.0 with the Json extension.
Keymaster_XH also requires [Plib_XH](https://github.com/cmb69/plib_xh) ≥ 1.8;
if that is not already installed (see *Settings*→*Info*),
get the [lastest release](https://github.com/cmb69/plib_xh/releases/latest),
and install it.

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
You only need to remember to properly logout when you are finished
with editing the site, so that other admins do not need to wait
until your session is considered inactive.

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

Copyright 2013-2023 Christoph M. Becker

French translation © 2014 Patrick Varlet

## Credits

Keymaster_XH was inspired by
[LoginLocker](https://ge-webdesign.de/cmsimpleplugins/?Eigene_Plugins___LoginLocker)
by Martin Damken and Gert Ebersbach,
and a brilliant Movie (guess which one ;).

The plugin icon is designed by [Alessandro Rei](http://www.mentalrey.it/).
Many thanks for publishing this icon under GPL.

Many thanks to the community at the
[CMSimple_XH Forum](https://www.cmsimpleforum.com/) for tips, suggestions
and testing.

And last but not least many thanks to [Peter Harteg](httsp://www.harteg.dk),
the “father” of CMSimple,
and all developers of [CMSimple_XH](https://www.cmsimple-xh.org)
without whom this amazing CMS would not exist.
