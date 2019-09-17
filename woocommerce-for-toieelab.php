<?php

/*
 * Plugin Name: WooCommerce Extension for toiee Lab
 * Plugin URI: http://toiee.jp
 * Description: WooCommerceの商品と商品をまとめるデータと連動して、コンテンツの閲覧制限、Seriously Simple Podcastの閲覧制限・機能拡張、ユーザー固有のフィードURL生成、マイライブラリ機能、ショートコードなどを実装
 * Author: toiee Lab
 * Version: 3.6
 * Author URI: http://toiee.jp
 */


/*
	Copyright 2017 toiee Lab (email : desk@toiee.jp)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Use version 2.0 of the update checker.
require 'plugin-update-checker/plugin-update-checker.php';
$MyUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/toiee-lab/wc-restrict/raw/master/update-metadata.json',
	__FILE__,
	'wc-restrict'
);

/**
 * ACFプラグインの存在をチェックする。
 */
function exist_acf_admin_notice_error() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		$class   = 'notice notice-error';
		$message = __( '【重要】WooCommerce for toiee Lab を利用するには、ACFプラグインが必須です。インストールし、有効にしてください。', 'woocommerce-for-toieelab' );

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}
}
add_action( 'admin_notices', 'exist_acf_admin_notice_error' );

/* 主にMailerlite系のcomposerライブラリの読み込み */
require 'vendor/autoload.php';

/* include some feature */
require_once 'includes/woocommerce_settings.php';
require_once 'includes/wcr-functions.php';
require_once 'includes/toiee-shortcodes.php';

/* メインの機能を */
require_once 'includes/class-wcr-content.php';
global $wcr_content;
$wcr_content             = new Woocommerce_SimpleRestrictContent();
$wcr_content->plugin_url = plugins_url( '', __FILE__ );

/* Seriously Simple Podcast 拡張 */
if ( $wcr_content->get_func_option( 'ssp' ) ) {
	require_once 'includes/class-wcr-ssp.php';
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( is_plugin_active( 'seriously-simple-podcasting/seriously-simple-podcasting.php' ) ) {
		global $wcr_ssp;
		$wcr_ssp             = new WCR_SSP();
		$wcr_ssp->plugin_url = plugins_url( '', __FILE__ );
	}
}

/* WooCommerce Subscriptions を分割支払い化する */
if ( $wcr_content->get_func_option( 'sub_inst' ) ) {
	require_once 'includes/class-installment.php';
	global $wc_installment;
	$wc_installment = new ToieeLab_Installment();
}

/* WooCommerce Subscriptions の銀行支払いで有効期限を延長 */
if ( $wcr_content->get_func_option( 'sub_bank' ) ) {
	require_once 'includes/class-subscription-bank.php';
	global $wc_subscription_bank;
	$wc_subscription_bank = new ToieeLab_Subscription_Bank();
}

/* WooCommerce にカスタムタブ機能を追加する */
if ( $wcr_content->get_func_option( 'ctab' ) ) {
	require_once 'includes/class-wcr-ctag.php';
	global $wcr_customtab;
	$wcr_customtab = new Woocommerce_CustomTabs();
}

/* WooCommerce にマイライブラリ機能を追加する */
if ( $wcr_content->get_func_option( 'mylib' ) ) {
	require_once 'includes/class-wcr-mylib.php';
	global $wcr_mylibrary;
	$wcr_mylibrary = new toiee_woocommerce_mylibrary();
}

/* シンプルイベント機能を追加する */
if ( $wcr_content->get_func_option( 'event' ) ) {
	require_once 'includes/class-simple-event.php';
	global $toiee_simple_event;
	$toiee_simple_event = new Toiee_SimpleEvent();
}

/* Mailerlite連携機能を追加する */
if ( $wcr_content->get_func_option( 'mailerlite' ) ) {
	require_once 'includes/class-mailerlite-group.php';
	global $toiee_ml_group;
	$toiee_ml_group = new Toiee_Mailerlite_Group();
}

/* リモートログイン機能を追加する */
if ( $wcr_content->get_func_option( 'rlogin' ) ) {
	require_once 'includes/class-wcr-login.php';
	global $wcr_login;
	$wdr_login = new Toiee_WCLogin();
}

