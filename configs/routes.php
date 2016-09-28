<?php

App::get('router')->attach('', array(
    'name_prefix' => 'admin-',
    'values' => array(
        'sub-folder' => 'admin',
        'addon' => 'uploader'
    ),

    'routes' => array(
        'uploader-download' => array(
            'params' => array(
                'model_name' => '([a-zA-Z0-9_-]+)',
                'model_id' => '(\d+)',
                'filename' => '([a-zA-Z0-9._-]+)'
            ),
            'path' => '/uploader/download/{:model_name}/{:model_id}/{:filename}',
            'values' => array(
                'controller' => 'UploaderController',
                'action' => 'download'
            )
        ),
        'uploader-ajax-delete' => array(
            'params' => array(
                'model_name' => '([a-zA-Z0-9_-]+)',
                'model_id' => '(\d+)',
                'filename' => '([a-zA-Z0-9._-]+)'
            ),
            'path' => '/ajax/uploader/delete/{:model_name}/{:model_id}/{:filename}',
            'values' => array(
                'controller' => 'UploaderController',
                'action' => 'ajaxDelete'
            )
        )
    )

));
