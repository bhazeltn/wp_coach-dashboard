
<?php
// Functions for Skating Coach Management Plugin

add_filter('acf/fields/relationship/query/key=linked_yearly_plan', function ($args, $field, $post_id) {
    if (isset($_GET['skater_id'])) {
        $skater_id = intval($_GET['skater_id']);
        $args['meta_query'] = [
            [
                'key' => 'linked_skaters',
                'value' => '"' . $skater_id . '"',
                'compare' => 'LIKE'
            ]
        ];
    }
    return $args;
}, 10, 3);
