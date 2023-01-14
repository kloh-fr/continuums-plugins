<?php
/*
 * Plugin Name: Continuum(s) Plugins
 * Description: Enrichissements pour le site Continuum(s)
 * Version: 2014.04.30
 * Text Domain: continuums-plugins
 * @author Luc Poupard
 * @link http://www.kloh.ch
*/

/* ----------------------------- */
/* Sommaire */
/* ----------------------------- */
/*
  == Traduction
  == Nettoyage et désactivation de fonctions inutiles ou gênantes
    -- wp_head
    -- Extension Contact Form 7
    -- Thème ffeeeedd
  == Thumbnails
  == Colonnes latérales
    -- Blog
  == Custom Post Types
    -- Partenaires
  == Shortcodes
    -- Mise en page reportage
    -- Mise en page inventaire
    -- Mise en page dérive
    -- Notes
  == <footer> pour les articles
  == Exclusion des Pages dans les RSS
*/


/* == @section Traduction ==================== */
/**
 * @author Luc Poupard
 * @note I18n : déclare le domaine et l’emplacement des fichiers de traduction
 * @link https://codex.wordpress.org/I18n_for_WordPress_Developers
*/
function continnums_init() {
  $plugin_dir = basename( dirname( __FILE__ ) );
  load_plugin_textdomain( 'continuums-plugins', false, $plugin_dir );
}

add_action( 'plugins_loaded', 'continnums_init' );


/* == @section Nettoyage et désactivation de fonctions inutiles ou gênantes ==================== */
/* -- @subsection wp_head -------------------- */
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'parent_post_rel_link_wp_head', 10, 0 );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );

/* -- @subsection Extension Contact Form 7 -------------------- */
/**
 * @note Les CSS et JS de Contact Form 7 sont insérés sur toutes les pages par défaut. Cette fonction permet de l'afficher uniquement sur les pages où il y a un formulaire.
 * @see http://dannyvankooten.com/3935/only-load-contact-form-7-scripts-when-needed/
 */
function continuums_dequeue_contact() {
  if( is_page( 21 ) ) {
    // La page de contact porte l'ID 21 donc on veut garder les styles et scripts Contact Form 7
  } else {
    // Dans tous les autres cas, on ne veut pas appeler les styles et scripts du plugin
    wp_dequeue_script( 'contact-form-7' );
    wp_dequeue_style( 'contact-form-7' );
  } 
}
 
add_action( 'wp_enqueue_scripts', 'continuums_dequeue_contact' );

/* -- @subsection Thème ffeeeedd -------------------- */
/**
 * @link http://venutip.com/content/right-way-override-theme-functions
 */
function continuums_remove_ffeedd_actions() {
  // Référencement SEO et Social
  remove_action( 'wp_head', 'ffeeeedd__injection__description' );
  remove_action( 'wp_head', 'ffeeeedd__injection__image' );
  remove_filter( 'wp_head', 'ffeeeedd__injection__canonical' );
  // Minification HTML
  remove_action( 'get_header', 'ffeeeedd__minification__debut' );
}

add_action( 'init', 'continuums_remove_ffeedd_actions' );


/* == @section Thumbnails ==================== */
/**
* @author Luc Poupard
* @see http://wp.ruche.org/2010/12/20/utiliser-les-images-a-la-une/
*/
add_image_size( 'partage-social', 1600, 630, true );


/* == @section Colonnes latérales ==================== */
/**
*  @author Luc Poupard
*/
function continuums_widgets_init() {  
  /* -- @subsection Blog -------------------- */
  register_sidebar( array(
    'name' => __( 'Blog', 'continuums-plugins' ),
    'id' => 'blog',
    'before_widget' => '<div id="%1$s" class="widget %2$s col w-25">',
    'after_widget' => '</div>',
    'before_title' => '<h3 class="widget--title">',
    'after_title' => '</h3>',
  ) );
}

add_action( 'widgets_init', 'continuums_widgets_init' );


/* == @section Custom Post Types ==================== */
/**
*  @author Luc Poupard
*  @see http://www.kevinleary.net/wordpress-dashicons-custom-post-types/
*/

