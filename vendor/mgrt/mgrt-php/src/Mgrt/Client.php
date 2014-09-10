<?php

namespace Mgrt;

use Guzzle\Common\Collection;
use Guzzle\Service\Client as BaseClient;
use Guzzle\Service\Description\ServiceDescription;
use Mgrt\Model\ApiKey;
use Mgrt\Model\Campaign;
use Mgrt\Model\Contact;
use Mgrt\Model\Domain;
use Mgrt\Model\MailingList;
use Mgrt\Model\Sender;
use Mgrt\Model\Template;
use Mgrt\Model\Webhook;
use Mgrt\Model\WebhookCall;

class Client extends BaseClient
{
    /**
     * {@inheritDoc}
     */
    public static function factory($config = array())
    {
        $defaultOptions = array(
            'base_url' => '{scheme}://{hostname}',
            'hostname' => 'api.mgrt.net',
            'scheme'   => 'https',
        );

        $requiredOptions = array(
            'public_key',
            'private_key',
        );

        $config = Collection::fromConfig($config, $defaultOptions, $requiredOptions);
        $description = ServiceDescription::factory(__DIR__.'/Resources/service.php');
        $client = new self($config->get('base_url'), $config);
        $client->setDefaultOption('auth', array($config['public_key'], $config['private_key'], 'Basic'));
        $client->setDescription($description);
        $client->setUserAgent(sprintf('mgrt-php/%s guzzle/%s PHP/%s', \Mgrt\Version::VERSION, \Guzzle\Common\Version::VERSION, PHP_VERSION));

        return $client;
    }

    /**
     * CORE
     */
    public function getHelloWorld()
    {
        $command = $this->getCommand('HelloWorld');

        return $this->execute($command);
    }
    public function getSystemDate()
    {
        $command = $this->getCommand('SystemDate');

        return $this->execute($command);
    }

    /**
     * ACCOUNTS
     */
    public function getAccount()
    {
        $command = $this->getCommand('GetAccount');

        return $this->execute($command);
    }


    /**
     * API KEYS
     */
    public function getApiKeys($parameters = array())
    {
        $command = $this->getCommand('ListApiKeys', $parameters);

        return $this->execute($command);
    }
    public function getApiKey($apiKeyId)
    {
        $command = $this->getCommand('GetApiKey', array(
            'apiKeyId' => $apiKeyId,
        ));

        return $this->execute($command);
    }
    public function createApiKey(ApiKey $apiKey)
    {
        $command = $this->getCommand('CreateApiKey', array(
            'api_key' => array(
                'name' => $apiKey->getName(),
            ),
        ));

        return $this->execute($command);
    }
    public function updateApiKey(ApiKey $apiKey)
    {
        $command = $this->getCommand('UpdateApiKey', array(
            'apiKeyId' => $apiKey->getId(),
            'api_key' => array(
                'name' => $apiKey->getName(),
            ),
        ));

        return $this->execute($command);
    }
    public function deleteApiKey(ApiKey $apiKey)
    {
        $command = $this->getCommand('DeleteApiKey', array(
            'apiKeyId' => $apiKey->getId(),
        ));

        return $this->execute($command);
    }
    public function disableApiKey(ApiKey $apiKey)
    {
        $command = $this->getCommand('DisableApiKey', array(
            'apiKeyId' => $apiKey->getId(),
        ));

        return $this->execute($command);
    }
    public function enableApiKey(ApiKey $apiKey)
    {
        $command = $this->getCommand('EnableApiKey', array(
            'apiKeyId' => $apiKey->getId(),
        ));

        return $this->execute($command);
    }


    /**
     * CAMPAIGNS
     */
    public function getCampaigns($parameters = array())
    {
        $command = $this->getCommand('ListCampaigns', $parameters);

        return $this->execute($command);
    }
    public function getCampaign($campaignId)
    {
        $command = $this->getCommand('GetCampaign', array(
            'campaignId' => $campaignId,
        ));

        return $this->execute($command);
    }
    public function createCampaign(Campaign $campaign)
    {
        $command = $this->getCommand('CreateCampaign', array(
            'campaign' => array(
                'name' => $campaign->getName(),
                'subject' => $campaign->getSubject(),
                'fromMail' => $campaign->getFromMail(),
                'fromName' => $campaign->getFromName(),
                'replyMail' => $campaign->getReplyMail(),
                'body' => $campaign->getBody(),
                'mailingLists' => array_filter($campaign->getMailingLists(), function($mailingList) { return $mailingList->getId(); }),
            ),
        ));

        $savedCampaign = $this->execute($command);
        $campaign->setId($savedCampaign->getId());

        return $campaign;
    }
    public function updateCampaign(Campaign $campaign)
    {
        $command = $this->getCommand('UpdateCampaign', array(
            'campaignId' => $campaign->getId(),
            'campaign' => array(
                'name' => $campaign->getName(),
                'subject' => $campaign->getSubject(),
                'fromMail' => $campaign->getFromMail(),
                'fromName' => $campaign->getFromName(),
                'replyMail' => $campaign->getReplyMail(),
                'body' => $campaign->getBody(),
                'mailingLists' => array_filter($campaign->getMailingLists(), function($mailingList) { return $mailingList->getId(); }),
            ),
        ));

        return $this->execute($command);
    }
    public function deleteCampaign(Campaign $campaign)
    {
        $command = $this->getCommand('DeleteCampaign', array(
            'campaignId' => $campaign->getId(),
        ));

        return $this->execute($command);
    }
    public function scheduleCampaign(Campaign $campaign)
    {
        $command = $this->getCommand('ScheduleCampaign', array(
            'campaignId' => $campaign->getId(),
            'campaign' => array(
                'scheduledAt' => $campaign->getScheduledAt(),
            ),
        ));

        return $this->execute($command);
    }
    public function unscheduleCampaign(Campaign $campaign)
    {
        $command = $this->getCommand('UnscheduleCampaign', array(
            'campaignId' => $campaign->getId(),
        ));

        return $this->execute($command);
    }


