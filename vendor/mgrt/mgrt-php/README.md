# Mgrt PHP SDK
[![Latest Stable Version](https://poser.pugx.org/mgrt/mgrt-php/version.png)](https://packagist.org/packages/mgrt/mgrt-php)
[![Build Status](https://travis-ci.org/mgrt/mgrt-php.png)](https://travis-ci.org/mgrt/mgrt-php)

# Installation

The recommended way to install the SDK is through composer:

``` json
{
    "require": {
        "mgrt/mgrt-php": "@stable"
    }
}
```

# Usage
## Create an api client

You can find these credentials in your account inside the "Settings" section then "Api keys".

``` php
$api = \Mgrt\Client::factory(array(
    'public_key' => 'your_public_key',
    'private_key' => 'your_private_key',
));
```

Here is a complete list of settings available. See also Guzzle's manual for more options.

* ```public_key``` : Your API key
* ```private_key``` : Your API secret

## Retrieving collection

When retrieving a collection you will get a ```ResultCollection``` class. You can get the current page, the limit and the total number of elements in the global collection.

``` php
$contacts = $api->getContacts();

$contacts->getPage(); // 1
$contacts->getLimit(); // 50
$contacts->getTotal(); // 250 for example
```

You can also set parameters when retrieving a collection.

``` php
$contacts = $contacts->getContacts(array(
    'page'      => 2,
    'limit'     => 10,
    'sort'      => 'createdAt',
    'direction' => 'asc',
));

$contacts->getPage(); // 2
$contacts->getLimit(); // 10
$contacts->getTotal(); // 250 for example
```

With the ResultCollection you can iterate over the collection.

``` php
foreach ($contacts as $contact) {
    echo $contact->getId(); // 1 for example
}
```

## Creating entries

You can create a new object by using the default constructor and setting the fields one by one.

```php
$custom_field = new \Mgrt\Model\CustomField();
$custom_field
    ->setId(42);
    ->setValue('the answer to life the universe and everything');
$contact = new \Mgrt\Model\Contact();
$contact
    ->setEmail('somebody@example.com');
    ->setCustomFields(array(
        $custom_field
    ));
```
or you can use php array structure to define new objects
``` php
$contact = new \Mgrt\Model\Contact();
$contact->fromArray(array(
    'email'         => 'somebody@example.com',
    'custom_fields' => array(
        'id'    => 42,
        'value' => 'the answer to life the universe and everything'
    )
));
```



## Accounts

__Available API methods__

* ```$api->getAccount()``` will return a ```Account``` object.

__Available methods on ```Account``` object__

* ```$account->getId()``` will return an ```integer```.
* ```$account->getCompany()``` will return a ```string```.
* ```$account->getAddressStreet()``` will return a ```string```.
* ```$account->getAddressCity()``` will return a ```string```.
* ```$account->getAddressZipcode()``` will return a ```string```.
* ```$account->getAddressCountry()``` will return a ```string```.
* ```$account->getCurrency()``` will return a ```string```.
* ```$account->getTimezone()``` will return a ```string```.
* ```$account->getCredits()``` will return a ```integer```.
* ```$account->getPlanType()``` will return a ```string```.



## ApiKeys

__Available API methods__

* ```$api->getApiKeys()``` will return a ```ResultCollection ``` containing a collection of ```ApiKey```.
* ```$api->getApiKey($apiKeyId)``` will return a ```ApiKey``` object.
* ```$api->createApiKey(ApiKey $apiKey)``` will return a ```ApiKey``` object.
* ```$api->updateApiKey(ApiKey $apiKey)``` will return a ```boolean```.
* ```$api->deleteApiKey(ApiKey $apiKey)``` will return a ```boolean```.
* ```$api->disableApiKey(ApiKey $apiKey)``` will return a ```boolean```.
* ```$api->enableApiKey(ApiKey $apiKey)``` will return a ```boolean```.

__Available methods on ```ApiKeys``` object__

_Getters_

* ```$apiKey->getId()``` will return an ```integer```.
* ```$apiKey->getName()``` will return a ```string```.
* ```$apiKey->getPublicKey()``` will return a ```string```.
* ```$apiKey->getPrivateKey()``` will return a ```string```.
* ```$apiKey->getEnabled()``` will return a ```boolean```.
* ```$apiKey->getCreatedAt()``` will return a ```DateTime```.
* ```$apiKey->getDisabledAt()``` will return a ```DateTime```.

_Setters_

* ```$apiKey->setName($name)``` where ```$name``` is a ```string```.



## Campaigns

__Available API methods__

* ```$api->getCampaigns()``` will return a ```ResultCollection ``` containing a collection of ```Campaign```.
* ```$api->getCampaign($contactId)``` will return a ```Campaign``` object.
* ```$api->createCampaign(Campaign $campaign)``` will return a ```Campaign``` object.
* ```$api->updateCampaign(Campaign $campaign)``` will return a ```boolean```.
* ```$api->deleteCampaign(Campaign $campaign)``` will return a ```boolean```.
* ```$api->scheduleCampaign(Campaign $campaign)``` will return a ```boolean```.
* ```$api->unscheduleCampaign(Campaign $campaign)``` will return a ```boolean```.

__Available methods on ```Campaign``` object__

_Getters_

* ```$campaign->getId()``` will return an ```integer```.
* ```$campaign->getName()``` will return a ```string```.
* ```$campaign->getMailingLists()``` will return an array of ```MailingList``` objects.
* ```$campaign->getSubject()``` will return a ```string```.
* ```$campaign->getBody()``` will return a ```string```.
* ```$campaign->getFromMail()``` will return a ```string```.
* ```$campaign->getFromName()``` will return a ```string```.
* ```$campaign->getReplyMail()``` will return a ```string```.
* ```$campaign->getCreatedAt()``` will return a ```DateTime```.
* ```$campaign->getUpdatedAt()``` will return a ```DateTime```.
* ```$campaign->getScheduledAt()``` will return a ```DateTime```.
* ```$campaign->getSentAt()``` will return a ```DateTime```.
* ```$campaign->getTrackingEndsAt()``` will return a ```DateTime```.
* ```$campaign->getStatus()``` will return a ```string```.
* ```$campaign->getIsPublic()``` will return a ```boolean```.
* ```$campaign->getShareUrl()``` will return a ```string```.

_Setters_

* ```$campaign->setName($name)``` where ```$name``` is a ```string```.
* ```$campaign->setMailingLists($mailingList)``` where ```$mailingList``` is an array of ```MailingList``` objects.
* ```$campaign->setSubject($subject)``` where ```$subject``` is a ```string```.
* ```$campaign->setBody($body)``` where ```$body``` is a ```string```.
* ```$campaign->setFromMail($fromMail)``` where ```$fromMail``` is a ```string```.
* ```$campaign->setFromName($fromName)``` where ```$fromName``` is a ```string```.
* ```$campaign->setReplyMail($replyMail)``` where ```$replyMail``` is a ```string```.



## Contacts

__Available API methods__

* ```$api->getContacts()``` will return a ```ResultCollection ``` containing a collection of ```Contact```.
* ```$api->getContact($contactId)``` will return a ```Contact``` object.
* ```$api->getContact($contactEmail)``` will return a ```Contact``` object.
* ```$api->createContact(Contact $contact)``` will return a ```Contact``` object.
* ```$api->updateContact(Contact $contact)``` will return a ```boolean```.
* ```$api->deleteContact(Contact $contact)``` will return a ```boolean```.
* ```$api->unsubscribeContact(Contact $contact)``` will return a ```boolean```.
* ```$api->resubscribeContact(Contact $contact)``` will return a ```boolean```.

__Available methods on ```Contact``` object__

_Getters_

* ```$contact->getId()``` will return an ```integer```.
* ```$contact->getEmail()``` will return a ```string```.
* ```$contact->getMailingLists()``` will return an array of ```MailingList``` objects.
* ```$contact->getMailingListsToArray()``` will return an array of ```integer```.
* ```$contact->getCustomFields()``` will return an array of ```CustomField``` objects.
* ```$contact->getCustomFieldsToArray()``` will return an array of CustomFields, formatted as an array of {id, value}.
* ```$contact->getLatitude()``` will return a ```string```.
* ```$contact->getLongitude()``` will return a ```string```.
* ```$contact->getCountryCode()``` will return a ```string```.
* ```$contact->getTimeZone()``` will return a ```string```.
* ```$contact->getCreatedAt()``` will return a ```DateTime```.
* ```$contact->getUpdatedAt()``` will return a ```DateTime```.

_Setters_

* ```$contact->setEmail($email)``` where ```$email``` is a ```string```.
* ```$contact->setMailingLists($mailingList)``` where ```$mailingList``` is an array of ```MailingList``` objects.
* ```$contact->setCustomFields($customFields)``` where ```$customFields``` is an array of ```CustomField``` objects.

_Other methods_

* ```$contact->addMailingLists($mailingList)``` where ```$mailingList``` is an array of ```MailingList``` objects.
* ```$contact->removeMailingLists($mailingList)``` where ```$mailingList``` is an array of ```MailingList``` objects.
* ```$contact->addMailingList($mailingList)``` where ```$mailingList``` is a ```MailingList``` object.
* ```$contact->removeMailingList($mailingList)``` where ```$mailingList``` is a ```MailingList``` object.
* ```$contact->addCustomFields($customFields)``` where ```$customFields``` is an array of ```CustomField``` objects.
* ```$contact->removeCustomFields($customFields)``` where ```$customFields``` is an array of ```CustomField``` objects.
* ```$contact->addCustomField($customFields)``` where ```$customFields``` is a ```CustomField``` object.
* ```$contact->removeCustomField($customFields)``` where ```$customFields``` is a ```CustomField``` object.



## Custom Fields

_Custom fields cannot be created or updated from the API_

__Available API methods__

* ```$api->getCustomFields()``` will return a ```ResultCollection ``` containing a collection of ```CustomField```.

__Available methods on ```CustomField``` object__

* ```$customField->getId()``` will return an ```integer```.
* ```$customField->getName()``` will return a ```string```.
* ```$customField->getFieldType()``` will return a ```string```.
* ```$customField->getValue()``` will return a ```string```.
* ```$customField->getChoices()``` will return an array of ```string```.



## Domains

_Domains cannot be created or updated from the API_

__Available API methods__

* ```$api->getDomains()``` will return a ```ResultCollection ``` containing a collection of ```Domain```.
* ```$api->getDomain($domainId)``` will return a ```Domain``` object.
* ```$api->checkDomain(Domain $domain)``` will return a ```Domain``` object.

__Available methods on ```Domain``` object__

* ```$domain->getId()``` will return an ```integer```.
* ```$domain->getDomainName()``` will return a ```string```.
* ```$domain->getCheckedAt()``` will return a ```DateTime```.
* ```$domain->getSpfFqdn()``` will return a ```string```.
* ```$domain->getSpfStatus()``` will return an ```integer```.
* ```$domain->getDkimFqdn()``` will return a ```string```.
* ```$domain->getDkimStatus()``` will return an ```integer```.
* ```$domain->getPublicKey()``` will return a ```string```.



## Invoices

_Invoices cannot be created or updated from the API_

__Available API methods__

* ```$api->getInvoices()``` will return a ```ResultCollection ``` containing a collection of ```Invoice```.
* ```$api->getInvoice($invoiceId)``` will return a ```Invoice``` object.

__Available methods on ```Invoice``` object__

* ```$invoice->getId()``` will return an ```integer```.
* ```$invoice->getNumber()``` will return a ```string```.
* ```$invoice->getNetAmount()``` will return a ```float```.
* ```$invoice->getTaxAmount()``` will return a ```float```.
* ```$invoice->getTotalAmount()``` will return a ```float```.
* ```$invoice->getDueAt()``` will return a ```DateTime```.
* ```$invoice->getPaidAt()``` will return a ```DateTime```.
* ```$invoice->getInvoiceLines()``` will return an array of ```InvoiceLine``` objects.

__Available methods on ```InvoiceLine``` object__

* ```$invoiceLine->getId()``` will return an ```integer```.
* ```$invoiceLine->getTitle()``` will return a ```string```.
* ```$invoiceLine->getDescription()``` will return a ```string```.
* ```$invoiceLine->getQuantity()``` will return a ```float```.
* ```$invoiceLine->getPrice()``` will return a ```float```.



## MailingLists

__Available API methods__

* ```$api->getMailingLists()``` will return a ```ResultCollection ``` containing a collection of ```MailingList```.
* ```$api->getMailingList($mailingListId)``` will return a ```MailingList``` object.
* ```$api->createMailingList(MailingList $mailingList)``` will return a ```MailingList``` object.
* ```$api->updateMailingList(MailingList $mailingList)``` will return a ```boolean```.
* ```$api->deleteMailingList(MailingList $mailingList)``` will return a ```boolean```.
* ```$api->getMailingListContacts(MailingList $mailingList)``` will return a ```ResultCollection ``` containing a collection of ```Contact```.

__Available methods on ```MailingList``` object__

_Getters_

* ```$mailingList->getId()``` will return an ```integer```.
* ```$mailingList->getName()``` will return a ```string```.
* ```$mailingList->getCreatedAt()``` will return a ```DateTime```.
* ```$mailingList->getUpdatedAt()``` will return a ```DateTime```.

_Setters_

* ```$mailingList->setName($name)``` where ```$name``` is a ```string```.



## Senders

_Senders cannot be created or updated from the API_

__Available API methods__

* ```$api->getSenders()``` will return a ```ResultCollection ``` containing a collection of ```Sender```.
* ```$api->getSender($senderId)``` will return a ```Sender``` object.
* ```$api->deleteSender(Sender $sender)``` will return a ```boolean```.

__Available methods on ```Sender``` object__

* ```$sender->getId()``` will return an ```integer```.
* ```$sender->getEmail()``` will return a ```string```.
* ```$sender->getEmailType()``` will return a ```string```.
* ```$sender->getIsEnabled()``` will return a ```boolean```.



## Templates

__Available API methods__

* ```$api->getTemplates()``` will return a ```ResultCollection ``` containing a collection of ```Template```.
* ```$api->getTemplate($templateId)``` will return a ```Template``` object.
* ```$api->createTemplate(Template $template)``` will return a ```Template``` object.
* ```$api->deleteTemplate(Template $template)``` will return a ```boolean```.
* ```$api->updateTemplate(Template $template)``` will return a ```boolean```.

__Available methods on ```Template``` object__

_Getters_

* ```$template->getId()``` will return an ```integer```.
* ```$template->getName()``` will return a ```string```.
* ```$template->getBody()``` will return a ```DateTime```.

_Setters_

* ```$campaign->setName($name)``` where ```$name``` is a ```string```.
* ```$template->setBody($body)``` where ```$body``` is a ```string```.



## Unit Tests

To run unit tests, you'll need a set of dependencies you can install using Composer

Once installed, just launch the following command:

```
phpunit
```

Rename the phpunit.xml.dist file to phpunit.xml, then uncomment the following lines and add your own API keys:

``` php
<php>
    <!-- <server name="PUBLIC_KEY" value="your_public_key" /> -->
    <!-- <server name="PRIVATE_KEY" value="your_private_key" /> -->
</php>
```
You're done.

## More informations
* [Guzzle PHP HTTP Client](http://guzzlephp.org/)

## License

This software is released under the MIT License. See the bundled LICENSE file for details.