/* Podcast化機能を追加する */
if ( $wcr_content->get_func_option( 'pcast' ) ) {
	require_once 'includes/class-toiee-pcast.php';
	global $toiee_pcast;
	$toiee_pcast = new Toiee_Pcast( __FILE__ );
}

/* Podcast化機能を追加する */
if ( $wcr_content->get_func_option( 'postcast' ) ) {
	require_once 'includes/class-postcast.php';
	global $toiee_postcast;
	$toiee_postcast = new Toiee_Postcast( __FILE__ );
}

/* スクラム教材機能を追加する */
if ( $wcr_content->get_func_option( 'tlm' ) ) {
	require_once 'includes/class-tlm-post.php';
	global $toiee_tlm;
	$toiee_tlm = new Toiee_Tlm_Post( __FILE__ );
}

/* スクラム機能を追加する */
if ( $wcr_content->get_func_option( 'scrum' ) ) {
	require_once 'includes/class-scrum-post.php';
	global $toiee_scrum;
	$toiee_scrum = new Toiee_Scrum_Post( __FILE__ );
}

/* 関連ナレッジ機能を追加する */
if ( $wcr_content->get_func_option( 'tkb' ) ) {
	require_once 'includes/class-tkb-post.php';
	global $toiee_knowledge;
	$toiee_knowledge = new Toiee_Tkb_Post( __FILE__ );
}

/* マガジン機能を追加する */
if ( $wcr_content->get_func_option( 'mag' ) ) {
	require_once 'includes/class-magazine-post.php';
	global $toiee_magazine;
	$toiee_magazine = new Toiee_Magazine_Post();
}

/* ポケてら機能を追加する */
if ( $wcr_content->get_func_option( 'pkt' ) ) {
	require_once 'includes/class-poketera-post.php';
	global $toiee_pocketera;
	$toiee_pocketera = new Toiee_Pocketera_Post();
}

/* 耳デミー機能を追加する */
if ( $wcr_content->get_func_option( 'mdy' ) ) {
	require_once 'includes/class-mmdmy-post.php';
	global $toiee_mimidemy;
	$toiee_mimidemy = new Toiee_Mimidemy_Post( __FILE__ );
}

/* カメデミー機能を追加する */
if ( $wcr_content->get_func_option( 'kdy' ) ) {
	require_once 'includes/class-kdy-post.php';
	global $toiee_kdy;
	$toiee_kdy = new Toiee_Kdy_Post( __FILE__ );
}

/* JetPack を WooCommerce Productページでは実行しない */
function exclude_jetpack_related_from_products( $options ) {
	if ( is_product() ) {
		$options['enabled'] = false;
	}

	return $options;
}
add_filter( 'jetpack_relatedposts_filter_options', 'exclude_jetpack_related_from_products' );


/*
 *  検索の制御を行う。ここでは、toiee_exclude_search
 */
function exclude_search_podcasts( $query ) {
	if ( is_admin() || is_super_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_search() ) {
		$args       = array(
			'public'              => true,
			'_builtin'            => false,
			'exclude_from_search' => false,
		);
		$post_types = get_post_types( $args, 'names', 'and' );

		$exclude_post_type = array( 'scrum_post', 'scrum_episode', 'pkt_feedback', 'pkt_report', 'product' );
		$exclude_post_type = apply_filters( 'toiee_exclude_search_post_type', $exclude_post_type );

		$post_types = array_diff( $post_types, $exclude_post_type );

		$query->set( 'post_type', $post_types );
	}
}
add_filter( 'pre_get_posts', 'exclude_search_podcasts' );

/**
 * 検索エンジンから除外する noindex
 */
function toiee_noindex() {

	$exclude_post_type = array( 'scrum_post', 'pkt_feedback', 'pkt_report', 'product' );
	$exclude_post_type = apply_filters( 'toiee_exclude_search_post_type', $exclude_post_type );

	foreach ( $exclude_post_type as $ptype ) {
		if ( get_post_type() === $ptype ) {
			echo '<meta id="toiee_noindex" name="robots" content="noindex" />' . "\n";
			break;
		}
	}

	if ( is_tax( 'scrum' ) ) {
		echo '<meta id="toiee_noindex2" name="robots" content="noindex" />' . "\n";
	}
}
add_action( 'wp_head', 'toiee_noindex' );
