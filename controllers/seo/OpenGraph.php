<?php
/**
 * Open graph support
 *
 * @author Odys
 */
class Exceptional_OpenGraph extends Exceptional_AController
{
    public function Init()
    {
        // add opengraphnamespaces on header
        add_action('language_attributes', array($this, 'ActionLanguageAttributes'));
    }

    public function ActionLanguageAttributes($output)
    {
        return $output.' prefix="og: http://ogp.me/ns#"';
    }

    /**
     * 
     * @param type $appId The facebook app id
     * @param type $title The current page title
     * @param type $description The description
     * @param type $type The og:type
     * @param type $canonicalUrl The canonical url of the page
     * @param array $images The images of the page
     * @param type $locale The local of the page
     * @param array $altLocals Other locals the page is available at
     * @param array $customProperties keyValue array with property/content for custom meta properties
     */
    public function AddHeaderMeta($appId, $title, $description, $type, $canonicalUrl, $images, $locale, $altLocals = null, $customProperties = null)
    {?>
        <meta property="fb:app_id" content="<?php echo $appId; ?>"/>
        <meta property="og:site_name" content="<?php echo get_bloginfo('name'); ?>" />
        <meta property="og:title" content="<?php echo esc_attr($title); ?>" />
        <meta property="og:description" content="<?php echo esc_attr($description); ?>" />
        <meta property="og:type" content="<?php echo $type; ?>" /><?php
        if (!empty($images)):
            foreach ($images as $image):?>
                <meta property="og:image" content="<?php echo esc_attr($image); ?>" /><?php
            endforeach;
        endif;?>
        <meta property="og:url" content="<?php echo esc_attr($canonicalUrl); ?>" />
        <meta property="og:locale" content="<?php echo $locale; ?>" /><?php
        foreach ($altLocals as $altLoc):?>
            <meta property="og:locale:alternate" content="<?php echo $altLoc ?>" /><?php
        endforeach;
        if (!empty($customProperties)):
            foreach ($customProperties as $property => $content):?>
            <meta property="<?php echo esc_attr($property);?>" content="<?php echo esc_attr($content);?>" /><?php
            endforeach;
        endif;
    }
}
