# Release notes for Click & Collect for Craft Commerce

# Click & Collect for Craft Commerce 1.0.0 - 2024-11-14

Initial Release to Craft Store

- Adds logo to plugin.

# Click & Collect for Craft Commerce 0.1.5 - 2024-11-06

> {note} This plugin is still in very early stages and while it fully works now there are doubtless bugs.

### Enhancements
- Adds Default Craft Commerce templates and overloads.
- Updates readme with usage instructions.

# Click & Collect for Craft Commerce 0.1.4 - 2024-11-06

> {note} This plugin is still in very early stages and while it fully works now there are doubtless bugs.

### Enhancements
- Improved PostgreSQL Compatibility: Updated findNearby query to support PostgreSQL by using subqueries. PostgreSQL does not allow HAVING clauses to reference alias columns directly, which required adjusting the query structure to ensure compatibility.

# Click & Collect for Craft Commerce 0.1.3 - 2024-11-05

> {note} This plugin is still in very early stages and while it fully works now there are doubtless bugs.

### Fixes

- Adds github actions workflow.
- Updates front end template to clarify shipping address is a shipping point in the CraftCMS Admin Order Screen
- Updates README.

# Click & Collect for Craft Commerce 0.1.2 - 2024-11-05

> {note} This plugin is still in very early stages and while it fully works now there are doubtless bugs.

### Fixes

- Added a new button directly under the "Find a collection point" text field for easier access.
- Modified UI so that when a user selects a collection point, the "Find a collection point" section is hidden, and the heading updates to "Selected shop" instead of "Collection Points close to...".
- Updated the gift checkbox to be unchecked by default.
- Hid the entire gift message section (label, textarea, and additional message) until the checkbox is clicked to improve initial clarity.

# Click & Collect for Craft Commerce 0.1.1 - 2024-11-01

> {note} This plugin is still in very early stages and while it fully works now there are doubtless bugs.

### Fixes
- Made "Address Line 2" optional instead of mandatory.
- Improved error messaging on postcode search for a more user-friendly experience.
- Resolved issue where the "Grace Period" field in the admin interface was not saving correctly.
- Fixed "Country" selection field in the admin interface.
- Adjusted "Select Shop" functionality within the maps and opening times modal to ensure it properly selects the shop.
- "Change Shop" button now returns the user to the original page.
- Enabled postcode search for guest users.
- Added functionality to switch to delivery mode if no collection time is available.

### Enhancements
- Added a close ("X") button to the modal for improved usability.
- "Select Shop" button in the maps modal now properly assigns the selected shop, aligning with the delivery to store concept by design.


# Click & Collect for Craft Commerce 0.1.0 - 2024-10-25

> {note} This plugin is still in very early stages and while it fully works now there are doubtless bugs.

- Total rewrite of plugin and force rewrite of git history.

# Click & Collect for Craft Commerce 0.0.2 - 2022-07-13

> {note} This plugin is not yet ready for public release or consumption

> {note} Version has been bumped solely due to migrations being created

- Adds Models and ActiveRecord
- Adds Migrations for Shipping Method, Collection Points and Collection Times
- Allows user to control settings for Shipping Method, name, price, enabled, etc.
- Adjusts Twig Variable to just be a proxy for the main plugin class
- Adjusts Service to pull from ActiveRecord
- Adjusts model to add ActiveRecord helper classes

# Click & Collect for Craft Commerce 0.0.1 - 2022-07-01

> {note} This plugin is not yet ready for public release or consumption

Initial Alpha and Dev of Click & Collect for Craft Commerce Plugin

- Adds Settings Page
- Adds "Click & Collect" Shipping Method