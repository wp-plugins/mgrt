<?php

return array(
    'operations'  => array(
        'ListApiKeys' => array(
            'httpMethod'    => 'GET',
            'uri'           => 'api-keys',
            'responseClass' => 'Mgrt\Response\ResultCollectionResponse',
            'parameters' => array(
                'page' => array(
                    'location' => 'query',
                    'type'     => 'integer',
                    'required' => false,
                ),
                'limit' => array(
                    'location' => 'query',
                    'type'     => 'integer',
                    'minimum'  => 1,
                    'maximum'  => 50,
                    'required' => false,
                ),
                'sort' => array(
                    'location' => 'query',
                    'type'     => 'string',
                    'enum'     => array('name', 'createdAt'),
                    'required' => false,
                ),
                'direction' => array(
                    'location' => 'query',
                    'type'     => 'string',
                    'enum'     => array('asc', 'desc'),
                    'required' => false,
                ),
            ),
        ),
        'GetApiKey' => array(
            'httpMethod' => 'GET',
            'uri'        => 'api-keys/{apiKeyId}',
            'responseClass' => 'Mgrt\Response\ResultResponse',
            'parameters' => array(
                'apiKeyId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
            ),
        ),
        'CreateApiKey' => array(
            'httpMethod' => 'POST',
            'uri'        => 'api-keys',
            'responseClass' => 'Mgrt\Response\ResultResponse',
            'parameters' => array(
                'api_key' => array(
                    'type' => 'object',
                    'location' => 'postField',
                    'properties' => array(
                        'name' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                    ),
                ),
            ),
        ),
        'UpdateApiKey' => array(
            'httpMethod' => 'PUT',
            'uri'        => 'api-keys/{apiKeyId}',
            'responseClass' => 'Mgrt\Response\ResultUpdatedResponse',
            'parameters' => array(
                'apiKeyId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
                'api_key' => array(
                    'type' => 'object',
                    'location' => 'postField',
                    'properties' => array(
                        'name' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                    ),
                ),
            ),
        ),
        'DeleteApiKey' => array(
            'httpMethod' => 'DELETE',
            'uri'        => 'api-keys/{apiKeyId}',
            'responseClass' => 'Mgrt\Response\ResultDeletedResponse',
            'parameters' => array(
                'apiKeyId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
            ),
        ),
        'DisableApiKey' => array(
            'httpMethod' => 'PUT',
            'uri'        => 'api-keys/{apiKeyId}/disable',
            'responseClass' => 'Mgrt\Response\ResultUpdatedResponse',
            'parameters' => array(
                'apiKeyId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
            ),
        ),
        'EnableApiKey' => array(
            'httpMethod' => 'PUT',
            'uri'        => 'api-keys/{apiKeyId}/enable',
            'responseClass' => 'Mgrt\Response\ResultUpdatedResponse',
            'parameters' => array(
                'apiKeyId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
            ),
        ),
    ),
);
