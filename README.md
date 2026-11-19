# My Concert Diary 🩵🎀

Site pessoal para registrar shows, fotografias, ingressos, setlists e relatos, com administração privada.

## Como executar

1. Instale e abra o Docker Desktop.
2. Abra esta pasta no VS Code.
3. Execute `docker compose up -d --build`.
4. Acesse http://localhost:8080/admin/configurar.php para criar seu usuário administrador (apenas na primeira vez).
5. Faça login em http://localhost:8080/admin/login.php e cadastre seu primeiro show.
6. Site: http://localhost:8080 | phpMyAdmin: http://localhost:8081.

## Importante se você já tem um banco de dados

**Não execute `docker compose down -v`**: isso remove os dados do volume MySQL. O `init.sql` só é aplicado na criação de um volume vazio. As seis tabelas são compatíveis com a estrutura planejada anteriormente. Faça backup antes de substituir arquivos ou alterar volumes. Se já tiver uma pasta do projeto, copie o conteúdo desta versão para ela preservando seu volume Docker.

## Segurança e hospedagem

- As credenciais de banco em `docker-compose.yml` são **somente para desenvolvimento local**. Substitua-as por segredos fortes e configuração segura antes de hospedar.
- A primeira conta administrativa é criada pela página `/admin/configurar.php` apenas enquanto a tabela `usuarios` estiver vazia. Depois, remova ou bloqueie esse arquivo no servidor público.
- O sistema usa sessão PHP, hash de senha, tokens CSRF, consultas preparadas e validação de imagens.
- O diretório de uploads contém regras `.htaccess` para impedir execução de scripts. Confirme que a hospedagem as respeita.
- Imagens são armazenadas no diretório `src/uploads`; URLs HTTPS de vídeos ficam na tabela `midias`.
- A versão Docker é de desenvolvimento. Para produção, configure HTTPS, cookies seguros, limites de requisição e backups.
- Os dados de artistas favoritos são referências textuais; nenhuma foto protegida por direitos autorais foi incluída.

## Tecnologias

HTML, CSS, Bootstrap, JavaScript, PHP 8.2, MySQL 8 e Docker Compose.

## CRUD

Criar: `admin/cadastrar.php`; consultar: `shows.php` e `show.php`; atualizar: `admin/editar.php`; excluir: `admin/excluir.php`.
