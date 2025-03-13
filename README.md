# Click & Collect for Craft Commerce

Click & Collect for Craft Commerce adds drop in Click and Collect functionality to Craft Commerce.

**Note**: _The license fee for this plugin is $99 via the [Craft Plugin Store](https://plugins.craftcms.com/developer/burnthebook?craft4)._

## Requirements

- This plugin requires Craft CMS 5.6.0 or later.
- This plugin requires Craft Commerce 5.3.4 or later.
- This plugin requires PHP 8.2 or later.

https://github.com/user-attachments/assets/5ef784b5-4f44-4ee2-b3fc-0513100a5c68

> [!NOTE]  
> Craft Commerce must be 5.3.4 or later due to [a bug in previous versions of Commerce 5 impacting registration of custom shipping methods.](https://github.com/craftcms/commerce/commit/b769a1d2541ba14dccd974d863a13e2b479a6ca8)

## Compatibility & Previous Versions

| Click & Collect for Craft Commerce  | Craft 4            | Craft 5            |
|-------------------------------------|--------------------|--------------------|
| 1.x                                 | :white_check_mark: | :x:                |
| 2.x                                 | :x:                | :white_check_mark: |

## Installation

To install Click & Collect, follow these steps:

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require burnthebook/craft-commerce-click-and-collect

3. Install the plugin via `./craft install/plugin craft-commerce-click-and-collect` via the CLI, or in the Control Panel, go to Settings → Plugins and click the "Install" button for Click & Collect for Craft Commerce.

You can also install Click & Collect for Craft Commerce via the **Plugin Store** in the Craft Control Panel.

Click & Collect for Craft Commerce works on Craft 4.x and Craft Commerce 4.6.0

## Usage

Within your Shipping Template (see note below), simply add the following line:

```
{% include 'craft-commerce-click-and-collect/frontend/collection-points' with { csrfToken: craft.app.request.csrfToken } %}
```

> [!NOTE]  
> Your shipping template is where users choose a Shipping Method. Since Click & Collect provides its own option, consider adding a "Delivery" and "Collection" tab, or a "Use Collection Point" option when selecting an address. The Shipping Address will automatically be set to the chosen Collection Point.

The included template provides a postcode lookup form (using postcodes.io). This fetches configured collection points from Craft, sorting them by distance, and shows users the earliest available collection time on each collection point. It also integrates with Craft Commerce, adding the necessary hidden fields to ensure that the order makes sense in the admin.

There may be some changes needed to integrate this into your design. For example, we only set the Shipping Address, not the Billing Address. If you want to collect the customers Billing Address you will need to add your own method collection. Please see Craft Commerce's default templates for an example: [https://github.com/craftcms/commerce/tree/4.x/example-templates/dist/shop](https://github.com/craftcms/commerce/tree/4.x/example-templates/dist/shop)

If you want to reference example templates, or are using Craft Commerce's default templates, you can simply copy the modified templates from the plugin into your project with the following command (run within a terminal):

```
cp -r vendor/burnthebook/craft-commerce-click-and-collect/templates/dist/example-templates/* ./templates
```

This command will overwrite `/templates/shop/_private/address/list.twig` and `/templates/shop/checkout/shipping.twig` which have minimal modifications.

### Overloading Plugin Templates

If you do not want to use the included plugin template, or need to modify it, run the following command within a terminal to get your own copy of the template to modify to your design:

```
mkdir -p /templates/craft-commerce-click-and-collect/frontend
cp -r vendor/burnthebook/craft-commerce-click-and-collect/templates/frontend/collection-points.twig ./templates/craft-commerce-click-and-collect/frontend/collection-points.twig
```

You will now have a copy of the template that you can modify. The included template is using Tailwind CSS and should be easily modifiable. _You do not need to modify the {% include %} tag you used above if you do this._

## Configuration

Before the plugin will work effectively you need to create some Collection Points and Opening Times within the CraftCMS Admin.

Once the plugin is installed, you will see a new menu item called "Click & Collect", if you click it you will see the list of Collection Points, as well a button to create a new Collection Point.

<br>
<div style="display:flex;margin:10px 0;">
<div style="width:50%;padding:10px;">
<img src="https://raw.githubusercontent.com/Burnthebook/craft-commerce-click-and-collect/refs/heads/main/docs/img/collection-points-index.png">
</div>
<div style="width:50%;padding:10px;">
<img src="https://raw.githubusercontent.com/Burnthebook/craft-commerce-click-and-collect/refs/heads/main/docs/img/new-collection-point.png">
</div>
</div>
<br>

> [!IMPORTANT]  
> When creating a new Collection Point, ensure to click the "Get Latitude & Longitude" button to get the correct Lat/Lng for your address.

Once your Collection Point is created you can then create Opening Times for that Collection Point by clicking the Opening Times menu item.
<br>
<div style="display:flex;margin:10px 0;width:500px;">
<div style="width:250px;padding:10px;">
<img src="https://raw.githubusercontent.com/Burnthebook/craft-commerce-click-and-collect/refs/heads/main/docs/img/collection-times-index.png">
</div>
<div style="width:250px;padding:10px;">
<img src="https://raw.githubusercontent.com/Burnthebook/craft-commerce-click-and-collect/refs/heads/main/docs/img/new-collection-time.png">
</div>
</div>
<br>

> [!TIP]
> It's beneficial to ensure that each day for your Collection Point has an Opening Time as these are displayed as your Opening Hours within the modal that appears on the front end.

Once a Collection Point has been created with Opening Times, it will appear in the Postcode search on the front end and will be available for Click & Collect.

## Changelog

Please see CHANGELOG.md for a detailed Changelog.

## Roadmap

Some things to do, and ideas for potential features:

- Move away from raw javascript and html-in-js within the included templates, look into using `script type="text/template"`
- Extract as much critical javascript as possible to dedicated javascript files so that users only need worry about the html templates.
- Investigate if this works with zipcodes for US based shipping.

## Contributing

Thank you for considering contributing to the plugin! 

Did you find a bug and know how to fix it? Please submit a Github Issue with detailed replication steps, and then a Pull Request that references that issue.

Do you intend to add a new feature? Please submit a Github Issue for discussion before submitting a Pull Request.

Please format your code using [php-cs-fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer) for PSR-2 coding style before submitting a Pull Request.

If you discover a security vulnerability, please send an email to Burnthebook at support@burnthebook.co.uk. All security vulnerabilities will be promptly addressed.

## Thanks

#extending-craft channel in the Craft CMS Discord
