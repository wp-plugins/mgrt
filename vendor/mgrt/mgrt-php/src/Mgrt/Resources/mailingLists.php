<?php

return array(
    'operations'  => array(
        'ListMailingLists' => array(
            'httpMethod'    => 'GET',
            'uri'           => 'mailing-lists',
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
        'GetMailingList' => array(
            'httpMethod' => 'GET',
            'uri'        => 'mailing-lists/{mailingListId}',
            'responseClass' => 'Mgrt\Response\ResultResponse',
            'parameters' => array(
                'mailingListId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
            ),
        ),
        'CreateMailingList' => array(
            'httpMethod' => 'POST',
            'uri'        => 'mailing-lists',
            'responseClass' => 'Mgrt\Response\ResultResponse',
            'parameters' => array(
                'mailing_list' => array(
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
        'UpdateMailingList' => array(
            'httpMethod' => 'PUT',
            'uri'        => 'mailing-lists/{mailingListId}',
            'responseClass' => 'Mgrt\Response\ResultUpdatedResponse',
            'parameters' => array(
                'mailingListId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
                'mailing_list' => array(
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
        'DeleteMailingList' => array(
            'httpMethod' => 'DELETE',
            'uri'        => 'mailing-lists/{mailingListId}',
            'responseClass' => 'Mgrt\Response\ResultDeletedResponse',
            'parameters' => array(
                'mailingListId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
            ),
        ),
        'ListMailingListContacts' => array(
            'httpMethod'    => 'GET',
            'uri'        => 'mailing-lists/{mailingListId}/contacts',
            'responseClass' => 'Mgrt\Response\ResultCollectionResponse',
            'parameters' => array(
                'mailingListId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
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
                    'enum'     => array('email', 'createdAt', 'lastActivity'),
                    'required' => false,
                ),
                'direction' => array(
                    'location' => 'query',
                    'type'     => 'string',
                    'enum'     => array('asc', 'desc'),
                    'required' => false,
                ),
                'status' => array(
                    'location' => 'query',
                    'required' => false,
                    'enum'     => array('active', 'bounced', 'unsubscribed', 'all'),
                ),
            ),
        ),
    ),
);
