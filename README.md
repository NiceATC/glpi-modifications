# GLPI Modern Login Designer

Plugin moderno para customização completa da interface de login do GLPI 11.

## 🎨 Características

### Interface de Login Moderna
- **Layouts Split Screen**: Formulário à esquerda ou direita com painel decorativo
- **Painel Interativo**: Notificações, eventos e contador regressivo personalizáveis
- **Design Responsivo**: Adaptável para desktop, tablet e mobile
- **Animações Suaves**: Fade-in com delays sequenciais

### Customização Visual
- **Logos Personalizados**: Upload de logos em 3 tamanhos (small, medium, large)
- **Favicon Customizado**: Substitua o ícone do navegador
- **Background Personalizado**: Imagem JPG ou vídeo MP4 de fundo
- **Cores Customizáveis**: Escolha cores primárias e secundárias
- **Texto de Atribuição**: Adicione sua mensagem na parte inferior

### Painel Interativo
- **Countdown Timer**: Contador regressivo com date picker nativo
- **Notificações**: Lista de avisos importantes (uma por linha)
- **Eventos**: Calendário de próximos eventos (um por linha)
- **Layout Compacto**: Design clean com scroll automático quando necessário

## 📦 Instalação

1. Extraia o plugin na pasta `plugins/mod` do GLPI
2. Acesse **Configurar > Plugins** no GLPI
3. Instale e ative o plugin **"UI Branding"**
4. Configure em **Configurar > UI Branding**

## ⚙️ Configuração

### Basic Settings
- **Page Title**: Título da página de login
- **Attribution Text**: Texto exibido no rodapé do formulário
- **Show Login Background**: Ativar/desativar background personalizado
- **Use Custom Logos**: Ativar/desativar logos customizados

### Layout Style
- **Default Layout**: Layout padrão do GLPI
- **Split Screen - Form on Left**: Formulário à esquerda, painel à direita
- **Split Screen - Form on Right**: Formulário à direita, painel à esquerda

### Color Theme
- **Primary Color**: Cor principal da interface
- **Secondary Color**: Cor secundária

### Interactive Panel
- **Enable Interactive Panel**: Ativar/desativar painel lateral
- **Panel Title**: Título do painel (ex: "Bem-vindo!")
- **Panel Message**: Mensagem de boas-vindas
- **Show Notifications Area**: Exibir área de notificações
- **Show Events Area**: Exibir área de eventos
- **Show Countdown Timer**: Exibir contador regressivo
- **Countdown Date**: Data alvo (com date picker)
- **Countdown Text**: Texto do contador (ex: "Até o próximo evento")
- **Notifications**: Uma notificação por linha
- **Events**: Um evento por linha

### Custom Logos
- **Small Logo** (53x53 - PNG): Logo pequeno
- **Medium Logo** (100x55 - PNG): Logo médio
- **Large Logo** (250x138 - PNG): Logo grande
- **Favicon** (32x32 - ICO/PNG): Ícone do navegador

### Background Options
- **Video Background URL**: URL de vídeo MP4 para fundo animado
- **Background Image** (JPG): Imagem estática de fundo

## 🎯 Exemplos de Uso

### Notificações
```
Atualização do sistema para o GLPI 11
Novo Dashboard de auto atendimento disponível
Manutenção programada para esta noite às 22h
Backup automático configurado com sucesso
```

### Eventos
```
Reunião de equipe - Amanhã às 14h
Treinamento GLPI - Sexta-feira 10h
Auditoria de TI - Próxima semana
Atualização de segurança - 25/01/2025
```

## 🛠️ Tecnologias

- **PHP 7.4+**: Backend do plugin
- **JavaScript (ES5)**: Manipulação dinâmica do DOM
- **CSS3**: Layouts modernos com flexbox/grid
- **Twig**: Template engine do GLPI
- **INI Files**: Armazenamento de configurações

## 📋 Requisitos

- GLPI 11.0+
- PHP 7.4+
- Navegador moderno (Chrome, Firefox, Edge, Safari)

## 🔧 Estrutura de Arquivos

```
mod/
├── src/
│   ├── BrandManager.php       # Gerenciamento de configurações
│   └── UIBranding.php          # Controller do formulário admin
├── front/
│   ├── uibranding.php          # Página de configuração
│   └── resource.send.php       # Envio de recursos (logos, background)
├── templates/
│   └── uibranding.html.twig    # Template do admin
├── public/
│   └── css/
│       ├── mod_split_layouts.css    # Estilos dos layouts split
│       ├── mod_anonymous.css        # Estilos gerais
│       └── mod_responsive.css       # Media queries
├── resources/
│   ├── modifiers.ini           # Arquivo de configuração
│   └── images/                 # Imagens padrão
└── setup.php                   # Inicialização do plugin
```

## 📝 Notas Técnicas

### Persistência de Quebras de Linha
O plugin usa um sistema customizado de escaping para preservar quebras de linha nos campos de notificações e eventos:
- **Salvamento**: Converte `\n` → `\\n` (literal no INI)
- **Leitura**: Converte `\\n` → `\n` (quebra real)
- Todas as leituras usam `parseIniFileSafe()` para garantir compatibilidade

### Layout Split Screen
- Container com **98% width** e **max-width: 1800px**
- Grid com **col-md-5** (formulário) e **col-auto** (painel)
- Logo movido para `.card-header` dentro de `.col-md-5`
- Original `.text-center` escondido com `display: none !important`

### Countdown Timer
- Cards individuais para Dias/Horas/Min/Seg
- Background azul claro (#f0f9ff) com border (#e0f2fe)
- Números com padding zero (01, 02, 03...)
- Atualização a cada segundo com `setInterval()`

## 🎨 Customização Avançada

Para desenvolvedores que queiram extender o plugin:

### Adicionar Novos Layouts
Edite `BrandManager.php`:
```php
public const AVAILABLE_LAYOUTS = [
    'default' => 'Default Layout',
    'split-left' => 'Split Screen - Form on Left',
    'split-right' => 'Split Screen - Form on Right',
    'seu-layout' => 'Seu Novo Layout'
];
```

Crie CSS em `public/css/mod_split_layouts.css`:
```css
.mod-layout-seu-layout .container-tight {
    /* Seus estilos aqui */
}
```

### Modificar Cores do Painel
Edite `setup.php` nas linhas do painel interativo:
```javascript
'<div style="background: #SUA_COR; ...">'
```

## ⚠️ Problemas Conhecidos

Nenhum problema conhecido no momento. Se encontrar bugs, por favor reporte.

## 📜 Licença

Este plugin é distribuído sob licença **GNU General Public License v3.0**.

## 🙏 Créditos

Este plugin foi inspirado e baseado no trabalho original **"GLPI Modifications"** de:
- **Autor Original**: i-Vertix/PGUM
- **Repositório**: https://github.com/i-Vertix/glpi-modifications
- **Licença Original**: GPLv3

### O que foi modificado
O código foi extensivamente modificado e melhorado com as seguintes adições:
- ✅ Painel interativo com notificações, eventos e countdown
- ✅ Layout split screen responsivo
- ✅ Sistema de escape de quebras de linha para INI
- ✅ Date picker nativo para countdown
- ✅ Design moderno e minimalista
- ✅ Animações suaves e transições
- ✅ Scroll automático para listas longas
- ✅ Favicon customizado
- ✅ Integração completa com Twig fields macros

**Agradecimentos** ao time original pela ideia inicial e estrutura base do plugin! 🙌

## 🚀 Versão

**Versão Atual**: 11.0.0  
**Compatível com**: GLPI 11.x

---

**Desenvolvido com ❤️ para a comunidade GLPI**
