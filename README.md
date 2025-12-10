# Guia rápido para rodar o projeto com DDEV

Para instalar o DDEV no seu sistema operacional (Windows, macOS ou Linux), siga o guia oficial:
👉 [https://ddev.com/get-started/](https://ddev.com/get-started/)

Após instalar o DDEV, siga os passos abaixo para rodar o projeto.

---

## 🚀 Como rodar o projeto

1. No diretório do projeto, inicie o ambiente:

```bash
ddev start
```
2. Instale as dependências composer

```bash
ddev composer install
```

3. Instale as dependências composer

```bash
ddev artisan key:generate
```

4. Execute as migrations

```bash
ddev artisan migrate
```

5. Execute o seed

```bash
ddev artisan db:seed
```

6. Instale as dependências npm

```bash
ddev npm install
```

7. Faça o build ou rode a aplicação frontend

```bash
ddev npm run build 
or
ddev npm run dev
```

8. Acesse a aplicação no navegador:

```bash
https://transfer-cahya.ddev.site
```

9. Usuário e senha

```php
User::factory()->create([
    'name' => 'admin',
    'email' => 'admin@admin.com',
    'password' => '123',
]);
```

10. Os demais usuários gerados no seed a senha é `password`

---

Para outras instruções de instalação, configuração e uso, consulte o guia oficial completo:
👉 [https://ddev.com/get-started/](https://ddev.com/get-started/)
