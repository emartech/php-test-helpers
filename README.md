# php-test-helpers

Install dependencies with docker: 

```
$ docker run --rm --interactive --tty --volume $PWD:/app composer install --ignore-platform-reqs --no-scripts
```

Update dependencies with docker: 

```
$ docker run --rm --interactive --tty --volume $PWD:/app composer update --ignore-platform-reqs --no-scripts
```
