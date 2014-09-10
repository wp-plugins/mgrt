<?php

return array(
    'operations'  => array(
        'ListDomains' => array(
            'httpMethod'    => 'GET',
            'uri'           => 'domains',
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
        'GetDomain' => array(
            'httpMethod' => 'GET',
            'uri'        => 'domains/{domainId}',
            'responseClass' => 'Mgrt\Response\ResultResponse',
            'parameters' => array(
                'domainId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
            ),
        ),
        'CheckDomain' => array(
            'httpMethod' => 'PUT',
            'uri'        => 'domains/{domainId}/check',
            'responseClass' => 'Mgrt\Response\ResultResponse',
            'parameters' => array(
                'domainId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
            ),
        ),
    )
);
