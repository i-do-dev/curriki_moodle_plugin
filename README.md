# CurrikiGo Moodle Plugin

This plugin connect Moodle to CurrikiStudio to access Programs, Playlists and Activities by following IMS Global LTI Advantage (LTI 1.3). It setup Moodle's External Tool which connect to CurrikiStudio.

## Installation
1. Copy this plugin to the `local` directory of your Moodle instance: `git clone https://github.com/ActiveLearningStudio/curriki_moodle_plugin.git`
2. Or download repository, make `zip` and follow this guide `https://docs.moodle.org/39/en/Installing_plugins#Installing_via_uploaded_ZIP_file`
3. Optional - In case your own `CurrikiStudio` and `Tsugi` instances (or `local` environment) then edit `config.php` and set `TSUGI_HOST` and `TSUGI_HOST_API` constants.
4. Visit the `Site Administration` page or notifications page to complete the install process

## Configuration
1. Create a `token` as amdin user. Steps are:
    1. `Site Administration` > `Plugins` > `Web services` > `Manage tokens`
    2. Click on `Add`
    3. Select the admin user and `CurrikiStudio` service
    4. Click on Saves changes
2. Setup `External tool` with one click 
    1. Visit `Site administration` > `Plugins` > `Local plugins` > `CurrikiGo Settings`
    2. Click on `Setup CurrikiStudioLTI Tool`. This will auto configure new `External Tool` named `CurrikiStudioLTI`.