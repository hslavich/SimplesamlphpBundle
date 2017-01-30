SimplesamlphpBundle
===================

This is a SimpleSAMLphp Bundle for Symfony2.

**Note:** For Symfony 2.7 or lower, you need to use the 1.0.0 release of this bundle.

## Installation

Add this bundle to your Symfony2 project.

    composer require hslavich/simplesamlphp-bundle

or manually require this bundle in your `composer.json` file.

    "require": {
        ...
        "hslavich/simplesamlphp-bundle": "dev-master"
    }

Update your project.

    composer update hslavich/simplesamlphp-bundle

Activate the bundle in `app/AppKernel.php`.

    $bundles = array(
        ...
        new Hslavich\SimplesamlphpBundle\HslavichSimplesamlphpBundle(),
    )


## Configuration

Add bundle configuration settings to your Symfony2 config.

    # app/config/config.yml
    hslavich_simplesamlphp:
        # Service provider name
        sp: default-sp

You will need to create your own user provider. See the [Symfony2 documentation "How to Create a custom User Provider"](http://symfony.com/doc/current/cookbook/security/custom_provider.html).

1. First, create a User class (you can also place it in your `Entity/` folder)

        # src/Acme/MyBundle/Security/User/MyUser.php
        namespace Acme\MyBundle\Security\User;

        use Symfony\Component\Security\Core\User\UserInterface;
        use Symfony\Component\Security\Core\User\EquatableInterface;

        class MyUser implements UserInterface, EquatableInterface
        {
            ...
        }

2. Then create the UserProvider class

        # src/Acme/MyBundle/Security/User/MyUserProvider.php
        namespace Acme\MyBundle\Security\User;

        use Symfony\Component\Security\Core\User\UserProviderInterface;
        use Symfony\Component\Security\Core\User\UserInterface;
        use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
        use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

        class MyUserProvider implements UserProviderInterface
        {
            public function loadUserByUsername($username) { ... }
            public function refreshUser(UserInterface $user) { ... }
            public function supportsClass($class) { ... }
        }

3. And make your `UserProvider` a service

        # src/Acme/MyBundle/Resources/config/services.yml
        services:
            my_user_provider:
                class: Acme\MyBundle\Security\User\MyUserProvider

Then add the `provider` and `firewalls` settings to you Symfony2 security file.

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

Create the following file structure in your `app/` folder and place your configuration files in there.

    app/
      config/
        simplesamlphp/
          cert/
            saml.crt
            saml.pem
          config/
            config.php
            authsources.php
          metadata/
            saml20-idp-remote.php # Example

Make sure to correctly set the paths for `cert/` and `metadata/` folders in your `config.php` file (absolute paths recommended). The `metadata/saml20-idp-remote.php` is just an example. See the [SimpleSAMLphp documentation, "Adding IdPs to the SP"](https://simplesamlphp.org/docs/stable/simplesamlphp-sp#section_2) for more information.

You may also place those folders anywhere else on your machine, just make sure to correctly set the `SIMPLESAMLPHP_CONFIG_DIR` environment variable (see below).

Add the environment variable to your webserver configuration file, e.g. `/etc/apache2/httpd.conf.local`.

    <Directory *>
        ...
        SetEnv SIMPLESAMLPHP_CONFIG_DIR /var/path/to/my/config
    </Directory>

Enable session bridge storage (see [Symfony documentation](http://symfony.com/doc/current/cookbook/session/php_bridge.html) for more information).

    # app/config/config.yml
    framework:
        session:
            storage_id: session.storage.php_bridge
            handler_id: ~

Create an alias on your webserver, e.g. for an Apache2 webserver, add this line to you `http.conf.local` (or other desired configuration file).

    Alias /simplesaml /home/myapp/vendor/simplesamlphp/simplesamlphp/www