add_action( 'init', 'continuums_post_type' );

function continuums_post_type() {
  /* -- @subsection Partenaires -------------------- */
  register_post_type( 'continuums_partner',
    array(
      'labels' => array(
        'name' => __( 'Partenaires', 'continuums-plugins' ),
        'singular_name' => __( 'Partenaire', 'continuums-plugins' ),
        'add_new_item' => __( 'Ajouter un nouveau partenaire', 'continuums-plugins' ),
        'new_item' => __( 'Nouveau partenaire', 'continuums-plugins' ),
        'edit_item' => __( 'Éditer un partenaire', 'continuums-plugins' ),
        'view_item' => __( 'Voir le partenaire', 'continuums-plugins' ),
        'all_items' => __( 'Tous les partenaires', 'continuums-plugins' ),
        'search_items' => __( 'Chercher des partenaires', 'continuums-plugins' ),
        'not_found' => __( 'Pas de partenaire trouvé.', 'continuums-plugins' ),
        'not_found_in_trash' => __( 'Pas de partenaire trouvé dans la corbeille.', 'continuums-plugins' )
      ),
      'public' => true,
      'exclude_from_search' => true,
      'show_in_nav_menus' => false,
      'menu_position' => 10,
      'menu_icon' => 'dashicons-groups',
      'supports' =>  array(
        'title'
      ),
      'rewrite' => array(
        'slug' => 'partenaire'
      )
    )
  );
}


/* == @section Shortcodes ==================== */
/**
*  @author Luc Poupard
*  @see http://wp-themes-pro.com/shortcode-wordpress/
*/

/* On empêche l'insertion de <p> et <br> automatique dans les shortcodes
 * @see http://sww.co.nz/solution-to-wordpress-adding-br-and-p-tags-around-shortcodes/
 */
remove_filter( 'the_content', 'wpautop' );
add_filter( 'the_content', 'wpautop' , 99);

/* -- @subsection Mise en page reportage -------------------- */
// Mise en page centrée
function continnums_reportage( $atts, $content=null ) {
  $content = wpautop( trim( $content ) );
  return '<div class="mod reportage">' . do_shortcode($content) . '</div>';
}

add_shortcode( 'reportage', 'continnums_reportage' );

/* -- @subsection Mise en page inventaire -------------------- */
// Mise en page décalée à gauche
function continnums_inventaire( $atts, $content=null ) {
  $content = wpautop( trim( $content ) );
  return '<div class="mod inventaire">' . do_shortcode($content) . '</div>';
}

add_shortcode( 'inventaire', 'continnums_inventaire' );

/* -- @subsection Mise en page dérive -------------------- */
// Mise en page décalée à droite
function continnums_derive( $atts, $content=null ) {
  $content = wpautop( trim( $content ) );
  return '<div class="mod derive">' . do_shortcode($content) . '</div>';
}

add_shortcode( 'derive', 'continnums_derive' );

/* -- @subsection Notes -------------------- */
/* Affichage des renvois */
function continuums_renvoi( $atts, $content = null ) {
  global $post;
  return '<sup aria-describedby="note-' . $atts['id'] . '" id="lien-' . $atts['id'] . '"><a class="scroll print-hidden" href="' . get_permalink( $post->ID ) . '#note-' . $atts['id'] . '">[' . $atts['id'] . ']</a></sup>';
}

add_shortcode( 'renvoi', 'continuums_renvoi' );

/* Affichage de la liste des notes en bas de page */
function continuums_notes( $atts, $content=null ) {
  return '<ol class="m-reset pt2 pb2">' . do_shortcode($content) . '</ol>';
}

add_shortcode( 'notes', 'continuums_notes' );

function continuums_note( $atts, $content = null ) {
  global $post;
  extract( shortcode_atts(
    array(
      'id' => '',
    ),
    $atts, 'note' )
  );
  return '<li role="note" id="note-' . esc_attr( $id ) . '">' . $content . '&nbsp;<a class="scroll print-hidden" href="' . get_permalink( $post->ID ) . '#lien-' . esc_attr( $id ) . '"><span aria-hidden="true">[&rarr;]</span></a></li>';
}

