JonesCore is a set of different classes used to develop plugins. It includes automated installer/updater classes, easy WIO support and a MyAlerts bridge. Most of my plugins will be rewritten to make use of those classes, only the smallest plugins won't use them.

# Installation
If your host supports it, the Core classes will be installed automatically when installing a plugin. However that doesn't work on all hosts so you need to follow some steps to install it manually:

- Download the latest zip from [GitHub](https://github.com/JN-Jones/JonesCore/archive/master.zip)
- Unzip the package
- Upload all files. You should see a new folder "inc/plugins/jones/core" with some subfolders and files
- Install the plugin

JonesCore isn't shown in the plugin listing and doesn't need to be installed seperatly, everything is done while installing a plugin.

# Updating the Core
The Core is updated automatically if your host supports it when accessing the plugin listing. But as not all hosts support that you need to update the Core yourself. You can follow the same steps as done when installing the Core, but make sure to overwrite the old files.

# Updating a plugin
First you need to get the latest plugin files and upload them - that isn't done by the Core. After that the Core will detect that a new plugin version was uploaded and show a warning in the plugin description with a link to an upgrade page. Clicking on that link will run all needed database queries/template changes to update the plugin. Note that some plugins may require additional changes, those will be mentioned in the release announcements.

## Updating an old plugin to a version with JonesCore
As always: First upload the new plugin files. Afterwards navigate to the plugin listing page in your ACP. JonesCore will be auto installed (if possible), otherwise you'll see a notice with manuall instructions. Afterwards you can follow the steps above.

# Using JonesCore in your plugins
JonesCore allows other developers to make use of it's functionality. For more information about adding JonesCore to your plugin visit the [wiki on GitHub](https://github.com/JN-Jones/JonesCore/wiki).