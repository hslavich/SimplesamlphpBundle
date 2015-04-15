SimplesamlphpBundle
===================

SimpleSAMLphp Bundle for Symfony2

## Installation

* Install with composer
```
"require": {
    "hslavich/simplesamlphp-bundle": "dev-master"
}
```

* Activate bundle in `app/AppKernel.php`
```
$bundles = array(
    new Hslavich\SimplesamlphpBundle\HslavichSimplesamlphpBundle(),
)
```

* `composer update hslavich/simplesamlphp-bundle`

## Configuration

* Config bundle in `app/config/config.yml`
```
hslavich_simplesamlphp:
    # Service provider name
    sp: default-sp
    # Attribute which will be used as user identifier
    attribute: uid
```

* `security.yml`. You will need to create your own user provider.
```
security:
    providers:
        simplesaml:
            id: my_user_provider

    firewalls:
        saml:
            pattern:    ^/
            anonymous: true
            stateless:  true
            simple_preauth:
                authenticator: simplesamlphp.authenticator
                provider: simplesaml
            logout:
                path:   /logout
                success_handler: simplesamlphp.logout_handler
```

* SimpleSAMLphp config files in `app/config/simplesamlphp/`. Run command `simplesamlphp:config` to copy files to running simplesamlphp
```
app/
   config/
     simplesaml/
       cert/
       config/
       metadata/
```

* Enable session bridge storage. http://symfony.com/doc/current/cookbook/session/php_bridge.html
```
framework:
    session:
        storage_id: session.storage.php_bridge
        handler_id: ~
```

* Config your webserver
```
Alias /simplesaml /home/myapp/vendor/simplesamlphp/simplesamlphp/www
```
