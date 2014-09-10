<?php

return array(
    'operations'  => array(
        'HelloWorld' => array(
            'httpMethod'    => 'GET',
            'uri'           => 'hello-world',
            'responseClass' => 'Mgrt\Response\PlainTextResponse',
        ),
        'SystemDate' => array(
            'httpMethod'    => 'GET',
            'uri'           => 'system-date',
            'responseClass' => 'Mgrt\Response\PlainTextResponse',
        ),
    ),
);
