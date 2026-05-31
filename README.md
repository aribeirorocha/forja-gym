# FORJA Gym - Gestão de Ginásio

Projeto desenvolvido no âmbito da unidade curricular de **Laboratório de Tecnologias Web (LTW)** da Faculdade de Engenharia da Universidade do Porto (FEUP), edição 2026.

## Grupo LTW07G08

- André Rocha - up202407636
- Ricardo Oliveira - up202404557
- Cândido Junior - up202409106

---

## Descrição do Projeto

A **FORJA** é uma plataforma web completa projetada para facilitar e digitalizar as operações de um ginásio moderno. Com um design sofisticado e focado na usabilidade, a aplicação serve diferentes tipos de utilizadores (Administradores, Instrutores, Nutricionistas e Membros), cumprindo os requisitos funcionais e técnicos exigidos. Foi construída estritamente com tecnologias web base (HTML, CSS, PHP, SQLite e JavaScript/AJAX), sem o uso de frameworks.

## 🚀 Como Executar o Projeto

Para correr este projeto localmente, são necessários apenas o terminal e uma instalação do PHP:

1. Abra o terminal e navegue para o diretório `project` do repositório:
   ```bash
   cd project
   ```

2. Inicie o servidor local embutido do PHP na porta 9000:
   ```bash
   php -S localhost:9000
   ```

3. Abra o seu navegador e aceda à página inicial:
   [http://localhost:9000](http://localhost:9000)

## Organização do Projeto

A arquitetura da aplicação segue o padrão de estruturação MVC (Model-View-Controller), promovendo uma clara separação de responsabilidades e facilitando a manutenção e escalabilidade do código:

- `css/`: Folhas de estilo modulares (CSS Vanilla), assegurando um design moderno e responsivo (*Mobile First*).
- `database/`: Modelos de dados. Contém o ficheiro SQLite (`gym.db`), os guiões SQL de criação e povoamento (`gym.sql`, `populate1.sql`) e os ficheiros PHP com as queries (PDO).
- `js/`: Scripts de JavaScript para interatividade no lado do cliente, validações e pedidos assíncronos (AJAX).
- `pages/`: Controladores lógicos que gerem os pedidos HTTP, interagem com a base de dados e chamam os templates adequados.
- `templates/`: Componentes visuais PHP reutilizáveis (Vistas), que geram o HTML final da aplicação (ex: `header.php`, modais, listagens).
- `imgs/`: Recursos estáticos (imagens, ícones e fotografias de perfil).

## Contas de Teste

Pode explorar todas as funcionalidades do sistema através das seguintes contas pré-configuradas (a palavra-passe para todas as contas é **`1234`**):

| Papel (Role) | Email de Login | Password | Descrição |
| :--- | :--- | :--- | :--- |
| **Administrador** | `gustavosantosadmin@gmail.com` | `1234` | Acesso total à gestão do sistema e utilizadores. |
| **Trainer** | `anasilvatrainer@gmail.com` | `1234` | Instrutor: Gestão de aulas, disponibilidade e PT. |
| **Nutricionista** | `ananutri@gmail.com` | `1234` | Gestão de pacientes e planos alimentares. |
| **Membro (VIP)** | `rita.ferreira@gmail.com` | `1234` | Membro com acesso VIP. |
| **Membro (Normal)** | `pedro.martins@gmail.com` | `1234` | Membro com acesso normal. |
| **Membro (Básico)** | `carlos.oliveira@gmail.com`| `1234` | Membro com acesso básico. |

## Funcionalidades Implementadas (Requisitos Base e Extra)

A plataforma cobre integralmente os requisitos exigidos e vai mais além com funcionalidades exclusivas:

### 1. Perfil de Membro (Sócio)
- **Registo e Perfil:** Criação de conta, login/logout, edição de perfil e foto.
- **Catálogo de Aulas e Reservas:** Consulta de aulas disponíveis com filtros (tipo, trainer). Inscrição e cancelamento com gestão de capacidade.
- **Personal Training (Extra):** Possibilidade de agendar sessões 1-para-1 com os trainers.
- **Planos de Subscrição (Extra):** Diferenciação de acessos via planos (Básico, Normal, VIP).
- **Reviews e Avaliações:** Os membros podem avaliar (com estrelas e texto) aulas em que participaram e instrutores.

### 2. Perfil de Trainer (Instrutor)
- **Gestão de Perfil:** Atualização de bio, especializações e foto.
- **Gestão de Aulas:** Visualização das suas aulas atribuídas e da lista de inscritos.
- **Gestão de PT:** Aceitar, gerir ou rejeitar pedidos de Personal Training.

### 3. Perfil de Nutricionista (Inovação Exclusiva do Grupo)
- Criámos um papel inteiramente novo! Os nutricionistas gerem os seus pacientes e criam **Planos de Nutrição** detalhados (divisão por refeições, macronutrientes, peso e calorias).
- Os membros podem consultar as suas dietas ativas diretamente na plataforma.

### 4. Perfil de Administrador
- **Dashboard de Gestão:** Painel estatístico para controlo do ginásio (aulas com melhor avaliação, top trainers).
- **Gestão de Utilizadores:** Promoção de membros a admin, trainer ou nutricionista, e desativação de contas.
- **Gestão do Catálogo:** Adição/edição de aulas e equipamentos.

### 5. Biblioteca Interativa de Equipamentos (E-Learning)
- Catálogo de máquinas com filtragem por grupo muscular.
- Integração dinâmica de tutoriais do YouTube para demonstrar a biomecânica correta dos exercícios.

---

## Medidas de Segurança

A segurança dos dados dos utilizadores foi uma prioridade absoluta no desenvolvimento:
- **Prevenção contra SQL Injection:** Uso rigoroso e exclusivo de *Prepared Statements* via PDO.
- **Defesa contra XSS (Cross-Site Scripting):** Sanitização de todo o output gerado por utilizadores com `htmlspecialchars()`.
- **Prevenção contra CSRF:** Implementação de um sistema de tokens CSRF obrigatório em todas as submissões de formulários e ações sensíveis (ex: apagar conta, alterar privilégios, inscrições).
- **Armazenamento Seguro de Passwords:** As palavras-passe são encriptadas de forma unidirecional (hash) na base de dados utilizando o algoritmo nativo `password_hash()` do PHP.

---
*Projeto realizado para a cadeira LTW da FEUP - 2026. André Restivo.*
