# Notification Manager Plugin

Standalone hotel notification automation for booking, check-in, checkout, staff, SMS, email, templates, recipients, and delivery logs.

## Features

- Automatic notifications for guest booking creation, confirmation, check-in, checkout, and cancellation.
- Automatic staff notifications for account creation, status changes, shift start, and shift end.
- Email delivery through the application's configured Laravel mail transport.
- SMS delivery through the application's existing SMS gateway settings.
- Editable templates for every event, with placeholders such as `{{ guest_name }}`, `{{ room_number }}`, and `{{ staff_name }}`.
- Manual recipients and role-based recipients for super admin, admin, receptionist, and staff users.
- Delivery audit logs for sent and failed messages.
- QA suite available through the plugin manager.

## Admin Route

After enabling the plugin and running migrations, visit:

`/admin/notifications`

## Standalone Design

The plugin does not edit core booking, staff, or receptionist files. It registers Laravel model observers from its own service provider when enabled.
