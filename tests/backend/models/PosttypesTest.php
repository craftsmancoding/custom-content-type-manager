<?php

class PosttypesTest extends PHPUnit_Framework_TestCase
{
    private $dic;
    private $resource;
    private $controller;
    private $mock_resource;
    private $mock_controller;



    protected function setUp()
    {
        $this->dic = new Container();
        $this->dic['POST'] = array(
            'data' => array(
                'id'         => 'xyz',
                'attributes' => array(
                    'x' => 'xylophone',
                    'y' => 'yak',
                    'z' => 'zebra'
                )
            )
        );
        // TODO: put this in a dedicated mock-functions area
        $this->dic['get_post_types'] = $this->dic->protect(function ($args=array(), $output='names', $operator='and') {
            return array (
                'post' =>
                    stdClass::__set_state(array(
                        'labels' =>
                            stdClass::__set_state(array(
                                'name' => 'Posts',
                                'singular_name' => 'Post',
                                'add_new' => 'Add New',
                                'add_new_item' => 'Add New Post',
                                'edit_item' => 'Edit Post',
                                'new_item' => 'New Post',
                                'view_item' => 'View Post',
                                'search_items' => 'Search Posts',
                                'not_found' => 'No posts found.',
                                'not_found_in_trash' => 'No posts found in Trash.',
                                'parent_item_colon' => NULL,
                                'all_items' => 'All Posts',
                                'menu_name' => 'Posts',
                                'name_admin_bar' => 'Post',
                            )),
                        'description' => '',
                        'public' => true,
                        'hierarchical' => false,
                        'exclude_from_search' => false,
                        'publicly_queryable' => true,
                        'show_ui' => true,
                        'show_in_menu' => true,
                        'show_in_nav_menus' => true,
                        'show_in_admin_bar' => true,
                        'menu_position' => NULL,
                        'menu_icon' => NULL,
                        'capability_type' => 'post',
                        'map_meta_cap' => true,
                        'register_meta_box_cb' => NULL,
                        'taxonomies' =>
                            array (
                            ),
                        'has_archive' => false,
                        'rewrite' => false,
                        'query_var' => false,
                        'can_export' => true,
                        'delete_with_user' => true,
                        '_builtin' => true,
                        '_edit_link' => 'post.php?post=%d',
                        'name' => 'post',
                        'cap' =>
                            stdClass::__set_state(array(
                                'edit_post' => 'edit_post',
                                'read_post' => 'read_post',
                                'delete_post' => 'delete_post',
                                'edit_posts' => 'edit_posts',
                                'edit_others_posts' => 'edit_others_posts',
                                'publish_posts' => 'publish_posts',
                                'read_private_posts' => 'read_private_posts',
                                'read' => 'read',
                                'delete_posts' => 'delete_posts',
                                'delete_private_posts' => 'delete_private_posts',
                                'delete_published_posts' => 'delete_published_posts',
                                'delete_others_posts' => 'delete_others_posts',
                                'edit_private_posts' => 'edit_private_posts',
                                'edit_published_posts' => 'edit_published_posts',
                                'create_posts' => 'edit_posts',
                            )),
                        'label' => 'Posts',
                    )),
                'page' =>
                    stdClass::__set_state(array(
                        'labels' =>
                            stdClass::__set_state(array(
                                'name' => 'Pages',
                                'singular_name' => 'Page',
                                'add_new' => 'Add New',
                                'add_new_item' => 'Add New Page',
                                'edit_item' => 'Edit Page',
                                'new_item' => 'New Page',
                                'view_item' => 'View Page',
                                'search_items' => 'Search Pages',
                                'not_found' => 'No pages found.',
                                'not_found_in_trash' => 'No pages found in Trash.',
                                'parent_item_colon' => 'Parent Page:',
                                'all_items' => 'All Pages',
                                'menu_name' => 'Pages',
                                'name_admin_bar' => 'Page',
                            )),
                        'description' => '',
                        'public' => true,
                        'hierarchical' => true,
                        'exclude_from_search' => false,
                        'publicly_queryable' => false,
                        'show_ui' => true,
                        'show_in_menu' => true,
                        'show_in_nav_menus' => true,
                        'show_in_admin_bar' => true,
                        'menu_position' => NULL,
                        'menu_icon' => NULL,
                        'capability_type' => 'page',
                        'map_meta_cap' => true,
                        'register_meta_box_cb' => NULL,
                        'taxonomies' =>
                            array (
                            ),
                        'has_archive' => false,
                        'rewrite' => false,
                        'query_var' => false,
                        'can_export' => true,
                        'delete_with_user' => true,
                        '_builtin' => true,
                        '_edit_link' => 'post.php?post=%d',
                        'name' => 'page',
                        'cap' =>
                            stdClass::__set_state(array(
                                'edit_post' => 'edit_page',
                                'read_post' => 'read_page',
                                'delete_post' => 'delete_page',
                                'edit_posts' => 'edit_pages',
                                'edit_others_posts' => 'edit_others_pages',
                                'publish_posts' => 'publish_pages',
                                'read_private_posts' => 'read_private_pages',
                                'read' => 'read',
                                'delete_posts' => 'delete_pages',
                                'delete_private_posts' => 'delete_private_pages',
                                'delete_published_posts' => 'delete_published_pages',
                                'delete_others_posts' => 'delete_others_pages',
                                'edit_private_posts' => 'edit_private_pages',
                                'edit_published_posts' => 'edit_published_pages',
                                'create_posts' => 'edit_pages',
                            )),
                        'label' => 'Pages',
                    )),
                'attachment' =>
                    stdClass::__set_state(array(
                        'labels' =>
                            stdClass::__set_state(array(
                                'name' => 'Media',
                                'singular_name' => 'Media',
                                'add_new' => 'Add New',
                                'add_new_item' => 'Add New Post',
                                'edit_item' => 'Edit Media',
                                'new_item' => 'New Post',
                                'view_item' => 'View Attachment Page',
                                'search_items' => 'Search Posts',
                                'not_found' => 'No posts found.',
                                'not_found_in_trash' => 'No posts found in Trash.',
                                'parent_item_colon' => NULL,
                                'all_items' => 'Media',
                                'menu_name' => 'Media',
                                'name_admin_bar' => 'Media',
                            )),
                        'description' => '',
                        'public' => true,
                        'hierarchical' => false,
                        'exclude_from_search' => false,
                        'publicly_queryable' => true,
                        'show_ui' => true,
                        'show_in_menu' => true,
                        'show_in_nav_menus' => false,
                        'show_in_admin_bar' => true,
                        'menu_position' => NULL,
                        'menu_icon' => NULL,
                        'capability_type' => 'post',
                        'map_meta_cap' => true,
                        'register_meta_box_cb' => NULL,
                        'taxonomies' =>
                            array (
                            ),
                        'has_archive' => false,
                        'rewrite' => false,
                        'query_var' => false,
                        'can_export' => true,
                        'delete_with_user' => true,
                        '_builtin' => true,
                        '_edit_link' => 'post.php?post=%d',
                        'name' => 'attachment',
                        'cap' =>
                            stdClass::__set_state(array(
                                'edit_post' => 'edit_post',
                                'read_post' => 'read_post',
                                'delete_post' => 'delete_post',
                                'edit_posts' => 'edit_posts',
                                'edit_others_posts' => 'edit_others_posts',
                                'publish_posts' => 'publish_posts',
                                'read_private_posts' => 'read_private_posts',
                                'read' => 'read',
                                'delete_posts' => 'delete_posts',
                                'delete_private_posts' => 'delete_private_posts',
                                'delete_published_posts' => 'delete_published_posts',
                                'delete_others_posts' => 'delete_others_posts',
                                'edit_private_posts' => 'edit_private_posts',
                                'edit_published_posts' => 'edit_published_posts',
                                'create_posts' => 'upload_files',
                            )),
                        'label' => 'Media',
                    )),
                'revision' =>
                    stdClass::__set_state(array(
                        'labels' =>
                            stdClass::__set_state(array(
                                'name' => 'Revisions',
                                'singular_name' => 'Revision',
                                'add_new' => 'Add New',
                                'add_new_item' => 'Add New Post',
                                'edit_item' => 'Edit Post',
                                'new_item' => 'New Post',
                                'view_item' => 'View Post',
                                'search_items' => 'Search Posts',
                                'not_found' => 'No posts found.',
                                'not_found_in_trash' => 'No posts found in Trash.',
                                'parent_item_colon' => NULL,
                                'all_items' => 'Revisions',
                                'menu_name' => 'Revisions',
                                'name_admin_bar' => 'Revision',
                            )),
                        'description' => '',
                        'public' => false,
                        'hierarchical' => false,
                        'exclude_from_search' => true,
                        'publicly_queryable' => false,
                        'show_ui' => false,
                        'show_in_menu' => false,
                        'show_in_nav_menus' => false,
                        'show_in_admin_bar' => false,
                        'menu_position' => NULL,
                        'menu_icon' => NULL,
                        'capability_type' => 'post',
                        'map_meta_cap' => true,
                        'register_meta_box_cb' => NULL,
                        'taxonomies' =>
                            array (
                            ),
                        'has_archive' => false,
                        'rewrite' => false,
                        'query_var' => false,
                        'can_export' => false,
                        'delete_with_user' => true,
                        '_builtin' => true,
                        '_edit_link' => 'revision.php?revision=%d',
                        'name' => 'revision',
                        'cap' =>
                            stdClass::__set_state(array(
                                'edit_post' => 'edit_post',
                                'read_post' => 'read_post',
                                'delete_post' => 'delete_post',
                                'edit_posts' => 'edit_posts',
                                'edit_others_posts' => 'edit_others_posts',
                                'publish_posts' => 'publish_posts',
                                'read_private_posts' => 'read_private_posts',
                                'read' => 'read',
                                'delete_posts' => 'delete_posts',
                                'delete_private_posts' => 'delete_private_posts',
                                'delete_published_posts' => 'delete_published_posts',
                                'delete_others_posts' => 'delete_others_posts',
                                'edit_private_posts' => 'edit_private_posts',
                                'edit_published_posts' => 'edit_published_posts',
                                'create_posts' => 'edit_posts',
                            )),
                        'label' => 'Revisions',
                    )),
                'nav_menu_item' =>
                    stdClass::__set_state(array(
                        'labels' =>
                            stdClass::__set_state(array(
                                'name' => 'Navigation Menu Items',
                                'singular_name' => 'Navigation Menu Item',
                                'add_new' => 'Add New',
                                'add_new_item' => 'Add New Post',
                                'edit_item' => 'Edit Post',
                                'new_item' => 'New Post',
                                'view_item' => 'View Post',
                                'search_items' => 'Search Posts',
                                'not_found' => 'No posts found.',
                                'not_found_in_trash' => 'No posts found in Trash.',
                                'parent_item_colon' => NULL,
                                'all_items' => 'Navigation Menu Items',
                                'menu_name' => 'Navigation Menu Items',
                                'name_admin_bar' => 'Navigation Menu Item',
                            )),
                        'description' => '',
                        'public' => false,
                        'hierarchical' => false,
                        'exclude_from_search' => true,
                        'publicly_queryable' => false,
                        'show_ui' => false,
                        'show_in_menu' => false,
                        'show_in_nav_menus' => false,
                        'show_in_admin_bar' => false,
                        'menu_position' => NULL,
                        'menu_icon' => NULL,
                        'capability_type' => 'post',
                        'map_meta_cap' => true,
                        'supports' =>
                            array (
                            ),
                        'register_meta_box_cb' => NULL,
                        'taxonomies' =>
                            array (
                            ),
                        'has_archive' => false,
                        'rewrite' => false,
                        'query_var' => false,
                        'can_export' => true,
                        'delete_with_user' => false,
                        '_builtin' => true,
                        '_edit_link' => 'post.php?post=%d',
                        'name' => 'nav_menu_item',
                        'cap' =>
                            stdClass::__set_state(array(
                                'edit_post' => 'edit_post',
                                'read_post' => 'read_post',
                                'delete_post' => 'delete_post',
                                'edit_posts' => 'edit_posts',
                                'edit_others_posts' => 'edit_others_posts',
                                'publish_posts' => 'publish_posts',
                                'read_private_posts' => 'read_private_posts',
                                'read' => 'read',
                                'delete_posts' => 'delete_posts',
                                'delete_private_posts' => 'delete_private_posts',
                                'delete_published_posts' => 'delete_published_posts',
                                'delete_others_posts' => 'delete_others_posts',
                                'edit_private_posts' => 'edit_private_posts',
                                'edit_published_posts' => 'edit_published_posts',
                                'create_posts' => 'edit_posts',
                            )),
                        'label' => 'Navigation Menu Items',
                    )),
            );
        });
    }

    public function testX()
    {

    }
}