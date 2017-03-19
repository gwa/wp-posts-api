<?php

/**
 * Returns the most recent posts.
 *
 * @param int $num_posts No. of posts to return.
 * return array
 */
function gwasw_get_recent($num_posts = 10) {
    $args = [
        'numberposts' => $num_posts,
        'offset' => 0,
        'category' => 0,
        'orderby' => 'post_date',
        'order' => 'DESC',
        'include' => '',
        'exclude' => '',
        'meta_key' => '',
        'meta_value' =>'',
        'post_type' => 'post',
        'post_status' => 'publish'
    ];

    $posts = [];
    foreach (wp_get_recent_posts($args) as $post) {
        $posts[] = gwasw_get_post_data($post);
    }

    // Return, oldest first.
    return array_reverse($posts);
}

/**
 * Returns the posts following a particular post.
 *
 * @param int $idpost ID of post to fetch posts after.
 * @param int $num_posts No. of posts to return.
 * @return array
 */
function gwasw_get_since($idpost, $num_posts = 10) {
    global $wpdb;

    // We have to perform a custom query to get the posts IDs.
    $sql = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' AND ID > %d LIMIT 0,%d", $idpost, $num_posts);
    $idposts = $wpdb->get_col($sql);

    // Use get_posts to get the actual posts for the IDs.
    $args = [
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => $num_posts,
        'orderby' => 'ID',
        'order' => 'ASC',
        'post__in' => $idposts,
    ];
    $results = get_posts($args) ?: [];

    // Get post data for each post.
    $posts = gwasw_extract_post_array($results);

    return $posts;
}

/**
 * Returns the posts following a particular post.
 *
 * @param int $idpost ID of post to fetch posts after.
 * @return array|NULL
 */
function gwasw_get_single($idpost) {

    // Use get_posts to get the actual posts for the IDs.
    $args = [
        'post_type' => 'post',
        'post_status' => 'publish',
        'include' => $idpost,
    ];
    $results = get_posts($args) ?: [];

    if (!count($results)) {
        return NULL;
    }

    // Get post data for each post.
    $posts = gwasw_extract_post_array($results);

    // Return first
    return $posts[0];
}

// --------

function gwasw_get_post_data(array $post) {
    $idpost = $post['ID'];

    $postdata = new \stdClass;
    $postdata->id = $idpost;
    $postdata->guid = get_the_guid($idpost);
    $postdata->title = $post['post_title'];
    $postdata->excerpt = gwasw_get_post_excerpt($post);
    $postdata->published_gmt = $post['post_date_gmt'];
    $postdata->url = get_permalink($idpost);
    // TODO Setting for thumbnail image size?
    $postdata->imageurl = get_the_post_thumbnail_url($idpost, 'medium');

    // Get tags.
    $tags = [];
    $posttags = get_the_terms($idpost, 'post_tag') ?: [];
    foreach ($posttags as $tag) {
        $tags[] = $tag->slug;
    }
    $postdata->tags = $tags;

    return $postdata;
}

function gwasw_get_post_excerpt(array $post) {
    $maxlength = 200;

    if (isset($post['post_excerpt']) && !empty($post['post_excerpt'])) {
        $content = $post['post_excerpt'];
    } else {
        $content = $post['post_content'];
        $content = do_shortcode($content);
        $content = strip_tags($content);
    }

    if (strlen($content) > $maxlength) {
        $content = substr($content, 0, $maxlength) . '...';
    }

    return $content;
}

function gwasw_get_query_var($key) {
    global $wp;
    if (!array_key_exists($key, $wp->query_vars)) {
        return NULL;
    }
    return $wp->query_vars[$key];
}

function gwasw_extract_post_array(array $results) {
    $posts = [];
    foreach ($results as $post) {
        $arr = [];
        foreach (get_object_vars($post) as $key => $value) {
            $arr[$key] = $value;
        }
        $posts[] = gwasw_get_post_data($arr);
    }
    return $posts;
}

// --------

$data = new \stdClass;

if ($idpost = gwasw_get_query_var('idpost')) {
    // Get single post
    $data->post = gwasw_get_single((int) $idpost);
} elseif ($idsince = gwasw_get_query_var('idsince')) {
    // Get posts since
    $data->posts = gwasw_get_since($idsince);
} else {
    // Get latest posts
    $data->posts = gwasw_get_recent();
}

// Output content
header('Content-Type: application/json');
echo json_encode($data);
