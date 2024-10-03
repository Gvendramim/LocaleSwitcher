# LocaleSwitcher Plugin

**LocaleSwitcher** é um plugin para WordPress que define automaticamente o idioma do site com base na localização do usuário, usando mapeamentos de país para idioma. Ele também oferece uma interface de administração para gerenciar os mapeamentos diretamente no painel do WordPress.

## Índice

- [Instalação](#instalação)
- [Configuração](#configuração)
- [Como Funciona](#como-funciona)
- [Exemplo de Mapeamento](#exemplo-de-mapeamento)

## Instalação

### Pré-requisitos

- WordPress 5.0 ou superior
- Polylang plugin instalado e ativo
- Conta no [ipinfo.io](https://ipinfo.io/) para obter o token de geolocalização.

### Passos para Instalação

1. **Baixar o Plugin**

   Baixe o arquivo do plugin e mova o diretório `localeswitcher` para o diretório de plugins do WordPress:
   ```bash
   /wp-content/plugins/localeswitcher/

2. **Ativar o Plugin**
    No painel do WordPress, vá até Plugins > Plugins Instalados.
    Encontre o LocaleSwitcher na lista de plugins e clique em Ativar.

3. **Obter Token da API de Geolocalização**
    Cadastre-se no ipinfo.io para obter o seu token de API gratuito. Substitua YOUR_TOKEN_HERE na linha abaixo dentro do arquivo localeswitcher.php pelo token que você obteve:

    private $api_url = 'https://ipinfo.io/json?token=YOUR_TOKEN_HERE';


### Configuração

Configurando Mapeamentos de País para Idioma

Após ativar o plugin, você poderá configurar os mapeamentos de país para idioma diretamente no painel de administração do WordPress.

Vá para Configurações > LocaleSwitcher.

Insira um JSON no campo de texto contendo os mapeamentos de código de país (ISO 3166-1 alpha-2) para os códigos de idioma (compatível com Polylang). Um exemplo de mapeamento:

{
    "BR": "pt_BR",
    "US": "en_US",
    "ES": "es_ES",
    "FR": "fr_FR"
}

Clique em Salvar Alterações.


### Como Funciona
O plugin usa a API ipinfo.io para detectar a localização do visitante com base no endereço IP.
Ele então mapeia o código do país retornado pela API para o código de idioma associado.
O idioma é definido no site usando o plugin Polylang, e o idioma do usuário é armazenado em um cookie para persistência.

1. **Comportamento:**
    - Sem cookies: Se o usuário ainda não tiver um cookie de idioma, a geolocalização será executada e o idioma será definido.
    - Com cookies: Se o usuário já tiver o cookie de idioma, o site será exibido diretamente no idioma correspondente, sem consultar a API novamente.

### Exemplo de Mapeamento
   - Aqui está um exemplo de como você pode configurar o mapeamento de país para idioma:
    {
        "BR": "pt_BR",
        "US": "en_US",
        "ES": "es_ES",
        "FR": "fr_FR",
        "DE": "de_DE",
        "IT": "it_IT",
        "CN": "zh_CN",
        "JP": "ja",
        "KR": "ko"
    }


