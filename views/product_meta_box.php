<?php
if (!defined("ABSPATH")) {
	exit;
}

global $post;

$custom = get_post_custom($post->ID);

$houses = get_posts(array(
    "post_type"=>Sputnik::POST_TYPE
));
?>
<p>
	<select name="<?=Sputnik::PRODUCT_HOUSE_FIELD?>">
        <?foreach($houses as $house):?>
            <?$checked = @$custom[Sputnik::PRODUCT_HOUSE_FIELD][0]==$house->ID?>
            <option value="<?=$house->ID?>" <?if($checked):?>selected="true"<?endif?>>
                <?=htmlspecialchars($house->post_title)?>
            </option>
        <?endforeach?>
	</select>
</p>