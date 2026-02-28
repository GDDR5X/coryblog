<?php
// migrate_to_md.php
require_once 'includes/config.php';
require_once 'includes/md_db.php';

echo "Starting migration...\n";

// 1. Read existing posts
$json_posts = read_json(FILE_POSTS);
$json_comments = read_json(FILE_COMMENTS);

if (empty($json_posts)) {
    echo "No posts found in JSON database.\n";
} else {
    echo "Found " . count($json_posts) . " posts.\n";
    
    $comments_updated = false;

    foreach ($json_posts as $post) {
        $old_id = $post['id'];
        $slug = $post['slug'];
        
        echo "Migrating Post ID: $old_id (Slug: $slug)...\n";
        
        // Save to MD
        if (save_post_md($post)) {
            echo "  -> Saved to posts/$slug.md\n";
            
            // Update comments
            $count = 0;
            foreach ($json_comments as &$comment) {
                if ($comment['post_id'] == $old_id) {
                    $comment['post_id'] = $slug;
                    $count++;
                    $comments_updated = true;
                }
            }
            if ($count > 0) {
                echo "  -> Updated $count comments to link to new slug.\n";
            }
        } else {
            echo "  -> FAILED to save MD file.\n";
        }
    }
    
    // Save updated comments
    if ($comments_updated) {
        write_json(FILE_COMMENTS, $json_comments);
        echo "Comments database updated with new Post IDs (slugs).\n";
    }
    
    // Rename old posts.json
    if (rename(FILE_POSTS, FILE_POSTS . '.bak')) {
        echo "Renamed posts.json to posts.json.bak\n";
    }
}

echo "Migration complete.\n";
?>
