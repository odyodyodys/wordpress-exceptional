<?php
/**
 * Template engine base for displaying notices
 *
 * @author Antonis
 */
abstract class Exceptional_NoticesTemplate extends Exceptional_ATemplate
{
    /**
     * Displays the notices
     */
    abstract function DisplayNotices($notices);
}
