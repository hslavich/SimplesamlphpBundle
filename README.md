SimplesamlphpBundle
===================

SimpleSAMLphp Bundle for Symfony2

## Installation

* Install with composer

    "require": {
        ...
        "hslavich/simplesamlphp-bundle": "dev-master"
    }

* Activate bundle in `app/AppKernel.php`

    $bundles = array(
        ...
        new Hslavich\SimplesamlphpBundle\HslavichSimplesamlphpBundle(),
    )

* `composer update hslavich/simplesamlphp-bundle`

## Configuration

* Add bundle configuration

    # app/config/config.yml
    hslavich_simplesamlphp:
        # Service provider name
        sp: default-sp

* You will need to create your own user provider. See the [Symfony2 documentation "How to Create a custom User Provider"](http://symfony.com/doc/current/cookbook/security/custom_provider.html)

    # app/config/security.yml
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

* Create the following file structure in your `app/` folder:

    app/
       config/
         simplesamlphp/
           cert/
           config/
           metadata/

* Now place your certificates, your `config.php`, `authsources.php` and metadata files into the according folders.

* Run command `php app/console simplesamlphp:config` to copy files from `app/config/simplesamlphp` to `vendor/simplesamlphp`. (Note: When you upgrade your `simplesamlphp` library, you may have to run this command again as existing files may be overwritten.)

* Enable session bridge storage. http://symfony.com/doc/current/cookbook/session/php_bridge.html

    # app/config/config.yml
    framework:
        session:
            storage_id: session.storage.php_bridge
            handler_id: ~

* Config your webserver

    Alias /simplesaml /home/myapp/vendor/simplesamlphp/simplesamlphp/www
