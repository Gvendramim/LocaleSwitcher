<?php

/**
 * Plugin Name: LocaleSwitcher
 * Description: Detecta a localização do Usuário e Define o Idioma Automaticamente Usando Polylang.
 * Version: 1.0
 * Author: Gabriel Vendramim.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LocaleSwitcher {
    private $api_url = 'https://ipinfo.io/json?token=YOUR_TOKEN_HERE'; // Substitua pelo seu token do ipinfo.io

    public function __construct() {
        add_action( 'init', array( $this, 'set_language_based_on_location' ) );
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }


     // Define o idioma com base na localização do usuário
    public function set_language_based_on_location() {
        if ( isset( $_COOKIE['locale_switcher_lang'] ) ) {
            pll_set_language( sanitize_text_field( $_COOKIE['locale_switcher_lang'] ) );
            return;
        }

        $location = $this->get_user_location();

        if ( $location && isset( $location->country ) ) {
            $lang = $this->map_country_to_language( $location->country );

            if ( $lang ) {
                pll_set_language( $lang );
                setcookie( 'locale_switcher_lang', $lang, time() + ( 30 * DAY_IN_SECONDS ), COOKIEPATH, COOKIE_DOMAIN );
            }
        }
    }

    /**
     * Obtém a localização do usuário usando a API 
     * @return object|false 
     */
    private function get_user_location() {
        $response = wp_remote_get( $this->api_url );

        if ( is_wp_error( $response ) ) {
            return false;
        }

        $body = wp_remote_retrieve_body( $response );

        return json_decode( $body );
    }

    /**
     * Mapeia o código do país para o código de idioma do Polylang
     * @param string 
     * @return string|false 
     */
    private function map_country_to_language( $country_code ) {
        $country_lang_map = get_option( 'country_lang_map', array(
            'BR' => 'pt_BR',
            'US' => 'en_US',
            // Adicione mais mapeamentos conforme necessário
        ) );

        return isset( $country_lang_map[ $country_code ] ) ? $country_lang_map[ $country_code ] : false;
    }

    public function add_admin_menu() {
        add_options_page(
            'LocaleSwitcher Settings', 
            'LocaleSwitcher',          
            'manage_options',          
            'localeswitcher',          
            array( $this, 'create_admin_page' ) 
        );
    }

     // Registra as configurações do plugin
    public function register_settings() {
        register_setting( 'localeswitcher_options_group', 'country_lang_map', array( $this, 'sanitize_country_lang_map' ) );
    }

    /**
     * Dados de entrada para o mapeamento de país para idioma
     * @param string 
     * @return array 
     */
    public function sanitize_country_lang_map( $input ) {
        $decoded = json_decode( $input, true );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            add_settings_error(
                'country_lang_map',
                'invalid_json',
                'O JSON inserido é inválido.',
                'error'
            );
            return get_option( 'country_lang_map', array() );
        }

        $sanitized = array();
        foreach ( $decoded as $country => $lang ) {
            $sanitized[ strtoupper( sanitize_text_field( $country ) ) ] = sanitize_text_field( $lang );
        }
        return $sanitized;
    }

    public function create_admin_page() {
        ?>
        <div class="wrap">
            <h1>LocaleSwitcher Settings</h1>
            <form method="post" action="options.php">
                <?php
                    settings_fields( 'localeswitcher_options_group' );
                    do_settings_sections( 'localeswitcher_options_group' );
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Mapeamento de País para Idioma</th>
                        <td>
                            <textarea name="country_lang_map" rows="10" cols="50" class="large-text"><?php echo esc_textarea( json_encode( get_option( 'country_lang_map', array() ), JSON_PRETTY_PRINT ) ); ?></textarea>
                            <p class="description">Insira um JSON com os Mapeamentos de País para Idioma. Exemplo:<br><code>{
                                "BR": "pt_BR",
                                "US": "en_US"
                            }</code></p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}

new LocaleSwitcher();
