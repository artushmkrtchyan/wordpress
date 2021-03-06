<?php

// Add term page
function pippin_taxonomy_add_new_meta_field($term) {
    global $blog_id;
    switch_to_blog(1);
    // this will add the custom meta field to the add new term page
    $args = array(
        'type' => 'post',
        'child_of' => 0,
        'parent' => '',
        'orderby' => 'name',
        'order' => 'ASC',
        'hide_empty' => 0,
        'hierarchical' => 1,
        'exclude' => '',
        'include' => '',
        'number' => '',
        'taxonomy' => 'category',
        'pad_counts' => false
    );
    $transId = -1;
    $categories = get_categories($args);
    restore_current_blog();
    $options = get_option('categoryTranslateId');
    if ($options && $options[$term->term_id]) {
        $transId = $options[$term->term_id]['translatecat'];
    }

    if ($blog_id != 1) {
        ?>
        <table class="form-table">
            <tbody>
                <tr class="form-field term-parent-wrap">
                    <th scope="row"><label for="translatecat">Translate Am Category</label></th>
                    <td>
                        <select name="translatecat" id="translatecat" class="postform">
                            <option value="-1">None</option>
                            <?php
                            foreach ($categories as $cat) {

                                if ($transId == $cat->term_id) {
                                    echo '<option selected="selected" class="level-0" value="' . $cat->term_id . '">' . $cat->name . '</option>';
                                } else {
                                    echo '<option class="level-0" value="' . $cat->term_id . '">' . $cat->name . '</option>';
                                }
                            }
                            ?>

                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php
    }
}

function update_my_category_fields($term_id) {
    global $blog_id;
    if ($blog_id != 1) {
        if ($_POST['taxonomy'] == 'category'):
            $tag_extra_fields = get_option('categoryTranslateId');
            $tag_extra_fields[$term_id]['translatecat'] = strip_tags($_POST['translatecat']);
            update_option('categoryTranslateId', $tag_extra_fields);

            switch_to_blog(1);
            $tag_extra_fields = get_option('categoryTranslateId');
            $tag_extra_fields[strip_tags($_POST['translatecat'])]['translatecat'] = $term_id;
            update_option('categoryTranslateId', $tag_extra_fields);
            restore_current_blog();
        endif;
    }
}

add_action('category_edit_form', 'pippin_taxonomy_add_new_meta_field');
add_action('category_add_form', 'pippin_taxonomy_add_new_meta_field');
add_filter('edited_terms', 'update_my_category_fields');

function get_post_translate_url($post_id) {
    global $blog_id, $wpdb;
    $result = '';

    if ($blog_id == 1) {
        $en_id = $wpdb->get_var('SELECT post_id FROM wp_2_postmeta WHERE meta_key = "arm_post_id" AND meta_value = "' . $post_id . '"');

        switch_to_blog(1);
        $result .= '<div class="cube current lang"><a href="' . get_post_permalink($post_id) . '">' . __('Հայ', 'bootstrap-basic') . '</a></div>';
        restore_current_blog();

        if($en_id && $en_id !=''){
        	switch_to_blog(2);
        	$result .= '<div class="cube  lang"><a href="' . get_post_permalink($en_id) . '">' . __('Eng', 'bootstrap-basic') . '</a></div>';
        	restore_current_blog();
        }else{
        	$result .= '<div class="cube lang"><a href="/en">' . __('Eng', 'bootstrap-basic') . '</a></div>';
        }


    } elseif ($blog_id == 2) {

    	$arm_id = get_post_meta($post_id, "arm_post_id", true);

    	if($arm_id && $arm_id !=''){
    		switch_to_blog(1);
            $result .= '<div class="cube lang"><a href="' . get_post_permalink($arm_id) . '">' . __('Հայ', 'bootstrap-basic') . '</a></div>';
            restore_current_blog();

            $en_id = $wpdb->get_var('SELECT post_id FROM wp_2_postmeta WHERE meta_key = "arm_post_id" AND meta_value = "' . $arm_id . '"');
            if($en_id && $en_id !=''){
            switch_to_blog(2);
	        	$result .= '<div class="cube lang"><a href="' . get_post_permalink($en_id) . '">' . __('Eng', 'bootstrap-basic') . '</a></div>';
	        	restore_current_blog();
	        }else{
	        	$result .= '<div class="cube lang"><a href="/en">' . __('Eng', 'bootstrap-basic') . '</a></div>';
	        }

    	}

    }

    return $result;
}

