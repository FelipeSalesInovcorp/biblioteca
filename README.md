# 📚 Projeto Biblioteca

Sistema web para **gestão de biblioteca**, desenvolvido em **Laravel 12**, com autenticação segura, gestão de livros, autores e editoras, catálogo visual e exportação de dados.

Este projeto foi desenvolvido no contexto de **estágio**, com foco em boas práticas, organização, segurança e experiência do utilizador.

---

## 🚀 Funcionalidades Principais

### 🔐 Autenticação
- Registo e login de utilizadores
- Verificação de email
- **Autenticação de dois fatores (2FA)** via Laravel Jetstream
- Gestão de perfil do utilizador

### 📊 Dashboard
- Painel central de gestão
- Carrossel dinâmico com capas reais dos livros
- Acesso rápido aos módulos principais

### 📖 Gestão de Livros
- CRUD completo de livros
- Campos:
  - ISBN
  - Nome
  - Editora
  - Autores (relação muitos-para-muitos)
  - Bibliografia
  - Imagem da capa
  - Preço
- Pesquisa, filtros e ordenação
- Upload e visualização de capas
- **Exportação de livros para Excel**

### 👤 Gestão de Autores
- CRUD completo
- Nome e foto do autor
- Associação a múltiplos livros
- Pesquisa e paginação

### 🏢 Gestão de Editoras
- CRUD completo
- Nome e logótipo
- Relação com livros
- Pesquisa e ordenação

### 🗂️ Catálogo de Livros
- Página visual com cards
- Capas reais dos livros
- Informação resumida (nome, autores, editora e preço)
- Paginação

---

## 🎨 Interface e Design
- **Tailwind CSS**
- **DaisyUI (tema corporate)**
- Layout moderno e responsivo
- Componentes reutilizáveis
- Experiência intuitiva para o utilizador

---

## 🔐 Segurança
- Passwords cifradas automaticamente pelo Laravel
- Campos sensíveis protegidos
- Upload seguro de imagens
- Autorização baseada em sessão autenticada

---

## 🛠️ Tecnologias Utilizadas

- **Laravel 12**
- **Laravel Jetstream (Livewire)**
- **PHP 8**
- **SQLite**
- **Tailwind CSS**
- **DaisyUI**
- **Vite**
- **Maatwebsite Excel** (exportação)
- **Herd** (ambiente local)

---




