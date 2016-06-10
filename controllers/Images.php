<?php
/**
 * Controller for displaying images. It uses picturefill https://github.com/scottjehl/picturefill
 *
 * @author Odys
 */
class Exceptional_Images extends Exceptional_AController
{    
    /**
     * @var array key/value pair of query-name and media query
     */
    private $_breakingPointQueries;
    
    /**
     * @var array key/value where key the breakingPoint key and value the image-size
     */
    private $_sizes;
    
    /**
     * An image size to use as a fallback for legit browsers
     * @var string
     */
    private $_fallbackSize;

    /**
     * Constrcutror
     */
    protected function __construct()
    {
        parent::__construct();
        
        $this->_breakingPointQueries = array();
    }
        
    /**
     * Registers a breaking point by defining a key and a media query
     * @param string $key A unique key for the query
     * @param string $mediaQuery The css media query
     */
    public function RegisterBreakingPoint($key, $mediaQuery)
    {
        $this->_breakingPointQueries[$key] = $mediaQuery;
    }
    
    /**
     * @param array $sizes image size for each breaking point
     * @param string $fallbackSize Optional fallback image size
     */
    public function SetSizes($sizes, $fallbackSize = 'thumbnail')
    {
        $this->_sizes = $sizes;
        $this->_fallbackSize = $fallbackSize;
    }
    
    /**
     * When done with pictures, you must reset sizes so they aren't used elswhere
     */
    public function ResetSizes()
    {
        unset($this->_sizes);
    }
    
    /**
     * Outputs the image using the current responsive pic methodology
     * @param int $id The attachment id
     */
    public function ThePicture($id, $class = null, $alt = null)
    {
        // instead of calling wp_get_attachment_src multiple times, get all results and just construct results
        $picDir = dirname(wp_get_attachment_url($id));        
        $picMeta = wp_get_attachment_metadata($id, true);
        if (empty($picMeta))
        {
            // no attachment
            return;
        }
        $defaultSizeFile = basename($picMeta['file']);
        
        // get image data (src, width, height) for registered size. Note: many breaking points might use the same image size.
        $imageData = array();
        foreach ($this->_sizes as $imageSize)
        {
            $imageData[$imageSize] = path_join( $picDir, array_key_exists($imageSize, $picMeta['sizes']) ? $picMeta['sizes'][$imageSize]['file'] : $defaultSizeFile);
        }
        // if fallbackSize isn't included in _sizes, add this also
        if (!array_key_exists($this->_fallbackSize, $this->_sizes))
        {
            $imageData[$this->_fallbackSize] = path_join( $picDir, array_key_exists($this->_fallbackSize, $picMeta['sizes']) ? $picMeta['sizes'][$this->_fallbackSize]['file'] : $defaultSizeFile);
        }
        
        // get alt if needed
        if (empty($alt))
        {
            $meta = get_post_meta($id, '_wp_attachment_image_alt', true);
            if(!empty($meta))
            {
                $alt = $meta;
            }
        }
        if (empty($alt)) // couldn't get alt, take caption or title instead. Similar to what wp_get_attachment_image does
        {
            $post = get_post($id);
            $alt = trim(strip_tags($post->post_excerpt));
            if (empty($alt))
            {
                $alt = trim(strip_tags($post->post_title));
            }
        }
        
        // iterate through all sizes and create the <source> for each one together with their media
        ?>        
        <picture><?php
            // force IE9 to recognise the <source> tags?>
            <!--[if IE 9]><video style="display: none;"><![endif]--><?php
            foreach ($this->_sizes as $key => $value):?>
                <source srcset="<?php echo $imageData[$value];?>" media="<?php echo $this->_breakingPointQueries[$key]; ?>"/><?php
            endforeach;?>
            <!--[if IE 9]></video><![endif]--><?php
            // fallback image?>
            <img class="<?php echo $class;?>" srcset="<?php echo $imageData[$this->_fallbackSize];?>" alt="<?php echo $alt; ?>"/>
        </picture><?php
    }
}