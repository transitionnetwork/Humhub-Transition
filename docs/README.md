# Transition Movement

HumHub module for the [Transition Movement](https://transitionnetwork.org/) It is used on [vive.transitiontogether.org.uk](https://vive.transitiontogether.org.uk/s/transition-together/) You can preview the theme there. If you would like to support further development you could [donate here](https://opencollective.com/transition-platform)

## Overview

- Theme based on Clean Theme 2
- On account creation, add membership to a space based on the profile field "Country"
- Change the "Spaces" top menu button URL to add a filter showing the oldest spaces first
- Possibility to specify a specific group with `Module::spaceAdminsGroupId` to sync all admin users of all spaces with the members of this group (see in configuration) and the members of the related default spaces (space members removal after group member removal).

### Space hosts (Space admin or moderators)

- Sync with the members of a specific group
- For each space where the user is a host, a tag of the space name is attached to the user account

In the file `protected/config/common.php`:
```php
...
    'modules' => [
        ...
        'transition' => [
            'spaceHostsGroupId' => 123, // The group ID for space admins
        ],
        ...
    ],
...
```

For the first sync, go to https://your-humhub.tld/transition/admin/sync-all-space-admins to add a full sync to the cron job

## Installation

1. Clone [source](https://github.com/transitionnetwork/Humhub-Transition) in `protected/modules`.
2. Enable the module
3. Set the module as default -> Users -> Always activated

## Configuration

Enable the module in a space and go the module's settings.