function get_category_translate_url($catId) {
    global $blog_id;

    $option = get_option('categoryTranslateId');
    $transId = $option[$catId]['translatecat'];

    if ($blog_id == 1) {
        $result .= '<div class="cube current lang"><a href="' . get_category_link($catId) . '">' . __('Հայ', 'bootstrap-basic') . '</a></div>';

        switch_to_blog(2);
        $option = get_option('categoryTranslateId');
        foreach ($option as $key => $item) {
        	if($item['translatecat'] == $catId){
        		$en_id = $key;
        	}
        }
        $result .= '<div class="cube lang"><a href="' . get_category_link($en_id) . '">' . __('Eng', 'bootstrap-basic') . '</a></div>';
        restore_current_blog();

    } elseif ($blog_id == 2) {
        switch_to_blog(1);
        $result .= '<div class="cube lang"><a href="' . get_category_link($transId) . '">' . __('Հայ', 'bootstrap-basic') . '</a></div>';
        restore_current_blog();
        $result .= '<div class="cube current lang"><a href="' . get_category_link($catId) . '">' . __('Eng', 'bootstrap-basic') . '</a></div>';

    }
    return $result;
}

function get_page_translate_url($pageId) {
    global $blog_id;
    $result = '';
    $hyLink = '/';
    $enLink = '/en/';

    if ($pageId == SUPPORT_US) {
        switch_to_blog(1);
        $hyLink = get_page_link(268);
        restore_current_blog();
        switch_to_blog(2);
        $enLink = get_page_link(41);
        restore_current_blog();
    }
    
    if ($pageId == ABOUTPROJECT) {
        switch_to_blog(1);
        $hyLink = get_page_link(25);
        restore_current_blog();
        switch_to_blog(2);
        $enLink = get_page_link(98);
        restore_current_blog();
    }
    
    if ($pageId == CONSORTIUM) {
        switch_to_blog(1);
        $hyLink = get_page_link(27);
        restore_current_blog();
        switch_to_blog(2);
        $enLink = get_page_link(96);
        restore_current_blog();
    }
    
    if ($pageId == PERSONNEL) {
        switch_to_blog(1);
        $hyLink = get_page_link(29);
        restore_current_blog();
        switch_to_blog(2);
        $enLink = get_page_link(92);
        restore_current_blog();
    }
    
    if ($pageId == PARTNERS) {
        switch_to_blog(1);
        $hyLink = get_page_link(497);
        restore_current_blog();
        switch_to_blog(2);
        $enLink = get_page_link(94);
        restore_current_blog();
    }
    
    if ($pageId == CONTACTUS) {
        switch_to_blog(1);
        $hyLink = get_page_link(7);
        restore_current_blog();
        switch_to_blog(2);
        $enLink = get_page_link(77);
        restore_current_blog();
    }
    
    if ($pageId == ABOUTUS) {
        switch_to_blog(1);
        $hyLink = get_page_link(5);
        restore_current_blog();
        switch_to_blog(2);
        $enLink = get_page_link(87);
        restore_current_blog();
    }

    if ($blog_id == 1) {
        $result = '<div class="cube current lang"><a href="' . $hyLink . '">' . __('Հայ', 'bootstrap-basic') . '</a></div>';
        $result .= '<div class="cube lang"><a href="' . $enLink . '">' . __('Eng', 'bootstrap-basic') . '</a></div>';
    } elseif ($blog_id == 2) {
        $result = '<div class="cube lang"><a href="' . $hyLink . '">' . __('Հայ', 'bootstrap-basic') . '</a></div>';
        $result .= '<div class="cube current lang"><a href="' . $enLink . '">' . __('Eng', 'bootstrap-basic') . '</a></div>';
    }

    return $result;
}

function get_gallery_translate() {
    global $blog_id;
    if ($blog_id == 1) {
        $result = '<div class="cube current lang"><a href="/gallery">' . __('Հայ', 'bootstrap-basic') . '</a></div>';
        $result .= '<div class="cube lang"><a href="/en/gallery">' . __('Eng', 'bootstrap-basic') . '</a></div>';
    } elseif ($blog_id == 2) {
        $result = '<div class="cube lang"><a href="/gallery">' . __('Հայ', 'bootstrap-basic') . '</a></div>';
        $result .= '<div class="cube current lang"><a href="/en/gallery">' . __('Eng', 'bootstrap-basic') . '</a></div>';
    }
    return $result;
}

function getTranslateLinks() {
    global $blog_id;
    $hy = '';
    $en = '';
    if (is_single()) {
        $result = get_post_translate_url(get_the_ID());
    } elseif (is_category()) {
        $result = get_category_translate_url(get_queried_object()->term_id);
    } elseif (is_page()) {
        $result = get_page_translate_url(get_the_ID());
    } elseif (is_post_type_archive('gallery')) {
         $result = get_gallery_translate();
    }
    if ($result == '') {
        if ($blog_id == 1) {
            $hy = 'current';
        }else {
            $en = 'current';
        }
        $result .= '<div class="cube ' . $hy . ' lang"><a href="/">' . __('Հայ', 'bootstrap-basic') . '</a></div>';

        $result .= '<div class="cube ' . $en . ' lang"><a href="/en">' . __('Eng', 'bootstrap-basic') . '</a></div>';
    }
    return $result;
}
