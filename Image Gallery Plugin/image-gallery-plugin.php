<?php

/**
 * Plugin Name: Image Gallery Plugin
 * Description: Your plugin description goes here.
 * Version: 1.0
 * Author: Your Name
 */

// add styles
function image_gallery_enqueue_styles() {
    wp_enqueue_style('image-gallery-style', plugins_url('/assets/image-gallery-style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'image_gallery_enqueue_styles');

// Shortcode for displaying the image gallery
function image_gallery_shortcode() {
    global $wpdb;
    $table_name_categories = $wpdb->prefix . 'categories';
    $table_name_images_gallery = $wpdb->prefix . 'images_gallery';

    $sql = "SELECT c.id_cat, c.titre, c.url_imgCat, i.url_img
            FROM $table_name_categories c
            INNER JOIN $table_name_images_gallery i ON c.id_img = i.id_img";
    $result = $wpdb->get_results($sql);

    if (!empty($result)) {
        $output = "<div class='category-container'>";

        foreach ($result as $row) {
            $categoryID = $row->id_cat;
            $categoryTitle = $row->titre;
            $categoryImageURL = $row->url_imgCat;
            $imageURL = $row->url_img;

            $output .= "<a href='" . esc_url(add_query_arg('cat_id', $categoryID, get_permalink(get_page_by_path('template')))) . "'>
                <div class='category-img'>
                    <img src='$categoryImageURL' alt='$categoryTitle'>
                    <div class='category-title-overlay'>$categoryTitle</div>
                </div>
            </a>";
        }

        $output .= "</div>";

        return $output;
    } else {
        return "No categories found.";
    }
}
add_shortcode('image_gallery', 'image_gallery_shortcode');

// Redirect to the sub-images page
function redirect_to_sub_images_page() {
    if (isset($_GET['cat_id'])) {
        $categoryID = $_GET['cat_id'];
        $subImagesPageURL = get_permalink(get_page_by_path('template'));
        exit;
    }
}
add_action('template_redirect', 'redirect_to_sub_images_page');

// Template for displaying the sub-images page
function image_gallery_sub_images_page_template() {
    if (isset($_GET['cat_id'])) {
        $categoryID = $_GET['cat_id'];

        echo "<div class='banner-container'>
                <div class='category-title'>
                    <div class='title'>Category ID: $categoryID</div>
                </div>
            </div>";

        global $wpdb;
        $table_name_images_gallery = $wpdb->prefix . 'images_gallery';

        $sql = $wpdb->prepare("SELECT url_img FROM $table_name_images_gallery WHERE id_cat = %d", $categoryID);
        $result = $wpdb->get_results($sql);

        if (!empty($result)) {
            echo "<div class='image-container'>";

            foreach ($result as $row) {
                $imageURL = $row->url_img;

                echo "<img class='sub-img' src='$imageURL' alt='Sub Image'>";
            }

            echo "</div>";
        } else {
            echo "No images found for this category.";
        }
    } else {
        echo "Invalid category.";
    }
}
add_shortcode('image_gallery_sub_images', 'image_gallery_sub_images_page_template');
