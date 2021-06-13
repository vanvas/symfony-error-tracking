## Installation

```shell
composer require vim/symfony-error-tracking
```

## Configuration

`config/packages/error_tracking.yaml`
```yaml
error_tracking:
  url: '%env(ERROR_TRACKING_URL)%'
  #  url: 'http://localhost:8000/api/v1/error'
  ignored_exceptions: []
  ignored_codes: []
  ignored_levels: [DEBUG, INFO]
  ignored_messages: []
```

`config/packages/prod/monolog.yaml`
```yaml
monolog:
    handlers:
        # ...
        error_tracking:
            type: service
            level: error
            id: Vim\ErrorTracking\Handler\MonologHandler
```

`config/packages/doctrine.yaml`
```yaml
doctrine:
  # ...
  orm:
    # ...
    mappings:
      # ...
      ErrorTrackingBundle:
        is_bundle: true
        type: annotation
        prefix: 'Vim\ErrorTracking\Entity'
```

`config/routes.yaml`
```yaml
error_tracking_create:
  path: /api/v1/error
  methods: POST
  controller: Vim\ErrorTracking\Controller\ErrorController::create
error_tracking_list:
  path: /api/v1/error
  methods: GET
  controller: Vim\ErrorTracking\Controller\ErrorController::index
error_tracking_view:
  path: /api/v1/error/{id}
  methods: GET
  controller: Vim\ErrorTracking\Controller\ErrorController::view
error_tracking_delete_all:
  path: /api/v1/error
  methods: DELETE
  controller: Vim\ErrorTracking\Controller\ErrorController::deleteAll
error_tracking_delete:
  path: /api/v1/error/{id}
  methods: DELETE
  controller: Vim\ErrorTracking\Controller\ErrorController::delete
error_tracking_test:
  path: /api/v1/error-test
  methods: GET
  controller: Vim\ErrorTracking\Controller\ErrorController::test
```

`api/config/bundles.php`
```PHP
<?php
return [
  // ...
  Vim\ErrorTracking\ErrorTrackingBundle::class => ['all' => true],
];
```
