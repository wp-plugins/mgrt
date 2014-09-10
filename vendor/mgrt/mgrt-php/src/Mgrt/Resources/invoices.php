<?php

return array(
    'operations'  => array(
        'GetInvoice' => array(
            'httpMethod' => 'GET',
            'uri'        => 'invoices/{invoiceId}',
            'responseClass' => 'Mgrt\Response\ResultResponse',
            'parameters' => array(
                'invoiceId' => array(
                    'location' => 'uri',
                    'type'     => array('integer'),
                    'required' => true,
                ),
            ),
        ),
        'ListInvoices' => array(
            'httpMethod' => 'GET',
            'uri'        => 'invoices',
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
                    'enum'     => array('totalAmount', 'createdAt'),
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
    ),
);
