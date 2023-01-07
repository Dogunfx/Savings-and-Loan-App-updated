# Communication

- [Communication](#communication)
    - [SMS Gateways](#sms-gateways)
      - [Add SMS Gateway](#add-sms-gateway)
    - [View Campaigns](#view-campaigns)
    - [Add Campaign](#add-campaign)
    - [View Logs](#view-logs)

<a name="communication"></a>
# Communication

This module allows you to send various communication campaigns to your clients.

<a name="sms-gateways"></a>
## SMS Gateways

You will need to add an sms gateway in order to send sms notifications. We only support HTTP API based sms gateways
Clicking Manage SMS Gateways will open sms gateways list page. On this page you will see a table with columns below:
- **Name:** The name of the gateway
- **Active:** Whether the gateway is active or not
- **Action:** Further actions that you can take on the row like edit or details

<a name="add-sms-gateway"></a>
## Add SMS Gateway

To add new sms gateway , click on Manage SMS Gateways menu then click Create Gateway.
This will open the gateway create page offering a series of text  inputs.
The form contains:

- **Name:** The name of the gateway
- **To Name:** The parameter which represents the recipient number in the original url.
- **Msg Name:** The parameter which represents the message name in the original url.
- **URL:** The http api url **excluding** the **To Name** and **Msg Name**
- **Active:** Whether the gateway is active or not

<a name="view-campaigns"></a>
## View Campaigns

Clicking View Campaigns will open campaigns list page. On this page you will see a table with columns below:
- **Name:** The name of the campaign
- **Type:** The campaign trigger type
- **Created By:** The user who created the record
- **Campaign Type:** The campaign type, sms or email
- **Status:** Whether the campaign is active or not
- **Action:** Further actions that you can take on the row like edit or details

<a name="add-campaign"></a>
## Add Campaign

To add a new campaign, click on View Campaigns menu then click Add Campaign.
This will open the campaign create page offering a series of text  inputs.
The form contains:

- **Name:** The name of the campaign
- **Campaign Type:** The campaign type, sms or email
- **Trigger Type:** The campaign trigger type. This determines how the campaign is triggered. Direct means the campaign is sent immediately. Schedule means the campaign we be sent at the set time and date. Triggered means it will be sent based on an event set on business rule.
- **Schedule Date:** This is the date that you want the campaign to be sent on if trigger type is scheduled.
- **Schedule Time:** This is the time that you want the campaign to be sent on if trigger type is scheduled.
- **Schedule Frequency:** This is the frequency at which the campaign is resent  if trigger type is scheduled.
- **Frequency Type:** This is the frequency type at which the campaign is resent  if trigger type is scheduled.
- **SMS Gateway:** The sms gateway which will be used to send sms if campaign type is sms.
- **Business Rule:** This is used to select the recipients
- **Report Attachment:** If campaign type is email, you can select the report to be attached
- **Branch:** The branch where the campaign applies
- **Branch:** The branch where the campaign applies
- **Loan Officer:** Filter recipients  by loan officer
- **Loan Product:** Filter recipients  by loan product
- **Email Subject:** The subject to be used when sending email
- **Description:** The message content of the email or sms
- **Status:** The status of the campaign

<a name="view-logs"></a>
## View Logs

This page shows a log of all your sent sms or emails.