# FORJA Gym

Plataforma web para gestão de um ginásio, desenvolvida no âmbito da unidade curricular de Laboratório de Tecnologias Web (LTW) da FEUP, edição 2026.

## Grupo LTW07G08

- André Rocha - up202407636
- Ricardo Oliveira - up202404557
- Cândido Junior - up...

## Descrição

A FORJA é uma plataforma que permite a membros, trainers e administradores gerir as atividades de um ginásio. Na primeira entrega, foram desenvolvidas três páginas em HTML e CSS:

- **Homepage** — página principal com hero section, missão, aulas em destaque e equipa de trainers
- **Trainers** — listagem dos trainers com perfis expansíveis e sistema de reviews por estrelas
- **Login / Registo** — formulário com troca entre login e registo sem recarregar a página, usando apenas CSS e radio buttons

## Estrutura de ficheiros

```
project/
├── css/
│   ├── style.css        # variáveis globais, reset, componentes reutilizáveis (header, footer, cards, botões)
│   ├── forms.css        # estilos específicos de formulários (login/registo)
│   ├── homepage.css     # estilos específicos da homepage (hero, mission, classes, trainers)
│   ├── trainers.css     # estilos específicos da página de trainers (perfis, reviews, estrelas)
│   └── responsive.css   # media queries e comportamento responsivo
├── html/
│   ├── homepage.html
│   ├── login.html
│   └── trainers.html
└── imgs/
    ├── trainer1.jpg
    ├── trainer2.jpg
    ├── trainer3.jpg
    ├── trainer4.jpg
    ├── trainer5.jpg
    ├── trainer6.jpg
    ├── yoga.jpg
    ├── crossfit.jpg
    ├── spinning.jpg
    └── hiit.jpg
```

## Organização do CSS

O CSS está dividido em três camadas:

1. **Base** (`style.css`) — variáveis de cor e tipografia, reset, estilização de elementos HTML base (`body`, `a`, `ul`), componentes globais reutilizáveis como o header, footer, cards e botões.
2. **Componentes** (`forms.css`) — estilos de formulários usados em várias situações (inputs, botões de submit, separadores).
3. **Casos específicos** (`homepage.css`, `trainers.css`, `responsive.css`) — estilos exclusivos de cada página e media queries para adaptação a diferentes tamanhos de ecrã.

## Como abrir o projeto

As páginas podem ser abertas diretamente no browser, ou servidas via PHP para garantir os caminhos relativos corretos:

```bash
cd LTW_PROJECT
php -S localhost:5500
```

Depois aceder a:

- [http://localhost:5500/project/html/homepage.html](http://localhost:5500/project/html/homepage.html)
- [http://localhost:5500/project/html/trainers.html](http://localhost:5500/project/html/trainers.html)
- [http://localhost:5500/project/html/login.html](http://localhost:5500/project/html/login.html)

## Decisões de design

- Paleta baseada em preto, branco e laranja (#f27a38), transmitindo energia e foco.
- Cards com hover animado para feedback visual imediato.
- A página de trainers usa checkboxes escondidas e o seletor `:checked` para expandir perfis sem JavaScript.
- O formulário de login/registo usa radio buttons e seletores CSS (`~`) para alternar entre os dois tabs sem JavaScript.
- Design responsivo com breakpoint a 600px, reorganizando o header em grid para mobile.
