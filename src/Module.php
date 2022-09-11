<?php

declare(strict_types=1);

namespace Zae\IndexNow;

use Closure;
use Craft;
use craft\base\Element;
use craft\elements\Entry;
use craft\events\ModelEvent;
use craft\helpers\ElementHelper;
use craft\helpers\Queue;
use yii\base\Event;
use yii\base\Module as BaseModule;
use Zae\IndexNow\Jobs\PingJob;

class Module extends BaseModule
{
    /**
     * Initializes the module.
     */
    public function init(): void
    {
        Craft::setAlias('@IndexNow', $this->getBasePath());
        $this->controllerNamespace = 'IndexNow\\Console\\Controllers';

        parent::init();

        $this->registerEvents();
    }

    /**
     * @return void
     */
    private function registerEvents(): void
    {
        Event::on(Entry::class, Element::EVENT_AFTER_PROPAGATE, Closure::fromCallable([$this, 'handleUpdate']));
        Event::on(Entry::class, Element::EVENT_AFTER_DELETE, Closure::fromCallable([$this, 'handleUpdate']));
        Event::on(Entry::class, Element::EVENT_AFTER_RESTORE, Closure::fromCallable([$this, 'handleUpdate']));
    }

    /**
     * @param ModelEvent $e
     * @return void
     */
    private function handleUpdate(ModelEvent $e): void
    {
        /* @var Entry $entry */
        $entry = $e->sender;

        if (ElementHelper::isCanonical($entry) && !ElementHelper::isDraftOrRevision($entry)) {
            Queue::push(new PingJob(['entry' => $entry->id]));
        }
    }
}
