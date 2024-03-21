# GCRintegrator for PrestaShop

`GCRintegrator` enables Google Customer Reviews integration with PrestaShop, facilitating the collection of customer feedback and display of ratings.

## Features

- Adds Google Customer Reviews opt-in to order confirmation.
- Configurable estimated delivery days.
- Allows the use of different sources (EAN-13, ISBN, and UPC) as GTIN data.
- Customizable opt-in style.
- Simplifies Google Customer Review Badge insertion.

## Installation & Configuration

1. **Prepare**: Package the entire `gcrintegrator` directory into a `.zip` file.
2. **Install**: In your PrestaShop admin panel, go to **Modules and Services**, click **Upload a Module**, and then drag the `.zip` file into the upload area.
3. **Configure**: After installation, set your Google Merchant ID, Estimated Delivery Days, and opt-in style in the module's configuration settings.

## Usage

- The survey opt-in will automatically appear on the order confirmation page.
- To display the Google Customer Review Badge, include `{hook h="displayGCRbage"}` in your template.

## Disclaimer

This module is provided as-is without any warranty or support. Use it at your own risk.

## License

Released under [GPLv3](https://www.gnu.org/licenses/gpl-3.0.en.html).
