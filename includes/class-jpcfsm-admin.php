<?php

class JPCFSMAdmin
{
    private $network_active_plugins;

    public $actions;
    private $notices;

    public static function instance() {
        static $instance = null;
        
        if ( null === $instance ) {
            $instance = new JPCFSMAdmin;
            $instance->setup();
        }
        
        return $instance;
    }

    private function setup()
    {
        if ( !$this->network_active_plugins ) {
            $this->network_active_plugins = get_site_option('active_sitewide_plugins', array());
        }

        $this->actions = array(
            ($this->isNetworkActive() ? 'network_' : '') . 'admin_menu' => array( $this, 'pages' ),
            ($this->isNetworkActive() ? 'network_admin_' : '') . 'plugin_action_links_' . JPCFSM_BASE => array(
                $this, 'actionLinks'
            ),
            'admin_enqueue_scripts' => array( $this, 'scripts' )
        );

        foreach ( $this->actions as $tag => $callback ) {
            if ( is_callable($callback) ) {
                add_action ( $tag, $callback );
            }
        }
    }

    private function isNetworkActive()
    {
        if ( !is_multisite() ) {
            return false;
        }

        return is_array($this->network_active_plugins) && isset($this->network_active_plugins[JPCFSM_BASE]);
    }

    public function pages()
    {
        add_submenu_page(
            $this->isNetworkActive() ? 'settings.php' : 'options-general.php',
            sprintf(__('%s Settings', 'jetpack-contact-form-success-message'), JPCFSM_NAME),
            'DX Jetpack CF Message',
            'manage_options',
            'jpcfsm',
            array($this, 'display')
        );

        $this->maybeUpdate();

        return $this;
    }

    public function maybeUpdate()
    {
        $this->update();
    }

    private function update()
    {
        if ( !isset($_POST['submit']) )
            return;

        if ( !isset($_POST['jpcfm_nonce']) || !wp_verify_nonce($_POST['jpcfm_nonce'], 'jpcfm_update') )
            return;

        $settings = array();

        if ( isset($_POST['message']) && trim($_POST['message']) ) {
            $settings['message'] = sanitize_text_field($_POST['message']);
        }

        if ( isset( $_POST['strip_content'] ) ) {
            $settings['strip_content'] = true;
        }

        if ( $settings ) {
            update_option('jpcfsm_settings', $settings);
        } else {
            delete_option('jpcfsm_settings');
        }

        jp_cf_success_message()->flushSettings();

        $this->notices = '<div class="is-dismissible notice updated"><p>' . __('Changes saved successfully!', 'jetpack-contact-form-success-message') . '</p></div>';
    }

    public function display()
    {
        global $current_user;

        wp_enqueue_style('jpcfsm');
        ?>

        <div class="wrap">

            <h2><?php echo JPCFSM_NAME; ?></h2>

            <div class="jpcfsm-cont">

                <?php if ( $this->notices ) : ?>
                    <?php print $this->notices; ?>
                <?php endif; ?>

                <div class="jpcfsm-left">

                    <form method="post" id="poststuff" class="gc-settings">
                        <div id="postbox-container" class="postbox-container">
                            <div class="meta-box-sortables ui-sortable" id="normal-sortables">

                                <div class="postbox">
                                    <h3 class="hndle"><span><?php _e('Format Message', 'jetpack-contact-form-success-message'); ?></span></h3>
                                    <div class="inside">
                                        <p>
                                            <em><?php _e('Format your message below', 'jetpack-contact-form-success-message'); ?></em>
                                        </p>

                                        <?php wp_editor(jp_cf_success_message()->settings('message'), 'message'); ?>
                                    </div>
                                </div>

                                <div class="postbox">
                                    <h3 class="hndle"><span><?php _e('Other Settings', 'jetpack-contact-form-success-message'); ?></span></h3>
                                    <div class="inside">
                                        <p>
                                            <label>
                                                <input type="checkbox" name="strip_content" <?php checked(jp_cf_success_message()->settings('strip_content'), true); ?>/>
                                                <?php _e( 'Hide all post content and display only this message when the form is finally sent.', 'jetpack-contact-form-success-message' ); ?>
                                            </label>
                                        </p>
                                    </div>
                                </div>

                                <div class="postbox">
                                    <h3 class="hndle"><?php _e('Save Changes', 'jetpack-contact-form-success-message'); ?></h3>
                                    <div class="inside">
                                        <p>
                                            <?php wp_nonce_field('jpcfm_update', 'jpcfm_nonce'); ?>
                                            <input type="submit" name="submit" class="button button-primary" value="<?php _e('Save Changes', 'jetpack-contact-form-success-message'); ?>" />
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>

                </div>

                <div class="jpcfsm-right">
                    <div class="jcfsm_right">
                        <p><hr/></p>
                        <h3><?php _e('Are you looking for help?', 'jetpack-contact-form-success-message'); ?></h3>
                        <p><?php _e('Don\'t worry, we got you covered:', 'jetpack-contact-form-success-message'); ?></p>
                        <li><a href="https://wordpress.org/support/plugin/jetpack-contact-form-success-message"><?php _e('Go to plugin support forums on WordPress', 'jetpack-contact-form-success-message'); ?></a></li>
                        <li><a href="https://devrix.com/articles/"><?php _e('Browse our blog for tutorials', 'jetpack-contact-form-success-message'); ?></a></li>
                        <p><hr/></p>
                        <p>
                            <li><a href="https://wordpress.org/support/view/plugin-reviews/jetpack-contact-form-success-message?rate=5#postform"><?php _e('Rate &amp; review this plugin? &#9733;&#9733;&#9733;&#9733;&#9733;', 'jetpack-contact-form-success-message'); ?></a></li>
                            <li><a href="https://twitter.com/wpdevrix"><?php _e('Follow @WPDevriX on Twitter', 'jetpack-contact-form-success-message'); ?></a></li>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function actionLinks($l)
    {
        return array_merge(array(
            'settings' => '<a href="' . (
                $this->isNetworkActive() ? 'settings.php' : 'options-general.php'
            ) . '?page=jpcfsm">' . __('Settings', 'jetpack-contact-form-success-message') . '</a>'
        ), $l);
    }

    public function scripts()
    {
        wp_register_style('jpcfsm', plugin_dir_url(JPCFSM_FILE) . 'assets/css/admin.css');
    }
}