    /**
     * CONTACTS
     */
    public function getContacts($parameters = array())
    {
        $command = $this->getCommand('ListContacts', $parameters);

        return $this->execute($command);
    }
    public function createContact($contact)
    {
        $command = $this->getCommand('CreateContact', array(
            'contact' => array(
                'email' => $contact->getEmail(),
                'mailing_lists' => $contact->getMailingListsToArray(),
                'custom_fields' => $contact->getCustomFieldsToArray(),
            ),
        ));

        $savedContact = $this->execute($command);
        $contact->setId($savedContact->getId());

        return $contact;
    }
    public function updateContact($contact)
    {
        $command = $this->getCommand('UpdateContact', array(
            'contactId' => $contact->getId(),
            'contact' => array(
                'email' => $contact->getEmail(),
                'mailing_lists' => $contact->getMailingListsToArray(),
                'custom_fields' => $contact->getCustomFieldsToArray(),
            ),
        ));

        return $this->execute($command);
    }
    public function getContact($contactId)
    {
        $command = $this->getCommand('GetContact', array(
            'contactId' => $contactId,
        ));

        return $this->execute($command);
    }
    public function deleteContact(Contact $contact)
    {
        $command = $this->getCommand('DeleteContact', array(
            'contactId' => $contact->getId(),
        ));

        return $this->execute($command);
    }
    public function unsubscribeContact(Contact $contact)
    {
        $command = $this->getCommand('UnsubscribeContact', array(
            'contactId' => $contact->getId(),
        ));

        return $this->execute($command);
    }
    public function resubscribeContact(Contact $contact)
    {
        $command = $this->getCommand('ResubscribeContact', array(
            'contactId' => $contact->getId(),
        ));

        return $this->execute($command);
    }


    /**
     * CUSTOM FIELDS
     */
    public function getCustomFields()
    {
        $command = $this->getCommand('ListCustomFields');

        return $this->execute($command);
    }


    /**
     * DOMAINS
     */
    public function getDomains($parameters = array())
    {
        $command = $this->getCommand('ListDomains', $parameters);

        return $this->execute($command);
    }
    public function getDomain($domainId)
    {
        $command = $this->getCommand('GetDomain', array(
            'domainId' => $domainId,
        ));

        return $this->execute($command);
    }
    public function checkDomain(Domain $domain)
    {
        $command = $this->getCommand('CheckDomain', array(
            'domainId' => $domain->getId(),
        ));

        return $this->execute($command);
    }


    /**
     * INVOICES
     */
    public function getInvoices($parameters = array())
    {
        $command = $this->getCommand('ListInvoices', $parameters);

        return $this->execute($command);
    }
    public function getInvoice($invoiceId)
    {
        $command = $this->getCommand('GetInvoice', array(
            'invoiceId' => $invoiceId,
        ));

        return $this->execute($command);
    }


    /**
     * MAILING LISTS
     */
    public function getMailingLists($parameters = array())
    {
        $command = $this->getCommand('ListMailingLists', $parameters);

        return $this->execute($command);
    }
    public function getMailingList($mailingListId)
    {
        $command = $this->getCommand('GetMailingList', array(
            'mailingListId' => $mailingListId,
        ));

        return $this->execute($command);
    }
    public function createMailingList(MailingList $mailingList)
    {
        $command = $this->getCommand('CreateMailingList', array(
            'mailing_list' => array(
                'name' => $mailingList->getName(),
            ),
        ));

        $savedMailingList = $this->execute($command);
        $mailingList->setId($savedMailingList->getId());

        return $mailingList;
    }
    public function updateMailingList(MailingList $mailingList)
    {
        $command = $this->getCommand('UpdateMailingList', array(
            'mailingListId' => $mailingList->getId(),
            'mailing_list' => array(
                'name' => $mailingList->getName(),
            ),
        ));

        return $this->execute($command);
    }
    public function deleteMailingList(MailingList $mailingList)
    {
        $command = $this->getCommand('DeleteMailingList', array(
            'mailingListId' => $mailingList->getId(),
        ));

        return $this->execute($command);
    }
    public function getMailingListContacts(MailingList $mailingList, $parameters = array())
    {
        $parameters['mailingListId'] = $mailingList->getId();
        $command = $this->getCommand('ListMailingListContacts', $parameters);

        return $this->execute($command);
    }


