<?php

$container->setParameter('base_url', getenv('BASE_URL'));
$mysql = parse_url(getenv("CLEARDB_DATABASE_URL"));

$container->setParameter('database_driver', 'pdo_mysql');
$container->setParameter('database_host', $mysql['host']);
$container->setParameter('database_name', substr($mysql['path'], 1));
$container->setParameter('database_user', $mysql['user']);
$container->setParameter('database_password', $mysql['pass']);
$container->setParameter('database_path', null);

$container->setParameter('jwt_private_key_path', getenv('JWT_PRIVATE_KEY'));
$container->setParameter('jwt_public_key_path', getenv('JWT_PUBLIC_KEY'));
$container->setParameter('jwt_key_pass_phrase', getenv('JWT_PASS_PHRASE'));