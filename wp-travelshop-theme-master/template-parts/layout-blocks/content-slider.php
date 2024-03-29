<?php

/**
 * <code>
 *  $args = [
 *      'items' => [
 *          [
 *             'type' => 'content', // enum(content, product)
 *             'title' => 'Weihnachtsmarkt auf der Fraueninsel mit Gut Aiderbichl',
 *             'text' => 'Ein romantisches Wintermärchen, das jedes Jahr Besucher von Nah und Fern aufs Neue begeistert, ist der Christkindlmarkt auf der Frauninsel. Festliche Beleuchtung, feinstes kunstwerk ...',
 *             'image_post_id' => 12,
 *             'image' => get_stylesheet_directory_uri().'/assets/img/slide-1.jpg',
 *             'image_alt_tag' => '',
 *             'btn_link' => '/blog/',
 *             'btn_link_target' => '', // enum(_blank, _self)
 *             'btn_label' => 'Weiterlesen'
 *          ],
 *          [
 *              'type' => 'product',
 *              'pm-id' => '545926'
 *              'image_type' => '', // enum(from_product, custom)
 *              'image' => get_stylesheet_directory_uri().'/assets/img/slide-1.jpg', // img src if image_type is 'custom'
 *              'image_alt_tag' => '',
 *          ]
 *      ]
 *  ];
 * </code>
 * @var array $args
 */

use \Pressmind\Travelshop\Search;
use Pressmind\Travelshop\Template;

if (empty($args)) {
    return;
}

$slide_items = [];
foreach ($args['items'] as $item) {
    if ($item['type'] == 'product') {
        if(empty($item['pm-id'])){
            continue;
        }
        $product = Search::getResult(['pm-id' => (int)$item['pm-id']],2, 1, false, false, TS_TTL_FILTER, TS_TTL_SEARCH);
        if(empty($product['items']) === true){
            continue;
        }
        $item_origin = $item;
        $item = array_merge($item, $product['items'][0]);
        if($item['image_type'] == 'from_product' || empty($item['image_type'])){
            if(!empty($product['items'][0]['bigslide']['url'])) {
                $item['image'] = $product['items'][0]['bigslide']['url'];
                $item['image_alt_tag'] = $product['items'][0]['bigslide']['copyright'];
            }else{
                $item['image'] = SITE_URL . "/placeholder.svg?wh=250x170&text=image is not set";
            }
        } else {
            $item['image'] = $item_origin['image'];
        }
    }elseif($item['type'] == 'content'){
        if(!empty($item['image_post_id'])){
            $item['image'] = wp_get_attachment_image_url($item['image_post_id'], 'bigslide');
        }else{
            $item['image'] = SITE_URL . "/placeholder.svg?wh=250x170&text=image is not set";
        }
    }
    $slide_items[] = $item;
}
?>
<section class="content-block content-block-content-slider" id="content-slider-<?php echo $args['uid']; ?>">
    <div class="content-slider-inner">
        <?php
        foreach ($slide_items as $item) {
            ?>
            <article class="content-slider-item">
                <?php
                if ($item['type'] == 'content') {
                    if ($item['media_type'] == 'video' ) {
                    ?>
                        <div class="content-slider-video">

                            <?php
                            $video_src = wp_get_attachment_url( $item['video'] );
                            ?>
                            <div class="media media-video media-cover ratio-16x9 ratio-md-21x5">
                                <video autoplay muted loop style="pointer-events: none;">
                                    <source src="<?php echo $video_src; ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>

                        </div>
                    <?php
                    } else {
                    ?>
                        <div class="content-slider-image">
                            <div class="media media-cover ratio-16x9 ratio-md-21x5">
                                <img title="<?php echo !empty($args['headline']) ? $args['headline'] : ''; ?>" src="<?php echo $item['image']; ?>" />
                            </div>
                        </div>
                    <?php
                    }
                ?>
                <?php } else { ?>
                    <div class="content-slider-image">
                        <div class="media media-cover ratio-16x9 ratio-md-21x5">
                            <img title="<?php echo !empty($args['headline']) ? $args['headline'] : ''; ?>" src="<?php echo $item['image']; ?>" />
                        </div>
                    </div>
                <?php } ?>
                <div class="content-slider-content content-slider-content-<?php echo $item['type']; ?>">
                    <div class="content-slider-container">
                        <?php
                        if ($item['type'] == 'content') {
                            ?>
                            <div class="content-slider-box">
                                    <?php if(!empty($item['title'])){?>
                                    <h1 class="content-slider-box-title"><?php echo $item['title']; ?></h1>
                                    <?php } ?>
                                    <?php if(!empty($item['text'])){?>
                                        <p class="content-slider-box-text">
                                            <?php echo $item['text']; ?>
                                        </p>
                                    <?php } ?>
                                    <?php if(!empty($item['btn_link'])){?>
                                    <a class="btn btn-primary" href="<?php echo $item['btn_link']; ?>" target="<?php echo !empty($item['btn_link_target']) ? $item['btn_link_target'] : '_self';?>"><?php
                                        if (!empty($item['btn_label'])) {
                                            echo $item['btn_label'];
                                        } else {
                                            echo "Mehr erfahren";
                                        }
                                        ?></a>
                                    <?php } ?>
                            </div>
                            <?php
                        } elseif ($item['type'] == 'product') {
                            echo Template::render(get_stylesheet_directory().'/template-parts/pm-views/Teaser6.php', $item);
                        }
                        ?>
                    </div>
                </div>
            </article>
            <?php
        }
        if(empty($slide_items) && isset($_GET['fl_builder'])){
            echo '<p height="50px;">{content Slider without valid slides is placed here}</p>';
        }
        ?>
    </div>

    <?php load_template( get_stylesheet_directory().'/template-parts/micro-templates/slider-controls.php', false, []); ?>

</section>
