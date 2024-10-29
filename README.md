# Click & Collect for Craft Commerce

Click & Collect for Craft Commerce adds drop in Click and Collect functionality to Craft Commerce.

**Note**: _The license fee for this plugin is $??.00 via the Craft Plugin Store._

## Requirements

This plugin requires Craft CMS 4.0.0 or later.
This plugin requires Craft Commerce 4.0.0 or later.
This plugin requires PHP 8.0 or later.

## Installation

To install Click & Collect, follow these steps:

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require burnthebook/craft-commerce-click-and-collect

3. Install the plugin via `./craft install/plugin craft-commerce-click-and-collect` via the CLI, or in the Control Panel, go to Settings → Plugins and click the "Install" button for Click & Collect for Craft Commerce.

You can also install Click & Collect for Craft Commerce via the **Plugin Store** in the Craft Control Panel.

Click & Collect for Craft Commerce works on Craft 4.x and Craft Commerce 4.0.0

## Usage

On your Delivery Template:

```
{% include 'craft-commerce-click-and-collect/frontend/collection-points' with { csrfToken: csrfToken } %}
```

This will include the necessary form to interface with commerce, it provides a postcode lookup feature that uses postcodes.io, which then grabs the collection points from the admin and sorts them by distance. We also then calculate the earliest available collection time and display that to the user.

There may be some changes needed to integrate this into your design, or you can use our included full suite of Craft Commerce templates by running the following command:

```
@todo
```

## Changelog

## Roadmap

## Contributing

## Thanks

#extending-craft channel in the Craft CMS Discord