    /**
     * SENDERS
     */
    public function getSenders($parameters = array())
    {
        $command = $this->getCommand('ListSenders', $parameters);

        return $this->execute($command);
    }
    public function getSender($senderId)
    {
        $command = $this->getCommand('GetSender', array(
            'senderId' => $senderId,
        ));

        return $this->execute($command);
    }
    public function deleteSender(Sender $sender)
    {
        $command = $this->getCommand('DeleteSender', array(
            'senderId' => $sender->getId(),
        ));

        return $this->execute($command);
    }


    /**
     * TEMPLATES
     */
    public function getTemplates($parameters = array())
    {
        $command = $this->getCommand('ListTemplates', $parameters);

        return $this->execute($command);
    }
    public function getTemplate($templateId)
    {
        $command = $this->getCommand('GetTemplate', array(
            'templateId' => $templateId,
        ));

        return $this->execute($command);
    }
    public function createTemplate(Template $template)
    {
        $command = $this->getCommand('CreateTemplate', array(
            'template' => array(
                'name' => $template->getName(),
                'body' => $template->getBody(),
            ),
        ));

        $savedTemplate = $this->execute($command);
        $template->setId($savedTemplate->getId());

        return $template;
    }
    public function updateTemplate(Template $template)
    {
        $command = $this->getCommand('UpdateTemplate', array(
            'templateId' => $template->getId(),
            'template' => array(
                'name' => $template->getName(),
                'body' => $template->getBody(),
            ),
        ));

        return $this->execute($command);
    }
    public function deleteTemplate(Template $template)
    {
        $command = $this->getCommand('DeleteTemplate', array(
            'templateId' => $template->getId(),
        ));

        return $this->execute($command);
    }


    /**
     * WEBHOOKS
     */
    public function getWebhooks()
    {
        $command = $this->getCommand('ListWebhooks');

        return $this->execute($command);
    }
    public function getWebhook($webhookId)
    {
        $command = $this->getCommand('GetWebhook', array(
            'webhookId' => $webhookId,
        ));

        return $this->execute($command);
    }
    public function createWebhook(Webhook $webhook)
    {
        $command = $this->getCommand('CreateWebhook', array(
            'create_webhook' => array(
                'name'              => $webhook->getName(),
                'callbackUrl'      => $webhook->getCallbackUrl(),
                'listenedEvents'   => $webhook->getListenedEvents(),
                'listenedSources'  => $webhook->getListenedSources(),
            ),
        ));

        $savedWebhook = $this->execute($command);
        $webhook->setId($savedWebhook->getId());

        return $webhook;
    }
    public function updateWebhook(Webhook $webhook)
    {
        $command = $this->getCommand('UpdateWebhook', array(
            'webhookId' => $webhook->getId(),
            'edit_webhook'   => array(
                'name'              => $webhook->getName(),
                'callbackUrl'      => $webhook->getCallbackUrl(),
                'listenedEvents'   => $webhook->getListenedEvents(),
                'listenedSources'  => $webhook->getListenedSources(),
            ),
        ));

        return $this->execute($command);
    }
    public function deleteWebhook(Webhook $webhook)
    {
        $command = $this->getCommand('DeleteWebhook', array(
            'webhookId' => $webhook->getId(),
        ));

        return $this->execute($command);
    }
    public function disableWebhook(Webhook $webhook)
    {
        $command = $this->getCommand('DisableWebhook', array(
            'webhookId' => $webhook->getId(),
        ));

        return $this->execute($command);
    }
    public function enableWebhook(Webhook $webhook)
    {
        $command = $this->getCommand('EnableWebhook', array(
            'webhookId' => $webhook->getId(),
        ));

        return $this->execute($command);
    }
    public function resetKeyWebhook(Webhook $webhook)
    {
        $command = $this->getCommand('ResetKeyWebhook', array(
            'webhookId' => $webhook->getId(),
        ));

        return $this->execute($command);
    }
    public function triggerTestWebhook(Webhook $webhook)
    {
        $command = $this->getCommand('TriggerTestWebhook', array(
            'webhookId' => $webhook->getId(),
        ));

        return $this->execute($command);
    }
    public function getWebhookCalls(Webhook $webhook)
    {
        $command = $this->getCommand('ListWebhookCalls', array(
            'webhookId' => $webhook->getId(),
        ));

        return $this->execute($command);
    }
    public function getWebhookCall(Webhook $webhook, $webhookCallId)
    {
        $command = $this->getCommand('GetWebhook', array(
            'webhookId' => $webhook->getId(),
            'webhookCallId' => $webhookCallId,
        ));

        return $this->execute($command);
    }
}
