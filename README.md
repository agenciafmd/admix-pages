## F&MD - Páginas

[![Downloads](https://img.shields.io/packagist/dt/agenciafmd/admix-pages.svg?style=flat-square)](https://packagist.org/packages/agenciafmd/admix-pages)
[![Licença](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

- Gerenciador de páginas básico

## Instalação

```bash
composer require agenciafmd/admix-pages:v8.x-dev
```

Execute a migração

```bash
php artisan migrate
```

Os seeds funcionarão diretamente do pacote. Caso precise de alguma customização, faça a publicação.

Não esqueça de corrigir os namespaces, paths das pastas e rodar o `composer dumpautoload` para que os arquivos sejam encontrados

```bash
php artisan vendor:publish --tag=admix-pages:seeders
```

## Configuração

Por padrão, as configurações do pacote são:

```php
<?php

return [
    'name' => 'Páginas',
    'icon' => 'icon fe-file-text',
    'sort' => 20,
    'default_sort' => [
        '-is_active',
        'name',
    ],
    'wysiwyg' => true,
];
```

Se for preciso, você pode customizar estas configurações

```bash
php artisan vendor:publish --tag=admix-pages:configs
```


## Segurança

Caso encontre alguma falha de segurança, por favor, envie um email para carlos@fmd.ag ao invés de abrir uma issue

## Creditos

- [Carlos Seiji](https://github.com/cstamagawa)

## Licença

Licença MIT. [Clique aqui](LICENSE.md) para mais detalhes