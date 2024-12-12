<?php

namespace pixeldeluxe\familytree\models;

use Craft;
use craft\base\Model;

/**
 * Local Feedback Company settings
 */
class Settings extends Model
{
    public bool $entries = true;
    public bool $categories = true;

    public string $sortOrder = "childrenFirst";

    public array $excludeSections = [];
    public array $excludeCategoryGroups = [];
    public array $excludeEntries = [];
    public array $excludeCategories = [];

    public function defineRules(): array
    {
        $rules = parent::defineRules();

        return $rules;
    }
}
