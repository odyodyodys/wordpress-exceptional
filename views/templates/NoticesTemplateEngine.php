<?php
/**
 * Template engine base for displaying notices
 *
 * @author Antonis
 */
abstract class Exceptional_NoticesTemplateEngine extends Exceptional_TemplateEngineBase
{
    /**
     * Displays the notices
     */
    abstract function DisplayNotices($notices);
}
