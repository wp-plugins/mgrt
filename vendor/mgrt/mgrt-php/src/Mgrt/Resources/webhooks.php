<?php

return array(
    'operations'  => array(
        'ListWebhooks' => array(
            'httpMethod'    => 'GET',
            'uri'           => 'webhooks',
            'responseClass' => 'Mgrt\Response\ResultCollectionResponse'
        ),
        'GetWebhook' => array(
            'httpMethod' => 'GET',
            'uri'        => 'webhooks/{webhookId}',
            'responseClass' => 'Mgrt\Response\ResultResponse',
            'parameters' => array(
                'webhookId' => array(
                    'location' => 'uri',
                    'type'     => 'string',
                    'required' => true,
                ),
            ),
        ),
        'CreateWebhook' => array(
            'httpMethod' => 'POST',
            'uri'        => 'webhooks',
            'responseClass' => 'Mgrt\Response\ResultResponse',
            'parameters' => array(
                'create_webhook' => array(
                    'type' => 'object',
                    'location' => 'postField',
                    'properties' => array(
                        'name' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                        'callbackUrl' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                        'listenedEvents' => array(
                            'type'     => 'array',
                            'required' => false,
                        ),
                        'listenedSources' => array(
                            'type'     => 'array',
                            'required' => false,
                        ),
                    ),
                ),
            ),
        ),
        'UpdateWebhook' => array(
            'httpMethod' => 'PUT',
            'uri'        => 'webhooks/{webhookId}',
            'responseClass' => 'Mgrt\Response\ResultUpdatedResponse',
            'parameters' => array(
                'webhookId' => array(
                    'location' => 'uri',
                    'type'     => 'string',
                    'required' => true,
                ),
                'edit_webhook' => array(
                    'type' => 'object',
                    'location' => 'postField',
                    'properties' => array(
                        'name' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                        'callbackUrl' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                        'listenedEvents' => array(
                            'type'     => 'array',
                            'required' => false,
                        ),
                        'listenedSources' => array(
                            'type'     => 'array',
                            'required' => false,
                        ),
                    ),
                ),
            ),
        ),
        'DeleteWebhook' => array(
            'httpMethod' => 'DELETE',
            'uri'        => 'webhooks/{webhookId}',
            'responseClass' => 'Mgrt\Response\ResultDeletedResponse',
            'parameters' => array(
                'webhookId' => array(
                    'location' => 'uri',
                    'type'     => 'string',
                    'required' => true,
                ),
            ),
        ),
        'DisableWebhook' => array(
            'httpMethod' => 'PUT',
            'uri'        => 'webhooks/{webhookId}/disable',
            'responseClass' => 'Mgrt\Response\ResultUpdatedResponse',
            'parameters' => array(
                'webhookId' => array(
                    'location' => 'uri',
                    'type'     => 'string',
                    'required' => true,
                ),
            ),
        ),
        'EnableWebhook' => array(
            'httpMethod' => 'PUT',
            'uri'        => 'webhooks/{webhookId}/enable',
            'responseClass' => 'Mgrt\Response\ResultUpdatedResponse',
            'parameters' => array(
                'webhookId' => array(
                    'location' => 'uri',
                    'type'     => 'string',
                    'required' => true,
                ),
            ),
        ),
        'ResetKeyWebhook' => array(
            'httpMethod' => 'PUT',
            'uri'        => 'webhooks/{webhookId}/reset-key',
            'responseClass' => 'Mgrt\Response\ResultUpdatedResponse',
            'parameters' => array(
                'webhookId' => array(
                    'location' => 'uri',
                    'type'     => 'string',
                    'required' => true,
                ),
            ),
        ),
        'TriggerTestWebhook' => array(
            'httpMethod' => 'PUT',
            'uri'        => 'webhooks/{webhookId}/trigger-test',
            'responseClass' => 'Mgrt\Response\ResultUpdatedResponse',
            'parameters' => array(
                'webhookId' => array(
                    'location' => 'uri',
                    'type'     => 'string',
                    'required' => true,
                ),
            ),
        ),
        'ListWebhookCalls' => array(
            'httpMethod'    => 'GET',
            'uri'        => 'webhook/{webhookId}/calls',
            'responseClass' => 'Mgrt\Response\ResultCollectionResponse',
            'parameters' => array(
                'webhookId' => array(
                    'location' => 'uri',
                    'type'     => 'string',
                    'required' => true,
                ),
            ),
        ),
        'GetWebhookCall' => array(
            'httpMethod'    => 'GET',
            'uri'        => 'webhook/{webhookId}/calls/{webhookCallId}',
            'responseClass' => 'Mgrt\Response\ResultCollectionResponse',
            'parameters' => array(
                'webhookId' => array(
                    'location' => 'uri',
                    'type'     => 'string',
                    'required' => true,
                ),
                'webhookCallId' => array(
                    'location' => 'uri',
                    'type'     => 'string',
                    'required' => true,
                ),
            ),
        ),
    ),
);