add_shortcode( 'note', 'continuums_note' );


/* == @section <footer> pour les articles ==================== */
/**
 * @author Luc Poupard
 * @note Inspiré de la fonction «ffeeeedd__meta» du thème ffeeeedd
 * @author Gaël Poupard
 * @see http://wordpress.org/extend/themes/twentytwelve
 */

if ( ! function_exists( 'continuums_meta' ) ) {
  function continuums_meta() {
    // Liste des catégories & tags avec un séparateur.
    $categories_list = get_the_category_list( ( ', ' ) );
    $tag_list = get_the_tag_list( '', ( ', ' ) );
    // On génère le contenu en fonction des informations disponibles ( mots-clés, catégories, auteur ).
    if ( '' != $tag_list ) {
      echo '<p>' . __( 'Article rédigé par', 'continuums-plugins' ) . ' <a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" itemprop="author" class="vcard author"><span class="fn">' . get_the_author() . '</span></a> ' . __( 'dans', 'continuums-plugins' ) . ' <span itemprop="keywords">' . $categories_list . '.</span><br />' . __( 'Mots-clés&nbsp;:', 'continuums-plugins' ) .' <span itemprop="keywords">' . $tag_list . '.</span><br />';
    } elseif ( '' != $categories_list ) {
      echo '<p>' . __( 'Article rédigé par', 'continuums-plugins' ) . ' <a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" itemprop="author" class="vcard author"><span class="fn">' . get_the_author() . '</span></a> ' . __( 'dans', 'continuums-plugins' ) . ' <span itemprop="keywords">' . $categories_list . '.</span><br />';
    } else {
      echo '<p>' . __( 'Article rédigé par', 'continuums-plugins' ) . ' <a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" itemprop="author" class="vcard author"><span class="fn">' . get_the_author() . '</span></a>.<br />';
    }

    // On génère la date de dernière modification si elle est différente de celle de publication
    if ( get_the_modified_time( 'Y-m-j' ) != get_the_time( 'Y-m-j' ) ) {
      echo ' ' . __( 'Publié le', 'continuums-plugins' ) . ' <time datetime="' . get_the_time( 'Y-m-j' ) . '" itemprop="datePublished">' . get_the_time( __( 'j F Y', 'continuums-plugins' ) ) . '</time> (' . __( 'modifié le', 'continuums-plugins' ) . ' <time class="updated" datetime="' . get_the_modified_date( 'Y-m-d' ) . '" itemprop="dateModified">' . get_the_modified_date() . '</time>).</p>';
    } else {
      echo ' ' . __( 'Publié le', 'continuums-plugins' ) . ' <time datetime="' . get_the_time( 'Y-m-j' ) . '" itemprop="datePublished">' . get_the_time( __( 'j F Y', 'continuums-plugins' ) ) . '</time>.</p>';
    }
  }
}


/* == @section Exclusion des Pages dans les RSS ==================== */
/**
 * @author Luc Poupard
 * @note Exclusion par ID de chaque page
 * @see http://wpengineer.com/2175/exclude-post-from-wordpress-feed/
 */
function continuums_feed( $query ) {
  if ( !$query->is_admin && $query->is_feed ) {
    $query->set( 'post__not_in', array(
      2, // Le projet
      10, // Partenaires
      13, // Mentions légales
      15, // Accueil
      18, // Blog
      21, // Contact
      25, // Planning
      83, // Plan du site
      271, // Devenir partenaire
      273 // Itinéraire
    ) );
  }
  return $query;
}

add_filter( 'pre_get_posts', 'continuums_feed' );


/* == @section Personnalisation Move Login ==================== */
/**
 * @note Plugin de sécurité permettant de changer les URL sensibles de l'admin
 * @see http://www.screenfeed.fr/blog/move-login-passe-en-version-1-1-01588
 */
$sfml_slugs = apply_filters ( 'sfml_slugs', array(
  'logout'  => 'au-revoir',
  'lostpassword'  => 'oups-un-oubli',
  'resetpass' => 'resetez-moi',
  'register'  => 'enregistrez-vous',
  'login'   => 'entrez-svp',
) );