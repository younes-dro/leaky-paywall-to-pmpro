# Leaky Paywall to PMPro CSV Generator

This WordPress plugin generates a CSV file compatible with the [Paid Memberships Pro (PMPro)](https://www.paidmembershipspro.com/) import functionality. The CSV file includes active members from the [Leaky Paywall](https://leakypaywall.com/) plugin, making it easy to migrate membership data.

## Features

- **Automated CSV Generation:** Exports active Leaky Paywall members in a format ready for PMPro import.
- **Easy to Use:** Generate the file with a few clicks within your WordPress admin dashboard.
- **Batch Processing:** Handles large datasets efficiently.
- **Compatible with PMPro:** Ensures the output meets PMPro's CSV import requirements.

## Installation

1. Download the plugin from this repository.
2. Log in to your WordPress admin dashboard.
3. Navigate to **Plugins > Add New > Upload Plugin**.
4. Upload the downloaded ZIP file and click **Install Now**.
5. Activate the plugin.

## Usage

1. After activating the plugin, navigate to **Tools > Leaky Paywall to PMPro** in your WordPress admin dashboard.
2. Click **Generate CSV** to create a CSV file with active Leaky Paywall members.
3. Download the generated file.
4. Import the file into PMPro using the [PMPro Import Users From CSV Add-On](https://www.paidmembershipspro.com/add-ons/import-users-from-csv/).

## CSV Format

The generated CSV file includes the following fields:

- `user_login`: WordPress username
- `user_email`: Email address
- `membership_level`: PMPro membership level
- `start_date`: Membership start date
- `end_date`: Membership end date (if applicable)
- Additional fields required by PMPro

## Requirements

- WordPress 5.6 or higher
- PHP 7.4 or higher
- Leaky Paywall Plugin
- Paid Memberships Pro Plugin

## Development

### File Structure

- `/includes`: Core functionality of the plugin.
- `/admin`: Admin page logic for generating and managing CSV files.
- `/assets`: Assets such as images or scripts (if applicable).

