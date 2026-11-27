# My Concert Diary 🩵🎀

Diário de shows com contas individuais. Cada pessoa gerencia seus shows no painel Meu diário. O catálogo e a linha do tempo exigem login e mostram os registros da conta conectada.

## Como executar

1. Instale e abra o Docker Desktop.
2. Abra esta pasta no VS Code.
3. Execute `docker compose up -d --build`.
4. Crie sua conta em http://localhost:8080/admin/criar-conta.php.
5. Abra Meu diário e cadastre seu primeiro show. Para entrar novamente, use http://localhost:8080/admin/login.php.
6. Site: http://localhost:8080 | phpMyAdmin: http://localhost:8081.

## Importante se você já tem um banco de dados

**Não execute `docker compose down -v`**: isso remove os dados do volume MySQL. O `init.sql` só é aplicado na criação de um volume vazio. Faça backup antes de substituir arquivos ou alterar volumes. Se já tiver uma pasta do projeto, copie o conteúdo desta versão para ela preservando seu volume Docker.

Para atualizar um banco existente, faça um backup e importe `database/migrar_contas.sql` uma vez com uma conta que possa alterar tabelas. A migração vincula os shows existentes à conta mais antiga, que era a única disponível na versão anterior. Bancos novos criados com `database/init.sql` já têm essa estrutura.

No cliente MySQL, selecione o banco e execute `SOURCE database/migrar_contas.sql` a partir da raiz do projeto.

## Segurança e hospedagem

- As credenciais de banco em `docker-compose.yml` são **somente para desenvolvimento local**. Substitua-as por segredos fortes e configuração segura antes de hospedar.
- O sistema usa sessão PHP, hash de senha, tokens CSRF, consultas preparadas e validação de imagens.
- O diretório de uploads contém regras `.htaccess` para impedir execução de scripts. Confirme que a hospedagem as respeita.
- Imagens ficam em `storage/uploads`, fora da raiz pública `src`. O PHP precisa de permissão de escrita em `storage`. Ao atualizar uma instalação antiga, mova as imagens de `src/uploads/{capas,ingressos,fotos}` para as subpastas correspondentes de `storage/uploads`. As referências no banco permanecem iguais. URLs HTTPS de vídeos ficam na tabela `midias`.
- A versão Docker é de desenvolvimento. Para produção, configure HTTPS, cookies seguros, limites de requisição e backups.
- Os dados de artistas favoritos são referências textuais; nenhuma foto protegida por direitos autorais foi incluída.

## Tecnologias

HTML, CSS, Bootstrap, JavaScript, PHP 8.2, MySQL 8 e Docker Compose.

## CRUD

Criar: `admin/cadastrar.php`; consultar: `shows.php` e `show.php`; atualizar: `admin/editar.php`; excluir: `admin/excluir.php`.
