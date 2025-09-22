<?php

namespace pixeldeluxe\familytree;

use Craft;
use craft\base\Element;
use craft\base\Event;
use craft\base\Model;
use craft\base\Plugin as BasePlugin;
use craft\elements\Category;
use craft\elements\Entry;
use pixeldeluxe\familytree\models\Settings;
use pixeldeluxe\familytree\web\assets\element\ElementAsset;

/**
 * Child Viewer plugin
 *
 * @method static Plugin getInstance()
 * @author pixeldeluxe
 * @copyright pixeldeluxe
 * @license https://craftcms.github.io/license/ Craft License
 */
class Plugin extends BasePlugin
{
    public string $schemaVersion = '1.0.0';

    public bool $hasCpSettings = true;

    public function init(): void
    {
        parent::init();

        $this->attachEventHandlers();

        Craft::$app->onInit(function() {
            // ...
        });
    }

    public static function config(): array
    {
        return [
            'components' => [

            ],
        ];
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('family-tree/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    private function attachEventHandlers(): void
    {

        if($this->getSettings()->entries){
            Event::on(
                Entry::class,
                Entry::EVENT_DEFINE_META_FIELDS_HTML,
                function ($event) {
                    $entry = $event->sender;

                    if ($entry instanceof Entry && $entry->section !== null) {
                        if ($entry->section->type !== "structure") {
                            return null;
                        }

                        $settings = $this->getSettings();
                        if (in_array($entry->section->handle, $settings->excludeSections) || in_array($entry->id, $settings->excludeEntries)) {
                            return null;
                        }

                        Craft::$app->getView()->registerAssetBundle(ElementAsset::class);

                        if ($settings->sortOrder == "childrenFirst") {
                            $event->html .= Craft::$app->view->renderTemplate('family-tree/children', ['element' => $entry]);
                            $event->html .= Craft::$app->view->renderTemplate('family-tree/siblings', ['element' => $entry]);
                        } else {
                            $event->html .= Craft::$app->view->renderTemplate('family-tree/siblings', ['element' => $entry]);
                            $event->html .= Craft::$app->view->renderTemplate('family-tree/children', ['element' => $entry]);
                        }
                    }
                }
            );
        }

        if($this->getSettings()->categories) {
            Event::on(
                Category::class,
                Category::EVENT_DEFINE_META_FIELDS_HTML,
                function ($event) {
                    $category = $event->sender;

                    $settings = $this->getSettings();
                    if(in_array($category->group->handle,$settings->excludeCategoryGroups) || in_array($category->id, $settings->excludeCategories)){
                        return null;
                    }

                    Craft::$app->getView()->registerAssetBundle(ElementAsset::class);
                    if($settings->sortOrder == "childrenFirst") {
                        $event->html .= Craft::$app->view->renderTemplate('family-tree/children', ['element' => $category]);
                        $event->html .= Craft::$app->view->renderTemplate('family-tree/siblings', ['element' => $category]);
                    }else{
                        $event->html .= Craft::$app->view->renderTemplate('family-tree/siblings', ['element' => $category]);
                        $event->html .= Craft::$app->view->renderTemplate('family-tree/children', ['element' => $category]);
                    }
                }
            );
        }
    }
}
