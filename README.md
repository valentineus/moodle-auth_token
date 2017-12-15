# Authorization by token's

Token-based authentication (also known as
[JSON Web Token authentication](https://jwt.io/))
is a new way of handling authentication of users in applications.
It is an alternative to
[session-based authentication](https://security.stackexchange.com/questions/81756/).

The most notable difference between the session-based and token-based authentication is that former relies heavily on the server.
A record is created for each logged-in user.

Token-based authentication is stateless - it does not store anything on the server but creates a unique encoded token that gets checked every time a request is made.

Unlike session-based authentication, a token approach would not associate a user with login information but with a unique token that is used to carry client-host transactions.
Many applications, including Facebook, Google, and GitHub, use the token-based approach.

## Requirements

* **PHP**: 5.6.32+;
* **Moodle**: 3.2+;
* **Plug-in**: [tool_managertokens](https://github.com/valentineus/moodle-tool_managertokens);

## Documentation

* [Install the plugin](docs/getting-started.md);
* [User's Manual](docs/getting-started.md);

## License

<img height="256px" alt="GNU Banner" src="https://www.gnu.org/graphics/gnu_headshadow.png" />

[GNU GPLv3](LICENSE.txt).
Copyright (c)
[Valentin Popov](mailto:info@valentineus.link).