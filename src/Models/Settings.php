<?php
/**
 * Click and Collect plugin for Craft CMS 4.x
 *
 * Add drop in Click and Collect functionality to Craft Commerce.
 *
 * @link        https://burnthebook.co.uk
 * @author      Michael Burton <mikey@burnthebook.co.uk>
 * @since       0.0.1
 * @copyright   Copyright (c) 2022 burnthebook
 */

namespace burnthebook\ClickCollect\Models;

use craft\base\Model;

/**
 * Plugin settings object, containing values for configuring the plugin
 *
 * @author    burnthebook
 * @package   ClickCollect
 * @since     0.0.1
 */
class Settings extends Model
{
    /**
     * @var string The public-facing name of the plugin
     */
    public $pluginName = 'Click & Collect';

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['pluginName'], 'required'],
        ];
    }

}