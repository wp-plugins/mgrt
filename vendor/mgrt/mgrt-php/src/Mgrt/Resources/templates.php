<?php

return array(
    'operations'  => array(
        'ListTemplates' => array(
            'httpMethod'    => 'GET',
            'uri'           => 'templates',
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
        'GetTemplate' => array(
            'httpMethod' => 'GET',
            'uri'        => 'templates/{templateId}',
            'responseClass' => 'Mgrt\Response\ResultResponse',
            'parameters' => array(
                'templateId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
            ),
        ),
        'CreateTemplate' => array(
            'httpMethod' => 'POST',
            'uri'        => 'templates',
            'responseClass' => 'Mgrt\Response\ResultResponse',
            'parameters' => array(
                'template' => array(
                    'type' => 'object',
                    'location' => 'postField',
                    'properties' => array(
                        'name' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                        'body' => array(
                            'type'     => 'string',
                            'required' => false,
                        ),
                    ),
                ),
            ),
        ),
        'UpdateTemplate' => array(
            'httpMethod' => 'PUT',
            'uri'        => 'templates/{templateId}',
            'responseClass' => 'Mgrt\Response\ResultUpdatedResponse',
            'parameters' => array(
                'templateId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
                'template' => array(
                    'type' => 'object',
                    'location' => 'postField',
                    'properties' => array(
                        'name' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                        'body' => array(
                            'type'     => 'string',
                            'required' => false,
                        ),
                    ),
                ),
            ),
        ),
        'DeleteTemplate' => array(
            'httpMethod' => 'DELETE',
            'uri'        => 'templates/{templateId}',
            'responseClass' => 'Mgrt\Response\ResultDeletedResponse',
            'parameters' => array(
                'templateId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
            ),
        ),
    ),
);
