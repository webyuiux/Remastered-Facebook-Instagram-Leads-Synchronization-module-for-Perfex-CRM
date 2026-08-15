# Facebook & Instagram Leads Synchronization module for Perfex CRM - Marketing Automation for Meta Ads

> **Collect, manage, and assign leads from Facebook & Instagram Lead Ads directly inside Perfex CRM.**

[![Perfex CRM](https://img.shields.io/badge/Perfex%20CRM-2.3%2B-blue?style=flat-square)](https://codecanyon.net/item/perfex-powerful-open-source-crm/16999468)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4?style=flat-square&logo=php)](https://php.net)
[![Meta Graph API](https://img.shields.io/badge/Meta%20Graph%20API-v17.0-1877F2?style=flat-square&logo=facebook)](https://developers.facebook.com/docs/graph-api/)
[![License](https://img.shields.io/badge/License-Commercial-green?style=flat-square)](#license)

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Setup Guide](#setup-guide)
  - [1. Create a Meta App](#1-create-a-meta-app)
  - [2. Configure the Module](#2-configure-the-module)
  - [3. Connect Facebook](#3-connect-facebook)
  - [4. Map Campaigns](#4-map-campaigns)
  - [5. Sync Past Leads](#5-sync-past-leads)
- [How It Works](#how-it-works)
- [Role-Based Access](#role-based-access)
- [Pages and Features](#pages-and-features)
- [Bulk Actions](#bulk-actions)
- [Database Schema](#database-schema)
- [File Structure](#file-structure)
- [API Endpoints](#api-endpoints)
- [Troubleshooting](#troubleshooting)
- [Changelog](#changelog)
- [License](#license)

---

## Overview

**Meta Leads** (also called *Meta Sync*) is a Perfex CRM module that bridges your **Facebook and Instagram Lead Ads** with your CRM workflow. When a potential customer fills a Meta Lead Ad form, the lead is automatically captured via webhook and stored in the module. Admins assign campaigns to staff members, and each staff member sees only their own leads.

Leads stay inside the module until you deliberately push them to **Perfex CRM Leads** — giving you full control over lead quality before they enter your pipeline.

---

## Features

| Feature | Details |
|---|---|
| **Facebook and Instagram** | Collects leads from both platforms via Meta Graph API v17.0 |
| **Real-time Webhooks** | Instant lead capture as soon as a form is submitted |
| **Past Lead Sync** | Backfill all historical leads for any campaign with one click |
| **Campaign Mapping** | Assign each Facebook form to a staff member, lead status, and lead source |
| **Role-Based Access** | Staff sees only their own assigned campaign leads |
| **Manual CRM Push** | Push leads to Perfex CRM Leads individually or in bulk — never automatically |
| **Filter and Search** | Filter by status, campaign, staff; search by name/email/phone |
| **Bulk Actions** | Bulk Add to CRM, Bulk Delete, Export CSV, Export Excel |
| **Sync Logs** | Full audit trail of every sync event with status and message |
| **Data Isolation** | Staff can never see other staff members' leads |

---

## Requirements

- **Perfex CRM** v2.3 or higher
- **PHP** 7.4 or higher (PHP 8.x supported)
- **MySQL** 5.7+ or MariaDB 10.3+
- A **Meta (Facebook) Developer App** with `leads_retrieval` and `pages_manage_metadata` permissions
- HTTPS on your Perfex installation (required by Meta for webhooks)
- `allow_url_fopen` or cURL enabled on your server

---

## Installation

1. Download the `meta_leads` folder.
2. Upload it to your Perfex CRM modules directory:
   ```
   /path/to/perfex/modules/meta_leads/
   ```
3. Log in to Perfex CRM as **Administrator**.
4. Go to **Setup > Modules**.
5. Find **Meta Leads** and click **Activate**.

> The module automatically creates all required database tables on activation.

---

## Setup Guide

### 1. Create a Meta App

1. Go to [developers.facebook.com](https://developers.facebook.com) and create a new App (type: **Business**).
2. Add the **Facebook Login** and **Webhooks** products to your app.
3. Under **App Settings > Basic**, note your **App ID** and **App Secret**.
4. Generate a **User Access Token** with permissions:
   - `leads_retrieval`
   - `pages_manage_metadata`
   - `pages_read_engagement`
5. Convert it to a **Long-Lived Token** (valid 60 days):
   ```
   GET https://graph.facebook.com/v17.0/oauth/access_token
       ?grant_type=fb_exchange_token
       &client_id={app_id}
       &client_secret={app_secret}
       &fb_exchange_token={short_lived_token}
   ```

### 2. Configure the Module

Navigate to **Meta Leads > Settings** inside Perfex CRM and fill in:

| Field | Description |
|---|---|
| App ID | Your Meta App ID |
| App Secret | Your Meta App Secret |
| Access Token | The long-lived User Access Token |
| Verify Token | A random secret string you create |

Click **Save Settings**.

### 3. Connect Facebook

On the Settings page, click **Connect with Facebook**. This will validate your token, fetch your Facebook Pages, and display a live connection status.

### 4. Map Campaigns

Navigate to **Meta Leads > Campaign Mapping**.

For each Lead Ad form you want to track:
1. Select the **Staff** member to receive leads from this campaign.
2. Select the **Lead Status** (applied when pushed to CRM).
3. Select the **Lead Source** (applied when pushed to CRM).
4. Click **Save Mapping**.

After saving, the module automatically updates `assigned_staff` on all existing leads for that form.

### 5. Sync Past Leads

After mapping, click the **Sync** button on a campaign card to pull all historical leads from Meta. The module skips leads already in the database (logged as `Duplicate`).

---

## How It Works

```
Facebook Lead Ad Form Submitted
          |
          v
   Meta Webhook  POST /meta_leads/webhook
          |
          v
   Stored in meta_leads_data  (status: Pending)
   assigned_staff set from campaign mapping
          |
          v
   Staff logs in  Sees their own leads only
          |
          v
   Review lead  Click "Add to CRM" (single)
             or Bulk Manage > Add to CRM
          |
          v
   Lead created in Perfex CRM Leads
   (module status: Pending -> Added)
```

**Key principle:** Leads never auto-enter Perfex CRM. Every push to CRM is intentional and manual.

---

## Role-Based Access

| Action | Admin | Assigned Staff | Other Staff |
|---|---|---|---|
| View all leads | Yes | No | No |
| View own leads | Yes | Yes | No |
| View Campaign Mapping | Yes | Yes (own campaigns) | Yes (own campaigns) |
| Save Campaign Mapping | Yes | No | No |
| Sync past leads | Yes | Yes (own campaigns) | No |
| View Sync Logs | Yes | Yes (own leads only) | No |
| Bulk Add to CRM | Yes | Yes (own leads only) | No |
| Bulk Delete | Yes | Yes (own leads only) | No |
| Settings | Yes | No | No |
| Reset Module | Yes | No | No |

Staff members with no campaign assignment see an empty leads page (not an error).

---

## Pages and Features

### Leads Page `/admin/meta_leads/submitted_leads`

- Dashboard stat cards: Total Synced, Total Added, Pending count
- Real-time search across name, email, phone, and campaign
- Filters by Status, Campaign, and (admin only) Staff — via Apply/Clear buttons
- Lead detail popup with contact info, acquisition source, and raw JSON payload
- Configurable pagination (10 / 25 / 50 / 100 rows per page)
- **Bulk Manage** dropdown with all bulk and export actions

### Campaign Mapping `/admin/meta_leads/lead_settings`

- Lists all Facebook Lead Ad forms from your connected pages
- Each card shows: form name, page name, lead count, assigned staff, sync button
- Filter by Staff and/or Campaign (staff see only their own)
- Save Mapping assigns staff, lead status, and lead source
- Disconnect removes mapping (does not delete synced leads)
- Active badge indicator on filter button when filters are applied

### Sync Logs `/admin/meta_leads/sync_history`

- Audit trail of every lead sync event (webhook and manual)
- Columns: Date, Lead ID, Form, Status, Message
- Statuses: `Success`, `Failure`, `Duplicate`
- Filter by status and date range
- Admin can clear all logs (non-reversible)
- Staff see only logs for their own assigned leads

### Settings `/admin/meta_leads/settings`

- Facebook connection form (App ID, App Secret, Access Token, Verify Token)
- Live connection status indicator with animated pulse
- Webhook URL with one-click copy button
- Reset Module (admin only, requires confirmation — deletes all data)

---

## Bulk Actions

Select leads using the checkboxes, then click **Bulk Manage**:

| Action | Description |
|---|---|
| **Add to CRM** | Pushes all selected Pending leads into Perfex CRM Leads |
| **Delete Selected** | Permanently deletes selected leads from the module |
| **Export CSV** | Downloads selected leads (or all if none selected) as CSV |
| **Export Excel** | Downloads selected leads (or all if none selected) as Excel |

A confirmation dialog shows the exact count of affected leads before executing destructive actions.

---

## Database Schema

### `{prefix}meta_leads_data`

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT | Primary key |
| `meta_lead_id` | VARCHAR(100) UNIQUE | Facebook's lead ID (ensures no duplicates) |
| `form_id` | VARCHAR(100) | Facebook form ID |
| `page_id` | VARCHAR(100) | Facebook page ID |
| `name` | VARCHAR(255) | Lead's full name |
| `email` | VARCHAR(255) | Lead's email address |
| `phone` | VARCHAR(100) | Lead's phone number |
| `company` | VARCHAR(255) | Lead's company name |
| `city` | VARCHAR(255) | Lead's city |
| `platform` | VARCHAR(50) | `Facebook` or `Instagram` |
| `form_name` | VARCHAR(255) | Facebook form name |
| `page_name` | VARCHAR(255) | Facebook page name |
| `raw_data` | TEXT | JSON of additional custom form fields |
| `date_added` | DATETIME | When the lead submitted the form |
| `status` | VARCHAR(50) | `Pending` or `Added` |
| `assigned_staff` | INT(11) | Perfex staff ID assigned to this lead |
| `lead_status` | INT(11) | Perfex lead status ID (used on CRM push) |
| `lead_source` | INT(11) | Perfex lead source ID (used on CRM push) |

### `{prefix}meta_leads_sync_history`

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT | Primary key |
| `date_added` | DATETIME | Timestamp of the sync event |
| `meta_lead_id` | VARCHAR(100) | Facebook's lead ID |
| `status` | VARCHAR(50) | `Success`, `Failure`, or `Duplicate` |
| `message` | TEXT | Detailed sync message or error info |

### `{prefix}options` — Module Settings

Campaign mappings are stored as JSON:
```
Key:   meta_leads_mapping_{form_id}
Value: {"form_id":"...","form_name":"...","assigned_staff":1,"lead_status":2,"lead_source":3,...}
```

Credential options:
- `meta_leads_app_id`
- `meta_leads_app_secret`
- `meta_leads_access_token`
- `meta_leads_verify_token`

---

## File Structure

```
meta_leads/
|-- meta_leads.php              # Module entry: menu registration and permissions
|-- install.php                 # DB table creation and migration on activation
|-- controllers/
|   |-- Meta_leads.php          # Main controller (leads, mapping, sync, bulk actions)
|   `-- Webhook.php             # Webhook receiver for real-time Meta lead events
|-- models/
|   `-- Meta_leads_model.php    # Data queries, isolation logic, CRM push method
|-- views/
|   |-- submitted_leads.php     # Leads listing page
|   |-- lead_settings.php       # Campaign mapping page
|   |-- sync_history.php        # Sync logs page
|   `-- settings.php            # Settings and Facebook connection page
`-- language/
    `-- english/
        `-- meta_leads_lang.php # English language strings
```

---

## API Endpoints

All endpoints are under `/admin/meta_leads/` (webhook is public).

| Method | Endpoint | Description | Access |
|---|---|---|---|
| GET | `submitted_leads` | Leads listing page | Admin + Staff |
| GET | `lead_settings` | Campaign mapping page | Admin + Staff |
| GET | `sync_history` | Sync logs page | Admin + Staff |
| GET | `settings` | Module settings page | Admin only |
| POST | `save_mapping_ajax` | Save campaign mapping (AJAX) | Admin only |
| GET | `sync_past_leads/{form_id}/{page_id}` | Sync historical leads | Admin + assigned Staff |
| GET | `sync_all_forms` | Sync all mapped campaigns | Admin only |
| POST | `bulk_action_process` | Execute bulk action | Admin + Staff |
| GET | `add_to_crm/{id}` | Push single lead to Perfex CRM | Admin + Staff |
| GET | `delete_lead/{id}` | Delete a single lead | Admin + Staff |
| GET | `export` | Export leads CSV/Excel | Admin + Staff |
| GET | `repair_staff_leads` | Backfill assigned_staff from mappings | Admin only |
| GET | `clear_sync_logs` | Purge all sync log records | Admin only |
| POST | `reset_module` | Full module reset | Admin only |
| GET/POST | `/meta_leads/webhook` | Meta webhook receiver | Public |

---

## Troubleshooting

**Staff sees empty leads page**
The `assigned_staff` column may be `0` for leads synced before a mapping was configured. Fix: re-save the campaign mapping — this automatically backfills staff assignment on all existing leads for that form.

**Leads page 500 error**
Check PHP error logs. Common causes: model loading failure or a missing database column. Run the module deactivation and re-activation to trigger the migration script.

**Webhook not receiving events**
1. Your site must run on **HTTPS** (Meta requirement).
2. The **Verify Token** in module settings must match what you entered in Meta Developer Console.
3. Ensure the webhook is subscribed to the `leadgen` field on your Facebook Page.
4. Navigate to the webhook URL in your browser — it should return `200 OK`.

**No leads after clicking Sync**
- Access Token may be expired (60-day limit). Regenerate and update in Settings.
- Confirm your Meta App has `leads_retrieval` permission and is in **Live** mode (not Development).
- The form must have actual submissions on Facebook.

**Bulk action shows "Select leads first"**
JavaScript must be enabled. If a JS conflict from another plugin occurs, check your browser console for errors.

**Leads going directly to Perfex CRM Leads**
This was a bug in earlier versions. As of v1.1.0, synced leads stay in the module only. Leads are pushed to Perfex CRM only when you explicitly click **Add to CRM** or use **Bulk Manage > Add to CRM**.

---

## Changelog

### v1.1.0

- **Fixed:** Staff leads page was blank — filtering now uses the reliable `assigned_staff` column directly instead of indirect form_id lookup
- **Fixed:** Sync logs page was empty for staff — same fix applied
- **Fixed:** Bulk Manage caused full page refresh instead of AJAX modal
- **Fixed:** Filter dropdown was missing closing form tag — filters now apply via dedicated Apply button
- **New:** `repair_assigned_staff()` auto-runs on mapping save to backfill legacy lead assignments
- **New:** Campaign filter added to Campaign Mapping page (staff see own campaigns only)
- **New:** Bulk Manage dropdown consolidates Add to CRM, Delete, Export CSV, Export Excel into one control
- **Changed:** Leads no longer auto-push to Perfex CRM on sync — all CRM pushes are manual and intentional

### v1.0.0

- Initial release: webhook receiver, past lead sync, campaign mapping, staff data isolation, bulk actions, export

---

## License

This module is **commercially licensed**. One purchase covers a single Perfex CRM installation.
Redistribution, resale, or modification for resale is not permitted without written permission.

**Author:** Virrat Global — [virratglobal.com](https://virratglobal.com)
