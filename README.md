# Notices Data Module

## Description
This module fetches notices from The Gazette REST API and displays them with pagination using a Twig template.

## Features
- Consumes The Gazette API.
- Displays title, published date, and content of each notice.
- 10 results per page with Drupal's standard pager.
- Clean, semantic HTML output via Twig template.

## Installation
1. Place the `notices_data` module in your Drupal `modules/custom` directory.
2. Run `drush en notices_data` or enable it via the Drupal admin interface.
3. Visit `/notices-data` to see the notices.

## API Endpoint Used
`https://www.thegazette.co.uk/all-notices/notice/data.json`

**Note:** Self-signed certificate errors are handled by disabling SSL verification in API requests.

## Template Override
The markup is rendered through `notices-list.html.twig`. You can customize the template for styling.

## Maintainers
Custom module example provided for integration purposes.
