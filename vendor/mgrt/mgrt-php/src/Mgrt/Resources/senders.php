<?php

return array(
    'operations'  => array(
        'ListSenders' => array(
            'httpMethod'    => 'GET',
            'uri'           => 'senders',
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
                    'enum'     => array('email', 'createdAt'),
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
        'GetSender' => array(
            'httpMethod' => 'GET',
            'uri'        => 'senders/{senderId}',
            'responseClass' => 'Mgrt\Response\ResultResponse',
            'parameters' => array(
                'senderId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
            ),
        ),
        'DeleteSender' => array(
            'httpMethod' => 'DELETE',
            'uri'        => 'senders/{senderId}',
            'responseClass' => 'Mgrt\Response\ResultDeletedResponse',
            'parameters' => array(
                'senderId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
            ),
        ),
    ),
);
