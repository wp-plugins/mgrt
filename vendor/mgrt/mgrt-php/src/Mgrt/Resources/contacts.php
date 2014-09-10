<?php

return array(
    'operations'  => array(
        'GetContact' => array(
            'httpMethod' => 'GET',
            'uri'        => 'contacts/{contactId}',
            'responseClass' => 'Mgrt\Response\ResultResponse',
            'parameters' => array(
                'contactId' => array(
                    'location' => 'uri',
                    'type'     => array('string', 'integer'),
                    'required' => true,
                ),
            ),
        ),
        'ListContacts' => array(
            'httpMethod' => 'GET',
            'uri'        => 'contacts',
            'responseClass' => 'Mgrt\Response\ResultCollectionResponse',
            'parameters' => array(
                'status' => array(
                    'location' => 'query',
                    'required' => false,
                    'enum'     => array('active', 'bounced', 'unsubscribed', 'all'),
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
            ),
        ),
        'CreateContact' => array(
            'httpMethod' => 'POST',
            'uri'        => 'contacts',
            'responseClass' => 'Mgrt\Response\ResultResponse',
            'parameters' => array(
                'contact' => array(
                    'type' => 'object',
                    'location' => 'postField',
                    'properties' => array(
                        'email' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                        'mailing_lists' => array(
                            'type'     => 'array',
                            'required' => false,
                            'items' => array(
                                'type' => 'integer',
                                'required' => false,
                            ),
                        ),
                        'custom_fields' => array(
                            'type'     => 'array',
                            'required' => false,
                            'items' => array(
                                'type' => 'object',
                                'properties' => array(
                                    'id' => array(
                                        'type' => 'integer',
                                        'required' => false,
                                    ),
                                    'value' => array(
                                        'type' => array('string', 'number', 'integer', 'array'),
                                        'required' => false,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'UpdateContact' => array(
            'httpMethod' => 'PUT',
            'uri'        => 'contacts/{contactId}',
            'responseClass' => 'Mgrt\Response\ResultUpdatedResponse',
            'parameters' => array(
                'contactId' => array(
                    'location' => 'uri',
                    'type'     => array('integer'),
                    'required' => true,
                ),
                'contact' => array(
                    'type' => 'object',
                    'location' => 'postField',
                    'properties' => array(
                        'email' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                        'mailing_lists' => array(
                            'type'     => 'array',
                            'required' => false,
                            'items' => array(
                                'type' => 'integer',
                                'required' => false,
                            ),
                        ),
                        'custom_fields' => array(
                            'type'     => 'array',
                            'required' => false,
                            'items' => array(
                                'type' => 'object',
                                'properties' => array(
                                    'id' => array(
                                        'type' => 'integer',
                                        'required' => false,
                                    ),
                                    'value' => array(
                                        'type' => array('string', 'number', 'integer', 'boolean', 'array'),
                                        'required' => false,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'DeleteContact' => array(
            'httpMethod' => 'DELETE',
            'uri'        => 'contacts/{contactId}',
            'responseClass' => 'Mgrt\Response\ResultDeletedResponse',
            'parameters' => array(
                'contactId' => array(
                    'location' => 'uri',
                    'type'     => array('string', 'integer'),
                    'required' => true,
                ),
            ),
        ),
        'UnsubscribeContact' => array(
            'httpMethod' => 'PUT',
            'uri'        => 'contacts/{contactId}/unsubscribe',
            'responseClass' => 'Mgrt\Response\ResultUpdatedResponse',
            'parameters' => array(
                'contactId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
            ),
        ),
        'ResubscribeContact' => array(
            'httpMethod' => 'PUT',
            'uri'        => 'contacts/{contactId}/resubscribe',
            'responseClass' => 'Mgrt\Response\ResultUpdatedResponse',
            'parameters' => array(
                'contactId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
            ),
        ),
    ),
);
