<?php

return array(
    'operations'  => array(
        'ListCampaigns' => array(
            'httpMethod'    => 'GET',
            'uri'           => 'campaigns',
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
                    'enum'     => array('name', 'createdAt', 'sentAt'),
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
                    'type'     => 'string',
                    'enum'     => array('sent', 'drafted', 'scheduled', 'sending'),
                    'required' => false,
                ),
                'public' => array(
                    'location' => 'query',
                    'type'     => 'integer',
                    'enum'     => array(0, 1),
                    'required' => false,
                ),
            ),
        ),
        'GetCampaign' => array(
            'httpMethod' => 'GET',
            'uri'        => 'campaigns/{campaignId}',
            'responseClass' => 'Mgrt\Response\ResultResponse',
            'parameters' => array(
                'campaignId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
            ),
        ),
        'CreateCampaign' => array(
            'httpMethod' => 'POST',
            'uri'        => 'campaigns',
            'responseClass' => 'Mgrt\Response\ResultResponse',
            'parameters' => array(
                'campaign' => array(
                    'type' => 'object',
                    'location' => 'postField',
                    'properties' => array(
                        'name' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                        'subject' => array(
                            'type'     => 'string',
                            'required' => false,
                        ),
                        'fromMail' => array(
                            'type'     => 'string',
                            'required' => false,
                        ),
                        'fromName' => array(
                            'type'     => 'string',
                            'required' => false,
                        ),
                        'replyMail' => array(
                            'type'     => 'string',
                            'required' => false,
                        ),
                        'body' => array(
                            'type'     => 'string',
                            'required' => false,
                        ),
                        'mailing_lists' => array(
                            'type'     => 'array',
                            'required' => false,
                            'items' => array(
                                'type' => 'integer',
                                'required' => false,
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'UpdateCampaign' => array(
            'httpMethod' => 'PUT',
            'uri'        => 'campaigns/{campaignId}',
            'responseClass' => 'Mgrt\Response\ResultUpdatedResponse',
            'parameters' => array(
                'campaignId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
                'campaign' => array(
                    'type' => 'object',
                    'location' => 'postField',
                    'properties' => array(
                        'name' => array(
                            'type'     => 'string',
                            'required' => true,
                        ),
                        'subject' => array(
                            'type'     => 'string',
                            'required' => false,
                        ),
                        'fromMail' => array(
                            'type'     => 'string',
                            'required' => false,
                        ),
                        'fromName' => array(
                            'type'     => 'string',
                            'required' => false,
                        ),
                        'replyMail' => array(
                            'type'     => 'string',
                            'required' => false,
                        ),
                        'body' => array(
                            'type'     => 'string',
                            'required' => false,
                        ),
                        'mailing_lists' => array(
                            'type'     => 'array',
                            'required' => false,
                            'items' => array(
                                'type' => 'integer',
                                'required' => false,
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'DeleteCampaign' => array(
            'httpMethod' => 'DELETE',
            'uri'        => 'campaigns/{campaignId}',
            'responseClass' => 'Mgrt\Response\ResultDeletedResponse',
            'parameters' => array(
                'campaignId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
            ),
        ),
        'ScheduleCampaign' => array(
            'httpMethod' => 'PUT',
            'uri'        => 'campaigns/{campaignId}/schedule',
            'responseClass' => 'Mgrt\Response\ResultUpdatedResponse',
            'parameters' => array(
                'campaignId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
                'campaign' => array(
                    'type' => 'object',
                    'location' => 'postField',
                    'properties' => array(
                        'scheduledAt' => array(
                            'type'     => 'string',
                            'required' => true,
                            'sentAs'   => 'sendDateSchedule',
                        ),
                    ),
                ),
            ),
        ),
        'UnscheduleCampaign' => array(
            'httpMethod' => 'PUT',
            'uri'        => 'campaigns/{campaignId}/unschedule',
            'responseClass' => 'Mgrt\Response\ResultUpdatedResponse',
            'parameters' => array(
                'campaignId' => array(
                    'location' => 'uri',
                    'type'     => 'integer',
                    'required' => true,
                ),
            ),
        ),
    ),
);
