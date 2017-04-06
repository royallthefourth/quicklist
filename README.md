# Quicklist
Quicklist is a mailing list manager that's designed to be as straightforward as possible.

## Features
* Simple, no-configuration SQLite storage
* Command line and web interfaces
* Scheduled sending
* Send rate limiting
* SMTP
* Unsubscribe and opt-in

## Requirements
* An SMTP provider
* A web server with `readline` available (ie not Windows)
* PHP 7.1 or greater
* Composer
* SQLite

## Installation
1. Decompress the most recent release on your server
1. `composer install`
1. `vendor/bin/phinx migrate`
1. Move `config/config.example.yml` to `config/config.yml` and fill in your SMTP information
1. Configure your webserver to serve the `public` directory
1. `chmod +x ql`
1. Use the `ql` command line tool to add a user
1. Use either `ql` or the web GUI to
    1. add contacts
    1. add a list
    1. associate contacts with the list
1. Schedule a delivery
1. Configure `cron` to run `ql delivery:process` every minute

## FAQ
### Why haven't my messages been sent?
There are a few possibilities:
* Your list does not have any contacts on it
* You have not set up a `cron` job. If you prefer, you may send the next chunk manually with `ql delivery:send`.
* Quicklist attempts to send messages spaced out evenly within your hourly send limit. To check the status of your message sending, use `ql message:status <message-id>`.